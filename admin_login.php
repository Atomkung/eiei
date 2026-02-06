<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, password, fullname FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['fullname'];
                header('Location: admin_dashboard.php');
                exit;
            } else {
                $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
            }
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
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
    <title>เข้าสู่ระบบผู้ดูแล</title>
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
        }
        .container {
            background: #34495e;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 90%;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }
        .form-group {
            margin-bottom: 25px;
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
        .info-box {
            background: #3498db;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 เข้าสู่ระบบผู้ดูแล</h1>
        
        <div class="info-box">
            <strong>ข้อมูลเริ่มต้น:</strong><br>
            Username: admin<br>
            Password: 909876
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">เข้าสู่ระบบ</button>
        </form>
        
        <div class="back-link">
            <a href="index.php">← กลับหน้าหลัก</a>
        </div>
    </div>
</body>
</html>
