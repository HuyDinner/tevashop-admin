<?php
// admin/controllers/ReviewController.php

require_once MODELS_PATH . 'Review.php';

class ReviewController {
    private $reviewModel;

    public function __construct($db) {
        $this->reviewModel = new Review($db);
    }

    public function index() {
        $reviews = $this->reviewModel->getAllReviews();
        
        $pageTitle = "Quản lý Đánh giá";
        $pageIcon = "fas fa-star";

        $controllerName = "Review"; 

        include VIEWS_PATH . 'reviews' . DIRECTORY_SEPARATOR . 'index.php'; 
    }

    // Phương thức để xóa đánh giá
    public function delete($id) {
        if (!isset($_SESSION['admin_logged_in'])) { // Basic check, full check in index.php
            header("Location: " . BASE_URL . "dashboard/index");
            exit();
        }

        if ($this->reviewModel->deleteReview($id)) {
            $_SESSION['message'] = "Xóa đánh giá thành công!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Xóa đánh giá thất bại.";
            $_SESSION['message_type'] = "error";
        }
        header("Location: " . BASE_URL . "review/index");
        exit();
    }
}
