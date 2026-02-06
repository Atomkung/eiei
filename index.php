<?php
require_once 'config.php';

// ถ้าล็อกอินแล้วให้ redirect
if (isset($_SESSION['member_id'])) {
    header('Location: member_dashboard.php');
    exit;
}
if (isset($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบยืม-คืนหนังสือ</title>
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
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 40px;
            color: #ecf0f1;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .btn {
            padding: 18px 40px;
            font-size: 1.2em;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: #2c3e50;
            color: #ecf0f1;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 ระบบยืม-คืนหนังสือ</h1>
        <div class="btn-group">
            <a href="member_login.php" class="btn">เข้าสู่ระบบสมาชิก</a>
            <a href="member_register.php" class="btn">สมัครสมาชิก</a>
            <a href="admin_login.php" class="btn">เข้าสู่ระบบผู้ดูแล</a>
        </div>
    </div>
</body>
</html>
