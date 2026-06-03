<?php
session_start();
include('includes/config.php');

// Jika user belum login, arahkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Role-based logic (opsional)
if ($_SESSION['role'] === 'admin') {
    header('Location: admin/dashboard.php');
    exit();
}

// Fetch images for slider (untuk cadangan jika query bawah berbeda)
$stmt = $conn->prepare("SELECT name, description, image_url FROM vitamins");
$stmt->execute();
$slider_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch semua produk dari database untuk katalog
$stmt_prod = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
$stmt_prod->execute();
$products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultigar - Premium Vitamin & Wellness</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN for PHP compatibility) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
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

    <!-- jQuery & Slick Slider -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <style>
        .slick-dots { bottom: 25px; }
        .slick-dots li button:before { color: #16a34a; opacity: 0.3; font-size: 12px; }
        .slick-dots li.slick-active button:before { color: #16a34a; opacity: 1; }
        
        .product-card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        
        body {
            background-color: #f8fafc;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-900">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <img src="uploads/logo/logo.png" alt="Logo" class="h-10 w-auto" />
                    <span class="text-2xl font-display font-bold tracking-tight text-primary-700">ULTIGAR</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="font-medium hover:text-primary-600 transition-colors">Home</a>
                    <a href="#catalogue" class="font-medium hover:text-primary-600 transition-colors">Catalogue</a>
                    <a href="#about-us" class="font-medium hover:text-primary-600 transition-colors">About Us</a>
                    <a href="chatbot.php" class="bg-primary-50 text-primary-700 px-4 py-2 rounded-full font-semibold border border-primary-100 hover:bg-primary-100 transition-all flex items-center gap-2">
                        <span>AI Chatbot</span>
                        <div class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></div>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <a href="admin/dashboard.php" class="text-sm font-bold text-slate-600 hover:text-primary-600">Dashboard</a>
                        <span class="text-slate-300">|</span>
                        <a href="logout.php" class="text-sm font-bold text-red-500">Logout</a>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="bg-slate-900 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-slate-800 transition-all">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-sm font-bold text-slate-600 hover:text-primary-600">Login</a>
                        <a href="register.php" class="bg-primary-600 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-primary-700 transition-all">Join Now</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Home Section (Hero Slider) -->
    <section id="home" class="relative py-12 md:py-20 px-4 overflow-hidden">
        <!-- Global Welcome Message -->
        <div class="max-w-6xl mx-auto mb-16 text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary-50 text-primary-700 rounded-full text-xs font-bold uppercase tracking-widest border border-primary-100">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                </span>
                New Collection 2026
            </div>
            <h1 class="text-5xl md:text-7xl font-display font-bold text-slate-900 tracking-tight">
                Welcome to <span class="text-primary-600">Ultigar</span>
            </h1>
            <p class="text-slate-500 text-lg md:text-xl max-w-2xl mx-auto font-medium leading-relaxed">
                Your daily dose of nature's finest. Discover premium botanical vitamins crafted to elevate your health and fuel your daily energy.
            </p>
        </div>

        <div class="max-w-6xl mx-auto">
            <div class="custom-slider shadow-2xl shadow-primary-900/10 rounded-[2.5rem] overflow-hidden bg-white border border-slate-100">
                <?php
                if (count($slider_results) > 0) {
                    foreach ($slider_results as $row) {
                        echo '<div class="relative min-h-[450px] md:min-h-[550px] outline-none">';
                        echo '  <div class="flex flex-col md:flex-row items-center justify-between p-10 md:p-20 gap-10 bg-gradient-to-br from-primary-50/30 to-white">';
                        echo '      <div class="md:w-1/2 space-y-8 order-2 md:order-1">';
                        echo '          <div class="space-y-4">';
                        echo '              <h2 class="text-4xl md:text-6xl font-display font-bold text-slate-900 leading-tight">' . htmlspecialchars($row['name']) . '</h2>';
                        echo '              <p class="text-lg md:text-xl text-slate-500 leading-relaxed max-w-md font-medium">' . htmlspecialchars($row['description']) . '</p>';
                        echo '          </div>';
                        echo '          <div class="flex flex-wrap gap-4 items-center">';
                        echo '              <button class="bg-primary-600 text-white px-10 py-5 rounded-2xl font-bold hover:bg-primary-700 shadow-xl shadow-primary-600/20 transition-all flex items-center gap-3">Order Now &rarr;</button>';
                        echo '              <div class="flex -space-x-3">';
                        echo '                  <div class="w-10 h-10 rounded-full border-4 border-white bg-slate-200"></div>';
                        echo '                  <div class="w-10 h-10 rounded-full border-4 border-white bg-slate-300"></div>';
                        echo '                  <div class="w-10 h-10 rounded-full border-4 border-white bg-slate-400 flex items-center justify-center text-[10px] font-bold">+1k</div>';
                        echo '              </div>';
                        echo '              <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Happy Users</span>';
                        echo '          </div>';
                        echo '      </div>';
                        echo '      <div class="md:w-1/2 flex justify-center order-1 md:order-2">';
                        echo '          <div class="relative group cursor-pointer">';
                        echo '              <div class="absolute -inset-10 bg-primary-300/20 rounded-full blur-3xl group-hover:bg-primary-400/30 transition-colors duration-700"></div>';
                        echo '              <img src="uploads/assets/' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '" class="relative w-72 md:w-96 h-auto drop-shadow-[0_35px_35px_rgba(0,0,0,0.15)] transition-transform duration-700 group-hover:scale-105 group-hover:-rotate-3">';
                        echo '          </div>';
                        echo '      </div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="p-20 text-center text-slate-400">No active promotions available.</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Catalogue Section -->
    <section id="catalogue" class="py-24 px-4 bg-white relative overflow-hidden">
        <!-- Background Patterns -->
        <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-primary-50/50 to-transparent"></div>
        <div class="absolute top-20 -left-20 w-64 h-64 bg-primary-100/20 rounded-full blur-3xl"></div>
        <div class="absolute top-40 -right-20 w-96 h-96 bg-primary-200/20 rounded-full blur-3xl"></div>
        
        <!-- SVG Pattern Overlay -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%2316a34a\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col items-center mb-16 text-center space-y-4">
                <span class="text-primary-600 font-bold uppercase tracking-[0.2em] text-sm">Vital Essentials</span>
                <h2 class="text-4xl md:text-5xl font-display font-bold text-slate-900">Our Premium Selection</h2>
                <div class="w-20 h-1.5 bg-primary-500 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($products as $product): ?>
                <div class="group product-card-hover bg-slate-50 border border-slate-100 p-6 rounded-3xl transition-all duration-500 cursor-pointer" onclick="showProductDetails(<?php echo $product['id']; ?>)">
                    <div class="aspect-square bg-white rounded-2xl mb-6 overflow-hidden flex items-center justify-center p-4">
                        <img src="uploads/products/<?php echo basename($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="space-y-4 text-center">
                        <h3 class="text-xl font-display font-bold text-slate-800 tracking-tight group-hover:text-primary-600 transition-colors"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="text-sm text-slate-500 font-medium">Click to view details</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about-us" class="py-24 px-4 bg-primary-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary-400/10 rounded-full blur-[100px] -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary-400/10 rounded-full blur-[100px] -ml-48 -mb-48"></div>
        
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2">
                    <h2 class="text-4xl lg:text-6xl font-display font-bold text-white mb-8 leading-tight">Your Trusted <span class="text-primary-400">Wellness Partner</span>.</h2>
                    <p class="text-primary-100 text-lg mb-8 leading-relaxed opacity-90">
                        Welcome to Ultigar, your trusted partner in health and wellness. We are dedicated to providing high-quality vitamins that support your daily health needs and improve your overall well-being. With a commitment to excellence and a passion for health, Ultigar is here to help you lead a healthier, happier life.
                    </p>
                    <div class="grid grid-cols-2 gap-8 mb-12">
                        <div>
                            <div class="text-3xl font-bold text-white mb-1">2024</div>
                            <div class="text-primary-300 text-sm font-medium uppercase tracking-widest">Established</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-white mb-1">100%</div>
                            <div class="text-primary-300 text-sm font-medium uppercase tracking-widest">Purity Guaranteed</div>
                        </div>
                    </div>
                    <button onclick="openPDF()" class="group bg-white text-primary-900 px-10 py-5 rounded-2xl font-bold hover:bg-primary-50 transition-all flex items-center gap-3 shadow-2xl shadow-black/30 text-lg">
                        Ketahui produk kami
                        <span class="group-hover:translate-x-2 transition-transform">&rarr;</span>
                    </button>
                </div>
                
                <div class="lg:w-1/2 flex justify-center">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-primary-400 to-primary-600 rounded-[2.5rem] blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                        <div class="relative bg-primary-800 p-8 rounded-[2.5rem] border border-primary-700">
                            <img src="uploads/logo/logo.png" alt="Ultigar Logo" class="w-64 h-auto brightness-0 invert opacity-40">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer" class="bg-slate-900 text-white pt-24 pb-12 px-4 border-t border-slate-800">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16 mb-20">
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <img src="uploads/logo/logo.png" alt="Logo" class="h-10 brightness-0 invert" />
                        <span class="text-2xl font-display font-bold tracking-tight">ULTIGAR</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed pr-8">
                        Elevating daily health through botanical excellence and scientific purity. Your journey to wellness begins here.
                    </p>
                </div>
                
                <div class="space-y-6">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-primary-400">Quick Links</h3>
                    <ul class="space-y-4 text-slate-300">
                        <li><a href="#home" class="hover:text-white transition-colors">Hero Spotlight</a></li>
                        <li><a href="#catalogue" class="hover:text-white transition-colors">Vitamin Guide</a></li>
                        <li><a href="#about-us" class="hover:text-white transition-colors">Our Philosophy</a></li>
                        <li><a href="chatbot.php" class="hover:text-white transition-colors">AI consultation</a></li>
                    </ul>
                </div>

                <div class="space-y-6">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-primary-400">Storefront</h3>
                    <p class="text-slate-400 text-sm">Stay updated with our latest news and offers on our marketplace.</p>
                    <a href="https://collshp.com/apylotaa" target="_blank" class="flex items-center gap-4 bg-[#FF4D2D]/10 text-[#FF4D2D] p-3 rounded-2xl border border-[#FF4D2D]/20 hover:bg-[#FF4D2D]/20 transition-all w-fit">
                        <img src="https://img.icons8.com/color/48/shopee.png" alt="Shopee" class="w-8 h-8">
                        <span class="font-bold pr-4">Visit Shopee Official</span>
                    </a>
                </div>
            </div>
            
            <div class="pt-12 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-slate-500 text-sm font-medium">
                    &copy; 2024 Ultigar. Preserving Health, Every Day.
                </p>
                <div class="flex gap-4">
                    <div class="w-3 h-3 rounded-full bg-green-500/20 border border-green-500/40"></div>
                    <span class="text-xs text-slate-500 font-bold tracking-widest uppercase">Certified Pure</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Product Detail Modal -->
    <div id="product-modal" class="modal flex items-center justify-center p-4">
        <div class="modal-content relative bg-white w-full max-w-4xl p-10 md:p-16 rounded-[2.5rem] shadow-2xl overflow-hidden scale-95 transition-transform duration-300">
            <button onclick="closeModal()" class="absolute top-8 right-8 text-slate-400 hover:text-slate-900 transition-colors p-2 bg-slate-50 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <div id="modal-details" class="space-y-6">
                <!-- Data will be injected here via script.js -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    $(document).ready(function(){
        $('.custom-slider').slick({
            arrows: false,
            dots: true,
            infinite: true,
            speed: 800,
            fade: true,
            cssEase: 'cubic-bezier(0.7, 0, 0.3, 1)',
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 4000,
        });
    });

    function showProductDetails(productId) {
        // Asumsi script.js anda menghandle ini, atau gunakan fetch di sini
        $('#product-modal').fadeIn(300).css('display', 'flex').find('.modal-content').addClass('scale-100').removeClass('scale-95');
        
        // Contoh injection data (sesuaikan dengan logic script.js anda)
        // $('#modal-details').html('<div class="animate-pulse">Loading...</div>');
    }

    function closeModal() {
        $('.modal-content').removeClass('scale-100').addClass('scale-95');
        $('#product-modal').fadeOut(300);
    }

    function openPDF() {
        window.open('uploads/file/ultigar.pdf', 'popup', 'width=600,height=800');
    }

    // Close modal on click outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('product-modal')) {
            closeModal();
        }
    }
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>
