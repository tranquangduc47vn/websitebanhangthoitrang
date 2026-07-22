<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'services/ai/IntentParser.php';
require_once APPPATH . 'services/ai/FaqMatcher.php';
require_once APPPATH . 'services/ai/ProductRecommender.php';
require_once APPPATH . 'services/ai/OrderLookupService.php';
require_once APPPATH . 'services/ai/PolicyContextProvider.php';
require_once APPPATH . 'services/ai/ShopKnowledgeProvider.php';
require_once APPPATH . 'services/ai/PromptBuilder.php';
require_once APPPATH . 'services/ai/ConversationLogger.php';
require_once APPPATH . 'services/ai/RateLimiter.php';
require_once APPPATH . 'services/ai/providers/ProviderFactory.php';

// AI assistant: rate-limit → FAQ → context → provider; PHP fetches all DB data.
class AiAssistantService {

	public function handle($message, $conversationId, $userId, $guestToken)
	{
		$CI =& get_instance();
		$CI->config->load('ai', true);

		$message = trim((string) $message);
		$userId = (int) $userId;

		$logger = new ConversationLogger();
		$conversation = $logger->resolveConversation($userId, $guestToken, $conversationId);

		if ($message === '') {
			return $this->respond($conversation->id, 'Bạn vui lòng nhập nội dung cần hỏi nhé.', false, array());
		}

		$rate = new RateLimiter();
		$maxMsg = (int) $CI->config->item('ai_rate_limit_max_messages', 'ai');
		$window = (int) $CI->config->item('ai_rate_limit_window_seconds', 'ai');
		if (!$rate->allow($maxMsg, $window)) {
			return $this->respond($conversation->id, 'Bạn gửi tin nhanh quá, vui lòng chờ một chút rồi thử lại nhé.', false, array());
		}

		$logger->logUserMessage($conversation->id, $message);

		$settings = $this->loadSettings($CI);

		if (!$settings['ai_enabled']) {
			if ($settings['staff_support_enabled']) {
				$text = 'Trợ lý AI hiện đang tắt. Mình chuyển bạn sang nhân viên hỗ trợ nhé.';
				$logger->logAiMessage($conversation->id, $text, array('intent' => 'disabled'));
				$logger->logSystemMessage($conversation->id, '🤝 Đang kết nối nhân viên...');
				$logger->touch($conversation->id, 'waiting_staff');
				return $this->respond($conversation->id, $text, true, array());
			}
			$text = 'Trợ lý AI hiện đang tắt. Vui lòng thử lại sau.';
			$logger->logAiMessage($conversation->id, $text, array('intent' => 'disabled'));
			$logger->touch($conversation->id, 'ai_active');
			return $this->respond($conversation->id, $text, false, array());
		}

		$parser = new IntentParser();
		$parsed = $parser->parse($message);

		if ($parsed['intent'] === 'handoff') {
			if ($settings['staff_support_enabled']) {
				$text = 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.';
				$logger->logAiMessage($conversation->id, $text, array('intent' => 'handoff'));
				$logger->logSystemMessage($conversation->id, '🤝 Đang kết nối nhân viên...');
				$logger->touch($conversation->id, 'waiting_staff');
				return $this->respond($conversation->id, $text, true, array());
			}
			$text = 'Hiện chưa thể chuyển sang nhân viên trực tuyến, bạn vui lòng để lại câu hỏi để mình hỗ trợ nhé.';
			$logger->logAiMessage($conversation->id, $text, array('intent' => 'handoff'));
			$logger->touch($conversation->id, 'ai_active');
			return $this->respond($conversation->id, $text, false, array());
		}

		if ($parsed['intent'] === 'exchange') {
			$policy = new PolicyContextProvider();
			$text = $policy->buildExchangeSizeGuide();
			$logger->logAiMessage($conversation->id, $text, array('intent' => 'exchange'));
			$logger->touch($conversation->id, 'ai_active');
			return $this->respond($conversation->id, $text, false, array());
		}

		// FAQ trước — không gọi AI nếu đã khớp.
		$CI->load->model('ai_faq_model');
		$faqRows = $CI->ai_faq_model->get_active();
		$faqMatcher = new FaqMatcher();
		$threshold = (float) $CI->config->item('ai_faq_match_threshold', 'ai');
		$faqMatch = $faqMatcher->match($message, $faqRows, $threshold);

		if ($faqMatch !== null) {
			$answer = $faqMatch['faq']->answer;
			$logger->logAiMessage($conversation->id, $answer, array(
				'intent' => 'faq',
				'faq_id' => (int) $faqMatch['faq']->id,
				'score' => $faqMatch['score'],
			));
			$logger->touch($conversation->id);
			return $this->respond($conversation->id, $answer, false, array());
		}

		list($contextText, $products) = $this->buildContext($parsed, $userId, $message);

		$historySize = (int) $CI->config->item('ai_history_context_size', 'ai');
		$history = $logger->history($conversation->id, $historySize);
		$historyForPrompt = array();
		foreach ($history as $h) {
			$historyForPrompt[] = array('sender' => $h->sender, 'content' => $h->content);
		}
		if (!empty($historyForPrompt)) {
			array_pop($historyForPrompt);
		}

		$promptBuilder = new PromptBuilder();
		$messages = $promptBuilder->build($message, $contextText, $historyForPrompt);

		$provider = ProviderFactory::make();
		$result = $provider->complete($messages);

		$handoff = false;
		$content = trim((string) $result['content']);
		if (!$result['ok'] || $content === '') {
			$content = 'Xin lỗi, mình chưa thể trả lời câu này.';
			if ($settings['fallback_to_staff'] && $settings['staff_support_enabled']) {
				$content .= ' Mình sẽ chuyển bạn sang nhân viên hỗ trợ.';
				$handoff = true;
			}
		}

		$logger->logAiMessage($conversation->id, $content, array(
			'intent' => $parsed['intent'],
			'provider' => $provider->name(),
			'ok' => $result['ok'],
		));

		if ($handoff) {
			$logger->logSystemMessage($conversation->id, '🤝 Đang kết nối nhân viên...');
			$logger->touch($conversation->id, 'waiting_staff');
		} else {
			$logger->touch($conversation->id, 'ai_active');
		}

		return $this->respond($conversation->id, $content, $handoff, $products);
	}

	protected function buildContext(array $parsed, $userId, $message = '')
	{
		$knowledge = new ShopKnowledgeProvider();
		$contextParts = array($knowledge->buildBase($userId));
		$products = array();

		if ($parsed['intent'] === 'product') {
			$products = $knowledge->searchProducts($parsed['filters'], 5);
			$contextParts[] = $knowledge->formatProductList($products, 'Sản phẩm phù hợp câu hỏi');
		} elseif ($parsed['intent'] === 'order' && $userId <= 0) {
			$contextParts[] = 'Khách chưa đăng nhập: không tra cứu đơn hàng cá nhân. Yêu cầu đăng nhập, không đoán mã đơn.';
		} elseif ($parsed['intent'] !== 'product' && $parsed['intent'] !== 'order') {
			$parser = new IntentParser();
			$softFilters = $parser->productFilters($message);
			if (!empty($softFilters)) {
				$products = $knowledge->searchProducts($softFilters, 5);
				if (!empty($products)) {
					$contextParts[] = $knowledge->formatProductList($products, 'Sản phẩm tìm thêm theo câu hỏi');
				}
			}
		}

		return array(implode("\n\n", array_filter($contextParts)), $products);
	}

	protected function loadSettings($CI)
	{
		$CI->load->model('ai_setting_model');
		return array(
			'ai_enabled' => $CI->ai_setting_model->get_bool('ai_enabled', true),
			'staff_support_enabled' => $CI->ai_setting_model->get_bool('staff_support_enabled', true),
			'fallback_to_staff' => $CI->ai_setting_model->get_bool('fallback_to_staff', true),
		);
	}

	protected function respond($conversationId, $content, $handoff, array $products)
	{
		return array(
			'ok' => true,
			'conversation_id' => (int) $conversationId,
			'content' => $content,
			'handoff' => (bool) $handoff,
			'products' => $products,
		);
	}
}
