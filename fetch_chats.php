<?php
session_start();
include 'includes/config.php';

$user_id = $_SESSION['user_id'] ?? 0; // Pastikan user_id ada di session
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM live_chats WHERE user_id = :user_id ORDER BY created_at ASC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [];
    foreach ($chats as $chat) {
        $response[] = [
            'type' => $chat['sender'] === 'admin' ? 'bot' : 'user',
            'message' => htmlspecialchars($chat['message']),
        ];
    }
    echo json_encode($response);
}
?>
