<?php
session_start();
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'] ?? '';
    $user_id = $_SESSION['user_id'];

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO live_chats (user_id, message, sender) VALUES (:user_id, :message, 'user')");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(['success' => true, 'response' => 'Pesan Anda telah terkirim ke admin.']);
    } else {
        echo json_encode(['success' => false, 'response' => 'Pesan tidak boleh kosong.']);
    }
}
?>
