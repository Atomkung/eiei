<?php
require_once 'config.php';

if (!isset($_SESSION['member_id'])) {
    header('Location: member_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['borrow_id'])) {
    $member_id = $_SESSION['member_id'];
    $borrow_id = intval($_POST['borrow_id']);
    
    $db = getDB();
    
    // ตรวจสอบว่าเป็นการยืมของสมาชิกคนนี้และยังไม่ได้คืน
    $stmt = $db->prepare("SELECT book_id FROM borrow_history WHERE id = ? AND member_id = ? AND status = 'borrowed'");
    $stmt->bind_param("ii", $borrow_id, $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $borrow = $result->fetch_assoc();
        $book_id = $borrow['book_id'];
        
        // เริ่ม transaction
        $db->begin_transaction();
        
        try {
            // อัพเดทสถานะการคืน
            $return_date = date('Y-m-d');
            $stmt = $db->prepare("UPDATE borrow_history SET return_date = ?, status = 'returned' WHERE id = ?");
            $stmt->bind_param("si", $return_date, $borrow_id);
            $stmt->execute();
            
            // อัพเดทสถานะหนังสือให้ว่าง
            $stmt = $db->prepare("UPDATE books SET status = 'available' WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            
            $db->commit();
            $_SESSION['success_message'] = 'คืนหนังสือสำเร็จ!';
        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาด กรุณาลองใหม่';
        }
    } else {
        $_SESSION['error_message'] = 'ไม่พบข้อมูลการยืม';
    }
    
    $stmt->close();
    $db->close();
}

header('Location: borrow_history.php');
exit;
?>
