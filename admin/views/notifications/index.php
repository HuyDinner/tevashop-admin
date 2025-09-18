<?php
// admin/views/notifications/index.php

// Các biến $pageTitle, $pageIcon đã được định nghĩa trong NotificationController::index()
// Các hằng số BASE_URL, CSS_PATH, JS_PATH đã được định nghĩa trong admin/index.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= CSS_PATH ?>styles.css"> <style>
        /* Đảm bảo các biến CSS đã được định nghĩa trong styles.css hoặc tại đây */
        :root {
            --primary-bg: #1c1a27;
            --card-bg: #2a283a;
            --sidebar-bg-dark: #22202f;
            --purple-main: #8a2be2;
            --teva-teal-bg: #00bcd4;
            --text-light: #f0f0f0;
            --text-dim: #b0b0b0;
            --border-color: #3b394b;
            --input-bg: #22202f;
            --primary-color: #4a90e2;
            --primary-color-dark: #357abd;
            --btn-danger-color: #dc3545;
            --btn-danger-dark: #bd2130;
            --hover-bg: #3a384b;
            --primary-bg-light: #252331; /* Màu nền cho hàng chưa đọc */
        }
        body { margin: 0; font-family: sans-serif; background-color: var(--primary-bg); }

        .notification-management {
            padding: 20px;
            background-color: var(--primary-bg);
            color: var(--text-light);
            min-height: calc(100vh - 70px); /* Adjust based on your header height */
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .notification-header h1 {
            font-size: 28px;
            color: var(--teva-teal-bg);
            margin: 0;
        }
        .create-notification-btn {
            background-color: var(--purple-main);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .create-notification-btn:hover {
            background-color: var(--teva-teal-bg);
        }
        .notification-filters {
            display: flex;
            flex-wrap: wrap; /* Cho phép xuống dòng trên màn hình nhỏ */
            gap: 15px;
            margin-bottom: 20px;
            background-color: var(--card-bg);
            padding: 15px;
            border-radius: 8px;
        }
        .notification-filters select,
        .notification-filters input[type="text"] {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            background-color: var(--input-bg);
            color: var(--text-light);
            font-size: 14px;
            min-width: 150px; /* Đảm bảo kích thước tối thiểu */
        }
        .notification-filters button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .notification-filters button:hover {
            background-color: var(--primary-color-dark);
        }
        .notification-filters button#markAllReadBtn {
            background-color: var(--purple-main);
        }
        .notification-filters button#markAllReadBtn:hover {
            background-color: var(--teva-teal-bg);
        }

        .notification-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .notification-table th,
        .notification-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-light);
        }
        .notification-table th {
            background-color: var(--sidebar-bg-dark);
            font-weight: 600;
            color: var(--teva-teal-bg);
        }
        .notification-table tr:last-child td {
            border-bottom: none;
        }
        .notification-table tr.unread-row {
            font-weight: bold;
            background-color: var(--primary-bg-light);
        }
        .notification-table tr:hover:not(.unread-row) { /* Tránh đổi màu quá khác biệt cho hàng chưa đọc */
            background-color: var(--hover-bg);
        }
        .notification-actions button {
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            transition: color 0.2s ease;
        }
        .notification-actions button:hover {
            color: var(--teva-teal-bg);
        }
        .notification-actions button.delete-btn {
            color: var(--btn-danger-color);
        }
        .notification-actions button.delete-btn:hover {
            color: var(--btn-danger-dark);
        }
        .no-notifications {
            text-align: center;
            padding: 50px;
            color: var(--text-dim);
            font-size: 18px;
        }
        /* Pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        .pagination a, .pagination span {
            padding: 8px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            text-decoration: none;
            color: var(--text-light);
            background-color: var(--card-bg);
            transition: background-color 0.2s;
        }
        .pagination a:hover {
            background-color: var(--hover-bg);
        }
        .pagination span.current {
            background-color: var(--teva-teal-bg);
            color: white;
            border-color: var(--teva-teal-bg);
            cursor: default;
        }

        /* Modal for Create/Edit Notification - Kế thừa từ CSS đã có nếu bạn đã định nghĩa cho hd-modal-overlay */
        .hd-modal-overlay { 
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .hd-modal-content {
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: 10px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            color: var(--text-light);
            position: relative;
        }
        .hd-modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-dim);
        }
        .hd-modal-close:hover {
            color: var(--teva-teal-bg);
        }
        .hd-modal-content h2 {
            margin-top: 0;
            color: var(--teva-teal-bg);
            margin-bottom: 20px;
            text-align: center;
        }
        .hd-modal-content label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .hd-modal-content input[type="text"],
        .hd-modal-content input[type="number"],
        .hd-modal-content textarea,
        .hd-modal-content select {
            width: calc(100% - 24px); /* Account for padding */
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background-color: var(--input-bg);
            color: var(--text-light);
            font-size: 14px;
            box-sizing: border-box; 
        }
        .hd-modal-content textarea {
            resize: vertical;
            min-height: 80px;
        }
        .hd-modal-content button[type="submit"] {
            background-color: var(--purple-main);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            float: right; 
            transition: background-color 0.3s ease;
        }
        .hd-modal-content button[type="submit"]:hover {
            background-color: var(--teva-teal-bg);
        }
    </style>
</head>
<body>
  <div class="dashboard-container">
        <?php include VIEWS_PATH . 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include VIEWS_PATH . 'includes/header.php'; ?>
        <div class="notification-management">
            <div class="notification-header">
                <h1><i class="fas fa-bell"></i> Quản lý Thông báo</h1>
                <button class="create-notification-btn" id="openCreateNotificationModal"><i class="fas fa-plus-circle"></i> Tạo thông báo mới</button>
            </div>

            <div class="notification-filters">
                <input type="text" id="filterSearch" placeholder="Tìm kiếm theo tiêu đề/nội dung...">
                <select id="filterStatus">
                    <option value="">Tất cả trạng thái</option>
                    <option value="false">Chưa đọc</option>
                    <option value="true">Đã đọc</option>
                </select>
                <select id="filterType">
                    <option value="">Tất cả loại</option>
                    <option value="general">Chung</option>
                    <option value="order">Đơn hàng</option>
                    <option value="product">Sản phẩm</option>
                    <option value="review">Đánh giá</option>
                    <option value="system">Hệ thống</option>
                </select>
                <button id="applyFiltersBtn"><i class="fas fa-filter"></i> Lọc</button>
                <button id="markAllReadBtn"><i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc</button>
            </div>

            <table class="notification-table" id="notificationTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Nội dung</th>
                        <th>Loại</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="notificationTableBody">
                    <tr><td colspan="7" class="no-notifications">Đang tải thông báo...</td></tr>
                </tbody>
            </table>

            <div class="pagination" id="notificationPagination">
                </div>
        </div>
    </main>

    <div class="hd-modal-overlay" id="createNotificationModal">
        <div class="hd-modal-content">
            <span class="hd-modal-close" id="closeCreateNotificationModal">&times;</span>
            <h2>Tạo Thông báo mới</h2>
            <form id="notificationForm">
                <input type="hidden" id="notificationId" name="id">
                <label for="notificationTitle">Tiêu đề:</label>
                <input type="text" id="notificationTitle" name="tieu_de" required>

                <label for="notificationContent">Nội dung:</label>
                <textarea id="notificationContent" name="noi_dung" required></textarea>

                <label for="notificationType">Loại thông báo:</label>
                <select id="notificationType" name="loai_thong_bao" required>
                    <option value="general">Chung</option>
                    <option value="order">Đơn hàng</option>
                    <option value="product">Sản phẩm</option>
                    <option value="review">Đánh giá</option>
                    <option value="system">Hệ thống</option>
                </select>

                <label for="notificationLink">Link URL (tùy chọn):</label>
                <input type="text" id="notificationLink" name="link_url">
                
                <label for="notificationAdminId">Gửi tới Admin ID (mặc định: admin hiện tại):</label>
                <input type="number" id="notificationAdminId" name="admin_id" value="<?= $_SESSION['admin_id'] ?? '' ?>">
                <small style="color: var(--text-dim);">Để trống để gửi tới admin đang đăng nhập, hoặc nhập ID admin cụ thể.</small>

                <button type="submit">Gửi thông báo</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationTableBody = document.getElementById('notificationTableBody');
            const openCreateNotificationModalBtn = document.getElementById('openCreateNotificationModal');
            const createNotificationModal = document.getElementById('createNotificationModal');
            const closeCreateNotificationModalBtn = document.getElementById('closeCreateNotificationModal');
            const notificationForm = document.getElementById('notificationForm');
            const notificationIdField = document.getElementById('notificationId');
            const notificationTitleField = document.getElementById('notificationTitle');
            const notificationContentField = document.getElementById('notificationContent');
            const notificationTypeField = document.getElementById('notificationType');
            const notificationLinkField = document.getElementById('notificationLink');
            const notificationAdminIdField = document.getElementById('notificationAdminId');

            const filterSearch = document.getElementById('filterSearch');
            const filterStatus = document.getElementById('filterStatus');
            const filterType = document.getElementById('filterType');
            const applyFiltersBtn = document.getElementById('applyFiltersBtn');
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            const notificationPagination = document.getElementById('notificationPagination');

            let currentPage = 1;
            const itemsPerPage = 10; // Số lượng thông báo mỗi trang

            // Hàm xử lý AJAX
            // Sử dụng BASE_URL đã định nghĩa trong PHP
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
                        const errorData = await response.json();
                        throw new Error(`HTTP error! status: ${response.status}, message: ${errorData.message || 'Unknown error'}`);
                    }
                    return await response.json();
                } catch (error) {
                    console.error('Lỗi khi fetch dữ liệu:', error);
                    return { success: false, message: error.message || 'Có lỗi xảy ra khi tải dữ liệu.' };
                }
            }

            // Hàm tải và hiển thị thông báo vào bảng
            async function loadNotificationsTable() {
                notificationTableBody.innerHTML = '<tr><td colspan="7" class="no-notifications"><i class="fas fa-spinner fa-spin"></i> Đang tải thông báo...</td></tr>';
                const searchQuery = filterSearch.value.trim();
                const statusFilter = filterStatus.value;
                const typeFilter = filterType.value;

                let url = `<?= BASE_URL ?>api/notifications.php?action=get_all&page=${currentPage}&limit=${itemsPerPage}`;
                if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;
                if (statusFilter !== '') url += `&is_read=${statusFilter}`; // 'true', 'false', hoặc rỗng
                if (typeFilter) url += `&type=${encodeURIComponent(typeFilter)}`;
                
                const response = await fetchData(url);

                if (response.success && response.notifications) {
                    notificationTableBody.innerHTML = '';
                    if (response.notifications.length > 0) {
                        response.notifications.forEach(notif => {
                            const row = document.createElement('tr');
                            if (!notif.da_doc) {
                                row.classList.add('unread-row');
                            }
                            row.innerHTML = `
                                <td>${notif.id}</td>
                                <td>${notif.tieu_de}</td>
                                <td>${notif.noi_dung.length > 100 ? notif.noi_dung.substring(0, 100) + '...' : notif.noi_dung}</td>
                                <td>${notif.loai_thong_bao}</td>
                                <td>${new Date(notif.ngay_tao).toLocaleString()}</td>
                                <td>${notif.da_doc ? 'Đã đọc' : 'Chưa đọc'}</td>
                                <td class="notification-actions">
                                    <button class="view-btn" data-id="${notif.id}"><i class="fas fa-eye"></i> Xem</button>
                                    <button class="edit-btn" data-id="${notif.id}" 
                                            data-title="${encodeURIComponent(notif.tieu_de)}" 
                                            data-content="${encodeURIComponent(notif.noi_dung)}"
                                            data-type="${notif.loai_thong_bao}"
                                            data-link="${encodeURIComponent(notif.link_url || '')}"
                                            data-admin-id="${notif.admin_id}"><i class="fas fa-edit"></i> Sửa</button>
                                    ${notif.da_doc ? '' : `<button class="mark-read-btn" data-id="${notif.id}"><i class="fas fa-check"></i> Đánh dấu đã đọc</button>`}
                                    <button class="delete-btn" data-id="${notif.id}"><i class="fas fa-trash"></i> Xóa</button>
                                </td>
                            `;
                            notificationTableBody.appendChild(row);
                        });
                        setupPagination(response.total_pages, response.current_page); // Cập nhật phân trang
                    } else {
                        notificationTableBody.innerHTML = '<tr><td colspan="7" class="no-notifications">Không tìm thấy thông báo nào.</td></tr>';
                        notificationPagination.innerHTML = ''; // Xóa phân trang nếu không có dữ liệu
                    }
                } else {
                    notificationTableBody.innerHTML = `<tr><td colspan="7" class="no-notifications">${response.message || 'Không thể tải thông báo từ máy chủ.'}</td></tr>`;
                    notificationPagination.innerHTML = '';
                }
            }

            // Hàm xử lý phân trang
            function setupPagination(totalPages, currentPage) {
                notificationPagination.innerHTML = '';
                if (totalPages <= 1) return;

                const createPageLink = (page, text, isCurrent = false) => {
                    const el = document.createElement(isCurrent ? 'span' : 'a');
                    el.textContent = text;
                    if (!isCurrent) {
                        el.href = '#';
                        el.addEventListener('click', (e) => {
                            e.preventDefault();
                            currentPage = page;
                            loadNotificationsTable();
                        });
                    } else {
                        el.classList.add('current');
                    }
                    return el;
                };

                if (currentPage > 1) {
                    notificationPagination.appendChild(createPageLink(currentPage - 1, 'Trước'));
                }

                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, currentPage + 2);

                if (startPage > 1) {
                    notificationPagination.appendChild(createPageLink(1, '1'));
                    if (startPage > 2) {
                        const ellipsis = document.createElement('span');
                        ellipsis.textContent = '...';
                        notificationPagination.appendChild(ellipsis);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    notificationPagination.appendChild(createPageLink(i, i, i === currentPage));
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        const ellipsis = document.createElement('span');
                        ellipsis.textContent = '...';
                        notificationPagination.appendChild(ellipsis);
                    }
                    notificationPagination.appendChild(createPageLink(totalPages, totalPages));
                }

                if (currentPage < totalPages) {
                    notificationPagination.appendChild(createPageLink(currentPage + 1, 'Sau'));
                }
            }


            // --- Xử lý sự kiện cho các nút hành động trên bảng ---
            notificationTableBody.addEventListener('click', async function(e) {
                const target = e.target;
                const notificationId = target.dataset.id || target.closest('button')?.dataset.id;

                if (!notificationId) return;

                if (target.classList.contains('mark-read-btn') || target.closest('.mark-read-btn')) {
                    const response = await fetchData('<?= BASE_URL ?>api/notifications.php', 'POST', { 
                        action: 'mark_as_read', 
                        notification_id: notificationId 
                    });
                    if (response.success) {
                        loadNotificationsTable(); // Tải lại bảng để cập nhật trạng thái
                        // Cập nhật badge ở header
                        if (typeof updateNotificationBadge === 'function') {
                            updateNotificationBadge(-1); 
                        }
                    } else {
                        alert('Lỗi: ' + (response.message || 'Không thể đánh dấu đã đọc.'));
                    }
                } else if (target.classList.contains('delete-btn') || target.closest('.delete-btn')) {
                    if (confirm('Bạn có chắc chắn muốn xóa thông báo này?')) {
                        const response = await fetchData('<?= BASE_URL ?>api/notifications.php', 'POST', { 
                            action: 'delete', 
                            notification_id: notificationId 
                        });
                        if (response.success) {
                            loadNotificationsTable();
                            // Không cần cập nhật badge đặc biệt ở đây vì API đã đếm lại.
                            // Tuy nhiên, nếu bạn muốn có phản hồi nhanh, có thể gọi loadNotifications()
                            // để cập nhật badge ngay lập tức.
                            if (typeof loadNotifications === 'function') {
                                loadNotifications(); // Tải lại danh sách thông báo cho header
                            }
                        } else {
                            alert('Lỗi: ' + (response.message || 'Không thể xóa thông báo.'));
                        }
                    }
                } else if (target.classList.contains('view-btn') || target.closest('.view-btn')) {
                    const notificationRow = target.closest('tr');
                    const title = notificationRow.children[1].textContent;
                    const content = notificationRow.children[2].textContent; 
                    const type = notificationRow.children[3].textContent;
                    const link = notificationRow.children[4].textContent; // Lấy link từ cột Ngày tạo tạm thời, cần link_url thực tế
                    // Để lấy link_url chính xác, bạn có thể truyền nó vào data-attribute của nút view
                    
                    alert(`Chi tiết Thông báo:\n\nTiêu đề: ${title}\nNội dung: ${content}\nLoại: ${type}\nLink: ${link}`);
                    
                    // Nếu là tin nhắn chưa đọc, đánh dấu đã đọc sau khi xem
                    if (notificationRow.classList.contains('unread-row')) {
                        const response = await fetchData('<?= BASE_URL ?>api/notifications.php', 'POST', { 
                            action: 'mark_as_read', 
                            notification_id: notificationId 
                        });
                        if (response.success) {
                            loadNotificationsTable(); // Cập nhật lại trạng thái
                             if (typeof updateNotificationBadge === 'function') {
                                updateNotificationBadge(-1); 
                            }
                        }
                    }
                } else if (target.classList.contains('edit-btn') || target.closest('.edit-btn')) {
                    // Mở modal để chỉnh sửa thông báo
                    const btn = target.closest('.edit-btn');
                    notificationIdField.value = btn.dataset.id;
                    notificationTitleField.value = decodeURIComponent(btn.dataset.title);
                    notificationContentField.value = decodeURIComponent(btn.dataset.content);
                    notificationTypeField.value = btn.dataset.type;
                    notificationLinkField.value = decodeURIComponent(btn.dataset.link);
                    notificationAdminIdField.value = btn.dataset.adminId; // Hoặc để trống nếu chỉ sửa thông báo của chính mình
                    
                    document.querySelector('#createNotificationModal h2').textContent = 'Chỉnh sửa Thông báo';
                    document.querySelector('#notificationForm button[type="submit"]').textContent = 'Cập nhật Thông báo';
                    createNotificationModal.style.display = 'flex';
                }
            });

            // --- Xử lý sự kiện cho Form tạo/chỉnh sửa thông báo ---
            openCreateNotificationModalBtn?.addEventListener('click', function() {
                createNotificationModal.style.display = 'flex'; // Hiển thị modal
                notificationIdField.value = ''; // Reset form cho tạo mới
                notificationForm.reset();
                notificationAdminIdField.value = '<?= $_SESSION['admin_id'] ?? '' ?>'; // Đặt ID admin hiện tại mặc định
                document.querySelector('#createNotificationModal h2').textContent = 'Tạo Thông báo mới';
                document.querySelector('#notificationForm button[type="submit"]').textContent = 'Gửi thông báo';
            });

            closeCreateNotificationModalBtn?.addEventListener('click', function() {
                createNotificationModal.style.display = 'none'; // Ẩn modal
            });

            createNotificationModal?.addEventListener('click', function(e) {
                if (e.target === createNotificationModal) {
                    createNotificationModal.style.display = 'none';
                }
            });

            notificationForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(notificationForm);
                const notificationData = Object.fromEntries(formData.entries());
                
                // Chuyển admin_id sang kiểu số nếu có và không rỗng
                if (notificationData.admin_id && notificationData.admin_id !== '') {
                    notificationData.admin_id = parseInt(notificationData.admin_id);
                } else {
                    // Nếu không nhập admin_id, backend sẽ tự động gán admin_id hiện tại
                    // Xóa trường admin_id nếu nó rỗng để backend xử lý.
                    delete notificationData.admin_id; 
                }

                const action = notificationData.id ? 'update' : 'create';

                const response = await fetchData('<?= BASE_URL ?>api/notifications.php', 'POST', { 
                    action: action, 
                    ...notificationData 
                });

                if (response.success) {
                    alert('Thông báo đã được ' + (action === 'create' ? 'tạo' : 'cập nhật') + ' thành công!');
                    createNotificationModal.style.display = 'none';
                    loadNotificationsTable(); // Tải lại bảng
                    // Tải lại thông báo cho header để cập nhật badge (nếu là thông báo cho admin hiện tại)
                    if (typeof loadNotifications === 'function') {
                        loadNotifications(); 
                    }
                } else {
                    alert('Lỗi: ' + (response.message || 'Không thể ' + (action === 'create' ? 'tạo' : 'cập nhật') + ' thông báo.'));
                }
            });

            // --- Xử lý lọc và tìm kiếm ---
            applyFiltersBtn?.addEventListener('click', function() {
                currentPage = 1; // Reset về trang đầu tiên khi lọc
                loadNotificationsTable();
            });

            // --- Đánh dấu tất cả đã đọc ---
            markAllReadBtn?.addEventListener('click', async function() {
                if (confirm('Bạn có chắc chắn muốn đánh dấu TẤT CẢ thông báo chưa đọc là đã đọc không?')) {
                    const response = await fetchData('<?= BASE_URL ?>api/notifications.php', 'POST', { 
                        action: 'mark_all_as_read' 
                    });
                    if (response.success) {
                        alert('Tất cả thông báo đã được đánh dấu là đã đọc.');
                        loadNotificationsTable(); // Cập nhật lại bảng
                        // Cập nhật badge ở header
                         if (typeof loadNotifications === 'function') {
                            loadNotifications(); // Tải lại thông báo để badge cập nhật
                        }
                    } else {
                        alert('Lỗi: ' + (response.message || 'Không thể đánh dấu tất cả đã đọc.'));
                    }
                }
            });

            // Tải thông báo khi trang quản lý tải xong
            loadNotificationsTable();
        });
    </script>
</body>
</html>
