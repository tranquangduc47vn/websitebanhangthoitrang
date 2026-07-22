<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FaqMatcher {

	public function match($message, array $faqRows, $threshold = 0.45)
	{
		$queryTokens = $this->tokenize($message);
		if (empty($queryTokens) || empty($faqRows)) {
			return null;
		}

		$best = null;
		$bestScore = 0.0;

		foreach ($faqRows as $faq) {
			$haystack = $faq->question . ' ' . $faq->keywords;
			$tokens = $this->tokenize($haystack);
			$score = $this->overlapScore($queryTokens, $tokens);
			$score += $this->keywordPhraseBonus($message, (string) $faq->keywords);
			if ($score > $bestScore) {
				$bestScore = $score;
				$best = $faq;
			}
		}

		if ($best !== null && $bestScore >= $threshold) {
			return array('faq' => $best, 'score' => $bestScore);
		}
		return null;
	}

	protected function tokenize($text)
	{
		$text = mb_strtolower((string) $text, 'UTF-8');
		$text = preg_replace('/[^\p{L}\p{N}\s,]+/u', ' ', $text);
		$parts = preg_split('/[\s,]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
		$stopwords = array(
			'tôi', 'bạn', 'là', 'có', 'của', 'và', 'cho', 'ạ', 'nhé', 'với', 'vậy', 'thì',
			'làm', 'sao', 'để', 'như', 'thế', 'nào', 'gì', 'được', 'không', 'muốn', 'cần',
			'hỏi', 'mình', 'em', 'shop', 'thể', 'hay', 'à', 'nhỉ', 'đâu', 'khi', 'ra', 'về',
			'xin', 'dạ', 'vui', 'lòng', 'biết', 'giúp', 'hả', 'hen',
		);
		$tokens = array_values(array_diff(array_unique($parts), $stopwords));
		return $tokens;
	}

	protected function overlapScore(array $queryTokens, array $targetTokens)
	{
		if (empty($queryTokens) || empty($targetTokens)) {
			return 0.0;
		}
		$common = array_intersect($queryTokens, $targetTokens);
		return count($common) / count($queryTokens);
	}

	// Bonus when message contains FAQ keyword phrase.
	protected function keywordPhraseBonus($message, $keywordsCsv)
	{
		$normalized = mb_strtolower(trim((string) $message), 'UTF-8');
		$bonus = 0.0;
		foreach (preg_split('/[,;]+/u', $keywordsCsv) as $phrase) {
			$phrase = trim(mb_strtolower($phrase, 'UTF-8'));
			if ($phrase !== '' && mb_strpos($normalized, $phrase) !== false) {
				$bonus = max($bonus, 0.35);
			}
		}
		return $bonus;
	}
}
