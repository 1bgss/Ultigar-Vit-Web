<?php
session_start();
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    echo json_encode(['success' => true, 'message' => 'Live chat telah dimulai. Admin akan membalas pesan Anda.']);
}
?>
