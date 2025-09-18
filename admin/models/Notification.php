<?php
// admin/models/Notification.php

class Notification {
    private $pdo;
    private $table_name = "ThongBao";

    public function __construct(PDO $db) {
        $this->pdo = $db;
    }

    // Lấy thông báo chưa đọc cho dropdown
    public function getUnreadNotificationsForAdmin($adminId, $limit = 5) {
        $query = "SELECT id, tieu_de, noi_dung, loai_thong_bao, link_url, da_doc, ngay_tao 
                  FROM " . $this->table_name . " 
                  WHERE admin_id = :admin_id AND da_doc = FALSE
                  ORDER BY ngay_tao DESC 
                  LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm số lượng thông báo chưa đọc
    public function countUnreadNotificationsForAdmin($adminId) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE admin_id = :admin_id AND da_doc = FALSE";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Lấy tất cả thông báo với phân trang, tìm kiếm, lọc
    public function getAllNotifications(
        $adminId, 
        $page = 1, 
        $limit = 10, 
        $searchQuery = '', 
        $isReadFilter = null, 
        $typeFilter = ''
    ) {
        $offset = ($page - 1) * $limit;

        $sqlConditions = ["admin_id = :admin_id"];
        $sqlParams = [':admin_id' => $adminId];

        if ($searchQuery) {
            $sqlConditions[] = "(tieu_de LIKE :search_query OR noi_dung LIKE :search_query)";
            $sqlParams[':search_query'] = '%' . $searchQuery . '%';
        }
        if ($isReadFilter !== null) { // isReadFilter là boolean (true/false) hoặc null
            $sqlConditions[] = "da_doc = :is_read";
            $sqlParams[':is_read'] = $isReadFilter; 
        }
        if ($typeFilter) {
            $sqlConditions[] = "loai_thong_bao = :type_filter";
            $sqlParams[':type_filter'] = $typeFilter;
        }

        $whereClause = implode(' AND ', $sqlConditions);

        // Đếm tổng số thông báo
        $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM " . $this->table_name . " WHERE " . $whereClause);
        foreach ($sqlParams as $key => &$val) {
            $stmtCount->bindParam($key, $val, is_bool($val) ? PDO::PARAM_BOOL : PDO::PARAM_STR);
        }
        $stmtCount->execute();
        $totalNotifications = $stmtCount->fetchColumn();
        $totalPages = ceil($totalNotifications / $limit);

        // Lấy dữ liệu thông báo
        $query = "SELECT id, tieu_de, noi_dung, loai_thong_bao, link_url, da_doc, ngay_tao 
                  FROM " . $this->table_name . " 
                  WHERE " . $whereClause . "
                  ORDER BY ngay_tao DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($query);
        foreach ($sqlParams as $key => &$val) {
            $stmt->bindParam($key, $val, is_bool($val) ? PDO::PARAM_BOOL : PDO::PARAM_STR);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'notifications' => $notifications,
            'total_notifications' => $totalNotifications,
            'total_pages' => $totalPages,
            'current_page' => $page
        ];
    }

    // Đánh dấu một thông báo là đã đọc
    public function markAsRead($notificationId, $adminId) {
        $query = "UPDATE " . $this->table_name . " 
                  SET da_doc = TRUE 
                  WHERE id = :id AND admin_id = :admin_id AND da_doc = FALSE";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Đánh dấu tất cả thông báo chưa đọc là đã đọc
    public function markAllAsRead($adminId) {
        $query = "UPDATE " . $this->table_name . " SET da_doc = TRUE WHERE admin_id = :admin_id AND da_doc = FALSE";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Tạo thông báo mới
    public function createNotification($adminId, $tieu_de, $noi_dung, $loai_thong_bao, $link_url) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (admin_id, tieu_de, noi_dung, loai_thong_bao, link_url, da_doc) 
                  VALUES (:admin_id, :tieu_de, :noi_dung, :loai_thong_bao, :link_url, FALSE)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->bindParam(':tieu_de', $tieu_de, PDO::PARAM_STR);
        $stmt->bindParam(':noi_dung', $noi_dung, PDO::PARAM_STR);
        $stmt->bindParam(':loai_thong_bao', $loai_thong_bao, PDO::PARAM_STR);
        $stmt->bindParam(':link_url', $link_url, PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    // Cập nhật thông báo
    public function updateNotification($id, $adminId, $tieu_de, $noi_dung, $loai_thong_bao, $link_url) {
        $query = "UPDATE " . $this->table_name . " 
                  SET tieu_de = :tieu_de, noi_dung = :noi_dung, loai_thong_bao = :loai_thong_bao, link_url = :link_url 
                  WHERE id = :id AND admin_id = :admin_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':tieu_de', $tieu_de, PDO::PARAM_STR);
        $stmt->bindParam(':noi_dung', $noi_dung, PDO::PARAM_STR);
        $stmt->bindParam(':loai_thong_bao', $loai_thong_bao, PDO::PARAM_STR);
        $stmt->bindParam(':link_url', $link_url, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Xóa thông báo
    public function deleteNotification($id, $adminId) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND admin_id = :admin_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
