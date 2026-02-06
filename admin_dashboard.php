<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$admin_name = $_SESSION['admin_name'];
$db = getDB();

// สถิติต่างๆ
$total_members = $db->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count'];
$total_books = $db->query("SELECT COUNT(*) as count FROM books")->fetch_assoc()['count'];
$borrowed_books = $db->query("SELECT COUNT(*) as count FROM books WHERE status = 'borrowed'")->fetch_assoc()['count'];
$available_books = $db->query("SELECT COUNT(*) as count FROM books WHERE status = 'available'")->fetch_assoc()['count'];

// รายการสมาชิกทั้งหมด
$members_result = $db->query("SELECT * FROM members ORDER BY created_at DESC");

// รายการหนังสือทั้งหมด
$books_result = $db->query("SELECT * FROM books ORDER BY title");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าผู้ดูแลระบบ</title>
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #34495e;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-number {
            font-size: 2.5em;
            color: #3498db;
            margin-bottom: 10px;
        }
        .stat-label {
            font-size: 1.1em;
            color: #ecf0f1;
        }
        .container {
            background: #34495e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        h2 {
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #2c3e50;
        }
        th {
            background: #2c3e50;
            font-weight: 700;
        }
        tr:hover {
            background: #2c3e50;
        }
        .add-btn {
            padding: 12px 25px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1em;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .add-btn:hover {
            background: #229954;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.9em;
        }
        .status-available {
            background: #27ae60;
            color: white;
        }
        .status-borrowed {
            background: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛡️ หน้าผู้ดูแลระบบ</h1>
        <div class="header-right">
            <span class="user-info">ผู้ดูแล: <?= htmlspecialchars($admin_name) ?></span>
            <a href="admin_borrowed_books.php" class="nav-btn">หนังสือที่ถูกยืม</a>
            <a href="logout.php" class="nav-btn">ออกจากระบบ</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $total_members ?></div>
            <div class="stat-label">สมาชิกทั้งหมด</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $total_books ?></div>
            <div class="stat-label">หนังสือทั้งหมด</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $borrowed_books ?></div>
            <div class="stat-label">หนังสือที่ถูกยืม</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $available_books ?></div>
            <div class="stat-label">หนังสือว่าง</div>
        </div>
    </div>

    <div class="container">
        <h2>ข้อมูลสมาชิก</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อผู้ใช้</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทร</th>
                    <th>วันที่สมัคร</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($member = $members_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $member['id'] ?></td>
                        <td><?= htmlspecialchars($member['username']) ?></td>
                        <td><?= htmlspecialchars($member['fullname']) ?></td>
                        <td><?= htmlspecialchars($member['email']) ?></td>
                        <td><?= htmlspecialchars($member['phone']) ?></td>
                        <td><?= date('d/m/Y', strtotime($member['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin: 0;">รายการหนังสือทั้งหมด</h2>
            <a href="admin_add_book.php" class="add-btn">+ เพิ่มหนังสือ</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อหนังสือ</th>
                    <th>ผู้แต่ง</th>
                    <th>ISBN</th>
                    <th>หมวดหมู่</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = $books_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['isbn']) ?></td>
                        <td><?= htmlspecialchars($book['category']) ?></td>
                        <td>
                            <?php if ($book['status'] == 'available'): ?>
                                <span class="status-badge status-available">ว่าง</span>
                            <?php else: ?>
                                <span class="status-badge status-borrowed">ถูกยืม</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $db->close(); ?>
