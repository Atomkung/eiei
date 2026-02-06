<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$admin_name = $_SESSION['admin_name'];
$db = getDB();

// ดึงรายการหนังสือที่ถูกยืม
$query = "
    SELECT bh.*, b.title, b.author, b.isbn, b.category, m.fullname, m.email, m.phone
    FROM borrow_history bh
    JOIN books b ON bh.book_id = b.id
    JOIN members m ON bh.member_id = m.id
    WHERE bh.status = 'borrowed'
    ORDER BY bh.borrow_date DESC
";
$borrowed_result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการหนังสือที่ถูกยืม</title>
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
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h2 {
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        .borrow-item {
            background: #2c3e50;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .book-title {
            font-size: 1.4em;
            margin-bottom: 15px;
            color: #3498db;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        .info-item {
            margin: 5px 0;
        }
        .info-label {
            color: #95a5a6;
            font-size: 0.95em;
        }
        .info-value {
            font-size: 1.05em;
            margin-top: 3px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            font-size: 1.2em;
            color: #95a5a6;
        }
        .stats {
            background: #2c3e50;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .stats-number {
            font-size: 2.5em;
            color: #e74c3c;
        }
        .stats-label {
            font-size: 1.1em;
            color: #ecf0f1;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 รายการหนังสือที่ถูกยืม</h1>
        <div class="header-right">
            <span class="user-info">ผู้ดูแล: <?= htmlspecialchars($admin_name) ?></span>
            <a href="admin_dashboard.php" class="nav-btn">กลับหน้าหลัก</a>
            <a href="logout.php" class="nav-btn">ออกจากระบบ</a>
        </div>
    </div>

    <div class="stats">
        <div class="stats-number"><?= $borrowed_result->num_rows ?></div>
        <div class="stats-label">หนังสือที่กำลังถูกยืม</div>
    </div>

    <div class="container">
        <h2>รายละเอียดการยืม</h2>
        
        <?php if ($borrowed_result->num_rows > 0): ?>
            <?php while ($item = $borrowed_result->fetch_assoc()): ?>
                <div class="borrow-item">
                    <div class="book-title">📖 <?= htmlspecialchars($item['title']) ?></div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">ผู้แต่ง</div>
                            <div class="info-value"><?= htmlspecialchars($item['author']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">หมวดหมู่</div>
                            <div class="info-value"><?= htmlspecialchars($item['category']) ?></div>
                        </div>
                        <?php if ($item['isbn']): ?>
                            <div class="info-item">
                                <div class="info-label">ISBN</div>
                                <div class="info-value"><?= htmlspecialchars($item['isbn']) ?></div>
                            </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <div class="info-label">ผู้ยืม</div>
                            <div class="info-value"><?= htmlspecialchars($item['fullname']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">อีเมล</div>
                            <div class="info-value"><?= htmlspecialchars($item['email']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">เบอร์โทร</div>
                            <div class="info-value"><?= htmlspecialchars($item['phone']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">วันที่ยืม</div>
                            <div class="info-value"><?= date('d/m/Y', strtotime($item['borrow_date'])) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">จำนวนวันที่ยืม</div>
                            <div class="info-value">
                                <?php
                                $borrow_date = new DateTime($item['borrow_date']);
                                $today = new DateTime();
                                $days = $borrow_date->diff($today)->days;
                                echo $days . ' วัน';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-data">ไม่มีหนังสือที่ถูกยืมในขณะนี้</div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $db->close(); ?>
