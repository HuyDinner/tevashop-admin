<div class="dashboard-container">
           <?php include VIEWS_PATH . 'includes/sidebar.php'; ?>
    <main class="main-content">
            <?php include VIEWS_PATH . 'includes/header.php'; ?>
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Thông tin tài khoản Admin</h4>
                            </div>
                            <div class="card-body">
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?= $error ?>
                                    </div>
                                <?php elseif (isset($accountInfo)): ?>
                                    <form id="accountUpdateForm">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Tên đăng nhập</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($accountInfo['username']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($accountInfo['email']) ?>" required>
                                        </div>
                                        <hr>
                                        <h5>Thay đổi mật khẩu (Để trống nếu không muốn thay đổi)</h5>
                                        <div class="mb-3">
                                            <label for="old_password" class="form-label">Mật khẩu cũ</label>
                                            <input type="password" class="form-control" id="old_password" name="old_password">
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password">
                                        </div>
                                        <div class="mb-3">
                                            <label for="confirm_new_password" class="form-label">Xác nhận mật khẩu mới</label>
                                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Cập nhật tài khoản</button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-info" role="alert">
                                        Không có thông tin tài khoản để hiển thị.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountUpdateForm = document.getElementById('accountUpdateForm');
    if (accountUpdateForm) {
        accountUpdateForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }

            try {
                const response = await fetch('<?= BASE_URL ?>account/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    // Có thể tải lại trang hoặc cập nhật UI
                    // location.reload();
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Lỗi khi gửi yêu cầu:', error);
                alert('Đã xảy ra lỗi khi cập nhật tài khoản.');
            }
        });
    }
});
</script>
