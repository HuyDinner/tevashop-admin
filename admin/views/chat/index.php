<?php
// Đây là nơi bắt đầu hiển thị HTML của trang Chat
// Đảm bảo bạn đã nhúng header và sidebar ở Controller
// require_once VIEWS_PATH . 'layout/header.php';
// require_once VIEWS_PATH . 'layout/sidebar.php';
?>
<div class="dashboard-container">
    <?php include VIEWS_PATH . 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include VIEWS_PATH . 'includes/header.php'; ?>

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Hỗ trợ khách hàng qua Chat</h4>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-primary btn-sm" id="tevachat-refreshChatList">
                                        <i class="ri-refresh-line align-bottom"></i> Tải lại
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" id="tevachat-toggleChatPanel">
                                        <i class="ri-chat-1-line align-bottom"></i> Mở/Đóng Chat Panel
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tevachat-live-chat-wrapper">
                                    <div class="tevachat-chat-list-panel">
                                        <div class="p-3 tevachat-chat-list-header">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Tìm kiếm cuộc trò chuyện..." id="tevachat-chatSearchInput">
                                                <button class="btn btn-outline-secondary" type="button" id="tevachat-chatSearchBtn">
                                                    <i class="ri-search-line"></i>
                                                </button>
                                            </div>
                                            <div class="mt-2">
                                                <select class="form-select" id="tevachat-chatStatusFilter">
                                                    <option value="tat_ca">Tất cả</option>
                                                    <option value="cho_xu_ly">Chờ xử lý</option>
                                                    <option value="mo">Đang mở</option>
                                                    <option value="dong">Đã đóng</option>
                                                </select>
                                            </div>
                                        </div>
                                        <ul class="list-group list-group-flush tevachat-chat-list-items" id="tevachat-chatList">
                                        </ul>
                                        <div class="tevachat-chat-list-pagination p-2 text-center" id="tevachat-chatPagination">
                                        </div>
                                    </div>
                                    <div class="tevachat-chat-conversation-panel d-none">
                                        <div class="p-3 tevachat-chat-conversation-header border-bottom">
                                            <h5 class="mb-0" id="tevachat-currentChatCustomerName">Chọn một cuộc trò chuyện</h5>
                                            <span class="text-muted" id="tevachat-currentChatStatus"></span>
                                            <div class="btn-group float-end">
                                                <button type="button" class="btn btn-sm btn-outline-success" id="tevachat-assignChatBtn">Nhận Chat</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" id="tevachat-closeChatBtn">Đóng Chat</button>
                                            </div>
                                        </div>
                                        <div class="tevachat-chat-messages-container p-3" id="tevachat-chatMessagesContainer">
                                        </div>
                                        <div class="tevachat-chat-message-input p-3 border-top">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Gửi tin nhắn..." id="tevachat-messageInput">
                                                <button class="btn btn-primary" type="button" id="tevachat-sendMessageBtn">Gửi</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<style>
    .tevachat-live-chat-wrapper {
        display: flex;
        height: 70vh; /* Chiều cao tổng thể của khung chat */
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }
    .tevachat-chat-list-panel {
        width: 300px;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
    }
    .tevachat-chat-list-header {
        flex-shrink: 0;
        border-bottom: 1px solid #e0e0e0;
    }
    .tevachat-chat-list-items {
        flex-grow: 1;
        overflow-y: auto;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .tevachat-chat-list-items .list-group-item {
        cursor: pointer;
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    .tevachat-chat-list-items .list-group-item.active {
        background-color: #e6f7ff; /* Màu nhấn khi chọn */
        font-weight: bold;
    }
    .tevachat-chat-list-item-unread-count {
        float: right;
        background-color: #ff4d4f;
        color: white;
        border-radius: 50%;
        padding: 2px 7px;
        font-size: 0.75em;
    }
    .tevachat-chat-list-pagination {
        flex-shrink: 0;
        border-top: 1px solid #e0e0e0;
    }
    .tevachat-chat-conversation-panel {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .tevachat-chat-conversation-header {
        flex-shrink: 0;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .tevachat-chat-messages-container {
        flex-grow: 1;
        overflow-y: auto;
        background-color: #f5f7f9;
        padding: 15px;
        display: flex;
        flex-direction: column;
    }
    .tevachat-chat-message-input {
        flex-shrink: 0;
        background-color: #f8f9fa;
    }
    .tevachat-message-bubble {
        max-width: 70%;
        padding: 8px 12px;
        border-radius: 15px;
        margin-bottom: 10px;
        word-wrap: break-word;
    }
    .tevachat-message-bubble.admin {
        background-color: #d1e7dd; /* Màu xanh nhạt cho admin */
        align-self: flex-end;
        margin-left: auto;
        text-align: right;
    }
    .tevachat-message-bubble.customer {
        background-color: #e0f2f7; /* Màu xanh lam nhạt cho khách hàng */
        align-self: flex-start;
        margin-right: auto;
        text-align: left;
    }
    .tevachat-message-info {
        font-size: 0.75em;
        color: #6c757d;
        margin-top: 5px;
    }
</style>

<script>
    // Định nghĩa URL cho API Chat của bạn
    const CHAT_API_URL = '<?= BASE_URL ?>api/chat_api.php'; // Đảm bảo BASE_URL đúng

    let currentChatId = null;
    let currentPage = 1;
    let itemsPerPage = 10;
    let currentSearchQuery = '';
    let currentStatusFilter = 'tat_ca';

    // Hàm chung để gửi yêu cầu API
    async function fetchData(url, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'Accept': 'application/json'
            }
        };

        if (data) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                const errorText = await response.text();
                console.error('HTTP error response:', errorText);
                try {
                    const errorData = JSON.parse(errorText);
                    throw new Error(`Lỗi HTTP! Trạng thái: ${response.status}, Thông báo: ${errorData.message || 'Lỗi không xác định'}`);
                } catch (jsonError) {
                    throw new Error(`Lỗi HTTP! Trạng thái: ${response.status}, Phản hồi thô: ${errorText}`);
                }
            }
            return await response.json();
        } catch (error) {
            console.error('Lỗi khi fetch dữ liệu:', error);
            // Hiển thị thông báo lỗi cho người dùng
            alert('Có lỗi xảy ra: ' + error.message);
            return {
                success: false,
                message: error.message || 'Có lỗi xảy ra khi tải dữ liệu.'
            };
        }
    }

    // Hàm tải danh sách cuộc trò chuyện
    async function loadChatList() {
        const chatListElement = document.getElementById('tevachat-chatList'); // Đã đổi ID
        chatListElement.innerHTML = '<li class="list-group-item text-center">Đang tải cuộc trò chuyện...</li>';

        const url = `${CHAT_API_URL}?action=get_all_chats&page=${currentPage}&limit=${itemsPerPage}&search=${encodeURIComponent(currentSearchQuery)}&status=${encodeURIComponent(currentStatusFilter)}`;
        const result = await fetchData(url);

        chatListElement.innerHTML = ''; // Xóa nội dung tải
        if (result.success && result.data && result.data.cuoc_tro_chuyen.length > 0) {
            result.data.cuoc_tro_chuyen.forEach(chat => {
                const listItem = document.createElement('li');
                listItem.className = `list-group-item ${chat.id_cuoc_tro_chuyen === currentChatId ? 'active' : ''}`;
                listItem.dataset.chatId = chat.id_cuoc_tro_chuyen;
                listItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6>${chat.ten_khach_hang} <span class="badge bg-secondary">${chat.trang_thai}</span></h6>
                            <p class="mb-1 text-truncate" style="max-width: 200px;">${chat.noi_dung_tin_nhan_cuoi || 'Chưa có tin nhắn nào'}</p>
                            <small class="text-muted">${new Date(chat.thoi_gian_cap_nhat).toLocaleString()}</small>
                        </div>
                        ${chat.so_tin_nhan_khach_hang_chua_doc > 0 ? `<span class="tevachat-chat-list-item-unread-count">${chat.so_tin_nhan_khach_hang_chua_doc}</span>` : ''}
                    </div>
                `;
                listItem.addEventListener('click', () => openChat(chat.id_cuoc_tro_chuyen, chat.ten_khach_hang, chat.trang_thai));
                chatListElement.appendChild(listItem);
            });
            renderPagination(result.data.total_pages, result.data.current_page);
        } else {
            chatListElement.innerHTML = '<li class="list-group-item text-center">Không tìm thấy cuộc trò chuyện nào.</li>';
            document.getElementById('tevachat-chatPagination').innerHTML = ''; // Đã đổi ID
        }
    }

    // Hàm render phân trang
    function renderPagination(totalPages, currentPage) {
        const paginationContainer = document.getElementById('tevachat-chatPagination'); // Đã đổi ID
        paginationContainer.innerHTML = '';

        let paginationHtml = `<ul class="pagination pagination-sm justify-content-center mb-0">`;
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        paginationHtml += `</ul>`;
        paginationContainer.innerHTML = paginationHtml;

        paginationContainer.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = parseInt(e.target.dataset.page);
                loadChatList();
            });
        });
    }

    // Hàm mở một cuộc trò chuyện cụ thể
    async function openChat(chatId, customerName, chatStatus) {
        currentChatId = chatId;
        document.getElementById('tevachat-currentChatCustomerName').innerText = customerName; // Đã đổi ID
        document.getElementById('tevachat-currentChatStatus').innerText = `Trạng thái: ${chatStatus}`; // Đã đổi ID

        // Cập nhật trạng thái active cho item trong danh sách
        document.querySelectorAll('#tevachat-chatList .list-group-item').forEach(item => { // Đã đổi ID
            item.classList.remove('active');
            if (parseInt(item.dataset.chatId) === chatId) {
                item.classList.add('active');
            }
        });

        const messagesContainer = document.getElementById('tevachat-chatMessagesContainer'); // Đã đổi ID
        messagesContainer.innerHTML = '<div class="text-center">Đang tải tin nhắn...</div>';
        document.querySelector('.tevachat-chat-conversation-panel').classList.remove('d-none'); // Đã đổi class

        const result = await fetchData(`${CHAT_API_URL}?action=get_messages&chat_id=${chatId}`);
        messagesContainer.innerHTML = ''; // Xóa nội dung tải

        if (result.success && result.data && result.data.length > 0) {
            result.data.forEach(message => {
                const bubble = document.createElement('div');
                bubble.className = `tevachat-message-bubble ${message.loai_nguoi_gui}`; // Đã đổi class
                bubble.innerHTML = `
                    <p class="mb-0">${message.noi_dung}</p>
                    <div class="tevachat-message-info"> // Đã đổi class
                        ${message.loai_nguoi_gui === 'admin' ? 'Bạn' : message.ten_nguoi_gui} lúc ${new Date(message.thoi_gian_gui).toLocaleTimeString()}
                    </div>
                `;
                messagesContainer.appendChild(bubble);
            });
            messagesContainer.scrollTop = messagesContainer.scrollHeight; // Cuộn xuống cuối
            loadChatList(); // Tải lại danh sách chat để cập nhật số tin nhắn chưa đọc
        } else {
            messagesContainer.innerHTML = '<div class="text-center text-muted">Chưa có tin nhắn nào trong cuộc trò chuyện này.</div>';
        }

        // Cập nhật trạng thái nút
        const assignBtn = document.getElementById('tevachat-assignChatBtn'); // Đã đổi ID
        const closeBtn = document.getElementById('tevachat-closeChatBtn'); // Đã đổi ID
        const messageInput = document.getElementById('tevachat-messageInput'); // Đã đổi ID
        const sendMessageBtn = document.getElementById('tevachat-sendMessageBtn'); // Đã đổi ID

        if (chatStatus === 'dong') {
            assignBtn.style.display = 'none';
            closeBtn.style.display = 'none';
            messageInput.disabled = true;
            sendMessageBtn.disabled = true;
        } else {
            assignBtn.style.display = 'inline-block';
            closeBtn.style.display = 'inline-block';
            messageInput.disabled = false;
            sendMessageBtn.disabled = false;
            if (chatStatus === 'mo') {
                assignBtn.style.display = 'none'; // Đã mở rồi thì không cần nút nhận nữa
            }
        }
    }

    // Hàm gửi tin nhắn
    async function sendMessage() {
        if (!currentChatId) {
            alert('Vui lòng chọn một cuộc trò chuyện để gửi tin nhắn.');
            return;
        }
        const messageInput = document.getElementById('tevachat-messageInput'); // Đã đổi ID
        const messageContent = messageInput.value.trim();

        if (messageContent === '') {
            alert('Nội dung tin nhắn không được trống.');
            return;
        }

        const result = await fetchData(CHAT_API_URL, 'POST', {
            action: 'send_message',
            id_cuoc_tro_chuyen: currentChatId,
            noi_dung: messageContent
        });

        if (result.success) {
            messageInput.value = ''; // Xóa nội dung input
            openChat(currentChatId, document.getElementById('tevachat-currentChatCustomerName').innerText, document.getElementById('tevachat-currentChatStatus').innerText); // Đã đổi ID
        } else {
            alert(result.message);
        }
    }

    // Hàm nhận chat
    async function assignChat() {
        if (!currentChatId) {
            alert('Vui lòng chọn một cuộc trò chuyện để nhận.');
            return;
        }
        if (!confirm('Bạn có chắc chắn muốn nhận cuộc trò chuyện này?')) {
            return;
        }

        const result = await fetchData(CHAT_API_URL, 'POST', {
            action: 'assign_chat',
            id_cuoc_tro_chuyen: currentChatId
        });

        if (result.success) {
            alert(result.message);
            loadChatList(); // Tải lại danh sách
            openChat(currentChatId, document.getElementById('tevachat-currentChatCustomerName').innerText, 'mo'); // Đã đổi ID
        } else {
            alert(result.message);
        }
    }

    // Hàm đóng chat
    async function closeChat() {
        if (!currentChatId) {
            alert('Vui lòng chọn một cuộc trò chuyện để đóng.');
            return;
        }
        if (!confirm('Bạn có chắc chắn muốn đóng cuộc trò chuyện này?')) {
            return;
        }

        const result = await fetchData(CHAT_API_URL, 'POST', {
            action: 'close_chat',
            id_cuoc_tro_chuyen: currentChatId
        });

        if (result.success) {
            alert(result.message);
            loadChatList(); // Tải lại danh sách
            openChat(currentChatId, document.getElementById('tevachat-currentChatCustomerName').innerText, 'dong'); // Đã đổi ID
        } else {
            alert(result.message);
        }
    }

    // Sự kiện khi DOM đã tải xong
    document.addEventListener('DOMContentLoaded', () => {
        loadChatList(); // Tải danh sách chat ban đầu

        // Sự kiện tìm kiếm
        document.getElementById('tevachat-chatSearchBtn').addEventListener('click', () => { // Đã đổi ID
            currentSearchQuery = document.getElementById('tevachat-chatSearchInput').value; // Đã đổi ID
            currentPage = 1; // Reset về trang 1 khi tìm kiếm mới
            loadChatList();
        });
        document.getElementById('tevachat-chatSearchInput').addEventListener('keypress', (e) => { // Đã đổi ID
            if (e.key === 'Enter') {
                document.getElementById('tevachat-chatSearchBtn').click(); // Đã đổi ID
            }
        });

        // Sự kiện lọc theo trạng thái
        document.getElementById('tevachat-chatStatusFilter').addEventListener('change', () => { // Đã đổi ID
            currentStatusFilter = document.getElementById('tevachat-chatStatusFilter').value; // Đã đổi ID
            currentPage = 1; // Reset về trang 1 khi lọc mới
            loadChatList();
        });

        // Sự kiện gửi tin nhắn
        document.getElementById('tevachat-sendMessageBtn').addEventListener('click', sendMessage); // Đã đổi ID
        document.getElementById('tevachat-messageInput').addEventListener('keypress', (e) => { // Đã đổi ID
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Sự kiện refresh list
        document.getElementById('tevachat-refreshChatList').addEventListener('click', loadChatList); // Đã đổi ID

        // Sự kiện nhận chat
        document.getElementById('tevachat-assignChatBtn').addEventListener('click', assignChat); // Đã đổi ID

        // Sự kiện đóng chat
        document.getElementById('tevachat-closeChatBtn').addEventListener('click', closeChat); // Đã đổi ID

        // Sự kiện ẩn/hiện chat panel
        document.getElementById('tevachat-toggleChatPanel').addEventListener('click', () => { // Đã đổi ID
            const chatListPanel = document.querySelector('.tevachat-chat-list-panel'); // Đã đổi class
            const chatConversationPanel = document.querySelector('.tevachat-chat-conversation-panel'); // Đã đổi class

            if (chatListPanel.style.width === '100%') { // Nếu đang full list, thì ẩn list và hiện chat conversation
                chatListPanel.style.width = '300px';
                chatConversationPanel.classList.remove('d-none');
            } else if (chatConversationPanel.classList.contains('d-none')) { // Nếu đang ẩn chat conversation
                chatListPanel.style.width = '100%'; // Mở rộng danh sách
            } else { // Đang hiển thị cả 2, thì ẩn panel chat và mở rộng list
                chatListPanel.style.width = '100%';
                chatConversationPanel.classList.add('d-none');
            }
        });

        // Tải tổng số tin nhắn chưa đọc lên biểu tượng chat (ví dụ, ở sidebar hoặc header)
        // Nếu bạn có một element để hiển thị số lượng tin nhắn chưa đọc tổng thể
        async function loadTotalUnreadMessages() {
            const result = await fetchData(`${CHAT_API_URL}?action=get_total_unread_customer_messages`);
            if (result.success) {
                // Ví dụ: Cập nhật một span có id="unreadChatCount"
                const unreadChatCountElement = document.getElementById('tevachat-unreadChatCount'); // Đã đổi ID
                if (unreadChatCountElement) {
                    unreadChatCountElement.innerText = result.total_unread_messages;
                    unreadChatCountElement.style.display = result.total_unread_messages > 0 ? 'inline-block' : 'none';
                }
            }
        }
        loadTotalUnreadMessages();
        // Tùy chọn: Tải lại số lượng chưa đọc mỗi X giây để có thông báo thời gian thực
        setInterval(loadTotalUnreadMessages, 15000); // Mỗi 15 giây
    });
</script>

<?php
// Đây là nơi kết thúc HTML của trang Chat
// require_once VIEWS_PATH . 'layout/footer.php';
?>
