<?php
session_start();
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'] ?? '';
    $user_id = $_POST['user_id'] ?? 0;

    // Validasi input
    if (!empty($message) && $user_id > 0) {
        $stmt = $conn->prepare("INSERT INTO live_chats (user_id, message, sender) VALUES (:user_id, :message, 'admin')");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();

        // Redirect kembali ke dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        echo "Pesan atau user ID tidak valid.";
    }
}
?>
