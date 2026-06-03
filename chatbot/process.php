<?php
include('../includes/config.php');

session_start();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);

    // Query untuk mencari keyword
    $stmt = $conn->prepare("SELECT response FROM keywords WHERE keyword = :keyword");
    $stmt->bindParam(':keyword', $message);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetch(PDO::FETCH_ASSOC)['response'];
    } else {
        $response = "Maaf, saya belum mengerti pertanyaan Anda.";
    }

    // Simpan riwayat chat
    $insertChat = $conn->prepare("INSERT INTO chat_history (user_id, message, response) VALUES (:user_id, :message, :response)");
    $insertChat->bindParam(':user_id', $user_id);
    $insertChat->bindParam(':message', $message);
    $insertChat->bindParam(':response', $response);
    $insertChat->execute();

    echo json_encode(['response' => $response]);
}
?>
