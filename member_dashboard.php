<?php
require_once 'config.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: member_login.php');
    exit;
}

$db = getDB();
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['member_name'];

// ดึงรายการหนังสือทั้งหมด
$books_result = $db->query("SELECT * FROM books ORDER BY title");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลักสมาชิก</title>
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
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .book-card {
            background: #2c3e50;
            padding: 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .book-title {
            font-size: 1.3em;
            margin-bottom: 10px;
            color: #ecf0f1;
        }
        .book-info {
            margin: 8px 0;
            font-size: 1em;
        }
        .status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 700;
            margin-top: 10px;
        }
        .status.available {
            background: #27ae60;
            color: white;
        }
        .status.borrowed {
            background: #e74c3c;
            color: white;
        }
        .borrow-btn {
            margin-top: 15px;
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1em;
            transition: all 0.3s;
        }
        .borrow-btn:hover {
            background: #229954;
        }
        .borrow-btn:disabled {
            background: #7f8c8d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 ระบบยืม-คืนหนังสือ</h1>
        <div class="header-right">
            <span class="user-info">สวัสดี, <?= htmlspecialchars($member_name) ?></span>
            <a href="borrow_history.php" class="nav-btn">ประวัติการยืม-คืน</a>
            <a href="logout.php" class="nav-btn">ออกจากระบบ</a>
        </div>
    </div>

    <div class="container">
        <h2>รายการหนังสือทั้งหมด</h2>
        <div class="books-grid">
            <?php while ($book = $books_result->fetch_assoc()): ?>
                <div class="book-card">
                    <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
                    <div class="book-info">ผู้แต่ง: <?= htmlspecialchars($book['author']) ?></div>
                    <div class="book-info">หมวดหมู่: <?= htmlspecialchars($book['category']) ?></div>
                    <?php if ($book['isbn']): ?>
                        <div class="book-info">ISBN: <?= htmlspecialchars($book['isbn']) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($book['status'] == 'available'): ?>
                        <span class="status available">✓ ว่าง - ยืมได้</span>
                        <form action="borrow_book.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="borrow-btn">ยืมหนังสือเล่มนี้</button>
                        </form>
                    <?php else: ?>
                        <span class="status borrowed">✗ ถูกยืมแล้ว</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
<?php $db->close(); ?>
