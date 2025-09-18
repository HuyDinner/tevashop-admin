<?php
// admin/index.php

session_start();
// Bật báo lỗi (chỉ trong môi trường phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Định nghĩa các đường dẫn cơ bản
define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR); // D:\xampp\htdocs\tevashop-admin\admin\
define('APP_PATH', ROOT_PATH); // Giữ nguyên như ROOT_PATH cho thư mục admin
define('VIEWS_PATH', APP_PATH . 'views' . DIRECTORY_SEPARATOR);
define('CONTROLLERS_PATH', APP_PATH . 'controllers' . DIRECTORY_SEPARATOR);
define('MODELS_PATH', APP_PATH . 'models' . DIRECTORY_SEPARATOR);
define('CONFIG_PATH', APP_PATH . 'config' . DIRECTORY_SEPARATOR);

// Nhúng file cấu hình database (singleton Database class)
// Giả định file db_config.php của bạn trả về một đối tượng PDO hoặc có hàm getDbConnection()
require_once CONFIG_PATH . 'db_config.php';

// Class Database trong db_config.php của bạn có getInstance() trả về kết nối PDO
// Hoặc sử dụng hàm getDbConnection() nếu bạn đã định nghĩa như vậy
$db = Database::getInstance()->getConnection();

// Điều chỉnh lại base URL cho đúng cấu trúc
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// Lấy đường dẫn script hiện tại, ví dụ: /tevashop-admin/admin/index.php
$script_name = $_SERVER['SCRIPT_NAME'];

// Loại bỏ 'index.php' để có được đường dẫn thư mục gốc của admin
// Ví dụ: từ /tevashop-admin/admin/index.php -> /tevashop-admin/admin/
$base_web_root_path = str_replace(basename($script_name), '', $script_name);

// Định nghĩa BASE_URL, CSS_PATH, JS_PATH dựa trên base_web_root_path
define('BASE_URL', $protocol . "://" . $host . $base_web_root_path);
define('CSS_PATH', BASE_URL . 'css/'); // Đảm bảo thư mục css nằm trong thư mục admin
define('JS_PATH', BASE_URL . 'js/');   // Đảm bảo thư mục js nằm trong thư mục admin
define('IMAGES_PATH', BASE_URL . 'images/'); // Đảm bảo thư mục images nằm trong thư mục admin

// Yêu cầu các Controllers và Models
require_once MODELS_PATH . 'ChatModel.php';
require_once CONTROLLERS_PATH . 'DashboardController.php'; // Ví dụ một controller khác
require_once CONTROLLERS_PATH . 'NotificationsController.php'; // **ĐÂY LÀ DÒNG QUAN TRỌNG**
require_once MODELS_PATH . 'Notification.php'; // **ĐÂY LÀ DÒNG QUAN TRỌNG**
require_once CONTROLLERS_PATH . 'ChatController.php';

// Logic định tuyến đơn giản
// Mặc định: DashboardController, action index
$controllerName = 'Dashboard'; 
$actionName = 'index'; 
$id = null; 

if (isset($_SERVER['REQUEST_URI'])) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_parts = explode('/', trim($requestUri, '/'));

    // Tìm vị trí của thư mục 'admin' trong URL để xác định base path
    // Giả định cấu trúc URL luôn là /path/to/tevashop-admin/admin/controller/action/id
    $admin_segment_index = -1;
    foreach ($uri_parts as $key => $part) {
        if ($part === 'admin') {
            $admin_segment_index = $key;
            break;
        }
    }

    if ($admin_segment_index !== -1) {
        // Lấy các phân đoạn URL sau 'admin/'
        $path_segments = array_slice($uri_parts, $admin_segment_index + 1);

        // Xử lý route cho API (nếu bạn muốn route API qua index.php này)
        // Nếu API nằm ở /api/notifications.php (ngang hàng với admin/),
        // bạn cần điều chỉnh route này.
        // Tuy nhiên, thông thường API được gọi trực tiếp.
        // Tôi sẽ không xử lý API ở đây để giữ router admin đơn giản.
        // AJAX gọi trực tiếp /api/notifications.php sẽ không đi qua router này.

        if (!empty($path_segments[0])) {
            $controllerName = ucfirst($path_segments[0]); // Ví dụ: 'notifications' -> 'Notifications'
        }
        if (isset($path_segments[1])) {
            $actionName = $path_segments[1];
        }
        if (isset($path_segments[2])) {
            $id = $path_segments[2]; 
        }
    }
}

// =========================================================================
// LOGIC BẢO MẬT: Kiểm tra đăng nhập
// Chỉ áp dụng kiểm tra nếu không phải trang login/register/logout
$public_routes = [
    'Auth/login',
    'Auth/register',
    'Auth/logout'
];

$current_route = $controllerName . '/' . $actionName;

// Nếu không phải route công khai và chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!in_array($current_route, $public_routes) && !isset($_SESSION['admin_logged_in'])) {
    header("Location: " . BASE_URL . "auth/login");
    exit();
}
// Nếu đã đăng nhập và đang cố gắng truy cập login/register, chuyển hướng về dashboard
if (in_array($current_route, ['Auth/login', 'Auth/register']) && isset($_SESSION['admin_logged_in'])) {
    header("Location: " . BASE_URL . "dashboard/index"); // Hoặc dashboard
    exit();
}
// =========================================================================


$controllerFile = CONTROLLERS_PATH . $controllerName . 'Controller.php';


if (file_exists($controllerFile)) {
    require_once $controllerFile; 

    $controllerClass = $controllerName . 'Controller';

    $controller = new $controllerClass($db); 

    if (method_exists($controller, $actionName)) {
        if ($id !== null) {
            $controller->$actionName($id);
        } else {
            $controller->$actionName();
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found: Action '{$actionName}' not found in '{$controllerName}' controller.";
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found: Controller '{$controllerName}' not found.";
}
