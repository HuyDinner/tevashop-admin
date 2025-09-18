<?php
// admin/views/auth/login.php
// View này không cần header/sidebar/footer của admin panel thông thường

if (!isset($pageTitle)) {
    $pageTitle = "Đăng nhập"; // Fallback title
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Admin</title>
    <link rel="icon" href="<?= BASE_URL ?>assets/images/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background-color: #212529; /* Màu nền tối */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .login-container {
            background-color: #343a40; /* Màu nền thẻ tối hơn */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            color: #f8f9fa; /* Màu chữ sáng */
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ffffff;
        }
        .form-control {
            background-color: #495057; /* Màu nền input */
            border: 1px solid #6c757d; /* Màu border input */
            color: #f8f9fa; /* Màu chữ input */
        }
        .form-control::placeholder {
            color: #adb5bd; /* Màu placeholder */
        }
        .form-control:focus {
            background-color: #495057;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
            color: #f8f9fa;
        }
        .btn-primary {
            background-color: #6f42c1; /* Màu tím */
            border-color: #6f42c1;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            font-size: 1.1em;
        }
        .btn-primary:hover {
            background-color: #5d36a3; /* Màu tím đậm hơn khi hover */
            border-color: #5d36a3;
        }
        .text-center a {
            color: #007bff; /* Màu xanh dương cho link */
            text-decoration: none;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Đăng nhập Admin</h2>
        <form action="<?= BASE_URL ?>auth/login" method="POST">
            <div class="mb-3">
                <label for="username_or_email" class="form-label">Tên người dùng hoặc Email:</label>
                <input type="text" class="form-control" id="username_or_email" name="username_or_email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Đăng nhập</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
