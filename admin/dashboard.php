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
        mkdir($folderPath, 0777, true);
    }
}

// Validasi ekstensi file yang diperbolehkan
function isValidFile($fileName) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    return in_array($fileExtension, $allowedExtensions);
}

$statusMessage = '';
$statusType = '';

if (isset($_SESSION['flash_message'])) {
    $statusMessage = $_SESSION['flash_message'];
    $statusType = 'success';
    unset($_SESSION['flash_message']);
}

// Upload Vitamin Images (Slider)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image_slider'])) {
    $image = $_FILES['image_slider'];
    $targetDir = "../uploads/products/";
    ensureFolderExists($targetDir);
    $targetFile = $targetDir . basename($image['name']);
    
    if (!isValidFile($image['name'])) {
        $statusMessage = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        $statusType = "error";
    } elseif (move_uploaded_file($image['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO vitamin_images (image_path) VALUES (:image_path)");
        $stmt->bindParam(':image_path', $targetFile);
        $stmt->execute();
        $statusMessage = "Slider image uploaded successfully.";
        $statusType = "success";
    } else {
        $statusMessage = "Failed to upload slider image.";
        $statusType = "error";
    }
}

// Add Product to Catalogue
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $shopee_link = $_POST['shopee_link'];

    $image = $_FILES['product_image'];
    $targetDir = "../uploads/products/";
    ensureFolderExists($targetDir);
    $targetFile = $targetDir . basename($image['name']);
    
    if (!isValidFile($image['name'])) {
        $statusMessage = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        $statusType = "error";
    } elseif (move_uploaded_file($image['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_path, shopee_link) 
                                VALUES (:name, :description, :price, :image_path, :shopee_link)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image_path', $targetFile);
        $stmt->bindParam(':shopee_link', $shopee_link);
        $stmt->execute();
        $statusMessage = "Product added successfully.";
        $statusType = "success";
    } else {
        $statusMessage = "Failed to upload product image.";
        $statusType = "error";
    }
}

// Get products for dropdowns
$stmt_list = $conn->prepare("SELECT id, name FROM products ORDER BY created_at DESC");
$stmt_list->execute();
$products = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

// Get Chat history
$stmt_chat = $conn->prepare("SELECT * FROM live_chats ORDER BY created_at ASC");
$stmt_chat->execute();
$chats = $stmt_chat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultigar HQ - Admin Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac', 400: '#4ade80',
                            500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">

    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside class="w-72 bg-slate-900 text-white shrink-0 hidden lg:flex flex-col border-r border-slate-800">
            <div class="p-8 border-b border-white/5 flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center p-2">
                    <img src="../uploads/logo/logo.png" alt="Logo" class="brightness-0 invert w-full">
                </div>
                <span class="font-display font-bold text-xl tracking-tight">ULTIGAR <small class="text-[10px] opacity-40 uppercase ml-1">Admin</small></span>
            </div>
            
            <nav class="flex-1 p-6 space-y-2">
                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-primary-600 text-white rounded-xl font-bold shadow-lg shadow-primary-600/20 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    Dashboard
                </a>
                <a href="../index.php" target="_blank" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7"/><path d="M16 19l2 2 4-4"/></svg>
                    Lihat Website
                </a>
                <div class="pt-6 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest pl-4">Management</div>
                <a href="#catalogue-form" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.27 6.96 8.73 5.04 8.73-5.04"/><path d="M12 22.08V12"/></svg>
                    Catalogue
                </a>
                <a href="#inquiries" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                    Inquiries
                </a>
            </nav>
            
            <div class="p-6 border-t border-white/5">
                <a href="../logout.php" class="flex items-center justify-center gap-3 px-4 py-4 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-2xl transition-all font-bold text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout System
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-4 md:p-10">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
                <div>
                    <h1 class="text-3xl md:text-4xl font-display font-bold text-slate-900 tracking-tight">Admin Dashboard</h1>
                    <p class="text-slate-500 font-medium">Monitoring and managing Ultigar wellness ecosystem.</p>
                </div>
                <div class="flex items-center gap-3 bg-white px-4 py-2 border border-slate-200 rounded-[1.25rem] shadow-sm">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-bold text-slate-700">Live Status: Secure</span>
                </div>
            </div>

            <!-- Status Alerts -->
            <?php if($statusMessage): ?>
            <div class="<?= $statusType === 'success' ? 'bg-primary-50 border-primary-200 text-primary-700' : 'bg-red-50 border-red-200 text-red-700' ?> border rounded-2xl p-4 mb-8 flex items-center gap-3 font-bold animate-in fade-in duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                <?= $statusMessage ?>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Forms -->
                <div class="xl:col-span-12 space-y-8">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Add Product Card -->
                        <section id="catalogue-form" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col">
                            <div class="p-8 bg-slate-50/50 border-b border-slate-100">
                                <h2 class="text-2xl font-display font-bold text-slate-900">Add Product</h2>
                                <p class="text-sm text-slate-500">Insert new item into the vitamins catalogue.</p>
                            </div>
                            <form method="post" enctype="multipart/form-data" class="p-8 space-y-5">
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Product Name</label>
                                        <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary-500/20 transition-all">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Price</label>
                                        <input type="number" step="0.01" name="price" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary-500/20 transition-all">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Description</label>
                                    <textarea name="description" required rows="3" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary-500/20 transition-all resize-none"></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Upload Image</label>
                                    <input type="file" name="product_image" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Shopee Link</label>
                                    <input type="url" name="shopee_link" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-300" placeholder="https://shopee.co.id/...">
                                </div>
                                <button type="submit" name="add_product" class="w-full bg-primary-600 text-white py-4 rounded-2xl font-bold hover:bg-primary-700 shadow-xl shadow-primary-600/20 transition-all">Add to Catalogue</button>
                            </form>
                        </section>

                        <!-- Secondary Controls -->
                        <div class="space-y-8">
                            <!-- Edit Product Selector -->
                            <section class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden p-8">
                                <h3 class="text-xl font-display font-bold text-slate-900 mb-6 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg></span>
                                    Modify Existing Product
                                </h3>
                                <form method="post" enctype="multipart/form-data" action="edit_catalogue.php" class="space-y-4">
                                    <select name="product_id" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 outline-none cursor-pointer hover:border-primary-200 transition-colors">
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="edit_product" class="w-full border-2 border-primary-600 text-primary-600 py-3 rounded-xl font-bold hover:bg-primary-50 transition-all">Update Product Info</button>
                                </form>
                            </section>

                            <!-- Delete Product -->
                            <section class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden p-8">
                                <h3 class="text-xl font-display font-bold text-slate-910 mb-6 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></span>
                                    Danger Zone
                                </h3>
                                <form method="post" action="edit_catalogue.php" class="space-y-4">
                                    <select name="delete_product_id" required class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 outline-none cursor-pointer">
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="delete_product" onclick="return confirm('Hapus produk ini secara permanen?');" class="w-full bg-red-50 text-red-600 py-3 rounded-xl font-bold hover:bg-red-100 transition-all">Delete Product</button>
                                </form>
                            </section>
                        </div>
                    </div>

                    <!-- User Inquiries / Chat -->
                    <section id="inquiries" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden lg:col-span-2">
                        <div class="p-8 bg-slate-900 text-white flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-display font-bold">Inquiry Management</h2>
                                <p class="text-slate-400 text-xs font-medium uppercase tracking-widest mt-1">Live customer support panel</p>
                            </div>
                            <div class="px-4 py-2 bg-white/10 rounded-xl border border-white/10 font-bold text-sm">
                                <?= count($chats) ?> Messages
                            </div>
                        </div>
                        <div class="h-[400px] overflow-y-auto p-8 space-y-6 bg-slate-100/50" id="live-chat">
                            <?php
                            $last_user_id = 0;
                            foreach ($chats as $chat):
                                $isAdmin = ($chat['sender'] === 'admin');
                                if (!$isAdmin) $last_user_id = $chat['user_id'];
                            ?>
                                <div class="flex <?= $isAdmin ? 'justify-end' : 'justify-start' ?>">
                                    <div class="max-w-[70%] <?= $isAdmin ? 'bg-primary-600 text-white rounded-2xl rounded-tr-none' : 'bg-white text-slate-800 rounded-2xl rounded-tl-none border border-slate-200' ?> px-6 py-4 shadow-sm">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-[10px] font-bold uppercase tracking-widest opacity-60"><?= $isAdmin ? 'Admin Agent' : 'User #'.$chat['user_id'] ?></span>
                                            <span class="text-[9px] opacity-40"><?= date('H:i', strtotime($chat['created_at'])) ?></span>
                                        </div>
                                        <p class="text-sm leading-relaxed font-medium"><?= htmlspecialchars($chat['message']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <form id="chat-reply" method="POST" action="reply_chat.php" class="p-6 bg-white border-t border-slate-100 flex gap-4">
                            <input type="hidden" name="user_id" value="<?= $last_user_id; ?>">
                            <div class="flex-1">
                                <textarea name="message" placeholder="Tulis balasan profesional untuk pengguna ini..." required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 outline-none focus:ring-2 focus:ring-primary-500/20 transition-all resize-none h-14"></textarea>
                            </div>
                            <button type="submit" class="bg-primary-600 text-white px-8 rounded-2xl font-bold hover:bg-primary-700 shadow-xl shadow-primary-600/20 transition-all flex items-center gap-2">
                                Reply
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                            </button>
                        </form>
                    </section>

                </div>
            </div>
            
            <footer class="mt-20 pt-10 border-t border-slate-200 text-center">
                <p class="text-slate-400 text-sm font-medium uppercase tracking-[0.2em]">&copy; 2024 Ultigar Wellness • Admin Console v2.0</p>
            </footer>
        </main>
    </div>

    <script>
        // Scroll live chat to bottom
        const chatContainer = document.getElementById('live-chat');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    </script>
</body>
</html>