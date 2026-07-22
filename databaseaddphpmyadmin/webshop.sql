-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th7 22, 2026 lúc 04:07 PM
-- Phiên bản máy phục vụ: 8.0.46
-- Phiên bản PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `webshop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `level` int NOT NULL,
  `created` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `level`, `created`) VALUES
(1, 'Công Quôc', 'congquoc@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 1, 2147483647),
(2, 'quoc', 'quoc@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 0, 2147483647),
(4, 'nguyencongquoc', 'congquoc1@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 2, 1782385775),
(5, 'Admin', 'admin@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 0, 1784631043),
(6, 'Quang Đức', 'user@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 2, 1784631249);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ai_conversation`
--

CREATE TABLE `ai_conversation` (
  `id` int NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `guest_token` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `staff_id` int NOT NULL DEFAULT '0',
  `staff_name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `status` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'open',
  `started` int NOT NULL DEFAULT '0',
  `last_message` int NOT NULL DEFAULT '0',
  `unread_staff` tinyint(1) NOT NULL DEFAULT '0',
  `unread_customer` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ai_conversation`
--

INSERT INTO `ai_conversation` (`id`, `user_id`, `guest_token`, `staff_id`, `staff_name`, `status`, `started`, `last_message`, `unread_staff`, `unread_customer`) VALUES
(12, 10, '', 0, '', 'closed', 1784645901, 1784651362, 0, 0),
(13, 11, '', 0, '', 'ai_active', 1784651096, 1784721074, 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ai_faq`
--

CREATE TABLE `ai_faq` (
  `id` int NOT NULL,
  `question` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `keywords` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `category` varchar(60) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created` int NOT NULL DEFAULT '0',
  `updated` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ai_faq`
--

INSERT INTO `ai_faq` (`id`, `question`, `answer`, `keywords`, `category`, `sort_order`, `is_active`, `created`, `updated`) VALUES
(1, 'Chính sách đổi trả như thế nào?', 'Bạn có thể đổi trả sản phẩm trong vòng 7 ngày kể từ khi nhận hàng nếu sản phẩm còn nguyên tem mác, chưa qua sử dụng. Vui lòng liên hệ nhân viên để được hướng dẫn chi tiết.', 'đổi trả, hoàn trả, trả hàng, đổi hàng', 'policy', 1, 1, 1784644874, 1784644874),
(2, 'Cửa hàng nhận thanh toán bằng hình thức nào?', 'Cửa hàng hỗ trợ thanh toán khi nhận hàng (COD) và chuyển khoản/QR ngay khi đặt hàng.', 'thanh toán, cod, chuyển khoản, qr', 'policy', 2, 1, 1784644874, 1784644874),
(3, 'Làm sao để theo dõi đơn hàng của tôi?', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', 'theo dõi đơn hàng, tra cứu đơn, trạng thái đơn', 'order', 3, 1, 1784644874, 1784644874),
(4, 'Giờ làm việc của cửa hàng?', 'Cửa hàng phục vụ từ 08h00 đến 21h00 tất cả các ngày trong tuần.', 'giờ làm việc, mở cửa, đóng cửa', 'general', 4, 1, 1784644874, 1784644874);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ai_message`
--

CREATE TABLE `ai_message` (
  `id` int NOT NULL,
  `conversation_id` int NOT NULL,
  `sender` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'user',
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `meta` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `created` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ai_message`
--

INSERT INTO `ai_message` (`id`, `conversation_id`, `sender`, `content`, `meta`, `created`) VALUES
(37, 12, 'customer', 'Tư vấn size', '[]', 1784645901),
(38, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"null\",\"ok\":false}', 1784645901),
(39, 12, 'customer', 'Tư vấn size', '[]', 1784646371),
(40, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"null\",\"ok\":false}', 1784646371),
(41, 12, 'customer', 'Theo dõi đơn hàng', '[]', 1784646396),
(42, 12, 'ai', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', '{\"intent\":\"faq\",\"faq_id\":3,\"score\":1}', 1784646396),
(43, 12, 'customer', 'Tư vấn size', '[]', 1784646407),
(44, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"null\",\"ok\":false}', 1784646407),
(45, 12, 'customer', 'Tư vấn size', '[]', 1784646794),
(46, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"null\",\"ok\":false}', 1784646794),
(47, 12, 'customer', 'Gặp nhân viên', '[]', 1784646801),
(48, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784646801),
(49, 12, 'customer', 'Gặp nhân viên', '[]', 1784646943),
(50, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784646943),
(51, 12, 'customer', 'Sản phẩm bán chạy', '[]', 1784647422),
(52, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-1.5-flash\",\"ok\":false}', 1784647424),
(53, 12, 'customer', 'Tư vấn size', '[]', 1784647434),
(54, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-1.5-flash\",\"ok\":false}', 1784647434),
(55, 12, 'customer', 'Tư vấn size', '[]', 1784647438),
(56, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-1.5-flash\",\"ok\":false}', 1784647438),
(57, 12, 'customer', 'Chính sách đổi trả', '[]', 1784647440),
(58, 12, 'ai', 'Bạn có thể đổi trả sản phẩm trong vòng 7 ngày kể từ khi nhận hàng nếu sản phẩm còn nguyên tem mác, chưa qua sử dụng. Vui lòng liên hệ nhân viên để được hướng dẫn chi tiết.', '{\"intent\":\"faq\",\"faq_id\":1,\"score\":1}', 1784647440),
(59, 12, 'customer', 'Sản phẩm bán chạy', '[]', 1784647443),
(60, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-1.5-flash\",\"ok\":false}', 1784647443),
(61, 12, 'customer', 'Theo dõi đơn hàng', '[]', 1784647445),
(62, 12, 'ai', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', '{\"intent\":\"faq\",\"faq_id\":3,\"score\":1}', 1784647445),
(63, 12, 'customer', 'Hướng dẫn thanh toán', '[]', 1784647447),
(64, 12, 'ai', 'Cửa hàng hỗ trợ thanh toán khi nhận hàng (COD) và chuyển khoản/QR ngay khi đặt hàng.', '{\"intent\":\"faq\",\"faq_id\":2,\"score\":0.5}', 1784647447),
(65, 12, 'customer', 'Theo dõi đơn hàng', '[]', 1784647463),
(66, 12, 'ai', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', '{\"intent\":\"faq\",\"faq_id\":3,\"score\":1}', 1784647463),
(67, 12, 'customer', 'Sản phẩm bán chạy', '[]', 1784647464),
(68, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-1.5-flash\",\"ok\":false}', 1784647465),
(69, 12, 'customer', 'Theo dõi đơn hàng', '[]', 1784647507),
(70, 12, 'ai', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', '{\"intent\":\"faq\",\"faq_id\":3,\"score\":1}', 1784647507),
(71, 12, 'customer', 'Chính sách đổi trả', '[]', 1784647509),
(72, 12, 'ai', 'Bạn có thể đổi trả sản phẩm trong vòng 7 ngày kể từ khi nhận hàng nếu sản phẩm còn nguyên tem mác, chưa qua sử dụng. Vui lòng liên hệ nhân viên để được hướng dẫn chi tiết.', '{\"intent\":\"faq\",\"faq_id\":1,\"score\":1}', 1784647509),
(73, 12, 'customer', 'Sản phẩm bán chạy', '[]', 1784647513),
(74, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-1.5-flash\",\"ok\":false}', 1784647513),
(75, 12, 'customer', 'Gặp nhân viên', '[]', 1784647542),
(76, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784647542),
(77, 12, 'customer', 'Giải thích ngắn gọn 3 bước mua hàng trên website này', '[]', 1784647562),
(78, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-1.5-flash\",\"ok\":false}', 1784647563),
(79, 12, 'customer', 'xin chào', '[]', 1784647808),
(80, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-1.5-flash\",\"ok\":false}', 1784647808),
(81, 12, 'customer', 'Giải thích ngắn gọn 3 bước mua hàng trên website này.', '[]', 1784647947),
(82, 12, 'ai', 'Dạ chào bạn, hiện tại em chưa có thông tin cụ thể về 3', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.6-flash\",\"ok\":true}', 1784647950),
(83, 12, 'customer', 'chào bạn', '[]', 1784647967),
(84, 12, 'ai', 'Dạ em chào bạn ạ! Em là trợ lý tư vấn của thời trang JM. Em có thể hỗ trợ gì cho bạn về thông tin cửa hàng, sản phẩm hay chính sách mua sắm ạ?', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.6-flash\",\"ok\":true}', 1784647971),
(85, 12, 'customer', 'chào bạn', '[]', 1784648008),
(86, 12, 'ai', 'Dạ em chào bạn ạ! Em có thể giúp gì cho bạn về thông tin cửa hàng, sản phẩm hoặc các chính sách mua sắm tại JM hôm nay ạ?', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.6-flash\",\"ok\":true}', 1784648011),
(87, 12, 'customer', 'Giải thích ngắn gọn 3 bước mua hàng trên website này.', '[]', 1784648074),
(88, 12, 'ai', 'Dạ, hiện tại thông tin về 3 bước mua hàng cụ thể trên website chưa được cập nhật trong hệ thống của em. Bạn vui lòng liên hệ trực tiếp qua hotline 0964170476 hoặc email marketing@jm.com.vn để nhân viên hỗ trợ thông tin chi tiết nhất nhé!', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784648076),
(89, 12, 'customer', 'Tư vấn size', '[]', 1784648109),
(90, 12, 'ai', 'Dạ, hiện tại em chưa có thông tin chi tiết về bảng size cho từng sản phẩm. Để được tư vấn size chính xác nhất, bạn vui lòng nhắn tin trực tiếp cho nhân viên hỗ trợ hoặc cung cấp chiều cao, cân nặng để bên em hỗ trợ kiểm tra giúp bạn nhé!', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784648113),
(91, 12, 'customer', 'Gặp nhân viên', '[]', 1784648194),
(92, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784648194),
(93, 12, 'customer', 'shop này bán gì', '[]', 1784648256),
(94, 12, 'ai', 'Chào bạn, JM Dress Design là thương hiệu thời trang thiết kế Việt Nam dành cho các quý cô công sở hiện đại. Các sản phẩm của JM mang tính ứng dụng cao, giúp bạn cân bằng giữa công việc, cuộc sống và phong cách cá nhân một cách tinh tế.\n\nNếu bạn cần tư vấn cụ thể hơn về sản phẩm, mời bạn liên hệ hotline 0964170476 hoặc email marketing@jm.com.vn để nhân viên hỗ trợ nhé!', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784648261),
(95, 12, 'customer', 'giờ làm', '[]', 1784648349),
(96, 12, 'ai', 'Cửa hàng phục vụ từ 08h00 đến 21h00 tất cả các ngày trong tuần.', '{\"intent\":\"faq\",\"faq_id\":4,\"score\":1}', 1784648349),
(97, 12, 'customer', 'theo dõi đơn hàng', '[]', 1784648719),
(98, 12, 'ai', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', '{\"intent\":\"faq\",\"faq_id\":3,\"score\":1}', 1784648719),
(99, 12, 'customer', 'kiểm tra đơn hàng của tôi', '[]', 1784648732),
(100, 12, 'ai', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', '{\"intent\":\"faq\",\"faq_id\":3,\"score\":0.75}', 1784648732),
(101, 12, 'customer', 'Gặp nhân viên', '[]', 1784649193),
(102, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784649193),
(103, 12, 'customer', 'Gặp nhân viên', '[]', 1784649451),
(104, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784649451),
(105, 12, 'customer', 'Gặp nhân viên', '[]', 1784649581),
(106, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784649581),
(107, 12, 'customer', 'Gặp nhân viên', '[]', 1784649766),
(108, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784649766),
(109, 12, 'customer', 'Gặp nhân viên', '[]', 1784649847),
(110, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784649847),
(111, 12, 'customer', 'Gặp nhân viên', '[]', 1784649908),
(112, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784649908),
(113, 12, 'customer', 'Gặp nhân viên', '[]', 1784649916),
(114, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784649916),
(115, 12, 'customer', 'chào bạn', '[]', 1784649975),
(116, 12, 'ai', 'Chào bạn! Rất vui được hỗ trợ bạn. Bạn cần JM tư vấn về sản phẩm hay chính sách nào không ạ?', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784649977),
(117, 12, 'customer', 'Gặp nhân viên', '[]', 1784649980),
(118, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784649980),
(119, 12, 'customer', 'chào', '[]', 1784649983),
(120, 12, 'ai', 'Chào bạn, JM rất vui được hỗ trợ bạn. Bạn cần tư vấn về sản phẩm hay thông tin gì tại cửa hàng không ạ?', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784649984),
(121, 12, 'customer', 'Gặp nhân viên', '[]', 1784650099),
(122, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784650099),
(123, 12, 'customer', 'chào bạn', '[]', 1784650272),
(124, 12, 'ai', 'Xin lỗi, mình chưa thể trả lời câu này. Mình sẽ chuyển bạn sang nhân viên hỗ trợ.', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":false}', 1784650282),
(125, 12, 'customer', 'chào bạn', '[]', 1784650369),
(126, 12, 'ai', 'Chào bạn, JM rất vui được hỗ trợ bạn. Bạn đang quan tâm đến sản phẩm hay cần giải đáp thông tin gì về cửa hàng không ạ?', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784650371),
(127, 12, 'customer', 'Gặp nhân viên', '[]', 1784650373),
(128, 12, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784650373),
(129, 13, 'customer', 'Tìm sản phẩm', '[]', 1784651096),
(130, 13, 'ai', 'Chào bạn, cửa hàng hiện có các sản phẩm sau:\n\n*   **Đầm Xuân Hè cổ đức, nhún sườn:** 875.000 đ (https://webshop.test/dam-xuan-he-co-duc-nhun-suon-p52)\n*   **Đầm Party 2 dây tà vắt bung:** 17.880.000 đ (https://webshop.test/dam-party-2-day-ta-vat-bung-p1)\n*   **Quần kaki Nam Lịch Lãm - D36:** 169.000 đ (https://webshop.test/quan-kaki-nam-lich-lam-d36-p28)\n*   **COMBO ĐẦM REN MÙA XUÂN:** 370.000 đ (https://webshop.test/combo-dam-ren-mua-xuan-p23)\n*   **Phối ren:** 200.000 đ (https://webshop.test/phoi-ren-p4)\n\nBạn đang quan tâm đến sản phẩm nào ạ?', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651099),
(131, 13, 'customer', 'có 300k', '[]', 1784651107),
(132, 13, 'ai', 'Chào bạn, với ngân sách 300.000 đ, bạn có thể tham khảo các sản phẩm sau tại cửa hàng:\n\n* **ÁO THU NGỰA MINI:** 70.000 đ (https://webshop.test/ao-thu-ngua-mini-p11)\n* **Áo Thun Form Rộng:** 69.000 đ (https://webshop.test/ao-thun-form-rong-p10)\n* **ẢO KIỂU HÀN QUỐC:** 150.000 đ (https://webshop.test/ao-kieu-han-quoc-p3)\n* **Quần kaki Nam Lịch Lãm - D36:** 169.000 đ (https://webshop.test/quan-kaki-nam-lich-lam-d36-p28)\n* **Phối ren:** 200.000 đ (https://webshop.test/phoi-ren-p4)\n\nBạn ưng ý mẫu nào không ạ?', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651109),
(133, 13, 'customer', 'Gặp nhân viên', '[]', 1784651257),
(134, 13, 'ai', 'Tôi sẽ chuyển cuộc trò chuyện sang nhân viên hỗ trợ.', '{\"intent\":\"handoff\"}', 1784651257),
(135, 13, 'system', '', '[]', 1784651257),
(136, 13, 'system', 'Nhân viên Admin đã tham gia hỗ trợ.', '[]', 1784651302),
(137, 13, 'staff', 'dạ em chào anh chị ạ mình cần gì ạ', '{\"staff_name\":\"Admin\"}', 1784651304),
(138, 13, 'system', 'Cuộc trò chuyện đã kết thúc. Cảm ơn bạn đã liên hệ!', '[]', 1784651331),
(139, 12, 'system', 'Cuộc trò chuyện đã kết thúc. Cảm ơn bạn đã liên hệ!', '[]', 1784651362),
(140, 13, 'customer', 'chào b', '[]', 1784651383),
(141, 13, 'ai', 'Chào bạn, JM rất vui được hỗ trợ bạn! Bạn cần tư vấn về sản phẩm hay thông tin gì tại cửa hàng ạ?', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651385),
(142, 13, 'customer', '1m7 nặng 65 ký thì size nào', '[]', 1784651404),
(143, 13, 'ai', 'Dạ, rất tiếc là hiện tại thông tin về bảng size chi tiết cho từng sản phẩm chưa được cập nhật trong hệ thống của em. Để được tư vấn chính xác nhất về size phù hợp với chiều cao và cân nặng của bạn, mời bạn chat trực tiếp với nhân viên hỗ trợ của cửa hàng nhé!', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651405),
(144, 13, 'customer', 'còn có 200k thì mua gì', '[]', 1784651445),
(145, 13, 'ai', 'Với ngân sách 200.000đ, bạn có thể lựa chọn các sản phẩm sau tại cửa hàng ạ:\n\n- **Phối ren**: 200.000đ (https://webshop.test/phoi-ren-p4)\n- **Quần kaki Nam Lịch Lãm - D36**: 169.000đ (https://webshop.test/quan-kaki-nam-lich-lam-d36-p28)\n- **ÁO KIỂU HÀN QUỐC**: 150.000đ (https://webshop.test/ao-kieu-han-quoc-p3)\n- **ÁO THU NGỰA MINI**: 70.000đ (https://webshop.test/ao-thu-ngua-mini-p11)\n- **Áo Thun Form Rộng**: 69.000đ (https://webshop.test/ao-thun-form-rong-p10)\n\nBạn ưng ý sản phẩm nào không ạ?', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651446),
(146, 13, 'customer', 'có màu đỏ không', '[]', 1784651454),
(147, 13, 'ai', 'Dạ, hiện tại trong dữ liệu của em chưa có thông tin cụ thể về màu sắc của các sản phẩm. Để biết chính xác sản phẩm bạn quan tâm có màu đỏ hay không, mời bạn liên hệ trực tiếp với nhân viên hỗ trợ hoặc ghé thăm cửa hàng tại số 140 Cầu Giấy để được tư vấn chi tiết ạ!', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651455),
(148, 13, 'customer', '1m6 54kg mặc size gì', '[]', 1784651621),
(149, 13, 'ai', 'Dựa trên bảng size tham khảo, với chiều cao 1m6 và cân nặng 54kg, bạn sẽ phù hợp với:\n\n- **Nữ:** Size M.\n- **Nam:** Size S (nếu thích mặc ôm) hoặc M (nếu thích mặc thoải mái).\n\nLưu ý đây là form regular, nếu bạn thích mặc rộng rãi (oversize) có thể cân nhắc lên 1 size nhé. Nếu sản phẩm bạn chọn có bảng size riêng trên trang sản phẩm, bạn hãy ưu tiên theo bảng size đó ạ!', '{\"intent\":\"size\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651622),
(150, 13, 'customer', 'shop có cái nào phù hợp với cân nặng mình', '[]', 1784651652),
(151, 13, 'ai', 'Dạ, với chiều cao 1m6 và cân nặng 54kg (size M cho nữ hoặc S-M cho nam), bạn có thể tham khảo các sản phẩm sau tại cửa hàng:\n\n- **Phối ren** (200.000đ)\n- **Quần kaki Nam Lịch Lãm - D36** (169.000đ)\n- **ÁO KIỂU HÀN QUỐC** (150.000đ)\n- **ÁO THU NGỰA MINI** (70.000đ)\n- **Áo Thun Form Rộng** (69.000đ)\n\nTuy nhiên, vì thông tin chi tiết về số đo cụ thể của từng mẫu sản phẩm không có trong hệ thống, để đảm bảo chọn được món đồ ưng ý nhất, bạn vui lòng chat với nhân viên hỗ trợ hoặc ghé trực tiếp cửa hàng tại **Số 140 Cầu Giấy, P. Quan Hoa, Q. Cầu Giấy, TP. Hà Nội** để được tư vấn và thử đồ trực tiếp nhé!', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651654),
(152, 13, 'customer', 'có bộ nào màu đỏ không', '[]', 1784651678),
(153, 13, 'ai', 'Dạ, hiện tại trong dữ liệu của em chưa có thông tin chi tiết về màu sắc của từng sản phẩm. Để biết chính xác cửa hàng có mẫu nào màu đỏ không, mời bạn liên hệ trực tiếp với nhân viên hỗ trợ qua hotline 0964170476 hoặc ghé thăm cửa hàng tại số 140 Cầu Giấy để được tư vấn chi tiết ạ!', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651680),
(154, 13, 'customer', 'cho biết thông tin của tôi\\', '[]', 1784651729),
(155, 13, 'ai', 'Dạ, rất xin lỗi bạn, hiện tại tôi không có quyền truy cập vào thông tin cá nhân, lịch sử mua hàng hay dữ liệu riêng tư của bạn. Tôi chỉ có thể hỗ trợ bạn các thông tin về sản phẩm, chính sách và dịch vụ của cửa hàng JM 140 Cầu Giấy thôi ạ.\n\nNếu bạn cần hỗ trợ thêm về thông tin đơn hàng hoặc tài khoản, mời bạn liên hệ trực tiếp với nhân viên hỗ trợ qua hotline **0964170476** hoặc email **marketing@jm.com.vn** để được kiểm tra chính xác nhất nhé!', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651730),
(156, 13, 'customer', 'Khuyến mãi', '[]', 1784651742),
(157, 13, 'ai', 'Dạ, hiện tại tôi chưa có thông tin về các chương trình khuyến mãi cụ thể. Để được cập nhật các ưu đãi mới nhất, bạn vui lòng liên hệ trực tiếp với nhân viên hỗ trợ hoặc ghé thăm cửa hàng để được tư vấn chi tiết nhé!', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651743),
(158, 13, 'customer', 'có bộ nào màu đỏ không', '[]', 1784651779),
(159, 13, 'ai', 'Dạ, hiện tại trong hệ thống của em chưa có thông tin về các sản phẩm màu đỏ. Để được hỗ trợ kiểm tra chính xác nhất, mời bạn liên hệ trực tiếp với nhân viên hỗ trợ qua hotline 0964170476 nhé!', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651780),
(160, 13, 'customer', 'thế đen', '[]', 1784651790),
(161, 13, 'ai', 'Bạn có thể đổi trả sản phẩm trong vòng 7 ngày kể từ khi nhận hàng nếu sản phẩm còn nguyên tem mác, chưa qua sử dụng. Vui lòng liên hệ nhân viên để được hướng dẫn chi tiết.', '{\"intent\":\"faq\",\"faq_id\":1,\"score\":0.5}', 1784651790),
(162, 13, 'customer', 'thế đen có không', '[]', 1784651799),
(163, 13, 'ai', 'Dạ, các sản phẩm có màu đen bao gồm:\n\n1. **Đầm Party 2 dây tà vắt bung**: 17.880.000 đ (https://webshop.test/dam-party-2-day-ta-vat-bung-p1)\n2. **Quần kaki Nam Lịch Lãm - D36**: 169.000 đ (https://webshop.test/quan-kaki-nam-lich-lam-d36-p28)\n3. **ẢO KIỂU HÀN QUỐC**: 150.000 đ (https://webshop.test/ao-kieu-han-quoc-p3)\n4. **Cổ trụ thắt nơ**: 204.000 đ (https://webshop.test/co-tru-that-no-p2)\n5. **Quần short kaki nam - QKN44**: 160.000 đ (https://webshop.test/quan-short-kaki-nam-qkn44-p27)\n\nBạn cần tư vấn thêm về sản phẩm nào không ạ?', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651801),
(164, 13, 'customer', 'Tra cứu đơn hàng', '[]', 1784651811),
(165, 13, 'ai', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', '{\"intent\":\"faq\",\"faq_id\":3,\"score\":1}', 1784651811),
(166, 13, 'customer', 'chào bạn', '[]', 1784651870),
(167, 13, 'ai', 'Trợ lý AI hiện đang tắt. Mình chuyển bạn sang nhân viên hỗ trợ nhé.', '{\"intent\":\"disabled\"}', 1784651870),
(168, 13, 'system', '', '[]', 1784651870),
(169, 13, 'system', 'Nhân viên Admin đã tham gia hỗ trợ.', '[]', 1784651883),
(170, 13, 'staff', 'chào anh', '{\"staff_name\":\"Admin\"}', 1784651883),
(171, 13, 'customer', 'giờ làm việc', '[]', 1784651925),
(172, 13, 'system', 'Trợ lý AI tiếp tục hỗ trợ bạn.', '[]', 1784651935),
(173, 13, 'customer', 'giờ làm việc', '[]', 1784651942),
(174, 13, 'ai', 'Cửa hàng phục vụ từ 08h00 đến 21h00 tất cả các ngày trong tuần.', '{\"intent\":\"faq\",\"faq_id\":4,\"score\":1}', 1784651942),
(175, 13, 'customer', 'Khuyến mãi', '[]', 1784651954),
(176, 13, 'ai', 'Hiện tại cửa hàng đang có các mã ưu đãi sau:\n\n*   **Mã JM20**: Giảm 20% cho đơn hàng từ 500.000 đ.\n*   **Mã JM10**: Giảm 10% cho đơn hàng từ 200.000 đ.\n\nBạn có thể áp dụng mã khi tiến hành thanh toán trên website nhé!', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784651956),
(177, 13, 'customer', 'phí ship thì như nào ạ', '[]', 1784651970),
(178, 13, 'ai', 'Bạn có thể đổi trả sản phẩm trong vòng 7 ngày kể từ khi nhận hàng nếu sản phẩm còn nguyên tem mác, chưa qua sử dụng. Vui lòng liên hệ nhân viên để được hướng dẫn chi tiết.', '{\"intent\":\"faq\",\"faq_id\":1,\"score\":0.5}', 1784651970),
(179, 13, 'customer', 'Kiểm tra đơn hàng gần đây', '[]', 1784652041),
(180, 13, 'ai', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', '{\"intent\":\"faq\",\"faq_id\":3,\"score\":0.5}', 1784652041),
(181, 13, 'customer', 'Làm sao để đổi size?', '[]', 1784652053),
(182, 13, 'ai', 'Bạn đăng nhập tài khoản rồi hỏi trợ lý AI \"kiểm tra đơn hàng của tôi\", hoặc vào mục Tài khoản > Đơn hàng để xem chi tiết trạng thái.', '{\"intent\":\"faq\",\"faq_id\":3,\"score\":0.6}', 1784652053),
(183, 13, 'customer', 'có ship sang mỹ không', '[]', 1784652082),
(184, 13, 'ai', 'Chào bạn, hiện tại JM nhận giao hàng toàn quốc (trong lãnh thổ Việt Nam). Rất tiếc thông tin về việc giao hàng sang Mỹ hiện chưa có trong hệ thống của chúng tôi. Bạn vui lòng liên hệ trực tiếp với nhân viên hỗ trợ qua hotline 0964170476 hoặc email marketing@jm.com.vn để được tư vấn chi tiết hơn nhé!', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784652084),
(185, 13, 'customer', 'Làm sao để đổi size?', '[]', 1784652142),
(186, 13, 'ai', 'Cách đổi size sau khi mua:\n1) Trong vòng 7 ngày kể từ khi nhận hàng, sản phẩm còn nguyên tem mác và chưa qua sử dụng.\n2) Liên hệ nhân viên (chat widget hoặc hotline) và cung cấp mã đơn + size hiện tại + size muốn đổi.\n3) Nếu shop còn size cần đổi, nhân viên sẽ hướng dẫn gửi hàng đổi hoặc đổi tại cửa hàng.\n4) Phí vận chuyển đổi size (nếu có) sẽ được thông báo cụ thể khi xác nhận đơn đổi.\nLưu ý: câu hỏi \"đổi size\" là đổi hàng đã mua — khác với tư vấn chọn size trước khi mua.', '{\"intent\":\"exchange\"}', 1784652142),
(187, 13, 'customer', 'Tìm sản phẩm', '[]', 1784719978),
(188, 13, 'ai', 'Chào bạn, mời bạn tham khảo các sản phẩm nổi bật hiện có tại JM:\n\n1. **Đầm Xuân Hè cổ đức, nhún sườn**: 875.000 đ (Trắng, Hồng) - [Xem tại đây](https://webshop.test/dam-xuan-he-co-duc-nhun-suon-p52)\n2. **Đầm Party bút chì, cape đổ**: 945.000 đ (Đen, Trắng, Hồng, Nâu) - [Xem tại đây](https://webshop.test/dam-party-but-chi-cape-do-p51)\n3. **Đầm Party 2 dây tà vắt bung**: 17.880.000 đ (Đen, Cam, Xám) - [Xem tại đây](https://webshop.test/dam-party-2-day-ta-vat-bung-p1)\n4. **Quần kaki Nam Lịch Lãm - D36**: 169.000 đ (Đen, Trắng, Đỏ, Cam) - [Xem tại đây](https://webshop.test/quan-kaki-nam-lich-lam-d36-p28)\n5. **Phối ren**: 200.000 đ - [Xem tại đây](https://webshop.test/phoi-ren-p4)\n\nBạn đang quan tâm đến dòng sản phẩm nào (Nam, Nữ hay Gia đình) để mình hỗ trợ tìm kiếm chính xác hơn nhé!', '{\"intent\":\"product\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784719981),
(189, 13, 'customer', 'Tư vấn size', '[]', 1784721045),
(190, 13, 'ai', 'Để được tư vấn size chính xác nhất, bạn vui lòng cho mình biết chiều cao và cân nặng của bạn nhé.\n\nDưới đây là bảng size tham khảo (form regular):\n\n*   **Nữ:**\n    *   S: 45–50 kg, 150–158 cm\n    *   M: 50–58 kg, 158–165 cm\n    *   L: 58–65 kg, 165–170 cm\n    *   XL: 65–72 kg, 170–175 cm\n*   **Nam:**\n    *   S: 50–58 kg, 160–168 cm\n    *   M: 58–65 kg, 168–172 cm\n    *   L: 65–72 kg, 172–178 cm\n    *   XL: 72–80 kg, 178–185 cm\n\n**Lưu ý:**\n- Nếu bạn thích mặc rộng/oversize hãy tăng 1 size, thích mặc ôm hãy giảm 1 size.\n- Nếu sản phẩm bạn chọn có bảng size riêng trên trang chi tiết, hãy ưu tiên theo bảng đó nhé.\n\nBạn cung cấp số đo để mình gợi ý cụ thể hơn nha!', '{\"intent\":\"size\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784721046),
(191, 13, 'customer', '50kg 1m6', '[]', 1784721057),
(192, 13, 'ai', 'Với chiều cao 1m60 và cân nặng 50kg, bạn sẽ phù hợp với **size M** (đối với nữ) để có độ thoải mái vừa vặn.\n\nNếu bạn thích mặc ôm sát hơn, bạn có thể cân nhắc xuống **size S**. Bạn đang quan tâm đến sản phẩm nào để mình kiểm tra thêm giúp bạn nhé?', '{\"intent\":\"size\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784721058),
(193, 13, 'customer', 'tôi là nam', '[]', 1784721073),
(194, 13, 'ai', 'Với chiều cao 1m60 và cân nặng 50kg, đối với nam, bạn sẽ phù hợp với **size S** nhé. \n\nSize S dành cho nam có thông số tham khảo là 50–58 kg và 160–168 cm. Bạn đang quan tâm đến mẫu sản phẩm nào không ạ?', '{\"intent\":\"general\",\"provider\":\"gemini:gemini-3.1-flash-lite\",\"ok\":true}', 1784721074);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ai_setting`
--

CREATE TABLE `ai_setting` (
  `id` int NOT NULL,
  `setting_key` varchar(60) COLLATE utf8mb3_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `updated` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ai_setting`
--

INSERT INTO `ai_setting` (`id`, `setting_key`, `setting_value`, `updated`) VALUES
(1, 'ai_enabled', '1', 1784651918),
(4, 'welcome_message', 'Xin chào! Mình là trợ lý AI của cửa hàng, mình có thể giúp gì cho bạn?', 1784651918),
(5, 'working_hours_text', 'Cửa hàng phục vụ từ 08h00 đến 21h00 tất cả các ngày trong tuần.', 1784651918),
(6, 'staff_support_enabled', '1', 1784651918),
(7, 'fallback_to_staff', '1', 1784651918);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banner`
--

CREATE TABLE `banner` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `image_link` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `banner`
--

INSERT INTO `banner` (`id`, `name`, `image_link`, `link`, `sort_order`) VALUES
(1, 'Thời trang nam', 'banner1.jpg', 'thoi-trang-nam-c7', 1),
(2, 'Thời trang nữ', 'banner2.jpg', 'thoi-trang-nu-c8', 2),
(3, 'Quần áo gia đình', 'banner3.jpg', 'quan-ao-gia-dinh-c9', 3),
(4, 'Đầm và Váy', 'banner4.jpg', 'dam-vay-c10', 4),
(5, 'Áo thun thời trang', 'banner5.jpg', 'ao-thun-c11', 5),
(6, 'Phụ kiện đi kèm', 'banner6.jpg', 'phu-kien-c12', 6);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `catalog`
--

CREATE TABLE `catalog` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `parent_id` int NOT NULL,
  `sort_order` tinyint NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `catalog`
--

INSERT INTO `catalog` (`id`, `name`, `description`, `parent_id`, `sort_order`, `created`) VALUES
(1, 'Thời trang', '', 0, 1, '2017-04-22 05:35:21'),
(2, 'Bán chạy', '', 0, 2, '2017-04-22 05:35:48'),
(3, 'Khuyến mại', '', 0, 3, '2017-04-22 05:35:59'),
(4, 'Tin tức', '', 0, 4, '2017-04-22 05:36:13'),
(5, 'Giỏ hàng', '', 0, 6, '2017-04-22 05:36:49'),
(6, 'Liên hệ', '', 0, 5, '2017-04-22 05:37:02'),
(7, 'Thời trang nam', '', 1, 1, '2017-04-22 05:37:23'),
(8, 'Thời trang nữ', '', 1, 2, '2017-04-22 05:37:36'),
(9, 'Quần áo gia đình', '', 1, 3, '2017-04-22 05:37:50'),
(10, 'Áo phông nam', '', 7, 1, '2017-04-22 09:08:19'),
(11, 'Áo sơ mi nam', '', 7, 2, '2017-04-22 09:08:36'),
(12, 'Quần Jeans', '', 7, 3, '2017-04-22 09:09:01'),
(13, 'Quần Kali', '', 7, 4, '2017-04-22 09:09:14'),
(14, 'Quần Short', '', 7, 5, '2017-04-22 09:09:31'),
(15, 'Đầm', '', 8, 1, '2017-04-22 09:09:46'),
(16, 'Áo', '', 8, 2, '2017-04-22 09:10:10'),
(17, 'Chân Váy', '', 8, 3, '2017-04-22 09:23:39'),
(18, 'Quần', '', 8, 4, '2017-04-22 09:23:57'),
(19, 'Áo gia đình hè', '', 9, 1, '2017-04-22 09:25:55'),
(20, 'Áo váy gia đình', '', 9, 2, '2017-04-22 09:26:21'),
(21, 'Mẹ và bé', '', 9, 4, '2017-04-22 09:26:34'),
(25, 'Áo Khoác', '', 8, 5, '2026-07-21 17:06:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hop_tac`
--

CREATE TABLE `hop_tac` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intro` text COLLATE utf8mb4_unicode_ci,
  `content` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hop_tac`
--

INSERT INTO `hop_tac` (`id`, `title`, `slug`, `image`, `intro`, `content`, `created_at`) VALUES
(1, 'LIÊN HỆ HỢP TÁC KINH DOANH', 'lien-he-hop-tac-kinh-doanh', 'tuyendung81.jpeg', 'JM Dress Design là thương hiệu thời trang thiết kế với gần 30 cửa hàng được đặt tại các thành phố lớn như Hà Nội, Hồ Chí Minh, Hải Phòng, Thanh Hoá với sứ mệnh mang đến cho phái đẹp những trang phục thanh lịch, nhẹ nhàng nhưng vẫn trẻ trung và hiện đại. \r\n\r\nHiện nay, JM đang tìm kiếm những đối tác, tổ chức, doanh nghiệp trên toàn quốc với mong muốn hợp tác kinh doanh, cùng nhau phát triển và đưa những sản phẩm của JM đến tay khách hàng trên khắp cả nước. ', 'Quyền lợi khi hợp tác kinh doanh cùng JM Dress Design: \r\n\r\n+ Không mất phí nhượng quyền,\r\n\r\n+ Lợi nhuận cao, mức chiết khấu hấp dẫn dành cho đối tác,\r\n\r\n+ Không cần lo lắng đến việc vận hàng, quản lý cửa hàng, \r\n\r\n+ Có đội ngũ marketing chuyên nghiệp hỗ trợ quảng cáo và xây dựng thương hiệu, \r\n\r\n+ Không phải lo lắng về hàng tồn kho (Thu hồi khi hết hợp đồng),\r\n\r\n+ Hàng hoá đa dạng, nhiều sản phẩm chất lượng cao. ', '2026-06-16 08:40:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news`
--

CREATE TABLE `news` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intro` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `image_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `news`
--

INSERT INTO `news` (`id`, `title`, `intro`, `content`, `image_link`, `created`) VALUES
(1, 'JM Dress Design ra mắt BST Jasmine x Hoa Hậu Thanh Thủy', 'Bộ sưu tập Jasmine đánh dấu sự kết hợp đặc biệt giữa JM và Hoa hậu Thanh Thủy, tôn vinh vẻ đẹp ngọt ngào đầy bản lĩnh.', 'Nội dung chi tiết về bộ sưu tập Jasmine kết hợp với Hoa Hậu Thanh Thủy...', 'tintuc1.jpg', '2026-06-11 08:00:00'),
(2, 'BST Áo Dài 2026 ‘THANH’ JM x Á Hậu Tường San có gì đặc biệt?', 'Lấy cảm hứng từ nét đẹp truyền thống giao thoa đương đại, BST Áo Dài Thanh mang đến làn gió mới cho mùa lễ hội.', 'Nội dung chi tiết về nét vẽ, phom dáng và chất liệu chọn lọc của BST Áo Dài 2026...', 'tintuc2.jpg', '2026-06-11 09:15:00'),
(3, 'Khai trương không gian mua sắm mới tại Aeon Mall Hà Đông', 'Showroom mới đem đến trải nghiệm mua sắm tối giản, thanh lịch và vô cùng ấm áp dành cho các quý cô công sở.', 'Nội dung chi tiết về ngày hội khai trương cùng hàng ngàn phần quà ưu đãi đặc biệt...', 'tintuc3.jpg', '2026-06-11 10:30:00'),
(4, 'Xu hướng thời trang công sở hiện đại dẫn đầu năm 2026', 'Điểm danh các thiết kế tinh giản, tôn vinh phong thái tự tin, bản lĩnh của người phụ nữ thế hệ mới.', 'Nội dung chia sẻ về cách phối đồ, lựa chọn bảng màu trang nhã nơi công sở...', 'tintuc4.jpg', '2026-06-11 11:00:00'),
(5, 'Hành trình 10 năm kiên định với định hướng thiết kế ứng dụng', 'Nhìn lại chặng đường định vị thương hiệu thời trang thiết kế Việt trong lòng hàng triệu khách hàng.', 'Nội dung kể về câu chuyện khởi nguồn từ năm 2015 và sự tận tâm trong từng trải nghiệm...', 'tintuc5.jpg', '2026-06-11 13:20:00'),
(6, 'Xu hướng thời trang công sở hiện đại dẫn đầu năm 2026', 'JM đồng hành cùng phụ nữ trên hành trình định hình phong cách sống một cách tự nhiên và đầy cảm xúc.', 'Nội dung chia sẻ mẹo nhỏ <p style=\"font-size: 16px; line-height: 1.6; text-align: justify; font-weight: bold; color: #333; margin-bottom: 20px;\">\r\n    Hòa chung không khí mua sắm sôi động cuối năm, JM Dress Design dành tặng bạn những phần quà giá trị khi tham gia chương trình ưu đãi lớn nhất tháng 11: “QUAY SỐ MAY MẮN – SĂN QUÀ PANASONIC”. Đây là cơ hội vàng để các quý cô không chỉ làm mới tủ đồ với những thiết kế thời thượng mà còn có thể mang về những món quà gia dụng thông minh, giúp chăm sóc trang phục một cách hoàn hảo.\r\n</p>\r\n\r\n<p style=\"font-size: 15px; line-height: 1.6; text-align: justify; margin-bottom: 15px;\">\r\n    Sở hữu váy xinh, rinh quà hiệu xịn chưa bao giờ dễ dàng đến thế! Với mỗi hóa đơn mua sắm phát sinh trong thời gian diễn ra chương trình, bạn sẽ tự động nhận được một mã dự thưởng để tham gia quay số hàng tuần và có cơ hội trở thành chủ nhân của những phần quà giá trị như: máy bàn ủi hơi nước cầm tay, máy cắt lông xơ vải. Càng mua sắm nhiều, cơ hội trúng thưởng của bạn càng cao!\r\n</p>\r\n\r\n<h3 style=\"font-size: 18px; color: #111; border-left: 4px solid #2ecc71; padding-left: 10px; margin-top: 30px; margin-bottom: 15px; font-weight: bold;\">\r\n    Chi tiết thể lệ chương trình “Quay Số May Mắn”\r\n</h3>\r\n\r\n<p style=\"font-size: 15px; line-height: 1.6; text-align: justify; margin-bottom: 15px;\">\r\n    Để đảm bảo mọi khách hàng đều nắm rõ thông tin, JM Dress Design xin công bố chi tiết thể lệ chương trình như sau:\r\n</p>\r\n\r\n<ul style=\"list-style-type: none; padding-left: 0; font-size: 15px; line-height: 1.8; margin-bottom: 25px;\">\r\n    <li style=\"margin-bottom: 10px;\"><strong>1. Thời gian áp dụng:</strong> Chương trình sẽ diễn ra duy nhất trong vòng 1 tháng, từ ngày <strong>30/10/2025 đến hết ngày 30/11/2025</strong>. Hãy lên lịch ngay từ bây giờ để không bỏ lỡ cơ hội mua sắm tuyệt vời này!</li>\r\n    <li style=\"margin-bottom: 10px;\"><strong>2. Phạm vi áp dụng:</strong> Chương trình được áp dụng trên toàn bộ hệ thống cửa hàng của JM Dress Design trên toàn quốc. Dù bạn ở đâu, chỉ cần ghé thăm cửa hàng JM gần nhất là có thể tham gia.</li>\r\n    <li style=\"margin-bottom: 10px;\"><strong>3. Đối tượng tham gia:</strong> Tất cả các khách hàng có phát sinh hoá đơn mua hàng trong thời gian từ 30/10 đến 30/11/2025.</li>\r\n    <li style=\"margin-bottom: 10px;\"><strong>4. Cách thức tham gia:</strong> Với mỗi hóa đơn mua hàng thành công, bạn sẽ nhận được 01 mã dự thưởng tương ứng với ID hóa đơn đó. Mã dự thưởng sẽ được dùng để tham gia chương trình quay số may mắn hàng tuần.</li>\r\n</ul>\r\n\r\n<h3 style=\"font-size: 18px; color: #111; border-left: 4px solid #2ecc71; padding-left: 10px; margin-top: 30px; margin-bottom: 15px; font-weight: bold;\">\r\n    Cơ cấu giải thưởng hấp dẫn chưa từng có\r\n</h3>\r\n\r\n<p style=\"font-size: 15px; line-height: 1.6; text-align: justify; margin-bottom: 15px;\">\r\n    JM đã chuẩn bị những phần quà vô cùng thiết thực và giá trị, giúp việc chăm sóc trang phục của bạn trở nên chuyên nghiệp và dễ dàng hơn bao giờ hết.\r\n</p>\r\n\r\n<div style=\"background: #f9f9f9; padding: 20px; border-radius: 5px; border: 1px solid #eee; margin-bottom: 25px; font-size: 15px; line-height: 1.6;\">\r\n    <p style=\"margin-top: 0; color: #e67e22; font-weight: bold; font-size: 16px;\">???? GIẢI THƯỞNG TUẦN:</p>\r\n    <p style=\"margin-bottom: 15px;\">Mỗi tuần, JM sẽ tìm ra <strong>15 khách hàng may mắn nhất</strong> để trao tặng một trong hai phần quà cao cấp:</p>\r\n    <ul style=\"padding-left: 20px; margin-bottom: 20px;\">\r\n        <li style=\"margin-bottom: 8px;\"><strong>Máy bàn ủi hơi nước cầm tay Panasonic:</strong> Trợ thủ đắc lực giúp là phẳng mọi nếp nhăn trên trang phục một cách nhanh chóng, tiện lợi, giữ cho những thiết kế của JM luôn hoàn hảo như mới.</li>\r\n        <li style=\"margin-bottom: 8px;\"><strong>Máy cắt lông xơ vải Panasonic:</strong> \"Cứu tinh\" cho những bộ trang phục vải len, dạ,... giúp loại bỏ phần lông xù, trả lại vẻ đẹp tinh tươm, sang trọng cho quần áo của bạn.</li>\r\n    </ul>\r\n\r\n    <p style=\"color: #d35400; font-weight: bold; font-size: 16px;\">???? GIẢI THƯỞNG ĐẶC BIỆT CỦA THÁNG:</p>\r\n    <p style=\"margin-bottom: 5px;\">Phần quà giá trị nhất sẽ dành cho <strong>03 khách hàng có tổng giá trị hóa đơn cao nhất</strong> trong suốt thời gian diễn ra chương trình:</p>\r\n    <ul style=\"padding-left: 20px; margin-bottom: 0;\">\r\n        <li><strong>Bàn ủi hơi nước đứng Panasonic:</strong> Siêu phẩm chăm sóc quần áo chuyên nghiệp ngay tại nhà, giúp bạn tiết kiệm thời gian, công sức và giữ cho tủ đồ luôn phẳng phiu, thơm mới.</li>\r\n    </ul>\r\n</div>\r\n\r\n<h3 style=\"font-size: 18px; color: #c0392b; border-left: 4px solid #c0392b; padding-left: 10px; margin-top: 30px; margin-bottom: 15px; font-weight: bold;\">\r\n    ???? ĐẶC BIỆT: JM TUNG HOT VOUCHER ĐỘC QUYỀN 25% TỪ PANASONIC\r\n</h3>\r\n\r\n<p style=\"font-size: 15px; line-height: 1.6; text-align: justify; margin-bottom: 15px;\">\r\n    Siêu HOT!!! Chỉ duy nhất trong đại tiệc Black Friday, JM mang đến loạt ưu đãi cực khủng, <strong>giảm ngay 25%</strong> khi mua các sản phẩm bàn ủi Panasonic trên trang Panasonic Club và Gian hàng Shopee, Lazada, Tiktok Shop của Panasonic. Ưu đãi giới hạn, dành tặng riêng toàn bộ khách hàng hạng Diamond ????\r\n</p>\r\n\r\n<p style=\"font-size: 14px; color: #666; font-style: italic; margin-bottom: 25px;\">\r\n    * Lưu ý: Mã voucher sẽ được JM gửi trực tiếp qua số điện thoại mà khách hàng đã đăng ký. Mỗi số điện thoại tương đương với 1 mã voucher duy nhất.\r\n</p>\r\n\r\n<p style=\"font-size: 16px; line-height: 1.6; text-align: center; font-weight: bold; color: #2c3e50; margin-top: 30px; background: #edf2f7; padding: 15px; border-radius: 4px;\">\r\n    ???? Đừng bỏ lỡ cơ hội vàng \"sắm một được hai\" này! Hãy đến ngay cửa hàng JM Dress Design gần nhất để trải nghiệm không gian mua sắm và nhận mã dự thưởng của mình!\r\n</p>giúp quý cô luôn thoải mái, là chính mình trong mọi kho Khoảnh khắc...', 'friday.jpg', '2026-06-11 14:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order`
--

CREATE TABLE `order` (
  `id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `product_id` int NOT NULL,
  `size` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `color` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order`
--

INSERT INTO `order` (`id`, `transaction_id`, `product_id`, `size`, `color`, `qty`, `amount`, `status`) VALUES
(1, 3, 12, NULL, NULL, 1, 360000.00, 0),
(3, 4, 7, NULL, NULL, 1, 350000.00, 0),
(6, 5, 23, NULL, NULL, 1, 370000.00, 0),
(7, 6, 28, NULL, NULL, 1, 169000.00, 0),
(10, 7, 11, NULL, NULL, 1, 70000.00, 0),
(11, 8, 10, NULL, NULL, 1, 69000.00, 0),
(12, 9, 4, NULL, NULL, 1, 200000.00, 0),
(13, 10, 17, NULL, NULL, 1, 450000.00, 0),
(15, 12, 28, NULL, NULL, 1, 169000.00, 0),
(16, 13, 3, NULL, NULL, 1, 150000.00, 0),
(17, 14, 24, NULL, NULL, 5, 1150000.00, 0),
(18, 15, 28, NULL, NULL, 1, 169000.00, 0),
(19, 16, 28, 'XL', 'Đen phối Trắng', 3, 507000.00, 0),
(20, 17, 28, 'M', 'Trắng phối Xanh Đen', 1, 169000.00, 0),
(21, 18, 28, 'M', 'đỏ', 2, 338000.00, 0),
(22, 19, 1, 'XXL', 'Xanh đen', 4, 71520000.00, 0),
(23, 20, 50, 'S', 'Vàng', 1, 397500.00, 0),
(25, 23, 52, 'L', 'Trắng', 2, 1750000.00, 0),
(27, 25, 28, 'L', 'Đỏ', 1, 169000.00, 0),
(28, 26, 18, 'Freesize', 'Mặc định', 5, 4500000.00, 0),
(29, 27, 52, 'L', 'Hồng', 2, 1750000.00, 0),
(30, 28, 6, 'Freesize', 'Mặc định', 1, 200000.00, 0),
(32, 30, 52, 'L', 'Trắng', 1, 875000.00, 0),
(33, 31, 52, 'L', 'Trắng', 1, 875000.00, 0),
(34, 32, 52, 'M', 'Trắng', 5, 4375000.00, 0),
(38, 36, 52, 'M', 'Trắng', 17, 14875000.00, 0),
(39, 37, 52, 'M', 'Trắng', 20, 17500000.00, 0),
(43, 41, 3, 'L', 'Trắng phối Đen', 2, 300000.00, 0),
(44, 42, 22, 'L', 'Trắng phối Đen', 2, 600000.00, 0),
(45, 42, 53, 'L', 'Trắng phối Đen', 1, 875000.00, 0),
(46, 42, 3, 'M', 'Xám', 1, 150000.00, 0),
(47, 42, 54, 'L', 'Trắng phối Đen', 1, 1055000.00, 0),
(48, 42, 17, 'L', 'Trắng phối Đen', 1, 450000.00, 0),
(49, 43, 53, 'L', 'Trắng phối Đen', 1, 875000.00, 0),
(50, 43, 15, 'L', 'Trắng phối Đen', 1, 170000.00, 0),
(51, 43, 17, 'L', 'Trắng phối Đen', 1, 450000.00, 0),
(52, 44, 51, 'S', 'Đen', 28, 26460000.00, 0),
(53, 45, 2, 'L', 'Trắng phối Đen', 1, 204000.00, 0),
(54, 46, 2, 'L', 'Trắng phối Đen', 1, 204000.00, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pages`
--

CREATE TABLE `pages` (
  `id` int NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `content`, `created_at`) VALUES
(1, 'gioi-thieu', 'giới thiệu trang', '<p>JM Dress Design là thương hiệu thời trang thiết kế Việt Nam, mang tính ứng dụng cao, dành cho quý cô công sở hiện đại. Lấy cảm hứng từ hình ảnh người phụ nữ mang vẻ đẹp giao thoa tinh tế giữa dịu dàng và bản lĩnh, giữa giá trị truyền thống và nhịp thở đương đại, JM đồng hành cùng phụ nữ trên hành trình cân bằng công việc, cuộc sống và phong cách – một cách tự nhiên và đầy cảm xúc.</p>\r\n\r\n<h2 style=\"font-size: 18px; font-weight: bold; margin-top: 25px; margin-bottom: 15px; color: #222;\">CÂU CHUYỆN KHỞI NGUỒN</h2>\r\n\r\n<p>Ra đời vào năm 2015, trong bối cảnh thị trường thời trang nội địa còn thiếu những thiết kế công sở chỉn chu cả về thẩm mỹ lẫn trải nghiệm, JM Dress Design được hình thành từ mong muốn tạo nên một thương hiệu thời trang thiết kế dành riêng cho phụ nữ Việt – nơi họ có thể tìm thấy những trang phục thanh lịch, dễ mặc và đủ tinh tế để tự tin mỗi ngày.</p>\r\n\r\n<p>Trải qua 10 năm hình thành và phát triển, JM không lựa chọn con đường chạy theo xu hướng nhanh hay sự hào놓áng nhất thời. Thương hiệu kiên định với định hướng thời trang thiết kế ứng dụng, từng bước xây dựng chỗ đứng riêng trong lòng khách hàng bằng sự chỉn chu trong từng thiết kế và sự tận tâm trong từng trải nghiệm.</p>\r\n\r\n<p>Hiện nay, JM Dress Design sở hữu hệ thống 30 showroom trải dài trên toàn quốc, có mặt tại các thành phố lớn như Hà Nội, TP. Hồ Chí Minh và nhiều tỉnh thành khác. Mỗi không gian JM được xây dựng với tinh thần đồng nhất: tối giản, thanh lịch và ấm áp – để mỗi quý cô khi ghé thăm đều cảm nhận được sự thoải mái, gần gũi nhưng vẫn tinh tế.</p>\r\n\r\n<div style=\"text-align: center; margin: 20px 0;\">\r\n    <img src=\"http://localhost/webshop/upload/aeon_hadong.jpg\" alt=\"Cửa hàng Aeon Mall Hà Đông\" style=\"max-width: 100%; height: auto; border-radius: 4px;\">\r\n    <p style=\"font-style: italic; font-size: 13px; color: #666; margin-top: 5px;\">Cửa hàng Aeon Mall Hà Đông</p>\r\n</div>\r\n\r\n<h2 style=\"font-size: 18px; font-weight: bold; margin-top: 25px; margin-bottom: 15px; color: #222;\">TUYÊN NGÔN THƯƠNG HIỆU</h2>\r\n\r\n<p>Thương hiệu thời trang JM lấy cảm hứng từ quý cô công sở, người phụ nữ mang vẻ đẹp giao thoa tinh tế giữa dịu dàng và bản lĩnh, giữa giá trị truyền thống và nhịp thở đương đại, cân bằng công việc, cuộc sống và phong cách.</p>\r\n\r\n<p>Từ nguồn cảm hứng ấy, JM theo đuổi phong cách thanh lịch – nữ tính – ứng dụng cao, tập trung vào phom dáng tinh giản, chất liệu chọn lọc và bảng màu trang nhã nhằm tôn vinh vẻ đẹp tự nhiên của người phụ nữ. JM tin rằng thời trang công sở không chỉ gói gọn trong môi trường làm việc, mà còn là người bạn đồng hành cùng quý cô trong nhiều khoảnh khắc đời sống. Với JM, thanh lịch không đến từ sự cầu kỳ, mà từ sự phù hợp – khi người phụ nữ khoác lên mình trang phục khiến cô ấy cảm thấy tự tin, thoải mái và là chính mình.</p>\r\n\r\n<div style=\"text-align: center; margin: 20px 0;\">\r\n    <img src=\"http://localhost/webshop/upload/bst_jasmine.jpg\" alt=\"Sản phẩm nằm trong BST Jasmine JM x Hoa Hậu Thanh Thủy\" style=\"max-width: 100%; height: auto; border-radius: 4px;\">\r\n    <p style=\"font-style: italic; font-size: 13px; color: #666; margin-top: 5px;\">Sản phẩm nằm trong BST Jasmine JM x Hoa Hậu Thanh Thủy</p>\r\n</div>\r\n\r\n<p>Trên hành trình phát triển, JM Dress Design vinh dự được đồng hành cùng many gương mặt truyền cảm hứng như Hoa hậu Tiểu Vy, Á hậu Tường San, Hoa hậu Đỗ Hà, Jimin – Hiwon, Nàng Mơ… Những hình ảnh ấy góp phần lan toả tinh thần người phụ nữ JM – thanh lịch, nữ tính và hiện đại – theo cách rất riêng, rất đời.</p>\r\n\r\n<div style=\"text-align: center; margin: 20px 0;\">\r\n    <img src=\"http://localhost/webshop/upload/bst_aodai.jpg\" alt=\"BST Áo Dài 2026 ‘THANH’ JM x Á Hậu Tường San\" style=\"max-width: 100%; height: auto; border-radius: 4px;\">\r\n    <p style=\"font-style: italic; font-size: 13px; color: #666; margin-top: 5px;\">BST Áo Dài 2026 ‘THANH’ JM x Á Hậu Tường San</p>\r\n</div>\r\n\r\n<h2 style=\"font-size: 18px; font-weight: bold; margin-top: 25px; margin-bottom: 15px; color: #222;\">SẢN PHẨM & TRẢI NGHIỆM TẠI JM</h2>\r\n\r\n<p>Bên cạnh các thiết kế thời trang mang tính ứng dụng cao, JM đặc biệt chú trọng xây dựng trải nghiệm mua sắm chỉn chu và tinh tế.</p>\r\n\r\n<p>Tại mỗi showroom, khách hàng không chỉ được tư vấn về trang phục, mà còn được lắng nghe và thấu hiểu – để mỗi lựa chọn đều trở nên nhẹ nhàng và phù hợp với chính mình.</p>\r\n\r\n<div style=\"text-align: center; margin: 20px 0;\">\r\n    <img src=\"http://localhost/webshop/upload/showroom_jm.jpg\" alt=\"Không gian mua sắm nhộn nhịp tại JM Aeon Mall Hà Đông\" style=\"max-width: 100%; height: auto; border-radius: 4px;\">\r\n    <p style=\"font-style: italic; font-size: 13px; color: #666; margin-top: 5px;\">Không gian mua sắm nhộn nhịp tại JM Aeon Mall Hà Đông</p>\r\n</div>\r\n\r\n<h2 style=\"font-size: 18px; font-weight: bold; margin-top: 25px; margin-bottom: 15px; color: #222;\">TẦM NHÌN & GIÁ TRỊ VIỆT</h2>\r\n\r\n<p>Trong tương lai, JM Dress Design hướng tới việc không ngừng nâng tầm thời trang thiết kế Việt, gìn giữ và lan toả những giá trị văn hoá truyền thống – đặc biệt là hình ảnh tà áo dài – đồng thời từng bước đưa tinh thần, phong thái người phụ nữ Việt đến gần hơn với thị trường quốc tế.</p>\r\n\r\n<p><strong>JM Dress Design</strong> - Nơi thời trang trở thành người bạn đồng hành cùng quý cô công sở trên hành trình sống, làm việc và toả sáng theo cách của riêng mình.</p>', '2026-06-11 06:40:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product`
--

CREATE TABLE `product` (
  `id` int NOT NULL,
  `catalog_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `size` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount` int DEFAULT '0',
  `image_link` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `image_list` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `view` int NOT NULL DEFAULT '0',
  `buyed` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '10',
  `rate_total` int NOT NULL DEFAULT '4',
  `rate_count` int NOT NULL DEFAULT '1',
  `created` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product`
--

INSERT INTO `product` (`id`, `catalog_id`, `name`, `content`, `color`, `size`, `price`, `discount`, `image_link`, `image_list`, `view`, `buyed`, `quantity`, `rate_total`, `rate_count`, `created`) VALUES
(1, 15, 'Đầm Party 2 dây tà vắt bung', '', 'Đen,Cam,Xám', 'M,L,XL,XXL', 17900000.00, 20000, 'dam-1.jpg', '[\"dam-1dikem3.jpg\"]', 34, 8, 10, 14, 3, 1493983674),
(2, 16, 'cổ trụ thắt nơ', '', 'Đen,Xanh đen,Xám', 'M,L,XL,XXL', 255000.00, 51000, 'hang-nhap-so-mi-nu-co-tru-that-no-sm1.jpg', '[\"hang-nhap-so-mi-nu-co-tru-that-no.jpg\",\"hang-nhap-so-mi-nu-co-tru-that-no-sm125-1.jpg\"]', 6, 3, 8, 4, 1, 1493983674),
(3, 16, 'ẢO KIỂU HÀN QUỐC', '<p>ẢO KIỂU H&Agrave;N QUỐC V0040&nbsp;&nbsp;tay lỡ l&agrave; gu chủ yếu cho những ng&agrave;y thu. Nếu như h&egrave; bạn c&oacute; thể t&aacute;o bạo diện một chiếc sơ mi kh&ocirc;ng tay hay kiểu cổ ph&oacute;ng kho&aacute;ng cho thời trang c&ocirc;ng sở th&igrave; sang thu sẽ k&iacute;n đ&aacute;o hơn nhiều với kiểu sơ mi tay lỡ hoặc d&aacute;ng d&agrave;i tay đều ph&ugrave; hợp.</p>\r\n\r\n<p>Những mẫu sơ mi thiết kế tay lỡ vẫn sử dụng gam đơn hoặc họa tiết nếu muốn mix ph&ugrave; hợp c&ugrave;ng quần t&acirc;y, jean hay ch&acirc;n v&aacute;y ăn &yacute;.</p>\r\n\r\n<p>ẢO KIỂU&nbsp;<a href=\"https://www.sendo.vn/han-quoc.htm\">H&Agrave;N QUỐC</a>&nbsp;V0040 với c&aacute;c th&ocirc;ng tin như sau:</p>\r\n\r\n<p>+ Mẫu m&atilde;: như h&igrave;nh;</p>\r\n\r\n<p>+ Xuất xứ: Việt Nam</p>\r\n\r\n<p>+ M&agrave;u sắc: Hồng, xanh, trắng, t&iacute;m</p>\r\n\r\n<p>+ Kiểu d&aacute;ng: tay lỡ, vạt ngang, cổ tr&ograve;n k&egrave;m d&acirc;y chuyền phụ kiện;</p>\r\n\r\n<p>+ Size: S, M, L, XL</p>\r\n', 'Đen,Xanh đen,Xám', 'M,L,XL,XXL', 300000.00, 150000, 'ao-kieu-han-quoc-v0040-1m4G3-8352b3_simg_d0daf0_800x1200_max.jpg', '[\"ao-kieu-han-quoc-v0040-1m4G3-7118e0_simg_d0daf0_800x1200_max.jpg\",\"ao-kieu-han-quoc-v0040-1m4G3-131527_simg_d0daf0_800x1200_max.jpg\"]', 58, 6, 9, 11, 3, 1493983674),
(4, 18, 'phối ren', '<p>ẢO KIỂU H&Agrave;N QUỐC V0040&nbsp;&nbsp;tay lỡ l&agrave; gu chủ yếu cho những ng&agrave;y thu. Nếu như h&egrave; bạn c&oacute; thể t&aacute;o bạo diện một chiếc sơ mi kh&ocirc;ng tay hay kiểu cổ ph&oacute;ng kho&aacute;ng cho thời trang c&ocirc;ng sở th&igrave; sang thu sẽ k&iacute;n đ&aacute;o hơn nhiều với kiểu sơ mi tay lỡ hoặc d&aacute;ng d&agrave;i tay đều ph&ugrave; hợp.</p>\r\n\r\n<p>Những mẫu sơ mi thiết kế tay lỡ vẫn sử dụng gam đơn hoặc họa tiết nếu muốn mix ph&ugrave; hợp c&ugrave;ng quần t&acirc;y, jean hay ch&acirc;n v&aacute;y ăn &yacute;.</p>\r\n\r\n<p>ẢO KIỂU&nbsp;<a href=\"https://www.sendo.vn/han-quoc.htm\">H&Agrave;N QUỐC</a>&nbsp;V0040 với c&aacute;c th&ocirc;ng tin như sau:</p>\r\n\r\n<p>+ Mẫu m&atilde;: như h&igrave;nh;</p>\r\n\r\n<p>+ Xuất xứ: Việt Nam</p>\r\n\r\n<p>+ M&agrave;u sắc: Hồng, xanh, trắng, t&iacute;m</p>\r\n\r\n<p>+ Kiểu d&aacute;ng: tay lỡ, vạt ngang, cổ tr&ograve;n k&egrave;m d&acirc;y chuyền phụ kiện;</p>\r\n\r\n<p>+ Size: S, M, L, XL</p>\r\n', NULL, NULL, 280000.00, 80000, 'hang-nhap-cao-cap-so-mi-nu-phoi-ren-sm115-1m4G3-HuHbF8_simg_d0daf0_800x1200_max.jpg', '[\"hang-nhap-cao-cap-so-mi-nu-phoi-ren-sm115-1m4G3-q1bUZr_simg_d0daf0_800x1200_max.jpg\"]', 25, 7, 10, 22, 5, 1493983674),
(6, 18, 'áo kiểu công sở', '<p>&Aacute;o kiểu mang đến vẻ đẹp nữ t&iacute;nh, dịu d&agrave;ng cho n&agrave;ng!</p>\r\n\r\n<p>Với chất vải v&ocirc; c&ugrave;ng mềm mại v&agrave; nhẹ nh&agrave;ng, chiếc &aacute;o kiểu l&agrave;m từ chất liệu voan n&agrave;y lu&ocirc;n ph&aacute;t huy v&agrave; t&ocirc; điểm được vẻ đẹp nữ t&iacute;nh, dịu d&agrave;ng của bạn g&aacute;i. Nhất l&agrave; với những kiểu d&aacute;ng cổ b&egrave;o c&aacute;ch điệu hay họa tiết xinh xắn lại c&agrave;ng gi&uacute;p n&agrave;ng khoe th&ecirc;m được sự điệu đ&agrave; v&agrave; ấn tượng của m&igrave;nh. Bởi thế, chiếc &aacute;o n&agrave;y v&ocirc; c&ugrave;ng ph&ugrave; hợp với những c&ocirc; n&agrave;ng c&oacute; phong c&aacute;ch thời trang nữ t&iacute;nh, nhẹ nh&agrave;ng.</p>\r\n', NULL, NULL, 300000.00, 100000, 'ao-kieu-cong-so-a0122-1m4G3-ZebjMN_simg_d0daf0_800x1200_max.png', '[\"ao-kieu-cong-so-a0122-1m4G3-o0hhot_simg_d0daf0_800x1200_max.png\",\"ao-kieu-cong-so-a0122-1m4G3-qXBUW2_simg_d0daf0_800x1200_max.png\",\"ao-kieu-cong-so-a0122-1m4G3-vS6ei3_simg_d0daf0_800x1200_max.png\"]', 7, 2, 10, 7, 2, 1493983674),
(7, 17, 'Đầm ren tay dài tiểu thư', '<p>Đầm ren tay d&agrave;i tiểu thư duy&ecirc;n d&aacute;ng nữ t&iacute;nh trị gi&aacute; 450.000 VNĐ nay chỉ c&ograve;n 350.000 VNĐ</p>\r\n\r\n<p>C&aacute;c th&ocirc;ng tin như sau:</p>\r\n\r\n<p>+ Mẫu m&atilde;: như h&igrave;nh;</p>\r\n\r\n<p>+ Xuất xứ: Việt Nam</p>\r\n\r\n<p>+ M&agrave;u sắc: Hồng, xanh, trắng, t&iacute;m</p>\r\n\r\n<p>+ Kiểu d&aacute;ng: tay lỡ, vạt ngang, cổ tr&ograve;n k&egrave;m d&acirc;y chuyền phụ kiện;</p>\r\n\r\n<p>+ Size: S, M, L, XL</p>\r\n', NULL, NULL, 450000.00, 100000, 'Dam_ren_den_tay_dai_tieu_thu_(3).jpg', '[\"Dam_ren_den_tay_dai_tieu_thu_(2).jpg\",\"Dam_ren_den_tay_dai_tieu_thu_(13).jpg\",\"Dam_ren_tieu_thu_tay_dai_(1).jpg\"]', 26, 6, 10, 16, 4, 1493983674),
(9, 15, 'Áo Thun Nữ ROMA', '<p>►Chất liệu cao cấp COTTON 4 CHIỀU mềm mại<br />\r\n►Co giãn tốt ; thoáng mát     ►Thiết kế thời trang<br />\r\n►Kiểu dáng đa phong cách   ►Đường may tinh tế sắc sảo<br />\r\n► Áo thun nữ được thiết kế và sản xuất bởi Trần Doanh mang vể đẹp trẻ trung năng động nhưng không kém phần duyên dáng.<br />\r\n►Áo được thiết kế đẹp, chuẩn form, đường may sắc xảo, vải cotton dày, mịn, thấm hút mồ hôi tạo sự thoải mái khi mặc!<br />\r\n►Thích hợp cho sự kết hợp vứi quần jean, sọt,legging!</p>\r\n', NULL, NULL, 180000.00, 100000, 'ao-thun-ao-phong-nu-hoa-tiet-chu-roma.jpg', '[\"ao-thun-ao-phong-nu-hoa-tiet-chu-roma-ca-tin.jpg\",\"ao-thun-ao-phong-nu-hoa-tiet-chu-roma-ca-tinh.jpg\"]', 6, 1, 10, 4, 1, 1493983674),
(10, 15, ' Áo Thun Form Rộng', '<p>- &Aacute;o thun nữ trẻ trung c&oacute; thiết kế năng động với cổ tr&ograve;n, tay ngắn mang lại cho bạn sự thoải m&aacute;i khi mặc.<br />\r\n- Thiết kế form rộng c&aacute; t&iacute;nh cho bạn lu&ocirc;n cảm thấy dễ chịu khi mặc trong thời gian d&agrave;i.<br />\r\n- In họa tiết chữ đơn giản, trẻ trung tạo n&eacute;t c&aacute; t&iacute;nh ri&ecirc;ng cho sản phẩm.<br />\r\n- Đường may chắc chắn, cẩn thận cho bạn tự tin hơn trong vận động.<br />\r\n- Chất liệu: thun cotton 4 chiều co gi&atilde;n tốt, thấm h&uacute;t mồ h&ocirc;i hiệu quả.<br />\r\n- Size: freesize<br />\r\n- M&agrave;u sắc: trắng, đen, xanh biển</p>\r\n', NULL, NULL, 129000.00, 60000, 'ao-thun-ao-phong-nu-eiffel-ca-tinh-msat28-1m4G3-PP5C91_simg_d0daf0_800x1200_max.jpg', '[\"ao-thun-ao-phong-nu-eiffel-ca-tinh-msat28-1m4G3-LpJZdC_simg_d0daf0_800x1200_max.jpg\",\"ao-thun-ao-phong-nu-eiffel-ca-tinh-msat28-1m4G3-ZyFQ9v_simg_d0daf0_800x1200_max.jpg\"]', 10, 3, 10, 4, 1, 1493983674),
(11, 15, 'ÁO THU NGỰA MINI', '<p>ẢO KIỂU H&Agrave;N QUỐC V0040&nbsp;&nbsp;tay lỡ l&agrave; gu chủ yếu cho những ng&agrave;y thu. Nếu như h&egrave; bạn c&oacute; thể t&aacute;o bạo diện một chiếc sơ mi kh&ocirc;ng tay hay kiểu cổ ph&oacute;ng kho&aacute;ng cho thời trang c&ocirc;ng sở th&igrave; sang thu sẽ k&iacute;n đ&aacute;o hơn nhiều với kiểu sơ mi tay lỡ hoặc d&aacute;ng d&agrave;i tay đều ph&ugrave; hợp.</p>\r\n\r\n<p>Những mẫu sơ mi thiết kế tay lỡ vẫn sử dụng gam đơn hoặc họa tiết nếu muốn mix ph&ugrave; hợp c&ugrave;ng quần t&acirc;y, jean hay ch&acirc;n v&aacute;y ăn &yacute;.</p>\r\n\r\n<p>ẢO KIỂU&nbsp;<a href=\"https://www.sendo.vn/han-quoc.htm\">H&Agrave;N QUỐC</a>&nbsp;với c&aacute;c th&ocirc;ng tin như sau:</p>\r\n\r\n<p>+ Mẫu m&atilde;: như h&igrave;nh;</p>\r\n\r\n<p>+ Xuất xứ: Việt Nam</p>\r\n\r\n<p>+ M&agrave;u sắc: Hồng, xanh, trắng, t&iacute;m</p>\r\n\r\n<p>+ Kiểu d&aacute;ng: tay lỡ, vạt ngang, cổ tr&ograve;n k&egrave;m d&acirc;y chuyền phụ kiện;</p>\r\n\r\n<p>+ Size: S, M, L, XL</p>\r\n', NULL, NULL, 80000.00, 10000, 'ao-thu-ngua-mini-1m4G3-57c588_simg_d0daf0_800x1200_max.jpg', '[\"ao-thu-ngua-mini-1m4G3-9f6f25_simg_d0daf0_800x1200_max.jpg\",\"ao-thu-ngua-mini-1m4G3-a959f5_simg_d0daf0_800x1200_max.jpg\"]', 41, 3, 10, 5, 1, 1493983674),
(12, 17, 'Đầm maxi phối ren cao cấp', '<p>Chất liệu: Chiffon phối ren cao cấp<br />\r\nM&agrave;u sắc: Đen, hồng<br />\r\nK&iacute;ch thước: S,M,L,XL<br />\r\nXuất Xứ : Việt Nam&nbsp;</p>\r\n\r\n<p>+ size S: Chiều d&agrave;i đầm: 130cm, Ngực 78-80cm, Eo 64-68cm, M&ocirc;ng 84-86cm</p>\r\n\r\n<p>+ size M: Chiều d&agrave;i đầm: 130cm, Ngực 80-84cm, Eo 68-72cm, M&ocirc;ng 86-90cm<br />\r\n+ size L: Chiều d&agrave;i đầm: 130cm, Ngực 84-88cm, Eo 72-76cm, M&ocirc;ng 90-96cm<br />\r\n+ size XL: Chiều d&agrave;i đầm: 130cm, Ngực 88-92cm, Eo 76-78cm, M&ocirc;ng 96-100cm</p>\r\n', NULL, NULL, 720000.00, 360000, 'dam-maxi-phoi-ren-cao-cap-1m4G3-QXVTv3_simg_d0daf0_800x1200_max.jpg', '[\"dam-maxi-phoi-ren-cao-cap-1m4G3-sh6ofY_simg_d0daf0_800x1200_max.jpg\",\"dam-maxi-phoi-ren-cao-cap-1m4G3-sUX4Gv_simg_d0daf0_800x1200_max.jpg\",\"dam-maxi-phoi-ren-cao-cap-1m4G3-VEbARk_simg_d0daf0_800x1200_max.jpg\"]', 29, 3, 10, 9, 2, 0),
(13, 17, 'Đầm ren Thái form dài', '<p><em>* Chất liệu:&nbsp;</em>Ren Th&aacute;i cao cấp, lớp l&oacute;t trong d&agrave;y dặn</p>\r\n\r\n<p>*&nbsp;<em>Kiểu d&aacute;ng</em>&nbsp;Đầm kh&ocirc;ng tay, cổ tr&ograve;n, Ch&acirc;n v&aacute;y x&ograve;e, d&agrave;i ngang bắp ch&acirc;n. Kiểu d&aacute;ng mềm mại thướt tha đầy nữ t&iacute;nh</p>\r\n\r\n<p>*&nbsp;<em>M&atilde; sản phẩm:</em>&nbsp;DR 26</p>\r\n', NULL, NULL, 200000.00, 0, 'dam-ren-thai-form-dai-1m4G3-9f2a11.jpg', '[\"dam-ren-thai-form-dai-1m4G3-38d74e.jpg\",\"dam-ren-thai-form-dai-1m4G3-918972.jpg\",\"dam-ren-thai-form-dai-1m4G3-d5e05d.jpg\"]', 7, 1, 10, 4, 1, 1493983674),
(14, 17, 'ĐẦM ÔM BODY CỔ ĐÍNH HẠT', '<p>CHẤT LIỆU : THUN COTON CO GI&Atilde;N THO&Aacute;NG M&Aacute;T DỂ CHIỆU&nbsp;</p>\r\n\r\n<p>TH&Iacute;CH HỢP MỌI HOẠT ĐỘNG : C&Ocirc;NG SỞ , DỰ TIỆC , DẠO PHỐ , ĐI BIỂN ....</p>\r\n\r\n<p>SIZE :</p>\r\n\r\n<p>M&Agrave;U : CAM N&Acirc;U, X&Aacute;M ĐEN ( &Ocirc; M&Agrave;U CHỌN L&Agrave; X&Aacute;M ) XANH LAM , TRẮNG&nbsp;</p>\r\n', NULL, NULL, 200000.00, 50000, 'dam-om-body-co-dinh-hat-1m4G3-22CEL4_simg_d0daf0_800x1200_max.jpg', '[\"dam-om-body-co-dinh-hat-1m4G3-qrWR6I_simg_d0daf0_800x1200_max.jpg\",\"dam-om-body-co-dinh-hat-1m4G3-tVjWlK_simg_d0daf0_800x1200_max.jpg\",\"dam-om-body-co-dinh-hat-1m4G3-XI1vLB_simg_d0daf0_800x1200_max.jpg\"]', 5, 2, 10, 4, 1, 1493983674),
(15, 17, 'ĐẦM XÒE PHỐI REN CAO CẤP', '<p>Chất liệu ren&nbsp;<a href=\"https://www.sendo.vn/cao-cap.htm\">cao cấp</a>&nbsp;cho 1 bạn 1 phong c&aacute;ch sang chảnh thu đ&ocirc;ng năm nay ,với c&aacute;c m&agrave;u diệu ,nồng nằng quyến rũ kh&ocirc;ng thể n&agrave;o kh&ocirc;ng cuốn h&uacute;t đươc tất cả &aacute;nh nh&igrave;n xung quanh h&ograve;a quyện v&agrave;o dạng x&ograve;e cổ điển&nbsp;<a href=\"https://www.sendo.vn/phoi-ren.htm\">phối ren</a>&nbsp; cao cấp .<br />\r\nM&agrave;u : đen , xanh , đỏ&nbsp;<br />\r\nSize : M 45 - 52 kg t&ugrave;y theo chiều cao&nbsp;<br />\r\nXưởng nhận may gia c&ocirc;ng tất cả c&aacute;c mặt h&agrave;ng thời trang nam nữ&nbsp;<br />\r\nVới chất liệu bắt mắt v&agrave; chất lượng rất ok nắm bắt xu hướng thời trang thu đ&ocirc;ng năm nay&nbsp;<br />\r\nMẫu v&aacute;y x&ograve;e ren l&agrave; sự lựa chọn tốt nhất cho bạn.</p>\r\n', NULL, NULL, 350000.00, 180000, 'dam-xoe-phoi-ren-cao-cap-1m4G3-lsWUnT.jpg', '[\"dam-xoe-phoi-ren-cao-cap-1m4G3-AQuuDj.jpg\",\"dam-xoe-phoi-ren-cao-cap-1m4G3-FGCII2.jpg\",\"dam-xoe-phoi-ren-cao-cap-1m4G3-qxyXGj.jpg\",\"dam-xoe-phoi-ren-cao-cap-1m4G3-ztYeGq.jpg\"]', 9, 1, 10, 4, 1, 1493983674),
(16, 19, 'Áo gia đình AG0560', '<p><strong><a href=\"http://aothun24h.vn/san-pham/170/Ao-gia-dinh.html\" target=\"_blank\">&Aacute;o gia đ&igrave;nh</a>&nbsp;kẻ sọc ngang</strong>&nbsp;rất được ưa chuộng hiện nay, d&ugrave; l&agrave; ở lứa tuổi n&agrave;o th&igrave; thời trang kẻ sọc cũng lu&ocirc;n mang đ&ecirc;n cho người mặc một phong c&aacute;ch trẻ trung năng động v&agrave; c&aacute; t&iacute;nh.</p>\r\n\r\n<p>-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kh&ocirc;ng mặc sọc ngang từ đầu đến ch&acirc;n l&agrave; b&iacute; quyết gia đ&igrave;nh bạn n&ecirc;n biết.</p>\r\n\r\n<p>-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chọn chất liệu mềm v&agrave; phom d&aacute;ng su&ocirc;n rộng để che khuyết điểm.</p>\r\n\r\n<p>-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chọn sọc kẻ ngang vừa phải, kh&ocirc;ng đụng tới sọc to.</p>\r\n', NULL, NULL, 580000.00, 0, 'ao-gia-dinh-AG0560-1.jpg', '[\"ao-gia-dinh-AG0560.jpg\",\"ao-gia-dinh-AG0560-2.jpg\",\"ao-gia-dinh-AG0560-3.jpg\",\"ao-gia-dinh-AG0560-4.jpg\"]', 13, 3, 10, 13, 3, 1493983674),
(17, 19, 'Áo gia đình AG0554', '<p><strong>Th&ocirc;ng tin về sản phẩm:</strong></p>\r\n\r\n<p>-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kiểu &aacute;o : &Aacute;o thun cổ tr&ograve;n tay ngắn.</p>\r\n\r\n<p>-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;M&agrave;u sắc: Nhiều m&agrave;u sắc để lựa chọn.</p>\r\n\r\n<p>-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chất liệu: Thun cotton 4 chiều.</p>\r\n\r\n<p>-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Size &aacute;o: Đủ size &aacute;o để lựa chọn.</p>\r\n\r\n<p>-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&ocirc;ng nghệ in: Mimaki của Nhật Bản.</p>\r\n', NULL, NULL, 500000.00, 50000, 'ao-gia-dinh-AG0554.jpg', '[\"ao-gia-dinh-AG0554-1.jpg\",\"ao-gia-dinh-AG0554-2.jpg\",\"ao-gia-dinh-AG0554-3.jpg\",\"ao-gia-dinh-AG0554-4.jpg\"]', 45, 1, 10, 14, 4, 1493983674),
(18, 20, 'ÁO VÁY GIA ĐÌNH AG0430 - AG0430', '<p><strong>Chất liệu cotton tho&aacute;ng m&aacute;t</strong></p>\r\n\r\n<p>Chất liệu cotton 4 chiều tho&aacute;ng m&aacute;t, mềm mại, dễ giặt, nhanh kh&ocirc; v&agrave; h&uacute;t ẩm tốt.</p>\r\n\r\n<p><strong>Thiết kế đơn giản m&agrave; tinh tế</strong></p>\r\n\r\n<p>Thiết kế &aacute;o đơn giản, trẻ trung thoải m&aacute;i cho gia đình bạn khi mặc, sửa lại áo mi&ecirc;̃n phí khi mặc quá r&ocirc;̣ng hoặc quá dài.</p>\r\n', NULL, NULL, 900000.00, 0, 'ao-vay-gia-dinh-ag0515-1m4G3-4UKwpv_simg_d0daf0_800x1200_max.jpg', '[\"ao-vay-gia-dinh-ag0515-1m4G3-pPlrtD_simg_d0daf0_800x1200_max.jpg\",\"ao-vay-gia-dinh-ag0515-1m4G3-t5DoaE_simg_d0daf0_800x1200_max.jpg\"]', 7, 6, 10, 5, 1, 0),
(19, 21, 'ComBo Đầm Đôi PENDI Xinh Xắn', '<p><strong>TH&Ocirc;NG TIN SẢN PHẨM&nbsp;</strong></p>\r\n\r\n<p>- Chất liệu : thun</p>\r\n\r\n<p>- Năm sản xuất : 2016</p>\r\n\r\n<p>- Xuất xứ : Việt nam ( c&ocirc;ng ty th&aacute;i ho&agrave;ng sx)</p>\r\n\r\n<p>- M&agrave;u sắc : xanh, đỏ , hồng</p>\r\n\r\n<p>- K&iacute;ch thước : Freesize d&agrave;nh cho mẹ từ 43</p>\r\n', NULL, NULL, 390000.00, 50000, 'combo-dam-doi-pendi-xinh-xan-th08560-1m4G3-GmhUQZ.jpg', '[\"combo-dam-doi-pendi-xinh-xan-th08560-1m4G3-mPSYrq.jpg\",\"combo-dam-doi-pendi-xinh-xan-th08560-1m4G3-tp7Ma5.jpg\",\"combo-dam-doi-pendi-xinh-xan-th08560-1m4G3-Xd5kQ5.jpg\"]', 5, 1, 10, 4, 1, 1493983674),
(20, 21, 'COMBO ĐẦM KÈM ÁO KHOÁC CHOÀNG', '<p><strong>TH&Ocirc;NG TIN SẢN PHẨM&nbsp;</strong></p>\r\n\r\n<p>- Chất liệu : thun</p>\r\n\r\n<p>- Năm sản xuất : 2016</p>\r\n\r\n<p>- Xuất xứ : Việt nam ( c&ocirc;ng ty th&aacute;i ho&agrave;ng sx)<br />\r\n- M&agrave;u sắc : caro&nbsp;</p>\r\n\r\n<p>- K&iacute;ch thước : Freesize d&agrave;nh cho mẹ từ 43-55kg - size M từ 13-17kg- L &nbsp;từ 17-22kg<br />\r\n&nbsp;</p>\r\n', NULL, NULL, 380000.00, 90000, 'combo-dam-kem-ao-khoac-choang-thoi-trang-th08603-gs195-1m4G3-1SqJve.jpg', '[\"combo-dam-kem-ao-khoac-choang-thoi-trang-th08603-gs195-1m4G3-FWKQKq.jpg\"]', 36, 1, 10, 4, 1, 1493983674),
(21, 21, 'COMBO ĐÔI ĐẦM MẸ VÀ BÉ MICKEY', '<p>T&ecirc;n sp:&nbsp;<a href=\"https://ban.sendo.vn/product\">Combo &aacute;o thun mẹ v&agrave; b&eacute; Mickey</a><br />\r\n<br />\r\nChất liệu: Thun cotton c&aacute; sấu cao cấp mềm mại thoải mai khi mặc cho c&aacute;c n&agrave;ng<br />\r\n<br />\r\nM&agrave;u sắc: &nbsp; &nbsp;Hồng - Trắng 2 m&agrave;u 100% như h&igrave;nh ảnh minh họa. Gam m&agrave;u trẻ trung cho c&aacute;c n&agrave;ng<br />\r\n<br />\r\nThiết kế đơn giản kiểu đầm su&ocirc;ng, form rộng , cổ tr&ograve;n tay lỡ &nbsp; ph&ocirc;i m&agrave;u &nbsp;trẻ trung xin xắn cho&nbsp;<a href=\"https://www.sendo.vn/me-va-be.htm\">mẹ v&agrave; b&eacute;</a><br />\r\n<br />\r\nPh&ugrave; hợp với c&aacute;c mặt dao phố, du lịch, mặc nh&agrave;., đi l&agrave;m, dự tiệc, event ...<br />\r\n<br />\r\nK&iacute;ch thước: Free Size<br />\r\n<br />\r\nCho b&eacute; từ 15 ---&gt; 22 kg</p>\r\n', NULL, NULL, 180000.00, 35000, 'combo-doi-dam-me-va-be-mickey-ddp08444-1.jpg', '[\"combo-doi-dam-me-va-be-mickey-ddp08444.jpg\",\"combo-doi-dam-me-va-be-mickey-ddp08444-1m4G.jpg\",\"combo-doi-dam-me-va-be-mickey-ddp08444-1m4G3-6653ea_simg_d0daf0_800x1200_max.jpg\"]', 5, 1, 10, 4, 1, 1493983674),
(22, 21, 'COMBO ĐẦM CẶP MẸ VÀ BÉ', '<p>Set đ&ocirc;i mẹ v&agrave; b&eacute; gồm :<br />\r\n&Aacute;o d&agrave;i tay + v&aacute;y yếm cho mẹ c&acirc;n nặng từ 43kg - 53kg<br />\r\n&Aacute;o d&agrave;i tay + quần yếm cho b&eacute; trai/ b&eacute; g&aacute;i c&acirc;n nặng từ 17kg- 24kg<br />\r\nM&agrave;u sắc y h&igrave;nh<br />\r\nChất cotton cao cấp d&agrave;y mịn đẹp. Bao d&agrave;y .<br />\r\nShop ko ship h&agrave;ng để xem hay l&yacute; do ko vừa ko th&iacute;ch ko hợp....<br />\r\nTất cả sp đều c&oacute; h&igrave;nh chụp đầy đủ n&ecirc;n kh&aacute;ch vui l&ograve;ng xem kỹ trước khi mua h&agrave;ng b&ecirc;n shop</p>\r\n', NULL, NULL, 400000.00, 100000, 'combo-dam-cap-me-va-be-1m4G3-epzjq8_simg_d0daf0_800x1200_max.jpg', '[\"combo-dam-cap-me-va-be-1m4G3-hKwaQm_simg_d0daf0_800x1200_max.jpg\",\"combo-dam-cap-me-va-be-1m4G3-SxVIlb_simg_d0daf0_800x1200_max.jpg\",\"combo-dam-cap-me-va-be-1m4G3-WqmKco_simg_d0daf0_800x1200_max.jpg\"]', 5, 1, 10, 4, 1, 1493983674),
(23, 15, 'COMBO ĐẦM REN MÙA XUÂN', '<p><strong>TH&Ocirc;NG TIN SẢN PHẨM&nbsp;</strong></p>\r\n\r\n<p>- Chất liệu : REN</p>\r\n\r\n<p>- Năm sản xuất : 2016</p>\r\n\r\n<p>- Xuất xứ : Việt nam&nbsp;</p>\r\n\r\n<p>- M&agrave;u sắc :đỏ</p>\r\n\r\n<p>- K&iacute;ch thước : Freesize từ 43-55k... size M từ 13-17. size L từ 17-25</p>\r\n', NULL, 'S,M,XXL', 450000.00, 80000, 'combo-dam-ren-mua-xuan-cho-me-va-be-th08602-gs210-1m4G3-g4rMfx.jpg', '[\"combo-dam-ren-mua-xuan-cho-me-va-be-th08602-gs210-1m4G3-kwPno1.jpg\"]', 28, 7, 10, 22, 5, 1493983674),
(24, 11, 'Phong Cách Phối Màu', '<p>Chất Liệu: Kaki Silk Thun</p>\r\n\r\n<p>M&agrave;u Sắc: Cổ&nbsp;Trắng Phối Đen, Cổ&nbsp;Trắng Phối Xanh Đen, Cổ Đen Phối Trắng, Cổ Đen Phối Xanh Đen</p>\r\n\r\n<p>Kiểu D&aacute;ng:&nbsp;Thiết Kế D&agrave;i Tay, Th&acirc;n Phối M&agrave;u Trẻ Trung</p>\r\n\r\n<p>Đơn Vị: Cm</p>\r\n\r\n<p>K&iacute;ch Thước: Size L - D&agrave;i &Aacute;o: 67, D&agrave;i Tay: 60, Rộng Vai: 37 - 41, V&ograve;ng Ngực: 78 - 88 (Ph&ugrave; Hợp Với Bạn Nam Dưới 60kg, Chiếu Cao Dưới 1,65 m&eacute;t)</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Size XL - D&agrave;i &Aacute;o: 69, D&agrave;i Tay: 60, Rộng Vai: 39 - 43, V&ograve;ng Ngực: 80 - 90 (Ph&ugrave; Hợp Với Bạn Nam Dưới 65kg, Chiếu Cao Dưới 1,7 m&eacute;t)</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Size XXL - D&agrave;i &Aacute;o: 70, D&agrave;i Tay: 61, Rộng Vai: 40 - 44, V&ograve;ng Ngực: 82 - 92 (Ph&ugrave; Hợp Với Bạn Nam Dưới 70kg, Chiếu Cao Dưới 1,75 m&eacute;t)</p>\r\n', NULL, NULL, 230000.00, 0, 'ao-so-mi-nam-phong-cach-phoi-mau-1m4G3-x9hhml.jpg', '[\"ao-so-mi-nam-phong-cach-phoi-mau-1m4G3-BSZiod.jpg\",\"ao-so-mi-nam-phong-cach-phoi-mau-1m4G3-xL4zQp.jpg\"]', 54, 1, 10, 9, 2, 1493983674),
(27, 7, 'Quần short kaki nam - QKN44', '<p>Quần short&nbsp;<a href=\"https://www.sendo.vn/kaki-nam.htm\">Kaki Nam</a></p>\r\n\r\n<p>Vải kaki loại 1, form chuẩn t&ocirc;n d&aacute;ng &nbsp;</p>\r\n\r\n<p>Size: 28-32</p>\r\n', 'Tím, Đen, Đỏ, Vàng', 'S, M,L,XXL', 200000.00, 40000, 'quan-short-kaki-nam-1m4G3-sexFoa_simg_d0daf0_800x1200_max.jpg', '[\"quan-short-kaki-nam-1m4G3-E4MW4M_simg_d0daf0_800x1200_max.jpg\",\"quan-short-kaki-nam-1m4G3-iKaEX7_simg_d0daf0_800x1200_max.jpg\",\"quan-short-kaki-nam-1m4G3-reyYEA_simg_d0daf0_800x1200_max.jpg\"]', 12, 1, 10, 11, 3, 1493983674),
(28, 11, 'Quần kaki Nam Lịch Lãm - D36', '<p>Quần kaki nam lịch l&atilde;m</p>\r\n\r\n<p>Chất liệu vải kaki loại 1 d&agrave;y mịn</p>\r\n\r\n<p>C&oacute; đủ size 28,29,30,31,32</p>\r\n\r\n<p>Với 3 t&ocirc;ng m&agrave;u trầm đen,xanh đen rất dễ phối với &aacute;o thun,&aacute;o sơ mi,...tạo phong c&aacute;ch thanh lịch cho c&aacute;c bạn nam khi diện đến c&ocirc;ng sở, đi chơi,du lịch,...</p>\r\n', 'Đen,Trắng,Đỏ,Cam', 'M,L,XL,XXL', 169000.00, 0, 'quan-kaki-nam-lich-lam-1m4G3-NvjQo7_simg_d0daf0_800x1200_max.jpg', '[\"quan-kaki-nam-lich-lam-1m4G3-tyzFof_simg_d0daf0_800x1200_max.png\",\"quan-kaki-nam-lich-lam-1m4G3-uSjiJP_simg_d0daf0_800x1200_max.jpg\"]', 36, 8, 10, 21, 5, 1493983674),
(49, 15, 'dam xinh', '<ol>\r\n	<li>&nbsp;</li>\r\n</ol>\r\n', 'Đen,Trắng', 'S,M,L,XL,XXL', 169000.00, 0, 'dam-om-body-co-dinh-hat-1m4G3-22CEL4_simg_d0daf0_800x1200_max1.jpg', '[]', 4, 0, 10, 4, 1, 1782148311),
(50, 15, 'Đầm cổ tròn dáng suông chân phối tơ', '', 'Đen,Trắng,Đỏ,Vàng', 'S,M,L,XL', 795000.00, 397500, 'dam2_1.jpeg', '[]', 5, 1, 10, 4, 1, 1782275755),
(51, 15, 'Đầm Party bút chì, cape đổ', '', 'Đen,Trắng,Hồng,Nâu', 'S,M', 945000.00, 0, 'dam3_1.jpg', '[\"dam4_21.jpg\",\"dam4_31.jpg\",\"dam4_41.jpg\"]', 6, 29, 1, 7, 2, 1782275996),
(52, 15, 'Đầm Xuân Hè cổ đức, nhún sườn', '', 'Trắng,Hồng', 'M,L', 875000.00, 0, 'dam4_1.jpg', '[\"dam4_22.jpg\",\"dam4_32.jpg\",\"dam4_42.jpg\"]', 7, 60, 11, 4, 1, 1782276145),
(53, 15, 'Đầm Party giật bom Xanh', '', 'Xanh dương,Hồng', 'M,L,XL', 875000.00, 0, 'dam5_11.jpg', '[\"dam5_21.jpg\",\"dam5_31.jpg\",\"dam5_41.jpg\",\"dam5_51.jpg\"]', 4, 0, 20, 9, 2, 1782693432),
(54, 15, 'Đầm Party dáng A tay cách điệu', '', 'Đen,Trắng,Hồng', 'S,M,L,XL', 1055000.00, 0, 'dam6_11.jpg', '[\"dam6_21.jpg\",\"dam6_31.jpg\",\"dam6_41.jpg\",\"dam6_51.jpg\",\"dam6_61.jpg\",\"dam6_71.jpg\"]', 4, 0, 30, 9, 2, 1782696544),
(55, 10, 'Áo Phông Nam Form Rộng - Chất Liệu Cotton Cao Cấp, Thoáng Mát', '', 'Trắng', 'S,M,L,XL', 199000.00, 20000, 'whi1__1__ed53acff67434825abb95647482e233c.webp', '[\"whi1__3__4bb2a3d567c44594b6c5482705f9f765_1024x1024.webp\",\"whi1__2__e2864da1ec0c4402bd9ef956bcd0129d_1024x1024.webp\"]', 1, 0, 5, 4, 1, 1784652504),
(56, 18, 'Quần dài cạp to 2 ly bung', '', 'Đen', NULL, 599000.00, 49000, 'Quan-dai-cap-to-2-ly-bung.jpg', '[\"Quan-dai-cap-to-2-ly-bung-Den-S-3.jpg\",\"Quan-dai-cap-to-2-ly-bung-Den-S-1.jpg\"]', 1, 0, 15, 4, 1, 1784652730),
(57, 15, 'Đầm Xuân Hè dáng A nơ cổ 2 túi ốp', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 895000.00, 0, 'đầm-xuân-hè-dáng-a-nơ-cổ-2-túi-ốp-main-b34849d1.jpg', '[\"đầm-xuân-hè-dáng-a-nơ-cổ-2-túi-ốp-g1-7c39eb00.jpg\",\"đầm-xuân-hè-dáng-a-nơ-cổ-2-túi-ốp-g2-4655d0f3.jpg\",\"đầm-xuân-hè-dáng-a-nơ-cổ-2-túi-ốp-g3-f62db39a.jpg\",\"đầm-xuân-hè-dáng-a-nơ-cổ-2-túi-ốp-g4-30fdf300.jpg\",\"đầm-xuân-hè-dáng-a-nơ-cổ-2-túi-ốp-g5-f1f724ae.jpg\"]', 0, 0, 10, 4, 1, 1784653179),
(58, 15, 'Đầm xuân hè dáng suông nhỏ, xếp nếp cách điệu cổ áo', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 895000.00, 0, 'đầm-xuân-hè-dáng-suông-nhỏ-xếp-nếp-cách-điệu-cổ-áo-main-d805eda8.jpg', '[\"đầm-xuân-hè-dáng-suông-nhỏ-xếp-nếp-cách-điệu-cổ-áo-g1-ba150a05.jpg\",\"đầm-xuân-hè-dáng-suông-nhỏ-xếp-nếp-cách-điệu-cổ-áo-g2-3b738c47.jpg\",\"đầm-xuân-hè-dáng-suông-nhỏ-xếp-nếp-cách-điệu-cổ-áo-g3-6553c8ff.jpg\",\"đầm-xuân-hè-dáng-suông-nhỏ-xếp-nếp-cách-điệu-cổ-áo-g4-8756925b.jpg\",\"đầm-xuân-hè-dáng-suông-nhỏ-xếp-nếp-cách-điệu-cổ-áo-g5-6d712c04.jpg\"]', 0, 0, 10, 4, 1, 1784653186),
(59, 15, 'Đầm Xuân Hè chân đổ tay chờm', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 875000.00, 0, 'đầm-xuân-hè-chân-đổ-tay-chờm-main-f24cfe9a.jpg', '[\"đầm-xuân-hè-chân-đổ-tay-chờm-g1-162cee6b.jpg\",\"đầm-xuân-hè-chân-đổ-tay-chờm-g2-a32dc29d.jpg\",\"đầm-xuân-hè-chân-đổ-tay-chờm-g3-5b4edc38.jpg\",\"đầm-xuân-hè-chân-đổ-tay-chờm-g4-4a424203.jpg\",\"đầm-xuân-hè-chân-đổ-tay-chờm-g5-5f6a8c87.jpg\"]', 1, 0, 10, 4, 1, 1784653192),
(60, 15, 'Đầm Xuân Hè chân nhún dáng dài', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 895000.00, 0, 'đầm-xuân-hè-chân-nhún-dáng-dài-main-a28bd085.jpg', '[\"đầm-xuân-hè-chân-nhún-dáng-dài-g1-868bd0b6.jpg\",\"đầm-xuân-hè-chân-nhún-dáng-dài-g2-82b79043.jpg\",\"đầm-xuân-hè-chân-nhún-dáng-dài-g3-0a7af8ab.jpg\",\"đầm-xuân-hè-chân-nhún-dáng-dài-g4-74f35cc5.jpg\",\"đầm-xuân-hè-chân-nhún-dáng-dài-g5-21f26d75.jpg\"]', 0, 0, 10, 4, 1, 1784653197),
(61, 15, 'Đầm Xuân Hè tay trùm phối nơ cổ, chân A ngắn', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 895000.00, 0, 'đầm-xuân-hè-tay-trùm-phối-nơ-cổ-chân-a-ngắn-main-43e3e472.jpg', '[\"đầm-xuân-hè-tay-trùm-phối-nơ-cổ-chân-a-ngắn-g1-410e6bbf.jpg\",\"đầm-xuân-hè-tay-trùm-phối-nơ-cổ-chân-a-ngắn-g2-ed5eab25.jpg\",\"đầm-xuân-hè-tay-trùm-phối-nơ-cổ-chân-a-ngắn-g3-7c0c1935.jpg\",\"đầm-xuân-hè-tay-trùm-phối-nơ-cổ-chân-a-ngắn-g4-6d7811cb.jpg\",\"đầm-xuân-hè-tay-trùm-phối-nơ-cổ-chân-a-ngắn-g5-a1bbe122.jpg\"]', 0, 0, 10, 4, 1, 1784653202),
(62, 15, 'Đầm Party ngang vai nhún hông', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 985000.00, 0, 'đầm-party-ngang-vai-nhún-hông-main-26061696.jpg', '[\"đầm-party-ngang-vai-nhún-hông-g1-5b26aefc.jpg\",\"đầm-party-ngang-vai-nhún-hông-g2-fa207c86.jpg\",\"đầm-party-ngang-vai-nhún-hông-g3-b2e51a45.jpg\",\"đầm-party-ngang-vai-nhún-hông-g4-76df1106.jpg\",\"đầm-party-ngang-vai-nhún-hông-g5-53ff3c58.jpg\"]', 1, 0, 10, 8, 2, 1784653209),
(63, 15, 'Đầm Party chân bom tà vắt', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 875000.00, 0, 'đầm-party-chân-bom-tà-vắt-main-02891a4b.jpg', '[\"đầm-party-chân-bom-tà-vắt-g1-a2a7aa82.jpg\",\"đầm-party-chân-bom-tà-vắt-g2-0f1746a9.jpg\",\"đầm-party-chân-bom-tà-vắt-g3-1161192a.jpg\",\"đầm-party-chân-bom-tà-vắt-g4-792b3e19.jpg\",\"đầm-party-chân-bom-tà-vắt-g5-f4dfefc1.jpg\"]', 2, 0, 10, 4, 1, 1784653216),
(64, 15, 'Đầm Party đổ nhún ngực xếp ly chéo eo, chân nhún', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 975000.00, 0, 'đầm-party-đổ-nhún-ngực-xếp-ly-chéo-eo-chân-nhún-main-45ec8290.jpg', '[\"đầm-party-đổ-nhún-ngực-xếp-ly-chéo-eo-chân-nhún-g1-c0a31059.jpg\",\"đầm-party-đổ-nhún-ngực-xếp-ly-chéo-eo-chân-nhún-g2-cc8aeecd.jpg\",\"đầm-party-đổ-nhún-ngực-xếp-ly-chéo-eo-chân-nhún-g3-cd18dfd9.jpg\",\"đầm-party-đổ-nhún-ngực-xếp-ly-chéo-eo-chân-nhún-g4-03349d1f.jpg\",\"đầm-party-đổ-nhún-ngực-xếp-ly-chéo-eo-chân-nhún-g5-abc9bd9f.jpg\"]', 2, 0, 10, 4, 1, 1784653222),
(65, 16, 'Áo kiểu cổ polo cộc tay', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 355000.00, 0, 'áo-kiểu-cổ-polo-cộc-tay-main-968b652e.jpg', '[\"áo-kiểu-cổ-polo-cộc-tay-g1-c83dc8eb.jpg\",\"áo-kiểu-cổ-polo-cộc-tay-g2-70ae624b.jpg\",\"áo-kiểu-cổ-polo-cộc-tay-g3-509ced8b.jpg\",\"áo-kiểu-cổ-polo-cộc-tay-g5-dbba4dd1.jpg\"]', 0, 0, 10, 4, 1, 1784653229),
(66, 16, 'Áo kiểu cổ tròn cộc tay dệt vặn thừng', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 295000.00, 0, 'áo-kiểu-cổ-tròn-cộc-tay-dệt-vặn-thừng-main-1326a831.jpg', '[\"áo-kiểu-cổ-tròn-cộc-tay-dệt-vặn-thừng-g1-09b37db0.jpg\",\"áo-kiểu-cổ-tròn-cộc-tay-dệt-vặn-thừng-g2-5bba0204.jpg\",\"áo-kiểu-cổ-tròn-cộc-tay-dệt-vặn-thừng-g3-4af34c26.jpg\",\"áo-kiểu-cổ-tròn-cộc-tay-dệt-vặn-thừng-g4-ddc08b3b.jpg\",\"áo-kiểu-cổ-tròn-cộc-tay-dệt-vặn-thừng-g5-08d054ed.jpg\"]', 0, 0, 10, 4, 1, 1784653236),
(67, 16, 'Áo kiểu cổ tròn giả 2 dây', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 295000.00, 0, 'áo-kiểu-cổ-tròn-giả-2-dây-main-a5bc10cb.jpg', '[\"áo-kiểu-cổ-tròn-giả-2-dây-g1-06237fd9.jpg\",\"áo-kiểu-cổ-tròn-giả-2-dây-g2-9a0c3252.jpg\",\"áo-kiểu-cổ-tròn-giả-2-dây-g3-ba8ad744.jpg\",\"áo-kiểu-cổ-tròn-giả-2-dây-g4-80bd5506.jpg\",\"áo-kiểu-cổ-tròn-giả-2-dây-g5-22623d9f.jpg\"]', 0, 0, 10, 4, 1, 1784653244),
(68, 16, 'Áo kiểu cổ tròn dáng ôm suông khăn buộc', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 295000.00, 0, 'áo-kiểu-cổ-tròn-dáng-ôm-suông-khăn-buộc-main-38bd3f5f.jpg', '[\"áo-kiểu-cổ-tròn-dáng-ôm-suông-khăn-buộc-g1-c0c4ef29.jpg\",\"áo-kiểu-cổ-tròn-dáng-ôm-suông-khăn-buộc-g2-aba8824c.jpg\",\"áo-kiểu-cổ-tròn-dáng-ôm-suông-khăn-buộc-g4-a87e525b.jpg\",\"áo-kiểu-cổ-tròn-dáng-ôm-suông-khăn-buộc-g5-085f36da.jpg\"]', 0, 0, 10, 4, 1, 1784653295),
(69, 16, 'Áo sơ mi vai trờm cổ nơ', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M', 525000.00, 0, 'áo-sơ-mi-vai-trờm-cổ-nơ-main-5f35d325.jpg', '[\"áo-sơ-mi-vai-trờm-cổ-nơ-g1-d2a0366d.jpg\",\"áo-sơ-mi-vai-trờm-cổ-nơ-g2-d2d66caa.jpg\",\"áo-sơ-mi-vai-trờm-cổ-nơ-g3-45f1213b.jpg\",\"áo-sơ-mi-vai-trờm-cổ-nơ-g4-0fd7d8ff.jpg\",\"áo-sơ-mi-vai-trờm-cổ-nơ-g5-65f92a55.jpg\"]', 0, 0, 10, 4, 1, 1784653303),
(70, 16, 'Áo sơ mi cổ đức phối khăn rập ly', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 585000.00, 0, 'áo-sơ-mi-cổ-đức-phối-khăn-rập-ly-main-65f92a55.jpg', '[\"áo-sơ-mi-cổ-đức-phối-khăn-rập-ly-g1-24299d55.jpg\",\"áo-sơ-mi-cổ-đức-phối-khăn-rập-ly-g2-23026fce.jpg\",\"áo-sơ-mi-cổ-đức-phối-khăn-rập-ly-g3-1e756883.jpg\",\"áo-sơ-mi-cổ-đức-phối-khăn-rập-ly-g4-c4f668c9.jpg\",\"áo-sơ-mi-cổ-đức-phối-khăn-rập-ly-g5-17adf581.jpg\"]', 0, 0, 10, 4, 1, 1784653310),
(71, 16, 'Áo kiểu cổ leo, thắt đai eo', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 585000.00, 0, 'áo-kiểu-cổ-leo-thắt-đai-eo-main-f945ed0f.jpg', '[\"áo-kiểu-cổ-leo-thắt-đai-eo-g1-be2f729f.jpg\",\"áo-kiểu-cổ-leo-thắt-đai-eo-g2-14df793a.jpg\",\"áo-kiểu-cổ-leo-thắt-đai-eo-g3-8422b628.jpg\",\"áo-kiểu-cổ-leo-thắt-đai-eo-g4-968b652e.jpg\",\"áo-kiểu-cổ-leo-thắt-đai-eo-g5-1326a831.jpg\"]', 0, 0, 10, 4, 1, 1784653316),
(72, 16, 'Áo kiểu cổ tròn, nhún thân', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 585000.00, 0, 'áo-kiểu-cổ-tròn-nhún-thân-main-bd46607c.jpg', '[\"áo-kiểu-cổ-tròn-nhún-thân-g1-21ec80bd.jpg\",\"áo-kiểu-cổ-tròn-nhún-thân-g2-3863fd43.jpg\",\"áo-kiểu-cổ-tròn-nhún-thân-g3-ad05fa57.jpg\",\"áo-kiểu-cổ-tròn-nhún-thân-g4-ae9a4401.jpg\",\"áo-kiểu-cổ-tròn-nhún-thân-g5-e8cb0e84.jpg\"]', 0, 0, 10, 4, 1, 1784653322),
(73, 16, 'Áo kiểu trễ vai vạt lệch', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 595000.00, 0, 'áo-kiểu-trễ-vai-vạt-lệch-main-c79a203c.jpg', '[\"áo-kiểu-trễ-vai-vạt-lệch-g1-6816ead5.jpg\",\"áo-kiểu-trễ-vai-vạt-lệch-g2-a147b1f3.jpg\",\"áo-kiểu-trễ-vai-vạt-lệch-g3-0ecf63e3.jpg\",\"áo-kiểu-trễ-vai-vạt-lệch-g4-606f27cf.jpg\",\"áo-kiểu-trễ-vai-vạt-lệch-g5-134355d1.jpg\"]', 0, 0, 10, 4, 1, 1784653328),
(74, 16, 'Áo kiểu 2 lớp phối ren', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 655000.00, 0, 'áo-kiểu-2-lớp-phối-ren-main-f1789b01.jpg', '[\"áo-kiểu-2-lớp-phối-ren-g1-b6b53954.jpg\",\"áo-kiểu-2-lớp-phối-ren-g2-09f0cf06.jpg\",\"áo-kiểu-2-lớp-phối-ren-g3-c1e732ab.jpg\",\"áo-kiểu-2-lớp-phối-ren-g4-ceb078b8.jpg\",\"áo-kiểu-2-lớp-phối-ren-g5-6c1f34cb.jpg\"]', 1, 0, 10, 4, 1, 1784653336),
(75, 18, 'Quần ngắn dáng A gấp gấu', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 485000.00, 0, 'quần-ngắn-dáng-a-gấp-gấu-main-ab873e2b.jpg', '[\"quần-ngắn-dáng-a-gấp-gấu-g1-5617926e.jpg\",\"quần-ngắn-dáng-a-gấp-gấu-g2-1d1e790c.jpg\",\"quần-ngắn-dáng-a-gấp-gấu-g3-4746bb76.jpg\",\"quần-ngắn-dáng-a-gấp-gấu-g4-2cc67392.jpg\",\"quần-ngắn-dáng-a-gấp-gấu-g5-1c0f4b5d.jpg\"]', 0, 0, 10, 4, 1, 1784653342),
(76, 18, 'Quần dài ống đứng cạp liền', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 585000.00, 0, 'quần-dài-ống-đứng-cạp-liền-main-2e196e86.jpg', '[\"quần-dài-ống-đứng-cạp-liền-g1-a4b619f1.jpg\",\"quần-dài-ống-đứng-cạp-liền-g2-876d1649.jpg\",\"quần-dài-ống-đứng-cạp-liền-g3-5a67dcab.jpg\",\"quần-dài-ống-đứng-cạp-liền-g4-d1f5e298.jpg\",\"quần-dài-ống-đứng-cạp-liền-g5-a29f36a5.jpg\"]', 0, 0, 10, 4, 1, 1784653349),
(77, 18, 'Quần ngắn 2 túi', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 455000.00, 0, 'quần-ngắn-2-túi-main-0d3a231e.jpg', '[\"quần-ngắn-2-túi-g1-a39aa03e.jpg\",\"quần-ngắn-2-túi-g2-8a2152c6.jpg\",\"quần-ngắn-2-túi-g3-fb6174fa.jpg\",\"quần-ngắn-2-túi-g4-d503ef59.jpg\",\"quần-ngắn-2-túi-g5-3716c43d.jpg\"]', 0, 0, 10, 4, 1, 1784653356),
(78, 18, 'Quần Resort tơ 2 lớp xếp ly cạp chun sau', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 585000.00, 0, 'quần-resort-tơ-2-lớp-xếp-ly-cạp-chun-sau-main-bda4f507.jpg', '[\"quần-resort-tơ-2-lớp-xếp-ly-cạp-chun-sau-g1-d4a60a61.jpg\",\"quần-resort-tơ-2-lớp-xếp-ly-cạp-chun-sau-g2-90e1f89e.jpg\",\"quần-resort-tơ-2-lớp-xếp-ly-cạp-chun-sau-g3-377a7d26.jpg\",\"quần-resort-tơ-2-lớp-xếp-ly-cạp-chun-sau-g4-7c458db4.jpg\",\"quần-resort-tơ-2-lớp-xếp-ly-cạp-chun-sau-g5-7eb6e814.jpg\"]', 0, 0, 10, 4, 1, 1784653365),
(79, 18, 'Quần Resort 2 ly bung', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 655000.00, 0, 'quần-resort-2-ly-bung-main-7c458db4.jpg', '[\"quần-resort-2-ly-bung-g1-145c4644.jpg\",\"quần-resort-2-ly-bung-g2-580c33ff.jpg\",\"quần-resort-2-ly-bung-g3-f91bc667.jpg\",\"quần-resort-2-ly-bung-g4-e6870343.jpg\",\"quần-resort-2-ly-bung-g5-77cd0e71.jpg\"]', 0, 0, 10, 4, 1, 1784653372),
(80, 18, 'Quần Resort ống rộng xếp ly chụm', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 655000.00, 0, 'quần-resort-ống-rộng-xếp-ly-chụm-main-7eb6e814.jpg', '[\"quần-resort-ống-rộng-xếp-ly-chụm-g1-8e24a95e.jpg\",\"quần-resort-ống-rộng-xếp-ly-chụm-g2-07694856.jpg\",\"quần-resort-ống-rộng-xếp-ly-chụm-g3-30d5c11c.jpg\",\"quần-resort-ống-rộng-xếp-ly-chụm-g4-c8bcdcfb.jpg\",\"quần-resort-ống-rộng-xếp-ly-chụm-g5-73c20d37.jpg\"]', 0, 0, 10, 4, 1, 1784653378),
(81, 18, 'Quần jean ống loe 2 túi', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 585000.00, 0, 'quần-jean-ống-loe-2-túi-main-15493e58.jpg', '[\"quần-jean-ống-loe-2-túi-g1-33705437.jpg\",\"quần-jean-ống-loe-2-túi-g2-f97b2bc1.jpg\",\"quần-jean-ống-loe-2-túi-g3-0f51fc3d.jpg\",\"quần-jean-ống-loe-2-túi-g4-7e9714f7.jpg\",\"quần-jean-ống-loe-2-túi-g5-4751e3be.jpg\"]', 0, 0, 10, 4, 1, 1784653385),
(82, 18, 'Quần Xuân Hè ống rộng phối cơi túi', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 585000.00, 0, 'quần-xuân-hè-ống-rộng-phối-cơi-túi-main-c1602d3a.jpg', '[\"quần-xuân-hè-ống-rộng-phối-cơi-túi-g1-206c16aa.jpg\",\"quần-xuân-hè-ống-rộng-phối-cơi-túi-g2-3bd0ad37.jpg\",\"quần-xuân-hè-ống-rộng-phối-cơi-túi-g3-62237891.jpg\",\"quần-xuân-hè-ống-rộng-phối-cơi-túi-g4-80fd4f2f.jpg\",\"quần-xuân-hè-ống-rộng-phối-cơi-túi-g5-480a7e7b.jpg\"]', 0, 0, 10, 4, 1, 1784653391),
(83, 18, 'Quần sooc ngắn cạp vừa', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 455000.00, 0, 'quần-sooc-ngắn-cạp-vừa-main-b144550f.jpg', '[\"quần-sooc-ngắn-cạp-vừa-g1-949c8df0.jpg\",\"quần-sooc-ngắn-cạp-vừa-g2-7f1595f4.jpg\",\"quần-sooc-ngắn-cạp-vừa-g3-c8fd5f3b.jpg\",\"quần-sooc-ngắn-cạp-vừa-g4-a35ccc06.jpg\",\"quần-sooc-ngắn-cạp-vừa-g5-2da20299.jpg\"]', 0, 0, 10, 4, 1, 1784653399),
(84, 17, 'Chân váy đổ 2 tầng đan dây', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 299000.00, 0, 'chân-váy-đổ-2-tầng-đan-dây-main-7144890a.jpg', '[\"chân-váy-đổ-2-tầng-đan-dây-g1-865e0022.jpg\",\"chân-váy-đổ-2-tầng-đan-dây-g2-afa34c24.jpg\",\"chân-váy-đổ-2-tầng-đan-dây-g3-9f7c443a.jpg\",\"chân-váy-đổ-2-tầng-đan-dây-g4-94e798c3.jpg\",\"chân-váy-đổ-2-tầng-đan-dây-g5-9bea833c.jpg\"]', 0, 0, 10, 4, 1, 1784653408),
(85, 17, 'Chân váy ngắn tà bung', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 525000.00, 0, 'chân-váy-ngắn-tà-bung-main-3b676598.jpg', '[\"chân-váy-ngắn-tà-bung-g1-827deeef.jpg\",\"chân-váy-ngắn-tà-bung-g2-f7d8e694.jpg\",\"chân-váy-ngắn-tà-bung-g3-29934c27.jpg\",\"chân-váy-ngắn-tà-bung-g4-9f01ce47.jpg\",\"chân-váy-ngắn-tà-bung-g5-c296317d.jpg\"]', 0, 0, 10, 4, 1, 1784653446),
(86, 17, 'Chân váy ngắn xếp ly tà bung', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 495000.00, 0, 'chân-váy-ngắn-xếp-ly-tà-bung-main-275726a0.jpg', '[\"chân-váy-ngắn-xếp-ly-tà-bung-g1-d4938ee3.jpg\",\"chân-váy-ngắn-xếp-ly-tà-bung-g2-858fcd22.jpg\",\"chân-váy-ngắn-xếp-ly-tà-bung-g3-bde20975.jpg\",\"chân-váy-ngắn-xếp-ly-tà-bung-g4-294c04ab.jpg\",\"chân-váy-ngắn-xếp-ly-tà-bung-g5-271742b2.jpg\"]', 0, 0, 10, 4, 1, 1784653454),
(87, 17, 'Chân váy dài dập ly tăm nhỏ xếp tầng', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 695000.00, 0, 'chân-váy-dài-dập-ly-tăm-nhỏ-xếp-tầng-main-7f40ab40.jpg', '[\"chân-váy-dài-dập-ly-tăm-nhỏ-xếp-tầng-g1-0e55772e.jpg\",\"chân-váy-dài-dập-ly-tăm-nhỏ-xếp-tầng-g2-ae85de06.jpg\",\"chân-váy-dài-dập-ly-tăm-nhỏ-xếp-tầng-g3-f22edb4b.jpg\",\"chân-váy-dài-dập-ly-tăm-nhỏ-xếp-tầng-g4-6970f90e.jpg\",\"chân-váy-dài-dập-ly-tăm-nhỏ-xếp-tầng-g5-75de3745.jpg\"]', 0, 0, 10, 4, 1, 1784653460),
(88, 17, 'Chân váy ngắn 2 túi ốp', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 485000.00, 0, 'chân-váy-ngắn-2-túi-ốp-main-400b43df.jpg', '[\"chân-váy-ngắn-2-túi-ốp-g1-fdc4b8a3.jpg\",\"chân-váy-ngắn-2-túi-ốp-g2-e3280e70.jpg\",\"chân-váy-ngắn-2-túi-ốp-g3-e6adceef.jpg\",\"chân-váy-ngắn-2-túi-ốp-g4-fb75c243.jpg\",\"chân-váy-ngắn-2-túi-ốp-g5-2ec7f948.jpg\"]', 0, 0, 10, 4, 1, 1784653467),
(89, 17, 'Chân váy dài lụa dáng đuôi cá nhẹ', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 555000.00, 0, 'chân-váy-dài-lụa-dáng-đuôi-cá-nhẹ-main-6970f90e.jpg', '[\"chân-váy-dài-lụa-dáng-đuôi-cá-nhẹ-g1-1095160e.jpg\",\"chân-váy-dài-lụa-dáng-đuôi-cá-nhẹ-g2-0179bf2f.jpg\",\"chân-váy-dài-lụa-dáng-đuôi-cá-nhẹ-g3-c732020d.jpg\",\"chân-váy-dài-lụa-dáng-đuôi-cá-nhẹ-g4-7f40ab40.jpg\",\"chân-váy-dài-lụa-dáng-đuôi-cá-nhẹ-g5-75de3745.jpg\"]', 0, 0, 10, 4, 1, 1784653473),
(90, 17, 'Chân váy dáng dài dập ly', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 625000.00, 0, 'chân-váy-dáng-dài-dập-ly-main-75de3745.jpg', '[\"chân-váy-dáng-dài-dập-ly-g1-783be9dc.jpg\",\"chân-váy-dáng-dài-dập-ly-g2-ec4dce06.jpg\",\"chân-váy-dáng-dài-dập-ly-g3-4d3f0a66.jpg\",\"chân-váy-dáng-dài-dập-ly-g4-0b5aee11.jpg\",\"chân-váy-dáng-dài-dập-ly-g5-7a499300.jpg\"]', 0, 0, 10, 4, 1, 1784653480),
(91, 17, 'Chân váy dáng dài chặn ly', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 625000.00, 0, 'chân-váy-dáng-dài-chặn-ly-main-7943bd40.jpg', '[\"chân-váy-dáng-dài-chặn-ly-g1-cfbcb5dc.jpg\",\"chân-váy-dáng-dài-chặn-ly-g2-73c8fdcf.jpg\",\"chân-váy-dáng-dài-chặn-ly-g3-da794e1f.jpg\",\"chân-váy-dáng-dài-chặn-ly-g4-abaaf2a4.jpg\",\"chân-váy-dáng-dài-chặn-ly-g5-64ba6d57.jpg\"]', 0, 0, 10, 4, 1, 1784653485),
(92, 17, 'Chân váy dài nhún ngang hông', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 585000.00, 0, 'chân-váy-dài-nhún-ngang-hông-main-8b7162e2.jpg', '[\"chân-váy-dài-nhún-ngang-hông-g1-55b5ef70.jpg\",\"chân-váy-dài-nhún-ngang-hông-g2-c847d9de.jpg\",\"chân-váy-dài-nhún-ngang-hông-g3-aba32967.jpg\",\"chân-váy-dài-nhún-ngang-hông-g4-cf0b0659.jpg\",\"chân-váy-dài-nhún-ngang-hông-g5-399246f6.jpg\"]', 0, 0, 10, 4, 1, 1784653490),
(93, 17, 'Chân váy A nhún tầng', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L', 555000.00, 0, 'chân-váy-a-nhún-tầng-main-5ebc00b8.jpg', '[\"chân-váy-a-nhún-tầng-g1-ca23fce0.jpg\",\"chân-váy-a-nhún-tầng-g2-24e678b2.jpg\",\"chân-váy-a-nhún-tầng-g3-ee972ab3.jpg\",\"chân-váy-a-nhún-tầng-g4-5c9c9ea6.jpg\",\"chân-váy-a-nhún-tầng-g5-4cdfc102.jpg\"]', 0, 0, 10, 4, 1, 1784653496),
(104, 25, 'Áo dạ 2 hàng cúc, cổ ve mèo', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M', 2285000.00, 0, 'áo-dạ-2-hàng-cúc-cổ-ve-mèo-main-8fcfd603.jpg', '[\"áo-dạ-2-hàng-cúc-cổ-ve-mèo-g1-d51260d5.jpg\",\"áo-dạ-2-hàng-cúc-cổ-ve-mèo-g2-6e41f4f3.jpg\",\"áo-dạ-2-hàng-cúc-cổ-ve-mèo-g3-976e7848.jpg\",\"áo-dạ-2-hàng-cúc-cổ-ve-mèo-g4-3b8ab127.jpg\",\"áo-dạ-2-hàng-cúc-cổ-ve-mèo-g5-01d58d6a.jpg\"]', 0, 0, 10, 4, 1, 1784653708),
(105, 25, 'Áo dạ dáng ngắn 1 hàng cúc', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M', 1355000.00, 0, 'áo-dạ-dáng-ngắn-1-hàng-cúc-main-01d58d6a.jpg', '[\"áo-dạ-dáng-ngắn-1-hàng-cúc-g1-815d22c0.jpg\",\"áo-dạ-dáng-ngắn-1-hàng-cúc-g2-e9c38889.jpg\",\"áo-dạ-dáng-ngắn-1-hàng-cúc-g3-368231e9.jpg\",\"áo-dạ-dáng-ngắn-1-hàng-cúc-g4-d223965d.jpg\",\"áo-dạ-dáng-ngắn-1-hàng-cúc-g5-8fcfd603.jpg\"]', 0, 0, 10, 4, 1, 1784653711),
(106, 25, 'Áo lông dáng suông 2 hàng cúc', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 1455000.00, 0, 'áo-lông-dáng-suông-2-hàng-cúc-main-0c53b051.jpg', '[\"áo-lông-dáng-suông-2-hàng-cúc-g1-1cf691eb.jpg\",\"áo-lông-dáng-suông-2-hàng-cúc-g2-8fcfd603.jpg\",\"áo-lông-dáng-suông-2-hàng-cúc-g3-01d58d6a.jpg\",\"áo-lông-dáng-suông-2-hàng-cúc-g4-5267fd4e.jpg\",\"áo-lông-dáng-suông-2-hàng-cúc-g5-a032f7c5.jpg\"]', 0, 0, 10, 4, 1, 1784653715),
(107, 25, 'Áo lông dáng ngắn 1 hàng cúc', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 1455000.00, 0, 'áo-lông-dáng-ngắn-1-hàng-cúc-main-5267fd4e.jpg', '[\"áo-lông-dáng-ngắn-1-hàng-cúc-g1-181cf59f.jpg\",\"áo-lông-dáng-ngắn-1-hàng-cúc-g2-8fcfd603.jpg\",\"áo-lông-dáng-ngắn-1-hàng-cúc-g3-01d58d6a.jpg\",\"áo-lông-dáng-ngắn-1-hàng-cúc-g4-0c53b051.jpg\",\"áo-lông-dáng-ngắn-1-hàng-cúc-g5-a032f7c5.jpg\"]', 0, 0, 10, 4, 1, 1784653718),
(108, 25, 'Áo da cổ lông', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M', 1385000.00, 0, 'áo-da-cổ-lông-main-a032f7c5.jpg', '[\"áo-da-cổ-lông-g1-4f7e1d21.jpg\",\"áo-da-cổ-lông-g2-a872d344.jpg\",\"áo-da-cổ-lông-g3-8fcfd603.jpg\",\"áo-da-cổ-lông-g4-01d58d6a.jpg\",\"áo-da-cổ-lông-g5-0c53b051.jpg\"]', 0, 0, 10, 4, 1, 1784653721),
(109, 25, 'Áo dạ suông cổ tròn 2 túi ốp', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 1455000.00, 0, 'áo-dạ-suông-cổ-tròn-2-túi-ốp-main-2433a9f5.jpg', '[\"áo-dạ-suông-cổ-tròn-2-túi-ốp-g1-09bf8fc6.jpg\",\"áo-dạ-suông-cổ-tròn-2-túi-ốp-g2-8fcfd603.jpg\",\"áo-dạ-suông-cổ-tròn-2-túi-ốp-g3-01d58d6a.jpg\",\"áo-dạ-suông-cổ-tròn-2-túi-ốp-g4-0c53b051.jpg\",\"áo-dạ-suông-cổ-tròn-2-túi-ốp-g5-5267fd4e.jpg\"]', 0, 0, 10, 4, 1, 1784653725),
(110, 25, 'Áo dạ cổ cách điệu, tay chờm', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 1455000.00, 0, 'áo-dạ-cổ-cách-điệu-tay-chờm-main-230e21a9.jpg', '[\"áo-dạ-cổ-cách-điệu-tay-chờm-g1-dcca6c3f.jpg\",\"áo-dạ-cổ-cách-điệu-tay-chờm-g2-8fcfd603.jpg\",\"áo-dạ-cổ-cách-điệu-tay-chờm-g3-01d58d6a.jpg\",\"áo-dạ-cổ-cách-điệu-tay-chờm-g4-0c53b051.jpg\",\"áo-dạ-cổ-cách-điệu-tay-chờm-g5-5267fd4e.jpg\"]', 0, 0, 10, 4, 1, 1784653729),
(111, 25, 'Áo da khóa kéo, cổ bẻ', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M', 1385000.00, 0, 'áo-da-khóa-kéo-cổ-bẻ-main-be0dbab0.jpg', '[\"áo-da-khóa-kéo-cổ-bẻ-g1-a1504b98.jpg\",\"áo-da-khóa-kéo-cổ-bẻ-g2-e245c677.jpg\",\"áo-da-khóa-kéo-cổ-bẻ-g3-8fcfd603.jpg\",\"áo-da-khóa-kéo-cổ-bẻ-g4-01d58d6a.jpg\",\"áo-da-khóa-kéo-cổ-bẻ-g5-0c53b051.jpg\"]', 0, 0, 10, 4, 1, 1784653732),
(112, 25, 'Áo dạ suông cổ tròn bèo 2 nắp túi', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 1385000.00, 0, 'áo-dạ-suông-cổ-tròn-bèo-2-nắp-túi-main-d4784493.jpg', '[\"áo-dạ-suông-cổ-tròn-bèo-2-nắp-túi-g1-4354b3fd.jpg\",\"áo-dạ-suông-cổ-tròn-bèo-2-nắp-túi-g2-8e918347.jpg\",\"áo-dạ-suông-cổ-tròn-bèo-2-nắp-túi-g3-8fcfd603.jpg\",\"áo-dạ-suông-cổ-tròn-bèo-2-nắp-túi-g4-01d58d6a.jpg\",\"áo-dạ-suông-cổ-tròn-bèo-2-nắp-túi-g5-0c53b051.jpg\"]', 0, 0, 10, 4, 1, 1784653736),
(113, 25, 'Áo lông cổ tròn 2 nắp túi', '<p>Sản phẩm thời trang JM — demo import local.</p>', NULL, 'S,M,L,XL', 1455000.00, 0, 'áo-lông-cổ-tròn-2-nắp-túi-main-67e5199a.jpg', '[\"áo-lông-cổ-tròn-2-nắp-túi-g1-13980d12.jpg\",\"áo-lông-cổ-tròn-2-nắp-túi-g2-bdd3de9a.jpg\",\"áo-lông-cổ-tròn-2-nắp-túi-g3-8fcfd603.jpg\",\"áo-lông-cổ-tròn-2-nắp-túi-g4-01d58d6a.jpg\",\"áo-lông-cổ-tròn-2-nắp-túi-g5-0c53b051.jpg\"]', 0, 0, 10, 4, 1, 1784653740),
(114, 13, 'Quần Kaki Nam Simple Form Slim Straight', '<p>ICONDENIM®&amp;nbsp;Quần Kaki Nam Simple Form Slim StraightChất liệu: Vải kaki (97% Cotton – 3% Spandex)Form: Slim StraightMàu sắc: Đen / Trắng / Nâu&amp;nbsp;► CHẤT LIỆU KAKIVải kaki với 97% cotton cho bề mặt chắc, thoáng và có texture gọn gàng, đồng thời 3% spandex giúp quần co giãn tự nhiên&amp;nbsp;để giữ form ổn định nhưng vẫn dễ chịu khi mặc cả ngày.► KỸ THUẬT THIẾT KẾThiết kế quần kaki tối giản, dễ mặc. Lưng quần bản 4cm được may gọn gàng, kết hợp nút gài tiệp màu vải chính và khóa kéo màu bạc. Thân trước có túi xéo may chắc chắn, thân sau dùng túi mổ 2 cơi kèm nút gài và&amp;nbsp;điểm nhấn thêu nhỏ&amp;nbsp;giúp tổng thể nhìn tinh tế.► FORM SLIM STRAIGHTForm slim straight ôm gọn phần hông và đùi vừa phải, ống quần thẳng giúp chân nhìn cân đối hơn. Dáng quần phù hợp với nam giới thích sự chỉn chu nhưng vẫn cần thoải mái khi di chuyển. Dễ phối cùng áo polo, sơ mi hoặc áo thun để tạo phong cách nam tính&amp;nbsp;và hiện đại.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần Kaki</p>', 'Đen-0068,Trắng-0068,Nâu-0068', '29,30,31,32,34,36', 529000.00, 0, 'quần-kaki-nam-simple-form-slim-straight-main-8e698fef.jpg', '[\"quần-kaki-nam-simple-form-slim-straight-g1-c51d4cd3.jpg\",\"quần-kaki-nam-simple-form-slim-straight-g2-14d3cf7c.jpg\",\"quần-kaki-nam-simple-form-slim-straight-g3-98782536.jpg\",\"quần-kaki-nam-simple-form-slim-straight-g4-26cd64a6.jpg\",\"quần-kaki-nam-simple-form-slim-straight-g5-9026ffe7.jpg\"]', 0, 0, 10, 4, 1, 1784721805),
(115, 13, 'Quần Kaki Nam Sandfold Form Loose', '<p>WORKWEAR FOR NEW GENERATIONMột hướng công sở mới từ ICONDENIM dành cho Gen Z — gọn gàng để đi làm, form dáng rộng rãi, thoải mái để mặc cả ngày và linh hoạt cho nhiều lịch trình. Đủ các lựa chọn từ sơ mi, quần tây, quần kaki.ICONDENIM® Quần Kaki Nam Sandfold Form LooseChất liệu: Khaki Twill 100% CottonForm: LooseMàu sắc: Vàng nâu, Đen, Be&amp;nbsp;&amp;nbsp;MÔ TẢ SẢN PHẨMGỌN GÀNGTHOÁI MÁIDỄ PHỐIChất liệu:&amp;nbsp;Khaki twill cottonMàu sắc:&amp;nbsp;Vàng nâu, đen, beKiểu dáng:&amp;nbsp;LoosePhù hợp:&amp;nbsp;Mặc hàng ngày&amp;nbsp;ĐƯỜNG MAY GỌNTừng đường chỉ đều nét tạo nên vẻ ngoài chỉn chu hơnKHUYU CÀI NÚTCố định lưng chắc chắn giúp mặc ổn định hơnTÚI MỔ SAUThêm không gian chứa đồ nhưng mặt sau vẫn gọn gàngCHẤT LIỆU KHAKI TWILLVải khaki twill 100% cotton có bề mặt dệt chéo chắc tay, giữ form ổn định và lên dáng gọn gàng khi mặc, đồng thời vẫn đảm bảo độ thoáng khí và cảm giác dễ chịu cho nhu cầu sử dụng hằng ngày.Chắc tayThoáng khíĐứng formBền đẹp&amp;nbsp;FORM LOOSEThiết kế ống rộng vừa phải, tạo độ rơi tự nhiên từ đùi xuống lai quần, mang lại cảm giác thoải mái. Phù hợp với nam có phần đùi to, chân không quá thẳng hoặc thích phong cách smart casual hiện đại.ỨNG DỤNG LINH HOẠTDáng quần dễ phối cùng áo sơ mi, polo hoặc áo thun tối giản để đi làm, đi gặp gỡ hay mặc thường ngày.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần Kaki</p>', 'Vàng Nâu-0055,Đen-0055,Be-0055', '29,30,31,32,34,36', 449000.00, 0, 'quần-kaki-nam-sandfold-form-loose-main-4fd541a0.jpg', '[\"quần-kaki-nam-sandfold-form-loose-g1-872ec408.jpg\",\"quần-kaki-nam-sandfold-form-loose-g2-88b66b23.jpg\",\"quần-kaki-nam-sandfold-form-loose-g3-44515519.jpg\",\"quần-kaki-nam-sandfold-form-loose-g4-b82625ca.jpg\",\"quần-kaki-nam-sandfold-form-loose-g5-3b6755ba.png\"]', 0, 0, 10, 4, 1, 1784721808),
(116, 13, 'Quần Cargo Kaki Nam Washed Form Straight', '<p>ICONDENIM® Quần Cargo Kaki Nam Washed Form StraightChất liệu: Khaki Twill (100% Cotton wash)Form: Straight&amp;nbsp;&amp;nbsp;MÔ TẢ SẢN PHẨMMỀM MỊNTHOÁI MÁICHUẨN FORMChất liệu:&amp;nbsp; &amp;nbsp;Khaki Twill (100% Cotton wash)Màu sắc:&amp;nbsp; &amp;nbsp; Đen, be, nâuKiểu dáng:&amp;nbsp;StraightPhù hợp:&amp;nbsp; &amp;nbsp;Mặc hàng ngày&amp;nbsp;HỆ TÚI CƠ BẢN2 túi hông và thêm túi đồng hồ nhỏTÚI HỘPĐặt dọc hai bên ống quầnĐƯỜNG MAY GỌNĐược xử lý gọn, đều và phẳngCHẤT LIỆU KHAKI TWILLVải khaki twill 100% cotton mang lại bề mặt chắc tay nhưng vẫn đủ độ mềm để mặc thoải mái trong thời gian dài.Bề mặt mềmDễ chịuThoáng khíDễ chăm sóc&amp;nbsp;FORM STRAIGHTForm Straight có độ suông vừa phải từ đùi xuống ống, không ôm sát nhưng cũng không quá rộng.ỨNG DỤNG LINH HOẠTThiết kế này có thể kết hợp cùng sneaker, boots hoặc sandal tùy phong cách, phù hợp cho outfit casual, streetwear.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Đen-0054,Be-0054,Nâu-0054', '29,30,31,32,34,36', 549000.00, 0, 'quần-cargo-kaki-nam-washed-form-straight-main-6de60498.jpg', '[\"quần-cargo-kaki-nam-washed-form-straight-g1-d11876e7.jpg\",\"quần-cargo-kaki-nam-washed-form-straight-g2-b380f02f.jpg\",\"quần-cargo-kaki-nam-washed-form-straight-g3-ec4f99e8.jpg\",\"quần-cargo-kaki-nam-washed-form-straight-g4-4ffc2275.jpg\",\"quần-cargo-kaki-nam-washed-form-straight-g5-e09efab5.jpg\"]', 0, 0, 10, 4, 1, 1784721810);
INSERT INTO `product` (`id`, `catalog_id`, `name`, `content`, `color`, `size`, `price`, `discount`, `image_link`, `image_list`, `view`, `buyed`, `quantity`, `rate_total`, `rate_count`, `created`) VALUES
(117, 13, 'Quần Kaki Nam Classic Five Form Slim', '<p>ICONDENIM®&amp;nbsp;Quần Kaki Nam Classic Five Form SlimChất liệu:&amp;nbsp;Khakis twill (98% cotton - 2% spandex)Form: Slim&amp;nbsp;► CHẤT LIỆU KHAKIS TWILL – MỀM NHƯNG VẪN GIỮ FORMKhakis twill kết hợp nền cotton chiếm đa số nên bề mặt vẫn mềm, thoáng và ít bí da, trong khi spandex được thêm vào để quần có độ giãn tự nhiên khi di chuyển.&amp;nbsp;Nhờ cấu trúc dệt twill, vải đứng dáng rõ ràng và&amp;nbsp;ổn định sau nhiều lần mặc.► THIẾT KẾ 5 TÚI KINH ĐIỂN – ĐƯỜNG MAY RÕ VÀ SẮC NÉTTổng thể quần đi theo bố cục 5 túi quen thuộc: phía trước có hai túi mổ sâu kèm túi đồng hồ nhỏ, phía sau là hai túi đắp may sạch để bảo đảm tính thực dụng khi dùng hằng ngày. Các vị trí chịu lực như con đĩa, góc túi đều may 2 kim chắc chắn; nút, rivet tông xám bạc kết hợp dây kéo răng kim loại giúp chi tiết kim loại đồng bộ và bền khi sử dụng lâu dài.► FORM SLIM GỌN THÂN – TÔN DÁNG VÀ DỄ PHỐI ĐỒForm Slim được căn chỉnh để ôm nhẹ từ hông xuống ống, tạo cảm giác chân thon gọn hơn nhưng vẫn đủ thoải mái cho nhịp sinh hoạt hằng ngày. Khi phối cùng áo thun, sơ mi hoặc áo len, phom quần giữ tỷ lệ cân đối, phù hợp cả môi trường đi làm lẫn những buổi đi chơi, cà phê cuối tuần.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Đen-0047,Nâu-0047,Trắng-0047', '29,30,31,32,34,36', 449000.00, 100000, 'quần-kaki-nam-classic-five-form-slim-main-623970e2.jpg', '[\"quần-kaki-nam-classic-five-form-slim-g1-115651fd.jpg\",\"quần-kaki-nam-classic-five-form-slim-g2-03603185.jpg\",\"quần-kaki-nam-classic-five-form-slim-g3-f50c03f2.jpg\",\"quần-kaki-nam-classic-five-form-slim-g4-ad447b26.jpg\",\"quần-kaki-nam-classic-five-form-slim-g5-2171944d.png\"]', 0, 0, 10, 4, 1, 1784721813),
(118, 13, 'Quần Kaki Nam Wash ID Form Straight', '<p>ICONDENIM® Quần Kaki Nam Wash ID Form StraightChất liệu: Kaki washForm: Straight   &amp;nbsp;   &amp;nbsp;&amp;nbsp;</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Rêu-0053,Nâu-0053,Đen Wash-0053', 'S,M,L,XL', 479000.00, 130000, 'quần-kaki-nam-wash-id-form-straight-main-af197656.jpg', '[\"quần-kaki-nam-wash-id-form-straight-g1-4f73c9ec.jpg\",\"quần-kaki-nam-wash-id-form-straight-g2-71845641.jpg\",\"quần-kaki-nam-wash-id-form-straight-g3-a6014fbf.jpg\",\"quần-kaki-nam-wash-id-form-straight-g4-901ea31c.jpg\",\"quần-kaki-nam-wash-id-form-straight-g5-c671e75d.png\"]', 1, 0, 10, 4, 1, 1784721815),
(119, 13, 'Quần Kaki Nam Ống Ôm StrecthBand Form Slim', '<p>ICONDENIM® Quần Kaki Nam Ống Ôm StrecthBand Form SlimChất liệu: Vải KakiForm: Slim        &amp;nbsp;</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Nâu-0051,Đen-0051', '29,30,31,32,34,36', 479000.00, 130000, 'quần-kaki-nam-ống-ôm-strecthband-form-slim-main-d193849a.jpg', '[\"quần-kaki-nam-ống-ôm-strecthband-form-slim-g1-875e545f.jpg\",\"quần-kaki-nam-ống-ôm-strecthband-form-slim-g2-7171b7f6.jpg\",\"quần-kaki-nam-ống-ôm-strecthband-form-slim-g3-0ffca85c.jpg\",\"quần-kaki-nam-ống-ôm-strecthband-form-slim-g4-632855a6.jpg\",\"quần-kaki-nam-ống-ôm-strecthband-form-slim-g5-a4bd89f5.png\"]', 0, 0, 10, 4, 1, 1784721817),
(120, 13, 'Quần Kaki Nam Classic Fit Sideband Form Straight', '<p>ICONDENIM® Quần Kaki Nam Classic Fit Sideband Form StraightChất liệu: KakiForm: Straight&amp;nbsp;► CHẤT LIỆU KAKI – BỀN &amp;amp; LINH HOẠTVải kaki bền màu, giữ phom chuẩn&amp;nbsp;nhưng vẫn thoáng khí và dễ chịu. Chi tiết phụ liệu cùng màu vải chính giúp tổng thể gọn gàng và&amp;nbsp;tinh tế.► FORM STRAIGHT – VỪA VẶNThiết kế straight cùng lưng phối thun hai bên hông, tăng độ linh hoạt và thoải mái khi vận động. Dễ mặc với nhiều dáng người, từ đi làm đến đi học hay dạo phố.► THIẾT KẾ SMART CASUAL – THANH LỊCH HẰNG NGÀYTúi trước sau chuẩn form, đường may tinh gọn tạo sự lịch sự. Phối cùng polo trơn, áo thun trắng hoặc sơ mi và sneaker để hoàn thiện phong cách smart casual tiện lợi.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Kem-0052,Đen-0052,Rêu-0052', '29,30,31,32,34,36', 429000.00, 0, 'quần-kaki-nam-classic-fit-sideband-form-straight-main-6bff00d4.jpg', '[\"quần-kaki-nam-classic-fit-sideband-form-straight-g1-2716e28d.jpg\",\"quần-kaki-nam-classic-fit-sideband-form-straight-g2-e1dc1be9.jpg\",\"quần-kaki-nam-classic-fit-sideband-form-straight-g3-26df99eb.jpg\",\"quần-kaki-nam-classic-fit-sideband-form-straight-g4-02d3a03e.jpg\",\"quần-kaki-nam-classic-fit-sideband-form-straight-g5-9fefb3b1.png\"]', 1, 0, 10, 4, 1, 1784721821),
(121, 13, 'Quần Kaki Nam Essential Baggy Pants Form Baggy', '<p>ICONDENIM®&amp;nbsp;Quần Kaki Nam Essential Baggy Pants Form BaggyChất liệu: KakiForm: Baggy&amp;nbsp;► CHẤT LIỆU KAKI – DỄ MẶC HẰNG NGÀYVải kaki không quá dày và cấu trúc vải&amp;nbsp;bề mặt mịn, giúp giữ phom ổn định nhưng vẫn tạo sự thoải mái khi vận động. Dễ bảo quàn nên phù hợp sử dụng thường xuyên.► FORM BAGGY – XẾP LY ĐÔI HIỆN ĐẠIThiết kế baggy với xếp ly đôi phía trước, 2 túi trước và 2 túi sau tiện dụng. Form rộng rãi che khuyết điểm tốt, phù hợp nhiều vóc dáng, đem lại sự thoải mái nhưng vẫn giữ nét gọn gàng.► THIẾT KẾ THANH LỊCH – PHONG CÁCH SMART CASUALĐường may gọn gàng, chi tiết tinh chỉnh tối giản tạo tổng thể nhã nhặn. Dễ phối cùng áo thun trơn, sơ mi trắng hoặc polo cổ đứng cho phong cách smart casual thanh lịch.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Kem-0048,Đen-0048,Be-0048', '29,30,31,32,34,36', 449000.00, 150000, 'quần-kaki-nam-essential-baggy-pants-form-baggy-main-bb1020ca.jpg', '[\"quần-kaki-nam-essential-baggy-pants-form-baggy-g1-41155925.jpg\",\"quần-kaki-nam-essential-baggy-pants-form-baggy-g2-9f64057d.jpg\",\"quần-kaki-nam-essential-baggy-pants-form-baggy-g3-733e9edd.jpg\",\"quần-kaki-nam-essential-baggy-pants-form-baggy-g4-9e223541.jpg\",\"quần-kaki-nam-essential-baggy-pants-form-baggy-g5-aa6a0d31.jpg\"]', 1, 0, 10, 4, 1, 1784721823),
(122, 13, 'Quần Kaki Nam Ống Suông DualPocket Edge Form Straight', '<p>&amp;nbsp;MÔ TẢ SẢN PHẨMỐng suôngMềm vừa phảiBền màuChất liệu:&amp;nbsp;Kaki garment dyeMàu sắc:Kiểu dáng:&amp;nbsp;StraightPhù hợp:&amp;nbsp; Mặc thường ngày&amp;nbsp;DỄ MẶC VÀ TÔN DÁNGForm suông giúp che khuyết điểm tốt và cân đối hình thể cho mọi vóc dángTONE MÀU TRUNG TÍNH3 tone màu trung tính đều dễ phối và&amp;nbsp;phù hợp cho mọi&amp;nbsp;phong cách hiện đạiTAG DA VÀ TÚI SAUThiết kế tag da ICONDENIM®&amp;nbsp;với đường chỉ nổi&amp;nbsp;ở lưng quầnCHẤT LIỆU KAKI GARMENT DYEVải kaki nổi bật với bề mặt dệt chắc tay, mềm mại khi chạm vào và mang lại cảm giác thoải mái khi mặc. Chất liệu này có độ đứng phom tốt, dễ phối trong nhiều hoàn cảnh và giữ vẻ chỉn chu cho tổng thể trang phục.Bền màuMềm mạiĐứng formDễ vận độngFORM STRAIGHTDáng suông gọn gàng&amp;nbsp;đặc biệt che khuyết điểm phần đùi và bắp chân. Phù hợp mọi vóc dáng nam giới.DỄ PHỐI ĐỒKết hợp linh hoạt với áo thun, polo, sơ mi hoặc layering blazer đều cho tổng thể hài hòa.CÁ TÍNHTag da dập nổi ở túi sau tạo điểm nhấn tinh tế, khẳng định chất riêng của dòng DualPocket Edge.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Kem-0049,Be-0049,Đen-0049', '29,30,31,32,34,36', 529000.00, 130000, 'qua-n-kaki-nam-ống-suông-dualpocket-edge-form-straight-main-0f002ccc.jpg', '[\"qua-n-kaki-nam-ống-suông-dualpocket-edge-form-straight-g1-4688419b.jpg\",\"qua-n-kaki-nam-ống-suông-dualpocket-edge-form-straight-g2-ab99793f.jpg\",\"qua-n-kaki-nam-ống-suông-dualpocket-edge-form-straight-g3-f35cc00a.jpg\",\"qua-n-kaki-nam-ống-suông-dualpocket-edge-form-straight-g4-f0fea359.jpg\",\"qua-n-kaki-nam-ống-suông-dualpocket-edge-form-straight-g5-178906a0.jpg\"]', 0, 0, 10, 4, 1, 1784721826),
(123, 13, 'Quần Kaki Nam Garment Dye With Leather Detail Form Straight', '<p>ICONDENIM® Quần Kaki Nam Garment Dye With Leather Detail Form StraightChất liệu: Kaki Garment DyeForm: StraightĐặc điểm:        &amp;nbsp;</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Kem-0046,Nâu-0046,Vàng Bò-0046,Xám Rêu-0046', '29,30,31,32,34,36', 529000.00, 130000, 'quần-kaki-nam-garment-dye-with-leather-detail-form-straight-main-f8b34468.jpg', '[\"quần-kaki-nam-garment-dye-with-leather-detail-form-straight-g1-831c5f07.jpg\",\"quần-kaki-nam-garment-dye-with-leather-detail-form-straight-g2-7e462204.jpg\",\"quần-kaki-nam-garment-dye-with-leather-detail-form-straight-g3-03e1bf56.jpg\",\"quần-kaki-nam-garment-dye-with-leather-detail-form-straight-g4-abd74dcc.jpg\",\"quần-kaki-nam-garment-dye-with-leather-detail-form-straight-g5-513af3fb.png\"]', 0, 0, 10, 4, 1, 1784721829),
(124, 13, 'Quần Kaki Nam Ống Suông Street-Style Logo Garment-Dyed Form Baggy', '<p>&amp;nbsp;ICONDENIM® Quần Kaki Nam Ống Suông Street-Style Logo Garment-Dyed Form BaggyChất liệu: KakiForm: BaggyĐặc điểm:&amp;nbsp;► CHẤT LIỆU KAKI SỚ DENIM NHUỘM GARMENT- DYE LÊN MÀU TỰ NHIÊNKaki là chất liệu bền chắc, có độ đứng form tốt nhưng vẫn đảm bảo sự thoải mái khi mặc. Vải kaki sớ denim sử dụng kỹ thuật nhuộm garment dye tạo hiệu ứng lên màu tự nhiên và cá tính.► FORM BAGGY RỘNG RÃI, THOẢI MÁI CHO MỌI DÁNG NGƯỜIKiểu dáng rộng rãi, thoải mái, giúp người&amp;nbsp;mặc&amp;nbsp;dễ dàng vận động và phù hợp với mọi vóc dáng.&amp;nbsp;Thiết kế phom dáng thời trang nên dễ phối đồ, mang đến vẻ ngoài hiện đại và cá tính.► NỔI BẬT VỚI LOGO THÊU NỔI Ở TÚI SAU ĐẦY CÁ TÍNHLogo thêu tinh tế trên túi sau cùng nhãn da in ombre&amp;nbsp;tạo điểm nhấn thời trang cho mẫu quần.&amp;nbsp;Thiết kế&amp;nbsp;trẻ trung&amp;nbsp;hợp phong cách streetwear, dễ phối với áo phông, áo hoodie hay giày sneaker.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Rêu Wash-0043,Xám Đen Wash-0043,Kem Wash-0043', '29,30,31,32,34,36', 579000.00, 280000, 'quần-kaki-nam-ống-suông-street-style-logo-garment-dyed-form--main-a90a8ae0.jpg', '[\"quần-kaki-nam-ống-suông-street-style-logo-garment-dyed-form--g1-698f43fe.jpg\",\"quần-kaki-nam-ống-suông-street-style-logo-garment-dyed-form--g2-f17b1d0c.jpg\",\"quần-kaki-nam-ống-suông-street-style-logo-garment-dyed-form--g3-6757d736.jpg\",\"quần-kaki-nam-ống-suông-street-style-logo-garment-dyed-form--g4-4032ece0.jpg\",\"quần-kaki-nam-ống-suông-street-style-logo-garment-dyed-form--g5-120723e0.jpg\"]', 0, 0, 10, 4, 1, 1784721834),
(125, 13, 'Quần Kaki Nam Ống Ôm DetailLine Form Slim Straight', '<p>ICONDENIM® Quần Kaki Nam Ống Ôm DetailLine Form Slim Straight&amp;nbsp;Chất liệu: Cotton spandexForm: Slim StraightĐặc điểm:&amp;nbsp;► CHẤT LIỆU MỀM MẠI, CO GIÃN TỐTQuần kaki với 98% Cotton và 2% Spandex có trọng lượng nhẹ, mềm mại, khả năng thấm hút&amp;nbsp;tốt nên mang lại sự thoái mái cho người mặc. Chất liệu co giãn tốt,&amp;nbsp;ít bám bẩn và có thể&amp;nbsp;dễ dàng giặt sạch.► FORM SLIM STRAIGHT TÔN DÁNG NHƯNG VẪN THOẢI MÁIKiểu dáng Slim straight ôm vừa vặn giúp người mặc tôn dáng nhưng vẫn thoải mái, đứng form và không gây cảm giác gò bó. Thiết kế gọn gàng dễ dàng phối với nhiều trang phục&amp;nbsp;khác nhau.► ĐIỂM NHẤN TINH TẾ Ở LƯNG TẠO NÊN VẺ LỊCH LÃMVới thiết kế điểm nhấn dây trang trí bằng vải và nhãn dệt kẹp lưng tạo nên nét phá cách độc đáo giúp chiếc quần không chỉ phù hợp với môi trường công sở mà còn trở thành điểm sáng trong các trang phục casual.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Trắng-0045,Đen-0045,Xanh Đen-0045,Nâu Nhạt-0045', '29,30,31,32,34,36', 419000.00, 120000, 'quần-kaki-nam-ống-ôm-detailline-form-slim-straight-main-3ff1b2d5.jpg', '[\"quần-kaki-nam-ống-ôm-detailline-form-slim-straight-g1-2907e198.jpg\",\"quần-kaki-nam-ống-ôm-detailline-form-slim-straight-g2-67a252a0.jpg\",\"quần-kaki-nam-ống-ôm-detailline-form-slim-straight-g3-b36286ae.jpg\",\"quần-kaki-nam-ống-ôm-detailline-form-slim-straight-g4-ddbd9df8.jpg\",\"quần-kaki-nam-ống-ôm-detailline-form-slim-straight-g5-84ee78c1.png\"]', 0, 0, 10, 4, 1, 1784721836),
(126, 13, 'Quần Kaki Nam Ống Suông Cargo ICDN Tag Form Straight', '<p>ICONDENIM® Quần Kaki Nam Ống Suông Cargo ICDN Tag Form StraightChất liệu: KakiForm: StraightĐặc điểm:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;MÔ TẢ SẢN PHẨMTHOÁI MÁIBỀN ĐẸPCHUẨN FORMChất liệu:&amp;nbsp;Vải kakiMàu sắc:&amp;nbsp;Kem, xám rêu, đenKiểu dáng:&amp;nbsp;StraightPhù hợp:&amp;nbsp;Mặc hàng ngày&amp;nbsp;TÚI HỘPĐược bố trí hợp lý 2 bên sườnĐƯỜNG MAY GỌNĐược xử lý gọn, đều và phẳngDETAIL LOGOThiết kế riêng cho dòng sản phẩmCHẤT LIỆU KAKIChất liệu kaki có độ bền cao, bề mặt chắc chắn và giữ form khá ổn định khi mặc.Đứng formBền đẹpDễ chịuDễ chăm sóc&amp;nbsp;FORM STRAIGHTForm straight giữ độ thẳng từ đùi xuống gấu, không ôm bó nhưng cũng không quá rộng, giúp che khuyết điểm phần đùi xuống bắp chân.ỨNG DỤNG LINH HOẠTDễ phối cùng áo thun, sơ mi hoặc áo khoác để mặc đi làm, đi chơi hay dạo phố cuối tuần.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Kem-0042,Xám Rêu-0042,Đen-0042', '29,30,31,32,34,36', 479000.00, 0, 'quần-kaki-nam-ống-suông-cargo-icdn-tag-form-straight-main-1c29d509.jpg', '[\"quần-kaki-nam-ống-suông-cargo-icdn-tag-form-straight-g1-756f5a5a.jpg\",\"quần-kaki-nam-ống-suông-cargo-icdn-tag-form-straight-g2-ec369916.png\",\"quần-kaki-nam-ống-suông-cargo-icdn-tag-form-straight-g3-f8058744.jpg\",\"quần-kaki-nam-ống-suông-cargo-icdn-tag-form-straight-g4-3bf31952.jpg\",\"quần-kaki-nam-ống-suông-cargo-icdn-tag-form-straight-g5-39c5864e.png\"]', 0, 0, 10, 4, 1, 1784721839),
(127, 13, 'Quần Kaki Nam Ống Ôm Fundamental Form Slim', '<p>ICONDENIM® Quần Kaki Nam Ống Ôm Fundamental Form SlimChất liệu: Vải pique kaki (97% Cotton, 3% Spandex)Form quần: SlimĐặc điểm:&amp;nbsp;&amp;nbsp;MÔ TẢ SẢN PHẨMTHANH LỊCHGỌN GÀNGDỄ PHỐIChất liệu:&amp;nbsp;Pique kakiMàu sắc:&amp;nbsp;Kem, xám, đenKiểu dáng:&amp;nbsp;SlimfitPhù hợp:&amp;nbsp;Mặc hàng ngày&amp;nbsp;TÚI XÉO TRƯỚCThiết kế miệng túi nghiêng giúp thao tác lấy đồ nhanh hơnKHÓA KÉO YKKGiúp đóng mở mượt tay hỗ trợ sử dụng thuận tiện hơnTÚI ĐẮP SAUCất ví gọn hơn đồng thời làm mặt sau nhìn chỉn chuCHẤT LIỆU PIQUE KAKIVải pique kaki với thành phần 97% cotton và 3% spandex mang lại cảm giác mềm, thoáng và dễ chịu khi mặc. Chất liệu có độ bền tốt, thêm khả năng co giãn nhẹ để người mặc thoải mái hơn.Thoáng khíBền đẹpCo giãnĐứng form&amp;nbsp;FORM SLIMForm slim có dáng ôm gọn vừa phải, giúp tổng thể trông hiện đại và chỉn chu hơn. Thiết kế tôn dáng nhưng không quá bó, phù hợp với nam giới thích phong cách gọn gàng.ỨNG DỤNG LINH HOẠTQuần dễ phối cùng áo sơ mi, áo polo hoặc áo thun để tạo outfit từ đi làm đến dạo phố. Dáng slim gọn gàng giúp sản phẩm phù hợp với nhiều phong cách, từ lịch sự hằng ngày đến casual nam tính.</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Kem-0032,Xám-0032,Đen-0032', '29,30,31,32,34,36', 420000.00, 0, 'quần-kaki-nam-ống-ôm-fundamental-form-slim-main-12ed983d.jpg', '[\"quần-kaki-nam-ống-ôm-fundamental-form-slim-g1-4f808f0b.jpg\",\"quần-kaki-nam-ống-ôm-fundamental-form-slim-g2-0137f425.jpg\",\"quần-kaki-nam-ống-ôm-fundamental-form-slim-g3-6e040e18.jpg\",\"quần-kaki-nam-ống-ôm-fundamental-form-slim-g4-012414ed.jpg\",\"quần-kaki-nam-ống-ôm-fundamental-form-slim-g5-ca3f2117.png\"]', 0, 0, 10, 4, 1, 1784721841),
(128, 13, 'Quần Kaki Nam Ống Suông Trơn Fit Classic Form Straight', '<p>&amp;nbsp;&amp;nbsp;</p>\n<p><strong>Thương hiệu:</strong> NCC002</p>\n<p><strong>Loại:</strong> Quần kaki</p>', 'Đen-0026,Kem-0026,Be-0026', '29,30,31,32,34,36', 420000.00, 0, 'quần-kaki-nam-ống-suông-trơn-fit-classic-form-straight-main-66766ee9.jpg', '[\"quần-kaki-nam-ống-suông-trơn-fit-classic-form-straight-g1-838322b4.jpg\",\"quần-kaki-nam-ống-suông-trơn-fit-classic-form-straight-g2-dd879a77.jpg\",\"quần-kaki-nam-ống-suông-trơn-fit-classic-form-straight-g3-4e230f5a.jpg\",\"quần-kaki-nam-ống-suông-trơn-fit-classic-form-straight-g4-b2f19da7.jpg\",\"quần-kaki-nam-ống-suông-trơn-fit-classic-form-straight-g5-551f0a97.png\"]', 0, 0, 10, 4, 1, 1784721843);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_colors`
--

CREATE TABLE `product_colors` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `color_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `slider`
--

CREATE TABLE `slider` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `image_link` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `link` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort_order` int NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `slider`
--

INSERT INTO `slider` (`id`, `name`, `image_link`, `link`, `sort_order`, `created`) VALUES
(4, '1', 'slide.jpg', 'http://localhost/webshop/khoanh-khac-thanh-thoi-p1', 1, '2017-04-25 15:36:41'),
(5, '2', 'slide1.jpg', 'http://localhost/webshop/phong-cach-phoi-do-p2', 2, '2017-04-25 15:37:00'),
(14, '3', 'slide4.jpg', 'http://localhost/webshop/tinh-yeu-p3', 3, '2026-06-10 04:23:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `stores`
--

CREATE TABLE `stores` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên cửa hàng (Ví dụ: JM 140 Cầu Giấy)',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Địa chỉ chi tiết',
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số điện thoại liên hệ',
  `map_link` text COLLATE utf8mb4_unicode_ci COMMENT 'Nhúng link iframe Google Maps',
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Hà Nội' COMMENT 'Phân loại Tỉnh/Thành phố'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `stores`
--

INSERT INTO `stores` (`id`, `name`, `address`, `phone`, `map_link`, `city`) VALUES
(1, 'JM 140 Cầu Giấy', 'Số 140 Cầu Giấy, P. Quan Hoa, Q. Cầu Giấy, TP. Hà Nội', '0964170476', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.9244304620573!2d105.7984852759664!3d21.035710587541624!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab4106517a9b%3A0xc6a8b79b9409b85c!2sJM%20Dress%20Design!5e0!3m2!1svi!2s!4v1718105000000!5m2!1svi!2s', 'Hà Nội'),
(2, 'JM 139 Chùa Bộc', 'Số 139 Chùa Bộc, P. Trung Liệt, Q. Đống Đa, TP. Hà Nội', '0965649948', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.4855073456385!2d105.82424847596593!3d21.0132497883637!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab838ae4ec5f%3A0x8898166c3c528f86!2sJM%20139%20Ch%C3%B9a%20B%E1%BB%99c!5e0!3m2!1svi!2s!4v1718105100000!5m2!1svi!2s', 'Hà Nội'),
(4, 'JM Cách Mạng Tháng 8', 'Số 308 Cách Mạng Tháng 8, Phường 10, Quận 3, TP. Hồ Chí Minh', '02862711308', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4674395641775!2d106.6749969757692!3d10.775466859211326!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f26046e7275%3A0x367f339f40c7490!2zMzA4IEPDoWNoIE3huqFuZyBUaMOhbmcgOCwgUGjGsOG7nW5nIDEwLCBRdeG6rW4gMywgVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5o!5e0!3m2!1svi!2s!4v1718105300000!5m2!1svi!2s', 'TP. Hồ Chí Minh'),
(5, 'JM Nguyễn Trãi', 'Số 160 Nguyễn Trãi, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh', '02862755160', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4979140413007!2d106.69018447576916!3d10.77313885925464!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3b9bd7e54f%3A0xf6fb9f7a7372d8a6!2zMTYwIE5ndXnhu4VuIFRyw6NpLCBQaMcl4budbmcgQuG6v24gVGjDoG5oLCBRdeG6rW4gMSwgVGhBbmggcGjhu5EgSOG7kyBDaMOtIE1pbmI!5e0!3m2!1svi!2s!4v1718105400000!5m2!1svi!2s', 'TP. Hồ Chí Minh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transaction`
--

CREATE TABLE `transaction` (
  `id` int NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `reason` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `user_name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_email` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_phone` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_address` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `voucher_code` varchar(40) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `discount_amount` int NOT NULL DEFAULT '0',
  `payment` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `transaction`
--

INSERT INTO `transaction` (`id`, `status`, `reason`, `user_id`, `user_name`, `user_email`, `user_phone`, `user_address`, `message`, `amount`, `voucher_code`, `discount_amount`, `payment`, `created`) VALUES
(3, 1, NULL, 5, 'GoO', 'GoO@gmail.com', '01215345336', 'Hải Phòng', 'GUi hang den dia chi tren', 360000.00, '', 0, '', 1493983674),
(4, 1, NULL, 0, 'An Nhiên', 'annhien@gmail.com', '0166666666', 'Hoàng Mai - Hà Nội', 'Vui lòng trao hàng đến địa chỉ trên...', 350000.00, '', 0, '', 1493983674),
(5, 1, NULL, 0, 'Bình Nguyễn', 'binh@gmail.com', '0987654321', 'Hà Nội ', 'Gửi đến địa chỉ trên', 370000.00, '', 0, '', 1494083674),
(6, 1, NULL, 0, 'Tô Nam', 'tonam@yahoo.com.vn', '098989876', 'Thủy Nguyên - Hải Phòng', 'Ship đến địa chỉ vào sáng ngày 23/5', 469000.00, '', 0, '', 1494283674),
(7, 1, NULL, 5, 'GoO', 'GoO@gmail.com', '01215345336', 'Hải Phòng', 'Ship vào sáng mai.', 70000.00, '', 0, '', 1494183674),
(8, 1, NULL, 0, 'Linh', 'ling@yahoo.com', '098798787', 'hai Phong', 'ship', 69000.00, '', 0, '', 1494342674),
(9, 1, NULL, 0, 'Nhi', 'nhi@test.com', '0987654321', 'Long Biên - Hà Nội', 'Gửi hàng đến địa chỉ trên vào ngày mai', 200000.00, '', 0, '', 1493983674),
(10, 1, NULL, 0, 'VIP User', 'test@gmail.com', '1234567890', 'Hải Phòng', 'Ship free', 450000.00, '', 0, '', 1493983674),
(11, 0, NULL, 0, 'test', 'test@gmail.com', '1234567890', 'Hải Phòng', 'TESE', 300000.00, '', 0, '', 1494383674),
(12, 3, NULL, 6, 'Nguyen An', 'khachhang1@gmail.com', '01201212222', 'Thủy Nguyên - Hải Phòng', 'SHIP TO', 169000.00, '', 0, '', 1494407353),
(13, 0, NULL, 0, 'nguyencongquoc', 'nguyencongquoc0105@gmail.com', '0345931752', 'jvut', 'ùtf\r\n', 150000.00, '', 0, '', 1780576110),
(14, 0, NULL, 0, 'nguyencongquoc', 'nguyencongquoc0105@gmail.com', '0345931752', 'jvut', 'mau', 1150000.00, '', 0, '', 1781009870),
(15, 0, NULL, 0, 'nguyencongquoc', 'nguyencongquoc0105@gmail.com', '0345931752', 'jvut', 'kjbkj', 169000.00, '', 0, '', 1781010508),
(16, 2, NULL, 0, 'nguyencongquoc', 'nguyencongquoc0105@gmail.com', '0345931752', 'hbba', 'ad', 507000.00, '', 0, '', 1781010984),
(17, 3, NULL, 0, 'nguyencongquoc', 'nguyencongquoc0105@gmail.com', '0345931752', 'jvut', 'khbjh', 169000.00, '', 0, '', 1781011130),
(18, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'hg', 338000.00, '', 0, '', 1781676567),
(19, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'gvhg', 71520000.00, '', 0, '', 1781676675),
(20, 4, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'hvj', 397500.00, '', 0, '', 1782353371),
(23, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'ẩ', 1750000.00, '', 0, '', 1782354377),
(25, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'ad', 169000.00, '', 0, 'Chuyển khoản', 1782356886),
(26, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'mau', 4500000.00, '', 0, 'COD', 1782361274),
(27, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'đang làm thống kê', 1750000.00, '', 0, 'COD', 1782614570),
(28, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'test\r\n', 200000.00, '', 0, 'COD', 1782615008),
(30, 4, 'Tôi không còn nhu cầu mua sản phẩm này nữa', 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'ghg', 875000.00, '', 0, 'COD', 1782618691),
(31, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'ưds', 875000.00, '', 0, 'COD', 1782618728),
(32, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'rrgfdz', 4375000.00, '', 0, 'COD', 1782618792),
(36, 2, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'hghjhv', 14875000.00, '', 0, 'COD', 1782636658),
(37, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', 'adsgz', 17500000.00, '', 0, 'COD', 1782636753),
(41, 3, NULL, 8, 'Test User', 'test@example.com', '0900000000', 'HN', '', 300000.00, '', 0, 'Chuyển khoản', 1784592851),
(42, 4, 'Tôi không còn nhu cầu mua sản phẩm này nữa', 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', '', 3130000.00, '', 0, 'COD', 1784596549),
(43, 4, 'g', 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', '', 1495000.00, '', 0, 'Chuyển khoản', 1784596650),
(44, 3, NULL, 8, 'nguyencongquoc', 'congquoc@gmail.com', '0345931752', 'jvut', '', 26460000.00, '', 0, 'COD', 1784630664),
(45, 3, NULL, 10, 'Nguyen Van Du', 'khachhang1@gmail.com', '+84978057956', 'D3 Street, Phường Hiệp Phú, Thành phố Thủ Đức, Thành phố Hồ Chí Minh', '', 204000.00, '', 0, 'COD', 1784633503),
(46, 3, NULL, 10, 'Nguyen Van Du', 'khachhang1@gmail.com', '+84978057956', 'D3 Street, Phường Hiệp Phú, Thành phố Thủ Đức, Thành phố Hồ Chí Minh', '', 183600.00, 'JM10', 20400, 'COD', 1784633781);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tuyendung`
--

CREATE TABLE `tuyendung` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `intro` text COLLATE utf8mb4_general_ci,
  `content` text COLLATE utf8mb4_general_ci,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tuyendung`
--

INSERT INTO `tuyendung` (`id`, `title`, `slug`, `intro`, `content`, `image`, `created_at`) VALUES
(1, 'Tuyển Dụng Nhân Viên Sourcing', 'tuyen-dung-nhan-vien-sourcing', 'Thời trang JM Dress Design liên tục tìm kiếm và chào đón các ứng viên tài năng gia nhập đội ngũ chuyên nghiệp...', '<p>Chi tiết nội dung tuyển dụng vị trí Sourcing...</p>', 'tintuc51.jpg', '2026-06-16 12:00:00'),
(2, 'TUYỂN DỤNG TRADE MKT', 'tuyen-dung-trade-mkt', 'THỜI TRANG JM DRESS DESIGN TUYỂN DỤNG VỊ TRÍ TRADE MKT\r\n\r\n', '1. Mô tả công việc\r\n\r\nXây dựng kế hoạch và triển khai trade marketing hàng tháng/quý/năm, phù hợp với chiến lược kinh doanh của thương hiệu.\r\nTheo dõi và đánh giá hiệu quả chiến dịch, ngân sách và đề xuất cải tiến.\r\nLên ý tưởng và phối hợp tổ chức các sự kiện tại cửa hàng hoặc trung tâm thương mại (ví dụ: ra mắt bộ sưu tập, khuyến mãi đặc biệt, workshop thời trang).\r\nLên kế hoạch và đảm bảo chất lượng, tín độ in ấn POSM (Poster, Standee, Banner, Brochure, v.v.) phù hợp với từng chiến dịch.\r\nĐàm phán và làm việc với các đối tác (nhà in, đơn vị tổ chức sự kiện, đối tác quảng cáo) để đảm bảo chất lượng và tiến độ công việc.\r\nXây dựng mối quan hệ tốt với các đối tác để tối ưu hóa chi phí và hiệu quả.\r\nLàm việc chặt chẽ với đội ngũ bán hàng để đảm bảo các hoạt động trade marketing hỗ trợ tốt nhất cho doanh số.\r\nPhối hợp với đội ngũ marketing để thống nhất thông điệp và hình ảnh thương hiệu.\r\n2. Yêu cầu\r\n\r\nƯu tiên ứng viên có 1-3 năm kinh nghiệm trong lĩnh vực trade marketing, đặc biệt là ngành thời trang hoặc FMCG.  \r\nCó kinh nghiệm tổ chức sự kiện, quản lý POSM, và làm việc với đối tác.\r\nKỹ năng lập kế hoạch, quản lý dự án và ngân sách.\r\nKỹ năng giao tiếp, đàm phán và làm việc với đối tác.\r\nKỹ năng phân tích dữ liệu để đo lường hiệu quả chiến dịch.\r\nTinh thần trách nhiệm cao, chủ động và sáng tạo trong công việc.\r\nTinh thần hợp tác và làm việc nhóm tốt.\r\n3. Quyền lợi\r\n\r\nLàm việc ở môi trường trẻ, năng động và có cơ hội thăng tiến cao và phát triển năng lực.\r\nMức lương cạnh tranh, thỏa thuận theo năng lực: 10.000.000đ - 15.000.000đ + KPI \r\nHưởng đầy đủ chính sách phúc lợi của Công ty: thưởng các dịp Lễ, Tết, hưởng ưu đãi khi mua hàng tại showroom, du lịch, teambuilding,…\r\nĐược xem xét tăng lương 2 lần/năm.\r\nHưởng mọi chế độ theo quy định về luật lao động.\r\nƯu đãi khi mua hàng tại Công ty.\r\n4. Địa điểm và thời gian làm việc\r\n\r\nĐịa điểm làm việc: Duy Tân, Cầu Giấy, Hà Nội.\r\nThời gian làm việc: Từ 08h00 đến 17h30 từ thứ 2 đến thứ 6\r\n5. Cách ứng tuyển\r\n\r\nỨng viên nộp CV đến địa chỉ email: tuyendungjm@jdd.com.vn\r\nTiêu đề email ghi theo mẫu: HỌ TÊN – VỊ TRÍ ỨNG TUYỂN', 'tuyendung54.jpg', '2026-06-16 08:24:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb3_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `loyalty_points` int NOT NULL DEFAULT '0',
  `loyalty_lifetime_spend` int NOT NULL DEFAULT '0',
  `loyalty_completed_orders` int NOT NULL DEFAULT '0',
  `loyalty_tier` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'member',
  `created` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `phone`, `address`, `loyalty_points`, `loyalty_lifetime_spend`, `loyalty_completed_orders`, `loyalty_tier`, `created`) VALUES
(10, 'Nguyen Van Du', 'khachhang1@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '+84978057956', 'D3 Street, Phường Hiệp Phú, Thành phố Thủ Đức, Thành phố Hồ Chí Minh', 38, 387600, 2, 'member', 2026),
(11, 'Nguyễn Công Quốc', 'khachhang2@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '+84978057956', 'Huyện Ea Kar, Xã Phú Tân, Huyện Phú Tân, Tỉnh Cà Mau', 0, 0, 0, 'member', 2026),
(12, 'Tran Quang Duc', 'khachhang3@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0765809354', 'Thôn 2, Xã Ea Sô, Huyện Ea Kar, Tỉnh Đắk Lắk', 0, 0, 0, 'member', 2026);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_address`
--

CREATE TABLE `user_address` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `address_note` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `province_id` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `district_id` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `ward_id` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `address_line` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user_address`
--

INSERT INTO `user_address` (`id`, `user_id`, `address_note`, `province_id`, `district_id`, `ward_id`, `address_line`, `is_default`, `created`) VALUES
(1, 10, '', '', '', '', 'D3 Street, Phường Hiệp Phú, Thành phố Thủ Đức, Thành phố Hồ Chí Minh', 1, 1784631788),
(2, 11, 'Huyện Ea Kar', '96', '972', '32218', 'Huyện Ea Kar, Xã Phú Tân, Huyện Phú Tân, Tỉnh Cà Mau', 1, 1784650081),
(3, 12, 'Thôn 2', '66', '651', '24379', 'Thôn 2, Xã Ea Sô, Huyện Ea Kar, Tỉnh Đắk Lắk', 1, 1784653988);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_point_log`
--

CREATE TABLE `user_point_log` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `points` int NOT NULL,
  `type` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'earn',
  `transaction_id` int NOT NULL DEFAULT '0',
  `note` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `created` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user_point_log`
--

INSERT INTO `user_point_log` (`id`, `user_id`, `points`, `type`, `transaction_id`, `note`, `created`) VALUES
(1, 10, 18, 'earn', 46, 'Tích điểm đơn hoàn thành #46', 1784653528),
(2, 10, 20, 'earn', 45, 'Tích điểm đơn hoàn thành #45', 1784653534);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `van_chuyen`
--

CREATE TABLE `van_chuyen` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intro` text COLLATE utf8mb4_unicode_ci,
  `content` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `van_chuyen`
--

INSERT INTO `van_chuyen` (`id`, `title`, `slug`, `image`, `intro`, `content`, `created_at`) VALUES
(1, ' CHÍNH SÁCH VẬN CHUYỂN', 'chinh-sach-van-chuyen', 'chinhsach.jpg', '- JM nhận giao hàng toàn quốc, khách hàng có thể lựa chọn 2 hình thức thanh toán khi đặt hàng như sau', '+ Thanh toán trực tiếp tiền hàng và chi phí vận chuyển khi nhận hàng (SHIP COD TOÀN QUỐC).\r\n\r\n+ Chuyển khoản thanh toán trước toàn bộ tiền hàng và chi phí vận chuyển.\r\n\r\n- Chi phí vận chuyển được áp dụng theo bảng cước của các đối tác vận chuyển (vnpost).\r\n\r\n- Đối với các đơn hàng tại Hà Nội, khách hàng thanh toán và nhận hàng ngay trong ngày.\r\n\r\n- Đối với khách hàng online tỉnh, JM hỗ trợ đổi hàng trong vòng 30 ngày. ', '2026-06-16 08:51:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `voucher`
--

CREATE TABLE `voucher` (
  `id` int NOT NULL,
  `code` varchar(40) COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(120) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `discount_type` enum('percent','fixed') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'fixed',
  `discount_value` int NOT NULL DEFAULT '0',
  `min_order_amount` int NOT NULL DEFAULT '0',
  `max_discount` int NOT NULL DEFAULT '0',
  `tier_min` varchar(20) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'member',
  `user_id` int NOT NULL DEFAULT '0',
  `usage_limit` int NOT NULL DEFAULT '0',
  `used_count` int NOT NULL DEFAULT '0',
  `per_user_limit` int NOT NULL DEFAULT '1',
  `valid_from` int NOT NULL DEFAULT '0',
  `valid_to` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `voucher`
--

INSERT INTO `voucher` (`id`, `code`, `name`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount`, `tier_min`, `user_id`, `usage_limit`, `used_count`, `per_user_limit`, `valid_from`, `valid_to`, `is_active`, `created`) VALUES
(1, 'JM10', 'Giảm 10%', '', 'percent', 10, 200000, 0, 'member', 0, 100, 1, 1, 0, 0, 1, 1784633342),
(2, 'JM20', 'Giảm 20%', 'Giảm 20 % cho đơn hàng trên 500k', 'percent', 20, 500000, 0, 'member', 0, 100, 0, 1, 0, 0, 1, 1784634003);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `voucher_use`
--

CREATE TABLE `voucher_use` (
  `id` int NOT NULL,
  `voucher_id` int NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `transaction_id` int NOT NULL,
  `discount_amount` int NOT NULL DEFAULT '0',
  `created` int NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `voucher_use`
--

INSERT INTO `voucher_use` (`id`, `voucher_id`, `user_id`, `transaction_id`, `discount_amount`, `created`) VALUES
(1, 1, 10, 46, 20400, 1784633781);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_admin_email` (`email`);

--
-- Chỉ mục cho bảng `ai_conversation`
--
ALTER TABLE `ai_conversation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_conv_user` (`user_id`),
  ADD KEY `idx_ai_conv_guest` (`guest_token`),
  ADD KEY `idx_ai_conv_status` (`status`),
  ADD KEY `idx_ai_conv_staff` (`staff_id`);

--
-- Chỉ mục cho bảng `ai_faq`
--
ALTER TABLE `ai_faq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_faq_active` (`is_active`);

--
-- Chỉ mục cho bảng `ai_message`
--
ALTER TABLE `ai_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_msg_conv` (`conversation_id`);

--
-- Chỉ mục cho bảng `ai_setting`
--
ALTER TABLE `ai_setting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ai_setting_key` (`setting_key`);

--
-- Chỉ mục cho bảng `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `catalog`
--
ALTER TABLE `catalog`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `hop_tac`
--
ALTER TABLE `hop_tac`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_transaction_id` (`transaction_id`),
  ADD KEY `idx_order_product_id` (`product_id`);

--
-- Chỉ mục cho bảng `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_catalog_id` (`catalog_id`);

--
-- Chỉ mục cho bảng `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_colors_product` (`product_id`);

--
-- Chỉ mục cho bảng `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_transaction_user_id` (`user_id`),
  ADD KEY `idx_transaction_status` (`status`);

--
-- Chỉ mục cho bảng `tuyendung`
--
ALTER TABLE `tuyendung`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_email` (`email`);

--
-- Chỉ mục cho bảng `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_address_user` (`user_id`);

--
-- Chỉ mục cho bảng `user_point_log`
--
ALTER TABLE `user_point_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_upl_user` (`user_id`);

--
-- Chỉ mục cho bảng `van_chuyen`
--
ALTER TABLE `van_chuyen`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_voucher_code` (`code`),
  ADD KEY `idx_voucher_user` (`user_id`);

--
-- Chỉ mục cho bảng `voucher_use`
--
ALTER TABLE `voucher_use`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vu_voucher` (`voucher_id`),
  ADD KEY `idx_vu_user` (`user_id`),
  ADD KEY `idx_vu_tx` (`transaction_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `ai_conversation`
--
ALTER TABLE `ai_conversation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `ai_faq`
--
ALTER TABLE `ai_faq`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `ai_message`
--
ALTER TABLE `ai_message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT cho bảng `ai_setting`
--
ALTER TABLE `ai_setting`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `catalog`
--
ALTER TABLE `catalog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `hop_tac`
--
ALTER TABLE `hop_tac`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `order`
--
ALTER TABLE `order`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT cho bảng `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `product`
--
ALTER TABLE `product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT cho bảng `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT cho bảng `tuyendung`
--
ALTER TABLE `tuyendung`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `user_address`
--
ALTER TABLE `user_address`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `user_point_log`
--
ALTER TABLE `user_point_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `van_chuyen`
--
ALTER TABLE `van_chuyen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `voucher`
--
ALTER TABLE `voucher`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `voucher_use`
--
ALTER TABLE `voucher_use`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transaction` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_catalog` FOREIGN KEY (`catalog_id`) REFERENCES `catalog` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `product_colors`
--
ALTER TABLE `product_colors`
  ADD CONSTRAINT `fk_product_colors_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
