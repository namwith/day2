<?php
require_once 'config.php';

$errors = [];
$success = false;
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $formData = [
        'username' => trim($_POST['username'] ?? ''),
        'password' => trim($_POST['password'] ?? ''),
        'confirm_password' => trim($_POST['confirm_password'] ?? ''),
        'fullname' => trim($_POST['fullname'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'gender' => $_POST['gender'] ?? '',
        'birthday' => $_POST['birthday'] ?? ''
    ];
    
    // Validation
    if (empty($formData['username'])) {
        $errors[] = "Tên đăng nhập không được để trống";
    } elseif (strlen($formData['username']) < 3) {
        $errors[] = "Tên đăng nhập phải có ít nhất 3 ký tự";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $formData['username'])) {
        $errors[] = "Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới";
    } elseif (usernameExists($formData['username'])) {
        $errors[] = "Tên đăng nhập đã tồn tại, vui lòng chọn tên khác";
    }
    
    if (empty($formData['password'])) {
        $errors[] = "Mật khẩu không được để trống";
    } elseif (strlen($formData['password']) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
    }
    
    if (empty($formData['confirm_password'])) {
        $errors[] = "Vui lòng xác nhận mật khẩu";
    } elseif ($formData['password'] !== $formData['confirm_password']) {
        $errors[] = "Mật khẩu xác nhận không khớp";
    }
    
    if (empty($formData['fullname'])) {
        $errors[] = "Họ tên không được để trống";
    } elseif (strlen($formData['fullname']) < 2) {
        $errors[] = "Họ tên phải có ít nhất 2 ký tự";
    }
    
    if (empty($formData['address'])) {
        $errors[] = "Địa chỉ không được để trống";
    }
    
    if (empty($formData['phone'])) {
        $errors[] = "Số điện thoại không được để trống";
    } elseif (!preg_match('/^[0-9]{10,11}$/', $formData['phone'])) {
        $errors[] = "Số điện thoại không hợp lệ (10-11 chữ số)";
    }
    
    if (empty($formData['gender'])) {
        $errors[] = "Vui lòng chọn giới tính";
    }
    
    if (empty($formData['birthday'])) {
        $errors[] = "Ngày sinh không được để trống";
    } else {
        $birthDate = new DateTime($formData['birthday']);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        if ($age < 16) {
            $errors[] = "Khách hàng phải từ 16 tuổi trở lên";
        } elseif ($age > 120) {
            $errors[] = "Ngày sinh không hợp lệ";
        }
    }
    
    // If no errors, create customer
    if (empty($errors)) {
        $id = getNextId();
        $customer = new Customer(
            $id, 
            $formData['username'], 
            $formData['password'], 
            $formData['fullname'], 
            $formData['address'], 
            $formData['phone'], 
            $formData['gender'], 
            $formData['birthday']
        );
        
        addCustomer($customer);
        $success = true;
        
        // Clear form data after successful submission
        $formData = [];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Khách hàng - PHP Session</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> Thêm Khách hàng mới</h1>
            <p>Điền đầy đủ thông tin khách hàng vào form bên dưới</p>
        </div>

        <!-- Navigation -->
        <div class="actions">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>

        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Thêm khách hàng thành công!</strong>
                    <p>Khách hàng đã được lưu vào hệ thống.</p>
                    <div class="alert-actions">
                        <a href="index.php" class="btn-link">
                            <i class="fas fa-list"></i> Xem danh sách khách hàng
                        </a>
                        <a href="add_customer.php" class="btn-link">
                            <i class="fas fa-plus"></i> Thêm khách hàng khác
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Có <?php echo count($errors); ?> lỗi cần được sửa:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form Container -->
        <div class="form-container">
            <form method="POST" class="customer-form" novalidate>
                <!-- Account Information -->
                <div class="form-section">
                    <h3><i class="fas fa-user-shield"></i> Thông tin tài khoản</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">
                                <i class="fas fa-user"></i> Tên đăng nhập *
                            </label>
                            <input type="text" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>" 
                                   placeholder="Nhập tên đăng nhập"
                                   required>
                            <small class="form-help">Chỉ được chứa chữ cái, số và dấu gạch dưới</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock"></i> Mật khẩu *
                            </label>
                            <input type="password" id="password" name="password" 
                                   placeholder="Nhập mật khẩu"
                                   required>
                            <small class="form-help">Ít nhất 6 ký tự</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-lock"></i> Xác nhận mật khẩu *
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               placeholder="Nhập lại mật khẩu"
                               required>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="form-section">
                    <h3><i class="fas fa-id-card"></i> Thông tin cá nhân</h3>
                    <div class="form-group">
                        <label for="fullname">
                            <i class="fas fa-signature"></i> Họ và tên *
                        </label>
                        <input type="text" id="fullname" name="fullname" 
                               value="<?php echo htmlspecialchars($formData['fullname'] ?? ''); ?>" 
                               placeholder="Nhập họ và tên đầy đủ"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="address">
                            <i class="fas fa-map-marker-alt"></i> Địa chỉ *
                        </label>
                        <textarea id="address" name="address" rows="3" 
                                  placeholder="Nhập địa chỉ chi tiết"
                                  required><?php echo htmlspecialchars($formData['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">
                                <i class="fas fa-phone"></i> Số điện thoại *
                            </label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" 
                                   placeholder="Nhập số điện thoại"
                                   required>
                            <small class="form-help">10-11 chữ số</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">
                                <i class="fas fa-venus-mars"></i> Giới tính *
                            </label>
                            <select id="gender" name="gender" required>
                                <option value="">Chọn giới tính</option>
                                <option value="Nam" <?php echo ($formData['gender'] ?? '') === 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                <option value="Nữ" <?php echo ($formData['gender'] ?? '') === 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="Khác" <?php echo ($formData['gender'] ?? '') === 'Khác' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="birthday">
                            <i class="fas fa-birthday-cake"></i> Ngày sinh *
                        </label>
                        <input type="date" id="birthday" name="birthday" 
                               value="<?php echo htmlspecialchars($formData['birthday'] ?? ''); ?>" 
                               max="<?php echo date('Y-m-d', strtotime('-16 years')); ?>"
                               min="<?php echo date('Y-m-d', strtotime('-120 years')); ?>"
                               required>
                        <small class="form-help">Khách hàng phải từ 16 tuổi trở lên</small>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Lưu khách hàng
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i>
                        Làm mới
                    </button>
                </div>
            </form>
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

        // Form validation
        document.querySelector('.customer-form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                document.getElementById('confirm_password').focus();
            }
        });

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Real-time username validation
        document.getElementById('username').addEventListener('blur', function(e) {
            const username = e.target.value;
            if (username.length > 0 && !/^[a-zA-Z0-9_]+$/.test(username)) {
                e.target.style.borderColor = '#e74c3c';
                e.target.nextElementSibling.textContent = 'Tên đăng nhập không hợp lệ';
                e.target.nextElementSibling.style.color = '#e74c3c';
            } else {
                e.target.style.borderColor = '';
                e.target.nextElementSibling.textContent = 'Chỉ được chứa chữ cái, số và dấu gạch dưới';
                e.target.nextElementSibling.style.color = '';
            }
        });
    </script>
</body>
</html>
