<?php
session_start();
include('../includes/config.php');

// Periksa apakah user login sebagai admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Fungsi untuk memastikan folder ada
function ensureFolderExists($folderPath) {
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0777, true); // Buat folder dengan izin penuh
    }
}

// Validasi ekstensi file yang diperbolehkan
function isValidFile($fileName) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    return in_array($fileExtension, $allowedExtensions);
}

// Proses update produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $shopee_link = $_POST['shopee_link'];

    // Jika ada gambar baru yang diunggah
    $image_path = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['product_image'];
        $targetDir = "../uploads/products/";
        ensureFolderExists($targetDir);
        $targetFile = $targetDir . basename($image['name']);

        if (!isValidFile($image['name'])) {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            exit();
        } elseif (move_uploaded_file($image['tmp_name'], $targetFile)) {
            $image_path = $targetFile;
        } else {
            echo "Failed to upload product image.";
            exit();
        }
    }

    // Update database
    $query = "UPDATE products SET name = :name, description = :description, price = :price, shopee_link = :shopee_link";
    if ($image_path) {
        $query .= ", image_path = :image_path";
    }
    $query .= " WHERE id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':shopee_link', $shopee_link);
    $stmt->bindParam(':id', $product_id);

    if ($image_path) {
        $stmt->bindParam(':image_path', $image_path);
    }

    if ($stmt->execute()) {
        echo "Product updated successfully.";
    } else {
        echo "Failed to update product.";
    }
}
// Proses delete produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $delete_product_id = $_POST['delete_product_id'];

    // Hapus gambar produk jika ada
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = :id");
    $stmt->bindParam(':id', $delete_product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && !empty($product['image_path']) && file_exists($product['image_path'])) {
        unlink($product['image_path']); // Hapus file gambar
    }

    // Hapus data produk dari database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $delete_product_id);

    if ($stmt->execute()) {
        echo "Product deleted successfully.";
    } else {
        echo "Failed to delete product.";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    // (Kode untuk proses update)

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Product updated successfully.";
    } else {
        $_SESSION['flash_message'] = "Failed to update product.";
    }

    header('Location: dashboard.php'); // Redirect ke dashboard
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    // (Kode untuk proses delete)

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Product deleted successfully.";
    } else {
        $_SESSION['flash_message'] = "Failed to delete product.";
    }

    header('Location: dashboard.php'); // Redirect ke dashboard
    exit();
}


?>

