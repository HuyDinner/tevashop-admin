<?php
// tevashop-admin/api/notifications.php

// Bật báo lỗi (chỉ trong môi trường phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cho phép CORS (chỉ trong môi trường dev, nên hạn chế trong prod)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Xử lý preflight OPTIONS request cho CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Định nghĩa đường dẫn ROOT_PATH
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR); // D:\xampp\htdocs\tevashop-admin\

// Đảm bảo session đã được khởi tạo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nhúng file cấu hình database
require_once ROOT_PATH . 'config/db_config.php';

// Nhúng Notification Model
require_once ROOT_PATH . 'models/Notification.php';

// Thiết lập header để trình duyệt biết đây là phản hồi JSON
header('Content-Type: application/json');

// Lấy kết nối PDO từ Database Singleton
try {
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối database: ' . $e->getMessage()]);
    exit();
}

// Khởi tạo Notification Model
$notificationModel = new Notification($db);

// Lấy ID của admin đang đăng nhập từ session
$adminId = $_SESSION['admin_id'] ?? null;

// Kiểm tra xem admin đã đăng nhập chưa
if ($adminId === null || !isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập hoặc không có quyền truy cập.']);
    exit();
}

// Lấy dữ liệu từ request body cho POST/PUT/DELETE
$input = json_decode(file_get_contents('php://input'), true);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest($notificationModel, $adminId);
        break;
    case 'POST':
        handlePostRequest($notificationModel, $adminId, $input);
        break;
    case 'PUT':
        handlePutRequest($notificationModel, $adminId, $input);
        break;
    case 'DELETE':
        handleDeleteRequest($notificationModel, $adminId, $input);
        break;
    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Phương thức HTTP không được hỗ trợ.']);
        break;
}

/**
 * Xử lý yêu cầu GET.
 * Có thể lấy thông báo cho dropdown (mặc định) hoặc tất cả thông báo với phân trang/lọc.
 */
function handleGetRequest($notificationModel, $adminId) {
    // Nếu có tham số 'action=get_all', đây là yêu cầu từ trang quản lý thông báo
    if (isset($_GET['action']) && $_GET['action'] === 'get_all') {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
        $isReadFilter = isset($_GET['is_read']) ? $_GET['is_read'] : null; // 'true', 'false', or null
        $typeFilter = isset($_GET['type']) ? trim($_GET['type']) : '';

        // Chuyển đổi 'true'/'false' string sang boolean
        if ($isReadFilter !== null) {
            $isReadFilter = ($isReadFilter === 'true');
        }

        try {
            $result = $notificationModel->getAllNotifications(
                $adminId, 
                $page, 
                $limit, 
                $searchQuery, 
                $isReadFilter, 
                $typeFilter
            );
            echo json_encode([
                'success' => true,
                'notifications' => $result['notifications'],
                'total_notifications' => $result['total_notifications'],
                'total_pages' => $result['total_pages'],
                'current_page' => $result['current_page']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            error_log("API Error (getAllNotifications): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ khi tải tất cả thông báo.']);
        }
    } else {
        // Mặc định: Lấy thông báo chưa đọc cho dropdown header
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        try {
            $notifications = $notificationModel->getUnreadNotificationsForAdmin($adminId, $limit);
            $unreadCount = $notificationModel->countUnreadNotificationsForAdmin($adminId);
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            error_log("API Error (getUnreadNotifications): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ khi tải thông báo.']);
        }
    }
}

/**
 * Xử lý yêu cầu POST (Tạo mới, Đánh dấu đã đọc/tất cả đã đọc)
 */
function handlePostRequest($notificationModel, $adminId, $input) {
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'create':
            // Dữ liệu cho tạo thông báo mới
            $tieu_de = $input['tieu_de'] ?? '';
            $noi_dung = $input['noi_dung'] ?? '';
            $loai_thong_bao = $input['loai_thong_bao'] ?? 'general';
            $link_url = $input['link_url'] ?? null;
            $admin_id_to_notify = isset($input['admin_id']) && $input['admin_id'] !== '' ? (int)$input['admin_id'] : $adminId; // Có thể gửi cho admin khác

            if (empty($tieu_de) || empty($noi_dung)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Tiêu đề và nội dung không được để trống.']);
                return;
            }

            try {
                $newId = $notificationModel->createNotification($admin_id_to_notify, $tieu_de, $noi_dung, $loai_thong_bao, $link_url);
                if ($newId) {
                    echo json_encode(['success' => true, 'message' => 'Thông báo đã được tạo thành công.', 'id' => $newId]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Không thể tạo thông báo.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Error (createNotification): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ khi tạo thông báo.']);
            }
            break;

        case 'mark_as_read':
            $notificationId = (int)($input['notification_id'] ?? 0);
            if ($notificationId === 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Thiếu ID thông báo để đánh dấu đã đọc.']);
                return;
            }
            try {
                $rowsAffected = $notificationModel->markAsRead($notificationId, $adminId);
                if ($rowsAffected > 0) {
                    echo json_encode(['success' => true, 'message' => 'Thông báo đã được đánh dấu là đã đọc.']);
                } else {
                    http_response_code(404); // Not Found or Already Read
                    echo json_encode(['success' => false, 'message' => 'Thông báo không tìm thấy hoặc đã được đọc.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Error (markAsRead): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ khi cập nhật thông báo.']);
            }
            break;

        case 'mark_all_as_read':
            try {
                $rowsAffected = $notificationModel->markAllAsRead($adminId);
                echo json_encode(['success' => true, 'message' => 'Tất cả thông báo đã được đánh dấu là đã đọc.']);
            } catch (Exception $e) {
                http_response_code(500);
                error_log("API Error (markAllAsRead): " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ khi cập nhật tất cả thông báo.']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ cho POST request.']);
            break;
    }
}

/**
 * Xử lý yêu cầu PUT (Cập nhật thông báo)
 */
function handlePutRequest($notificationModel, $adminId, $input) {
    $notificationId = (int)($input['id'] ?? 0);
    $tieu_de = $input['tieu_de'] ?? '';
    $noi_dung = $input['noi_dung'] ?? '';
    $loai_thong_bao = $input['loai_thong_bao'] ?? 'general';
    $link_url = $input['link_url'] ?? null;

    if ($notificationId === 0 || empty($tieu_de) || empty($noi_dung)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Thiếu ID, tiêu đề hoặc nội dung để cập nhật.']);
        return;
    }

    try {
        $rowsAffected = $notificationModel->updateNotification($notificationId, $adminId, $tieu_de, $noi_dung, $loai_thong_bao, $link_url);
        if ($rowsAffected > 0) {
            echo json_encode(['success' => true, 'message' => 'Thông báo đã được cập nhật.']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Thông báo không tìm thấy hoặc bạn không có quyền cập nhật.']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        error_log("API Error (updateNotification): " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ khi cập nhật thông báo.']);
    }
}

/**
 * Xử lý yêu cầu DELETE (Xóa thông báo)
 */
function handleDeleteRequest($notificationModel, $adminId, $input) {
    $notificationId = (int)($input['notification_id'] ?? 0);

    if ($notificationId === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Thiếu ID thông báo để xóa.']);
        return;
    }

    try {
        $rowsAffected = $notificationModel->deleteNotification($notificationId, $adminId);
        if ($rowsAffected > 0) {
            echo json_encode(['success' => true, 'message' => 'Thông báo đã được xóa.']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Thông báo không tìm thấy hoặc bạn không có quyền xóa.']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        error_log("API Error (deleteNotification): " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ khi xóa thông báo.']);
    }
}
