<?php
include 'includes/config.php';

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $stmt = $conn->prepare("SELECT name, description, shopee_link FROM vitamins WHERE id = :id");
    $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Cari gambar di folder uploads/products
        $directory = '../uploads/products/';
        $images = glob($directory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        foreach ($images as $image) {
            $imageName = basename($image);
            if (strpos($imageName, (string)$productId) !== false) {
                $product['image_path'] = $imageName;
                break;
            }
        }
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
}
?>
