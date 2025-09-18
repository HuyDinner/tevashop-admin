<?php
// admin/views/includes/header.php
// Đảm bảo session đã được khởi tạo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra trạng thái đăng nhập
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$adminUsername = $_SESSION['admin_username'] ?? 'Khách'; // Lấy username nếu đã đăng nhập

// Các biến cần thiết cho view (nếu chưa được định nghĩa từ controller)
if (!isset($pageTitle)) $pageTitle = "Admin Dashboard";
if (!isset($pageIcon)) $pageIcon = "fas fa-th-large";
if (!isset($controllerName)) $controllerName = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Dashboard'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= CSS_PATH ?>styles.css"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?= CSS_PATH ?>all.min.css"> 

    <style>
        /* CSS cho Header chính */
        .hd-main-header {
            background-color: var(--primary-bg, #1c1a27); /* Sử dụng fallback nếu biến không tồn tại */
            color: var(--text-light, #f0f0f0);
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color, #3b394b);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .hd-header-title {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 600;
        }

        .hd-header-title i {
            margin-right: 10px;
            color: var(--teva-teal-bg, #00bcd4);
        }

        /* Header Icons container */
        .hd-header-icons {
            display: flex;
            align-items: center;
            position: relative;
        }

        /* Base style for each icon item (bell, chat, cog, user) */
        .hd-icon-item {
            position: relative;
            margin-left: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px;
        }

        .hd-icon-item i {
            font-size: 20px;
            color: var(--text-dim, #b0b0b0);
            transition: color 0.3s ease;
        }

        .hd-icon-item i:hover {
            color: var(--teva-teal-bg, #00bcd4);
        }

        /* Badge for notifications/messages */
        .hd-icon-item .hd-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--btn-danger-color, #dc3545);
            color: white;
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 50%;
            line-height: 1;
            min-width: 18px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 18px;
        }

        /* Dropdown content container */
        .hd-dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: var(--card-bg, #2a283a);
            min-width: 250px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
            z-index: 101;
            border-radius: 8px;
            margin-top: 10px;
            overflow: hidden;
        }

        .hd-dropdown-content.hd-show {
            display: block;
        }

        .hd-dropdown-content .hd-dropdown-header {
            background-color: var(--purple-main, #8a2be2);
            color: #fff;
            padding: 10px 15px;
            font-weight: 600;
            font-size: 16px;
            border-bottom: 1px solid var(--border-color, #3b394b);
        }

        .hd-dropdown-content ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .hd-dropdown-content ul li {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color, #3b394b);
            font-size: 14px;
            color: var(--text-light, #f0f0f0);
            transition: background-color 0.2s ease;
        }

        .hd-dropdown-content ul li:last-child {
            border-bottom: none;
        }

        .hd-dropdown-content ul li:hover {
            background-color: var(--sidebar-bg-dark, #22202f);
        }

        .hd-dropdown-content ul li a {
            text-decoration: none;
            color: var(--text-light, #f0f0f0);
            display: flex;
            align-items: center;
            width: 100%;
        }

        .hd-dropdown-content ul li a i {
            margin-right: 10px;
            font-size: 16px;
            color: var(--text-dim, #b0b0b0);
        }

        .hd-dropdown-content .hd-dropdown-footer {
            background-color: var(--sidebar-bg-dark, #22202f);
            color: var(--text-dim, #b0b0b0);
            padding: 10px 15px;
            text-align: center;
            font-size: 13px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .hd-dropdown-content .hd-dropdown-footer:hover {
            background-color: var(--teva-teal-dark, #008c9e);
            color: #fff;
        }

        /* User Profile Specifics */
        .hd-user-profile-container {
            display: flex;
            align-items: center;
            margin-left: 25px;
            cursor: pointer;
        }

        .hd-user-profile-icon {
            font-size: 28px !important;
            color: var(--text-light, #f0f0f0) !important;
            background-color: var(--purple-main, #8a2be2);
            border-radius: 50%;
            padding: 5px;
            margin-right: 10px;
        }

        .hd-user-profile-container .hd-user-name {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-light, #f0f0f0);
        }

        .hd-user-profile-dropdown {
            min-width: 180px;
        }

        /* Style cho thông báo chưa đọc */
        .hd-dropdown-content ul li.hd-unread {
            font-weight: bold;
            background-color: var(--sidebar-bg-dark, #22202f);
        }

        .hd-dropdown-content ul li:hover a {
             color: inherit; /* Giữ màu chữ của link giống li khi hover */
        }


        /* Responsive adjustments for header icons */
        @media (max-width: 768px) {
            .hd-main-header {
                flex-direction: column;
                align-items: flex-start;
                padding-bottom: 10px;
            }
            .hd-header-icons {
                margin-top: 15px;
                width: 100%;
                justify-content: space-around;
                margin-left: 0;
            }
            .hd-icon-item {
                margin-left: 0;
            }
            .hd-header-title {
                font-size: 20px;
                margin-bottom: 10px;
            }
            .hd-user-profile-container .hd-user-name {
                display: none;
            }
            .hd-dropdown-content {
                right: auto;
                left: 50%;
                transform: translateX(-50%);
                min-width: 90%;
            }
        }
    </style>
</head>
<body>
            <header class="main-header">
                <div class="header-title">
                    <i class="<?= $pageIcon ?? 'fas fa-th-large'; ?>"></i> <?php echo $pageTitle ?? 'Tổng quan Thống kê'; ?> 
                </div>
                <div class="header-icons">
                    
                    <div class="hd-icon-item hd-notification-icon" id="notificationIcon">
                        <i class="fas fa-bell"></i>
                        <span class="hd-badge" id="notificationBadge">0</span>
                        <div class="hd-dropdown-content" id="notificationDropdown">
                            <div class="hd-dropdown-header">Thông báo mới</div>
                            <ul id="notificationList">
                                <li>Đang tải thông báo...</li>
                            </ul>
                            <div class="hd-dropdown-footer" id="viewAllNotifications"><a href="<?= BASE_URL ?>notifications/index" 
                            style="text-decoration: none; color:#00bcd4;">Xem tất cả thông báo</a></div>
                        </div>
                    </div>

                    <div class="hd-icon-item hd-chat-icon" id="chatIcon">
                        <i class="fa-regular fa-comments"></i>
                            <span class="hd-badge" id="chatBadge">0</span>
                            <div class="hd-dropdown-content" id="chatDropdown">
                                <div class="hd-dropdown-header">Tin nhắn mới</div>
                                    <ul id="chatList">
                                        <li>Đang tải tin nhắn...</li>
                                    </ul>
                                <div class="hd-dropdown-footer" id="viewAllChats"><a href="<?= BASE_URL ?>chat/index" 
                                style="text-decoration: none; color:#00bcd4;">Xem tất cả tin nhắn</a></div>
                            </div>
                    </div>
                    
                    <div class="hd-icon-item hd-settings-icon" id="settingsIcon">
                        <i class="fas fa-cog"></i>
                            <div class="hd-dropdown-content" id="settingsDropdown">
                                <div class="hd-dropdown-header">Cài đặt hệ thống</div>
                                    <ul>
                                        <li><a href="<?= BASE_URL ?>account/index" id="manageAccountLink"><i class="fas fa-user-circle"></i> Quản lý tài khoản</a></li>
                                        <li><a href="#" id="themeSettingsLink"><i class="fas fa-palette"></i> Tùy chỉnh giao diện</a></li>
                                        <li><a href="#" id="securitySettingsLink"><i class="fas fa-shield-alt"></i> Bảo mật & Quyền</a></li>
                                        <li><a href="#" id="languageSettingsLink"><i class="fas fa-globe"></i> Ngôn ngữ</a></li>
                                    </ul>
                                <div class="hd-dropdown-footer">Lưu cài đặt</div>
                                </div>
                            </div>

                    <?php if ($isLoggedIn): ?>
                        <a href="<?= BASE_URL ?>auth/logout" class="user-profile-link" title="Đăng xuất">
                                <i class="fas fa-user-circle user-profile-icon"></i>
                                    <span class="user-name"><?= htmlspecialchars($adminUsername) ?></span>
                        </a>
                    <?php else: ?>
                        <a href="#" class="user-profile-link" data-bs-toggle="modal" data-bs-target="#authModal" title="Đăng nhập / Đăng ký">
                                <i class="fas fa-user-circle user-profile-icon"></i>
                                    <span class="user-name">Guest</span>
                        </a>
                    <?php endif; ?>
                </div>
            </header>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy các phần tử (IDs vẫn giữ nguyên vì JS lấy theo ID)
            const notificationIcon = document.getElementById('notificationIcon');
            const chatIcon = document.getElementById('chatIcon');
            const settingsIcon = document.getElementById('settingsIcon');
            const userProfileContainer = document.getElementById('userProfileIcon');

            const notificationDropdown = document.getElementById('notificationDropdown');
            const chatDropdown = document.getElementById('chatDropdown');
            const settingsDropdown = document.getElementById('settingsDropdown');
            const userProfileDropdown = document.getElementById('userProfileDropdown');

            const notificationBadge = document.getElementById('notificationBadge');
            const chatBadge = document.getElementById('chatBadge');
            const notificationList = document.getElementById('notificationList');
            const chatList = document.getElementById('chatList');

            // Hàm đóng/mở dropdown
            function toggleDropdown(iconElement, dropdownElement) {
                // Đóng tất cả các dropdown khác trước khi mở cái mới
                [notificationDropdown, chatDropdown, settingsDropdown, userProfileDropdown].forEach(dd => {
                    if (dd && dd !== dropdownElement) {
                        dd.classList.remove('hd-show'); // Thay đổi class 'show' thành 'hd-show'
                    }
                });
                if (dropdownElement) {
                    dropdownElement.classList.toggle('hd-show'); // Thay đổi class 'show' thành 'hd-show'
                }
            }

            // Hàm xử lý AJAX (mô phỏng)
            async function fetchData(url, type = 'GET', data = null) {
                try {
                    const options = {
                        method: type,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    };
                    if (data) {
                        options.body = JSON.stringify(data);
                    }
                    const response = await fetch(url, options);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return await response.json();
                } catch (error) {
                    console.error('Lỗi khi fetch dữ liệu:', error);
                    return { success: false, message: 'Có lỗi xảy ra khi tải dữ liệu.' };
                }
            }

            // --- Chức năng Thông báo (vẫn giữ nguyên logic) ---
            async function loadNotifications() {
                notificationList.innerHTML = '<li><i class="fas fa-spinner fa-spin"></i> Đang tải...</li>';
                
                try {
                    const response = await fetchData('<?= BASE_URL ?>api/notifications.php?limit=5'); // Gọi API của bạn
                    
                    if (response.success && response.notifications) {
                        notificationList.innerHTML = ''; // Xóa thông báo "Đang tải"
                        if (response.notifications.length > 0) {
                            response.notifications.forEach(notif => {
                                const li = document.createElement('li');
                                let content = `<span>${notif.tieu_de}: ${notif.noi_dung}</span>`;
                                
                                if (notif.link_url) {
                                    content = `<a href="${notif.link_url}">${content}</a>`;
                                }

                                li.innerHTML = content;
                                if (!notif.da_doc) {
                                    li.classList.add('hd-unread'); // Thay đổi class 'unread' thành 'hd-unread'
                                    li.style.fontWeight = 'bold';
                                }
                                
                                li.addEventListener('click', async (event) => {
                                    if (event.target.tagName === 'A' || event.target.closest('a')) {
                                        // Để link hoạt động bình thường
                                    } else {
                                        event.preventDefault();
                                        if (!notif.da_doc) {
                                            const markAsReadResponse = await fetchData('<?= BASE_URL ?>api/notifications.php', 'POST', { 
                                                action: 'mark_as_read', 
                                                notification_id: notif.id 
                                            });
                                            if (markAsReadResponse.success) {
                                                notif.da_doc = true;
                                                li.classList.remove('hd-unread'); // Thay đổi class
                                                li.style.fontWeight = 'normal';
                                                updateNotificationBadge(-1);
                                            } else {
                                                console.error('Không thể đánh dấu thông báo đã đọc:', markAsReadResponse.message);
                                            }
                                        }
                                    }
                                });
                                notificationList.appendChild(li);
                            });
                            notificationBadge.textContent = response.unread_count;
                            notificationBadge.style.display = response.unread_count > 0 ? 'block' : 'none';
                        } else {
                            notificationList.innerHTML = '<li>Không có thông báo mới.</li>';
                            notificationBadge.style.display = 'none';
                        }
                    } else {
                        notificationList.innerHTML = `<li>${response.message || 'Không thể tải thông báo.'}</li>`;
                        notificationBadge.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Lỗi khi tải thông báo:', error);
                    notificationList.innerHTML = '<li>Lỗi khi tải thông báo từ máy chủ.</li>';
                    notificationBadge.style.display = 'none';
                }
            }

            function updateNotificationBadge(change) {
                let currentCount = parseInt(notificationBadge.textContent);
                currentCount += change;
                notificationBadge.textContent = currentCount;
                notificationBadge.style.display = currentCount > 0 ? 'block' : 'none';
            }

            notificationIcon?.addEventListener('click', function(event) {
                event.stopPropagation();
                toggleDropdown(this, notificationDropdown);
                if (notificationDropdown.classList.contains('hd-show')) { // Thay đổi class
                    loadNotifications();
                }
            });

            document.getElementById('viewAllNotifications')?.addEventListener('click', function() {
                window.location.href = '<?= BASE_URL ?>admin/notifications';
            });

            // --- Chức năng Chat (vẫn giữ nguyên logic, chỉ đổi class) ---
            async function loadChats() {
                chatList.innerHTML = '<li><i class="fas fa-spinner fa-spin"></i> Đang tải...</li>';
                
                try {
                    const data = await fetchData('<?= BASE_URL ?>api/chat/conversations'); // Thay bằng API của bạn
                    
                    if (data.success && data.conversations) {
                        chatList.innerHTML = '';
                        if (data.conversations.length > 0) {
                            let unreadChatCount = 0;
                            data.conversations.forEach(chat => {
                                if (chat.has_unread_messages) {
                                    unreadChatCount++;
                                }
                                const li = document.createElement('li');
                                li.innerHTML = `<span>**${chat.participant_name}**: ${chat.last_message_preview}</span>`;
                                if (chat.has_unread_messages) {
                                    li.classList.add('hd-unread'); // Thay đổi class
                                    li.style.fontWeight = 'bold';
                                }
                                li.addEventListener('click', () => {
                                    console.log('Mở cuộc trò chuyện với:', chat.participant_name);
                                    alert('Mở chat với ' + chat.participant_name);
                                });
                                chatList.appendChild(li);
                            });
                            chatBadge.textContent = unreadChatCount;
                            chatBadge.style.display = unreadChatCount > 0 ? 'block' : 'none';
                        } else {
                            chatList.innerHTML = '<li>Không có tin nhắn mới.</li>';
                            chatBadge.style.display = 'none';
                        }
                    } else {
                        chatList.innerHTML = '<li>Không thể tải tin nhắn.</li>';
                        chatBadge.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Lỗi khi tải tin nhắn:', error);
                    chatList.innerHTML = '<li>Lỗi khi tải tin nhắn từ máy chủ.</li>';
                    chatBadge.style.display = 'none';
                }
            }

            function updateChatBadge(change) {
                let currentCount = parseInt(chatBadge.textContent);
                currentCount += change;
                chatBadge.textContent = currentCount;
                chatBadge.style.display = currentCount > 0 ? 'block' : 'none';
            }

            chatIcon?.addEventListener('click', function(event) {
                event.stopPropagation();
                toggleDropdown(this, chatDropdown);
                if (chatDropdown.classList.contains('hd-show')) { // Thay đổi class
                    loadChats();
                }
            });

            document.getElementById('viewAllChats')?.addEventListener('click', function() {
                window.location.href = '<?= BASE_URL ?>admin/chat';
            });

            // --- Chức năng Cài đặt (vẫn giữ nguyên logic) ---
            document.getElementById('manageAccountLink')?.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Chuyển đến trang Quản lý tài khoản.');
                window.location.href = '<?= BASE_URL ?>account/index';
            });
            document.getElementById('themeSettingsLink')?.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Mở modal Tùy chỉnh giao diện.');
            });
            document.getElementById('securitySettingsLink')?.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Chuyển đến trang Bảo mật & Quyền.');
            });
            document.getElementById('languageSettingsLink')?.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Mở modal chọn Ngôn ngữ.');
            });

            settingsIcon?.addEventListener('click', function(event) {
                event.stopPropagation();
                toggleDropdown(this, settingsDropdown);
            });

            // --- Chức năng User Profile (Dropdown cho Profile) ---
            userProfileContainer?.addEventListener('click', function(event) {
                event.stopPropagation();
                toggleDropdown(this, userProfileDropdown);
            });

            // --- Đóng tất cả dropdown khi click ra ngoài ---
            document.addEventListener('click', function(event) {
                const dropdowns = [
                    { icon: notificationIcon, dropdown: notificationDropdown },
                    { icon: chatIcon, dropdown: chatDropdown },
                    { icon: settingsIcon, dropdown: settingsDropdown },
                    { icon: userProfileContainer, dropdown: userProfileDropdown }
                ];

                dropdowns.forEach(item => {
                    // Kiểm tra xem sự kiện click có nằm trong icon hoặc dropdown không
                    // Nếu không, đóng dropdown
                    if (item.icon && item.dropdown && !item.icon.contains(event.target) && !item.dropdown.contains(event.target)) {
                        item.dropdown.classList.remove('hd-show'); // Thay đổi class
                    }
                });
            });

            // Có thể bỏ dòng này nếu bạn muốn tải thông báo/chat chỉ khi người dùng click
            // loadNotifications();
            // loadChats();
        });
    </script>
</body>
</html>
