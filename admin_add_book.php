<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$admin_name = $_SESSION['admin_name'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $category = trim($_POST['category']);
    
    if (empty($title) || empty($author) || empty($category)) {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วน (ชื่อหนังสือ, ผู้แต่ง, หมวดหมู่)';
    } else {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO books (title, author, isbn, category, status) VALUES (?, ?, ?, ?, 'available')");
        $stmt->bind_param("ssss", $title, $author, $isbn, $category);
        
        if ($stmt->execute()) {
            $success = 'เพิ่มหนังสือสำเร็จ!';
            // Clear form
            $_POST = array();
        } else {
            $error = 'เกิดข้อผิดพลาด กรุณาลองใหม่';
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
    <title>เพิ่มหนังสือ</title>
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
            padding: 20px;
        }
        .header {
            background: #34495e;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .header h1 {
            font-size: 2em;
        }
        .header-right {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        .user-info {
            font-size: 1.1em;
        }
        .nav-btn {
            padding: 10px 20px;
            background: #2c3e50;
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .nav-btn:hover {
            background: #1a252f;
            transform: translateY(-2px);
        }
        .container {
            background: #34495e;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 600px;
            margin: 0 auto;
        }
        h2 {
            margin-bottom: 30px;
            font-size: 1.8em;
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
            background: #27ae60;
            color: white;
            margin-top: 10px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #229954;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 เพิ่มหนังสือใหม่</h1>
        <div class="header-right">
            <span class="user-info">ผู้ดูแล: <?= htmlspecialchars($admin_name) ?></span>
            <a href="admin_dashboard.php" class="nav-btn">กลับหน้าหลัก</a>
            <a href="logout.php" class="nav-btn">ออกจากระบบ</a>
        </div>
    </div>

    <div class="container">
        <h2>เพิ่มรายการหนังสือ</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>ชื่อหนังสือ *</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>ผู้แต่ง *</label>
                <input type="text" name="author" required>
            </div>
            
            <div class="form-group">
                <label>ISBN</label>
                <input type="text" name="isbn">
            </div>
            
            <div class="form-group">
                <label>หมวดหมู่ *</label>
                <input type="text" name="category" required>
            </div>
            
            <button type="submit" class="btn">+ เพิ่มหนังสือ</button>
        </form>
    </div>
</body>
</html>
