<?php
// tevashop-admin/admin/models/ChatModel.php

class ChatModel {
    private $conn;
    private $tableChats = 'cuoctrochuyen'; // Tên bảng cuộc trò chuyện của bạn
    private $tableMessages = 'tinnhan';
    private $tableCustomers = 'khachhang'; // Tên bảng khách hàng của bạn
    private $tableAdmins = 'admin_users'; // Tên bảng admin của bạn

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Lấy danh sách tất cả các cuộc trò chuyện.
     * @param int $adminId ID của admin hiện tại (để lọc hoặc kiểm tra quyền)
     * @param int $page Trang hiện tại
     * @param int $limit Số lượng bản ghi mỗi trang
     * @param string $search Từ khóa tìm kiếm
     * @param string $statusFilter Lọc theo trạng thái ('mo', 'dong', 'cho_xu_ly', 'tat_ca')
     * @return array Danh sách cuộc trò chuyện và thông tin phân trang
     */
    public function getAllChats($adminId, $page = 1, $limit = 10, $search = '', $statusFilter = 'tat_ca') {
        $offset = ($page - 1) * $limit;
        
        $query = "
            SELECT 
                ctc.IDCuocTroChuyen, ctc.IDKhachHang, ctc.IDAdmin, ctc.TrangThai, ctc.ThoiGianCapNhat, ctc.ThoiGianTao, ctc.NoiDungTinNhanCuoi,
                kh.TenKhachHang AS TenKhachHang, kh.Email AS Email,
                adm.username AS TenAdmin,
                (SELECT COUNT(tn.IDTinNhan) FROM " . $this->tableMessages . " tn WHERE tn.IDCuocTroChuyen = ctc.IDCuocTroChuyen AND tn.LoaiNguoiGui = 'khach_hang' AND tn.DaDoc = FALSE) AS so_tin_nhan_khach_hang_chua_doc
            FROM " . $this->tableChats . " ctc
            JOIN " . $this->tableCustomers . " kh ON ctc.IDKhachHang = kh.id
            LEFT JOIN " . $this->tableAdmins . " adm ON ctc.IDAdmin = adm.id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($search)) {
            $query .= " AND (kh.TenKhachHang LIKE :search OR kh.Email LIKE :search OR ctc.NoiDungTinNhanCuoi LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($statusFilter !== 'tat_ca') {
            $query .= " AND ctc.TrangThai = :statusFilter";
            $params[':statusFilter'] = $statusFilter;
        }
        
        // Bạn có thể thêm điều kiện để lọc theo adminId nếu muốn mỗi admin chỉ xem chat của mình
        // Ví dụ: $query .= " AND (c.id_admin = :adminId OR c.trang_thai = 'cho_xu_ly')";
        // $params[':adminId'] = $adminId;


        $countQuery = "SELECT COUNT(*) FROM (" . $query . ") AS subquery";
        $stmt = $this->conn->prepare($countQuery);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->execute();
        $totalChats = $stmt->fetchColumn();

        $query .= " ORDER BY ctc.ThoiGianCapNhat DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'cuoc_tro_chuyen' => $chats,
            'tong_so_cuoc_tro_chuyen' => $totalChats,
            'tong_so_trang' => ceil($totalChats / $limit),
            'trang_hien_tai' => $page
        ];
    }

    /**
     * Lấy các tin nhắn của một cuộc trò chuyện cụ thể.
     * @param int $chatId ID cuộc trò chuyện
     * @param int $adminId ID của admin hiện tại (để kiểm tra quyền truy cập)
     * @return array Danh sách tin nhắn
     */
    public function getMessagesByChatId($chatId, $adminId) {
        // Kiểm tra xem adminId có quyền truy cập chat này không (optional but recommended)
        $chatInfo = $this->getChatById($chatId);
        if (!$chatInfo || ($chatInfo['id_admin'] !== null && $chatInfo['id_admin'] != $adminId)) {
            // Admin không phải là người phụ trách chat này hoặc chat không tồn tại
            // Tùy thuộc vào chính sách, có thể cho phép admin khác xem nhưng không can thiệp.
            // Ở đây, tôi sẽ không cấm mà chỉ kiểm tra để bạn biết chỗ này.
        }

        $query = "
            SELECT 
                tn.IDTinNhan, tn.IDCuocTroChuyen, tn.LoaiNguoiGui, tn.IDNguoiGui, tn.NoiDung, tn.DaDoc, tn.ThoiGianGui,
                COALESCE(cust.name, adm.username) AS ten_nguoi_gui
            FROM " . $this->tableMessages . " tn
            LEFT JOIN " . $this->tableCustomers . " cust ON tn.LoaiNguoiGui = 'khach_hang' AND tn.IDNguoiGui = cust.customer_id
            LEFT JOIN " . $this->tableAdmins . " adm ON tn.LoaiNguoiGui = 'admin' AND tn.IDNguoiGui = adm.admin_id
            WHERE tn.IDCuocTroChuyen = :id_cuoc_tro_chuyen
            ORDER BY tn.ThoiGianGui ASC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cuoc_tro_chuyen', $chatId, PDO::PARAM_INT);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đánh dấu tin nhắn của khách hàng là đã đọc khi admin xem cuộc trò chuyện này
        $this->markCustomerMessagesAsRead($chatId);

        return $messages;
    }

    /**
     * Gửi tin nhắn mới từ admin.
     * @param int $chatId ID cuộc trò chuyện
     * @param int $adminId ID của admin gửi
     * @param string $messageContent Nội dung tin nhắn
     * @return bool True nếu gửi thành công, False nếu thất bại
     */
    public function sendMessage($chatId, $adminId, $messageContent) {
        $this->conn->beginTransaction();
        try {
            // Thêm tin nhắn
            $query = "INSERT INTO " . $this->tableMessages . " (IDCuocTroChuyen, LoaiNguoiGui, IDNguoiGui, NoiDung, DaDoc) VALUES (:IDCuocTroChuyen, 'admin', :IDNguoiGui, :NoiDung, TRUE)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':IDCuocTroChuyen', $chatId, PDO::PARAM_INT);
            $stmt->bindParam(':IDNguoiGui', $adminId, PDO::PARAM_INT);
            $stmt->bindParam(':NoiDung', $messageContent, PDO::PARAM_STR);
            $stmt->execute();

            // Cập nhật thoi_gian_cap_nhat và noi_dung_tin_nhan_cuoi trong bảng cuoc_tro_chuyen
            $query = "UPDATE " . $this->tableChats . " SET ThoiGianCapNhat = CURRENT_TIMESTAMP, NoiDungTinNhanCuoi = :NoiDung WHERE IDCuocTroChuyen = :IDCuocTroChuyen";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':NoiDung', $messageContent, PDO::PARAM_STR);
            $stmt->bindParam(':IDCuocTroChuyen', $chatId, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Lỗi khi admin gửi tin nhắn: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo một cuộc trò chuyện mới nếu chưa có, hoặc trả về id_cuoc_tro_chuyen nếu đã tồn tại và đang mở.
     * Phương thức này thường được gọi từ phía khách hàng khi họ gửi tin nhắn đầu tiên.
     * Admin cũng có thể dùng nếu muốn chủ động tạo chat với khách hàng.
     * @param int $customerId ID của khách hàng
     * @param string $initialMessageContent Nội dung tin nhắn đầu tiên (nếu có)
     * @param int|null $adminId ID của admin nếu muốn gán ngay (mặc định NULL)
     * @return int|bool ID của cuộc trò chuyện hoặc False nếu lỗi
     */
    public function createOrGetChat($customerId, $initialMessageContent = '', $adminId = null) {
        // Kiểm tra xem đã có chat nào với khách hàng này chưa và đang MỞ hoặc CHỜ XỬ LÝ
        $query = "SELECT IDCuocTroChuyen FROM " . $this->tableChats . " WHERE IDKhachHang = :IDKhachHang AND (TrangThai = 'mo' OR TrangThai = 'cho_xu_ly') LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':IDKhachHang', $customerId, PDO::PARAM_INT);
        $stmt->execute();
        $chatId = $stmt->fetchColumn();

        if ($chatId) {
            // Nếu đã có chat đang mở/chờ xử lý, thêm tin nhắn vào chat đó
            if (!empty($initialMessageContent)) {
                $query = "INSERT INTO " . $this->tableMessages . " (IDCuocTroChuyen, LoaiNguoiGui, IDNguoiGui, NoiDung, DaDoc) VALUES (:IDCuocTroChuyen, 'khach_hang', :IDNguoiGui, :NoiDung, FALSE)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':IDCuocTroChuyen', $chatId, PDO::PARAM_INT);
                $stmt->bindParam(':IDNguoiGui', $customerId, PDO::PARAM_INT);
                $stmt->bindParam(':NoiDung', $initialMessageContent, PDO::PARAM_STR);
                $stmt->execute();

                // Cập nhật thời gian và nội dung tin nhắn cuối
                $query = "UPDATE " . $this->tableChats . " SET ThoiGianCapNhat = CURRENT_TIMESTAMP, NoiDungTinNhanCuoi = :NoiDung WHERE IDCuocTroChuyen = :IDCuocTroChuyen";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':NoiDung', $initialMessageContent, PDO::PARAM_STR);
                $stmt->bindParam(':IDCuocTroChuyen', $chatId, PDO::PARAM_INT);
                $stmt->execute();
            }
            return $chatId;
        }

        // Nếu chưa có hoặc đã đóng, tạo chat mới
        $this->conn->beginTransaction();
        try {
            $initialStatus = ($adminId !== null) ? 'mo' : 'cho_xu_ly'; // Nếu gán admin ngay thì là 'mo', nếu không thì 'cho_xu_ly'
            $query = "INSERT INTO " . $this->tableChats . " (IDKhachHang, IDAdmin, TrangThai, NoiDungTinNhanCuoi) VALUES (:IDKhachHang, :IDAdmin, :TrangThai, :NoiDungTinNhanCuoi)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':IDKhachHang', $customerId, PDO::PARAM_INT);
            $stmt->bindParam(':IDAdmin', $adminId, PDO::PARAM_INT); // Có thể NULL
            $stmt->bindParam(':TrangThai', $initialStatus, PDO::PARAM_STR);
            $stmt->bindParam(':NoiDungTinNhanCuoi', $initialMessageContent, PDO::PARAM_STR);
            $stmt->execute();
            $newChatId = $this->conn->lastInsertId();

            if (!empty($initialMessageContent)) {
                // Thêm tin nhắn đầu tiên từ khách hàng vào chat mới
                $query = "INSERT INTO " . $this->tableMessages . " (IDCuocTroChuyen, LoaiNguoiGui, IDNguoiGui, NoiDung, DaDoc) VALUES (:IDCuocTroChuyen, 'khach_hang', :IDNguoiGui, :NoiDung, FALSE)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':IDCuocTroChuyen', $newChatId, PDO::PARAM_INT);
                $stmt->bindParam(':IDNguoiGui', $customerId, PDO::PARAM_INT);
                $stmt->bindParam(':NoiDung', $initialMessageContent, PDO::PARAM_STR);
                $stmt->execute();
            }

            $this->conn->commit();
            return $newChatId;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Lỗi khi tạo/lấy chat: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Admin nhận một cuộc trò chuyện.
     * @param int $chatId ID cuộc trò chuyện
     * @param int $adminId ID của admin nhận
     * @return bool True nếu nhận thành công, False nếu thất bại
     */
    public function assignChatToAdmin($chatId, $adminId) {
        $query = "UPDATE " . $this->tableChats . " SET IDAdmin = :IDAdmin, TrangThai = 'mo' WHERE IDCuocTroChuyen = :IDCuocTroChuyen AND (IDAdmin IS NULL OR IDAdmin = :IDAdminCheck) AND TrangThai != 'dong'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':IDAdmin', $adminId, PDO::PARAM_INT);
        $stmt->bindParam(':IDAdminCheck', $adminId, PDO::PARAM_INT); // Thêm điều kiện này để tránh trường hợp admin đã nhận rồi
        $stmt->bindParam(':IDCuocTroChuyen', $chatId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Đóng một cuộc trò chuyện.
     * @param int $chatId ID cuộc trò chuyện
     * @param int $adminId ID của admin thực hiện đóng (để kiểm tra quyền)
     * @return bool True nếu đóng thành công, False nếu thất bại
     */
    public function closeChat($chatId, $adminId) {
        $query = "UPDATE " . $this->tableChats . " SET TrangThai = 'dong' WHERE IDCuocTroChuyen = :IDCuocTroChuyen AND (IDAdmin = :IDAdmin OR IDAdmin IS NULL)"; // Cho phép admin đóng chat chưa được gán
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':IDCuocTroChuyen', $chatId, PDO::PARAM_INT);
        $stmt->bindParam(':IDAdmin', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Đánh dấu tất cả tin nhắn từ khách hàng trong một chat là đã đọc.
     * @param int $chatId ID cuộc trò chuyện
     * @return int Số hàng bị ảnh hưởng
     */
    private function markCustomerMessagesAsRead($chatId) {
        $query = "UPDATE " . $this->tableMessages . " SET DaDoc = TRUE WHERE IDCuocTroChuyen = :IDCuocTroChuyen AND LoaiNguoiGui = 'khach_hang' AND DaDoc = FALSE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':IDCuocTroChuyen', $chatId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    /**
     * Lấy tổng số tin nhắn chưa đọc của khách hàng trong tất cả các cuộc trò chuyện.
     * Dùng cho hiển thị số lượng thông báo tổng quát cho admin.
     * @return int Tổng số tin nhắn chưa đọc
     */
    public function getTotalUnreadCustomerMessages() {
        $query = "SELECT COUNT(tn.IDCuocTroChuyen) FROM " . $this->tableMessages . " tn
                  JOIN " . $this->tableChats . " ctc ON tn.IDCuocTroChuyen = ctc.IDCuocTroChuyen
                  WHERE tn.LoaiNguoiGui = 'khach_hang' AND tn.DaDoc = FALSE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Lấy thông tin chi tiết một cuộc trò chuyện bằng ID.
     * @param int $chatId ID cuộc trò chuyện
     * @return array|false Thông tin cuộc trò chuyện hoặc false nếu không tìm thấy
     */
    public function getChatById($chatId) {
        $query = "SELECT * FROM " . $this->tableChats . " WHERE IDCuocTroChuyen = :IDCuocTroChuyen LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':IDCuocTroChuyen', $chatId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
