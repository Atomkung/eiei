<?php
require_once 'config.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: member_login.php');
    exit;
}

$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['member_name'];

$db = getDB();

// ดึงประวัติการยืม-คืน
$stmt = $db->prepare("
    SELECT bh.*, b.title, b.author, b.category
    FROM borrow_history bh
    JOIN books b ON bh.book_id = b.id
    WHERE bh.member_id = ?
    ORDER BY bh.created_at DESC
");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$history_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการยืม-คืน</title>
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
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .nav-btn:hover {
            background: #1a252f;
            transform: translateY(-2px);
        }
        .container {
            background: #34495e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h2 {
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        .history-item {
            background: #2c3e50;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .book-title {
            font-size: 1.3em;
            margin-bottom: 10px;
        }
        .book-info {
            margin: 5px 0;
            font-size: 1em;
        }
        .status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 700;
            margin-top: 10px;
            margin-right: 10px;
        }
        .status.borrowed {
            background: #f39c12;
            color: white;
        }
        .status.returned {
            background: #27ae60;
            color: white;
        }
        .return-btn {
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1em;
            transition: all 0.3s;
        }
        .return-btn:hover {
            background: #2980b9;
        }
        .no-history {
            text-align: center;
            padding: 40px;
            font-size: 1.2em;
            color: #95a5a6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 ประวัติการยืม-คืนหนังสือ</h1>
        <div class="header-right">
            <span class="user-info">สวัสดี, <?= htmlspecialchars($member_name) ?></span>
            <a href="member_dashboard.php" class="nav-btn">หน้าหลัก</a>
            <a href="logout.php" class="nav-btn">ออกจากระบบ</a>
        </div>
    </div>

    <div class="container">
        <h2>ประวัติการยืม-คืนของคุณ</h2>
        
        <?php if ($history_result->num_rows > 0): ?>
            <?php while ($item = $history_result->fetch_assoc()): ?>
                <div class="history-item">
                    <div class="book-title"><?= htmlspecialchars($item['title']) ?></div>
                    <div class="book-info">ผู้แต่ง: <?= htmlspecialchars($item['author']) ?></div>
                    <div class="book-info">หมวดหมู่: <?= htmlspecialchars($item['category']) ?></div>
                    <div class="book-info">วันที่ยืม: <?= date('d/m/Y', strtotime($item['borrow_date'])) ?></div>
                    <?php if ($item['return_date']): ?>
                        <div class="book-info">วันที่คืน: <?= date('d/m/Y', strtotime($item['return_date'])) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($item['status'] == 'borrowed'): ?>
                        <span class="status borrowed">กำลังยืม</span>
                        <form action="return_book.php" method="POST" style="display: inline;">
                            <input type="hidden" name="borrow_id" value="<?= $item['id'] ?>">
                            <button type="submit" class="return-btn">คืนหนังสือ</button>
                        </form>
                    <?php else: ?>
                        <span class="status returned">คืนแล้ว</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-history">ยังไม่มีประวัติการยืม-คืนหนังสือ</div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php 
$stmt->close();
$db->close(); 
?>
