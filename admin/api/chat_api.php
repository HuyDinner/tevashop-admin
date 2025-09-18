<?php
// tevashop-admin/api/chat_api.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cho phép CORS (chỉ trong môi trường DEV, PROD cần cấu hình cụ thể và an toàn hơn)
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Xử lý preflight OPTIONS request cho CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Định nghĩa ROOT_PATH là thư mục gốc của dự án (tevashop-admin)
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once ROOT_PATH . 'config/db_config.php';
require_once ROOT_PATH . 'models/ChatModel.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối database: ' . $e->getMessage()]);
    exit();
}

$chatModel = new ChatModel($db);

$adminId = $_SESSION['admin_id'] ?? null;

// Kiểm tra xác thực admin
if ($adminId === null || !isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập hoặc không có quyền truy cập.']);
    exit();
}

// Lấy dữ liệu đầu vào JSON từ request body cho POST/PUT/DELETE
$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? ($input['action'] ?? ''); // Lấy hành động từ query param hoặc body

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleChatGetRequest($chatModel, $adminId, $action);
        break;
    case 'POST':
        handleChatPostRequest($chatModel, $adminId, $action, $input);
        break;
    case 'PUT':
        handleChatPutRequest($chatModel, $adminId, $action, $input);
        break;
    case 'DELETE':
        handleChatDeleteRequest($chatModel, $adminId, $action, $input);
        break;
    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Phương thức HTTP không được hỗ trợ.']);
        break;
}

function handleChatGetRequest($chatModel, $adminId, $action) {
    switch ($action) {
        case 'get_all_chats':
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $statusFilter = isset($_GET['status']) ? trim($_GET['status']) : 'tat_ca'; // 'mo', 'dong', 'cho_xu_ly', 'tat_ca'

            try {
                $result = $chatModel->getAllChats($adminId, $page, $limit, $search, $statusFilter);
                echo json_encode(['success' => true, 'data' => $result]);
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Chat GET error (get_all_chats): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi khi lấy danh sách cuộc trò chuyện.']);
            }
            break;

        case 'get_messages':
            $chatId = isset($_GET['chat_id']) ? (int)$_GET['chat_id'] : 0;
            if ($chatId === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Thiếu ID cuộc trò chuyện.']);
                return;
            }
            try {
                $messages = $chatModel->getMessagesByChatId($chatId, $adminId);
                echo json_encode(['success' => true, 'data' => $messages]);
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Chat GET error (get_messages): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi khi lấy tin nhắn.']);
            }
            break;

        case 'get_total_unread_customer_messages':
            try {
                $totalUnread = $chatModel->getTotalUnreadCustomerMessages();
                echo json_encode(['success' => true, 'total_unread_messages' => $totalUnread]);
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Chat GET error (get_total_unread_customer_messages): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi khi lấy tổng số tin nhắn chưa đọc.']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Hành động GET không hợp lệ.']);
            break;
    }
}

function handleChatPostRequest($chatModel, $adminId, $action, $input) {
    switch ($action) {
        case 'send_message':
            $chatId = (int)($input['id_cuoc_tro_chuyen'] ?? 0);
            $messageContent = $input['noi_dung'] ?? '';

            if ($chatId === 0 || empty($messageContent)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Thiếu ID cuộc trò chuyện hoặc nội dung tin nhắn.']);
                return;
            }

            try {
                if ($chatModel->sendMessage($chatId, $adminId, $messageContent)) {
                    echo json_encode(['success' => true, 'message' => 'Tin nhắn đã được gửi.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Không thể gửi tin nhắn.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Chat POST error (send_message): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi khi gửi tin nhắn.']);
            }
            break;
        
        case 'create_or_get_chat': // Dành cho khách hàng gửi tin nhắn đầu tiên, hoặc admin chủ động tạo
            $customerId = (int)($input['id_khach_hang'] ?? 0);
            $initialMessageContent = $input['noi_dung'] ?? '';
            // Admin có thể chủ động gán mình vào chat ngay khi tạo
            $assignAdminId = isset($input['gan_admin_ngay']) && $input['gan_admin_ngay'] ? $adminId : null; 

            if ($customerId === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Thiếu ID khách hàng.']);
                return;
            }

            try {
                $newChatId = $chatModel->createOrGetChat($customerId, $initialMessageContent, $assignAdminId);
                if ($newChatId) {
                    echo json_encode(['success' => true, 'message' => 'Cuộc trò chuyện đã được tạo/cập nhật.', 'id_cuoc_tro_chuyen' => $newChatId]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Không thể tạo/cập nhật cuộc trò chuyện.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Chat POST error (create_or_get_chat): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi khi tạo/lấy cuộc trò chuyện.']);
            }
            break;

        case 'assign_chat':
            $chatId = (int)($input['id_cuoc_tro_chuyen'] ?? 0);
            if ($chatId === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Thiếu ID cuộc trò chuyện để nhận.']);
                return;
            }
            try {
                if ($chatModel->assignChatToAdmin($chatId, $adminId)) {
                    echo json_encode(['success' => true, 'message' => 'Bạn đã nhận cuộc trò chuyện này.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Không thể nhận cuộc trò chuyện (có thể đã có người nhận hoặc đã đóng).']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Chat POST error (assign_chat): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi khi nhận cuộc trò chuyện.']);
            }
            break;

        case 'close_chat':
            $chatId = (int)($input['id_cuoc_tro_chuyen'] ?? 0);
            if ($chatId === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Thiếu ID cuộc trò chuyện để đóng.']);
                return;
            }
            try {
                if ($chatModel->closeChat($chatId, $adminId)) {
                    echo json_encode(['success' => true, 'message' => 'Cuộc trò chuyện đã được đóng.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Không thể đóng cuộc trò chuyện (có thể không phải của bạn hoặc đã đóng).']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Chat POST error (close_chat): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi khi đóng cuộc trò chuyện.']);
            }
            break;
        
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Hành động POST không hợp lệ.']);
            break;
    }
}

// Hàm xử lý PUT và DELETE có thể được bổ sung nếu cần cập nhật/xóa tin nhắn riêng lẻ,
// nhưng thường các hành động này được thực hiện qua POST (đánh dấu đã đọc/đóng chat).
// Hiện tại tôi bỏ trống PUT/DELETE vì nó không quá cần thiết cho Chat API cơ bản.
function handleChatPutRequest($chatModel, $adminId, $action, $input) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức PUT không được hỗ trợ cho API này.']);
}

function handleChatDeleteRequest($chatModel, $adminId, $action, $input) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức DELETE không được hỗ trợ cho API này.']);
}
