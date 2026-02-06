<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    if (empty($username) || empty($password) || empty($fullname) || empty($email)) {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    } elseif ($password !== $confirm_password) {
        $error = 'รหัสผ่านไม่ตรงกัน';
    } elseif (strlen($password) < 6) {
        $error = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
    } else {
        $db = getDB();
        
        // ตรวจสอบ username ซ้ำ
        $stmt = $db->prepare("SELECT id FROM members WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'ชื่อผู้ใช้นี้ถูกใช้แล้ว';
        } else {
            // เพิ่มสมาชิกใหม่
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO members (username, password, fullname, email, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $hashed_password, $fullname, $email, $phone);
            
            if ($stmt->execute()) {
                $success = 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ';
                header('refresh:2;url=member_login.php');
            } else {
                $error = 'เกิดข้อผิดพลาด กรุณาลองใหม่';
            }
        }
        $stmt->close();
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 600;
            background: #2c3e50;
            color: #ecf0f1;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: #34495e;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 1.1em;
        }
        input {
            width: 100%;
            padding: 12px;
            font-size: 1em;
            font-weight: 600;
            border: 2px solid #2c3e50;
            border-radius: 6px;
            background: #2c3e50;
            color: #ecf0f1;
        }
        input:focus {
            outline: none;
            border-color: #1a252f;
        }
        .btn {
            width: 100%;
            padding: 15px;
            font-size: 1.2em;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: #2c3e50;
            color: #ecf0f1;
            margin-top: 10px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #1a252f;
            transform: translateY(-2px);
        }
        .error {
            background: #e74c3c;
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success {
            background: #27ae60;
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 1.1em;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 สมัครสมาชิก</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>ยืนยันรหัสผ่าน</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label>ชื่อ-นามสกุล</label>
                <input type="text" name="fullname" required>
            </div>
            
            <div class="form-group">
                <label>อีเมล</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>เบอร์โทรศัพท์</label>
                <input type="text" name="phone">
            </div>
            
            <button type="submit" class="btn">สมัครสมาชิก</button>
        </form>
        
        <div class="back-link">
            <a href="index.php">← กลับหน้าหลัก</a>
        </div>
    </div>
</body>
</html>
