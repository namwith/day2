<?php
require_once 'config.php';

// Handle actions
$message = '';
$messageType = '';

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'clear':
            clearAllCustomers();
            $message = "Đã xóa tất cả khách hàng thành công!";
            $messageType = "success";
            break;
        case 'delete':
            if (isset($_GET['id'])) {
                if (deleteCustomer($_GET['id'])) {
                    $message = "Đã xóa khách hàng thành công!";
                    $messageType = "success";
                } else {
                    $message = "Không thể xóa khách hàng!";
                    $messageType = "error";
                }
            }
            break;
    }
}

// Handle search
$customers = [];
$searchKeyword = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchKeyword = trim($_GET['search']);
    $customers = searchCustomers($searchKeyword);
} else {
    $customers = getAllCustomers();
}

$stats = getCustomerStats();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khách hàng - PHP Session</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-users"></i> Hệ thống Quản lý Khách hàng</h1>
            <p>Sử dụng PHP Class và Session - Lưu trữ dữ liệu tạm thời</p>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="actions">
            <a href="add_customer.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm khách hàng mới
            </a>
            <?php if (count($customers) > 0): ?>
                <a href="?action=clear" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa tất cả khách hàng? Dữ liệu sẽ mất vĩnh viễn!')">
                    <i class="fas fa-trash"></i> Xóa tất cả
                </a>
            <?php endif; ?>
        </div>

        <!-- Search Form -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <div class="search-input-group">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên, username, địa chỉ, số điện thoại..." 
                           value="<?php echo htmlspecialchars($searchKeyword); ?>" class="search-input">
                    <button type="submit" class="btn btn-search">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <?php if (!empty($searchKeyword)): ?>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="stats">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div>
                    <h3><?php echo $stats['total']; ?></h3>
                    <p>Tổng khách hàng</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-male"></i>
                <div>
                    <h3><?php echo $stats['male']; ?></h3>
                    <p>Nam giới</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-female"></i>
                <div>
                    <h3><?php echo $stats['female']; ?></h3>
                    <p>Nữ giới</p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-question-circle"></i>
                <div>
                    <h3><?php echo $stats['other']; ?></h3>
                    <p>Khác</p>
                </div>
            </div>
        </div>

        <!-- Customer List -->
        <?php if (empty($customers)): ?>
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <h3><?php echo !empty($searchKeyword) ? 'Không tìm thấy khách hàng nào' : 'Chưa có khách hàng nào'; ?></h3>
                <p><?php echo !empty($searchKeyword) ? 'Thử tìm kiếm với từ khóa khác' : 'Hãy thêm khách hàng đầu tiên của bạn'; ?></p>
                <?php if (empty($searchKeyword)): ?>
                    <a href="add_customer.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm ngay
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-container">
                <div class="table-header">
                    <h2><i class="fas fa-list"></i> Danh sách Khách hàng</h2>
                    <?php if (!empty($searchKeyword)): ?>
                        <p class="search-result">Tìm thấy <strong><?php echo count($customers); ?></strong> khách hàng với từ khóa "<strong><?php echo htmlspecialchars($searchKeyword); ?></strong>"</p>
                    <?php endif; ?>
                </div>
                
                <div class="table-responsive">
                    <table class="customer-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên đăng nhập</th>
                                <th>Mật khẩu</th>
                                <th>Họ tên</th>
                                <th>Địa chỉ</th>
                                <th>Điện thoại</th>
                                <th>Giới tính</th>
                                <th>Ngày sinh</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><span class="id-badge"><?php echo $customer->getId(); ?></span></td>
                                    <td>
                                        <i class="fas fa-user"></i>
                                        <strong><?php echo htmlspecialchars($customer->getUsername()); ?></strong>
                                    </td>
                                    <td>
                                        <span class="password-hidden">
                                            <i class="fas fa-eye-slash"></i>
                                            ••••••••
                                        </span>
                                    </td>
                                    <td>
                                        <div class="customer-name">
                                            <strong><?php echo htmlspecialchars($customer->getFullname()); ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($customer->getAddress()); ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-phone"></i>
                                        <a href="tel:<?php echo $customer->getPhone(); ?>" class="phone-link">
                                            <?php echo htmlspecialchars($customer->getPhone()); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="gender-badge <?php echo strtolower($customer->getGender()); ?>">
                                            <i class="fas fa-<?php echo $customer->getGender() === 'Nam' ? 'male' : 'female'; ?>"></i>
                                            <?php echo $customer->getGender(); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <i class="fas fa-birthday-cake"></i>
                                        <?php echo date('d/m/Y', strtotime($customer->getBirthday())); ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?action=delete&id=<?php echo $customer->getId(); ?>" 
                                               class="btn-action btn-delete" 
                                               onclick="return confirm('Bạn có chắc muốn xóa khách hàng <?php echo htmlspecialchars($customer->getFullname()); ?>?')"
                                               title="Xóa khách hàng">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Footer Info -->
        <div class="footer-info">
            <p><i class="fas fa-info-circle"></i> Dữ liệu được lưu trong PHP Session và sẽ mất khi đóng trình duyệt hoặc hết phiên làm việc.</p>
        </div>
    </div>

    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }, 5000);
    </script>
</body>
</html>
