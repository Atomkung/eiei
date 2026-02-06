<?php
require_once 'config.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: member_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $member_id = $_SESSION['member_id'];
    $book_id = intval($_POST['book_id']);
    
    $db = getDB();
    
    // ตรวจสอบว่าหนังสือยังว่างอยู่หรือไม่
    $stmt = $db->prepare("SELECT status FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        
        if ($book['status'] == 'available') {
            // เริ่ม transaction
            $db->begin_transaction();
            
            try {
                // บันทึกการยืม
                $borrow_date = date('Y-m-d');
                $stmt = $db->prepare("INSERT INTO borrow_history (member_id, book_id, borrow_date, status) VALUES (?, ?, ?, 'borrowed')");
                $stmt->bind_param("iis", $member_id, $book_id, $borrow_date);
                $stmt->execute();
                
                // อัพเดทสถานะหนังสือ
                $stmt = $db->prepare("UPDATE books SET status = 'borrowed' WHERE id = ?");
                $stmt->bind_param("i", $book_id);
                $stmt->execute();
                
                $db->commit();
                $_SESSION['success_message'] = 'ยืมหนังสือสำเร็จ!';
            } catch (Exception $e) {
                $db->rollback();
                $_SESSION['error_message'] = 'เกิดข้อผิดพลาด กรุณาลองใหม่';
            }
        } else {
            $_SESSION['error_message'] = 'หนังสือเล่มนี้ถูกยืมไปแล้ว';
        }
    }
    
    $stmt->close();
    $db->close();
}

header('Location: member_dashboard.php');
exit;
?>
