<?php
session_start();
include 'includes/config.php';

// Inisialisasi session untuk chat jika belum ada
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
    // Tambahkan dua pesan pembuka dari bot
    $_SESSION['chat_history'][] = [
        'type' => 'bot',
        'message' => 'Hi Sobat Vit! 👋 Apakah ada yang bisa dibantu?',
    ];
    $_SESSION['chat_history'][] = [
        'type' => 'bot',
        'message' => 'Silahkan beri tahu kami apa keluhan anda?',
    ];
}

// Ambil user_id dari session jika user sudah login
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Proses tombol "Clear Chat"
if (isset($_POST['clear_chat'])) {
    $_SESSION['chat_history'] = []; // Reset session chat

    // Tambahkan kembali dua pesan pembuka
    $_SESSION['chat_history'][] = [
        'type' => 'bot',
        'message' => 'Hi Sobat Vit! 👋 Apakah ada yang bisa dibantu?',
    ];
    $_SESSION['chat_history'][] = [
        'type' => 'bot',
        'message' => 'Silahkan beri tahu kami apa keluhan anda?',
    ];

    // Hapus semua chat terkait user_id dari database
    if ($user_id) {
        $stmt = $conn->prepare("DELETE FROM live_chats WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Tambahkan flag untuk menandai bahwa chat telah dihapus
    $_SESSION['chat_cleared'] = true;

    header("Location: " . $_SERVER['PHP_SELF']); // Refresh halaman
    exit();
}

// Proses input user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['clear_chat']) && isset($_POST['message'])) {
    unset($_SESSION['chat_cleared']); // Reset flag ketika user mulai chat baru

    $user_input = strtolower($_POST['message']);

    // Simpan input user ke chat history
    $_SESSION['chat_history'][] = [
        'type' => 'user',
        'message' => htmlspecialchars($user_input),
    ];

    // Query database untuk rekomendasi vitamin hanya jika user login
    if ($user_id) {
        $sql = "SELECT * FROM vitamins WHERE keywords LIKE :search";
        $stmt = $conn->prepare($sql);
        $search = "%" . $user_input . "%";
        $stmt->bindValue(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Balasan bot untuk rekomendasi vitamin
        if (count($result) > 0) {
            foreach ($result as $row) {
                // Modern Card Template for Recommended Product
                $bot_response = "
                <div class='bg-white border border-primary-100 rounded-2xl p-4 shadow-sm space-y-3 mt-2'>
                    <div class='flex gap-4 items-start'>
                        <div class='w-20 h-20 bg-primary-50 rounded-xl flex-shrink-0 flex items-center justify-center p-2'>
                            <img src='uploads/assets/{$row['image_url']}' alt='{$row['name']}' class='max-h-full'>
                        </div>
                        <div class='flex-1'>
                            <h4 class='font-display font-bold text-slate-900'>{$row['name']}</h4>
                            <p class='text-xs text-slate-500 line-clamp-2'>{$row['description']}</p>
                        </div>
                    </div>
                    <button onclick='showModal(\"{$row['name']}\", \"{$row['description']}\", \"uploads/assets/{$row['image_url']}\")' 
                            class='w-full py-2 bg-primary-50 text-primary-700 rounded-xl text-xs font-bold hover:bg-primary-100 transition-all'>
                        Lihat Detail
                    </button>
                </div>";

                $_SESSION['chat_history'][] = [
                    'type' => 'bot',
                    'message' => $bot_response,
                ];
            }
        } else {
            $_SESSION['chat_history'][] = [
                'type' => 'bot',
                'message' => 'Maaf ya, kami belum punya rekomendasi tepat untuk keluhan tersebut. Coba ceritakan keluhan lain?',
            ];
        }
    } else {
        $_SESSION['chat_history'][] = [
            'type' => 'bot',
            'message' => 'Ups! Silakan login terlebih dahulu untuk mendapatkan rekomendasi vitamin yang pas buat kamu.',
        ];
    }
}

// Sinkronisasi dengan database live_chats
if ($user_id && empty($_SESSION['chat_cleared'])) {
    // Logic ini memastikan pesan dari database masuk ke tampilan jika diperlukan
    // Namun untuk chatbot interaktif, biasanya kita rely pada session atau polling AJAX
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SobatVit AI - Your Personal Wellness Assistant</title>
    
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .chat-scroll::-webkit-scrollbar { width: 5px; }
        .chat-scroll::-webkit-scrollbar-track { background: transparent; }
        .chat-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .chat-scroll::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        
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
        
        .bot-bubble { animation: slideInLeft 0.3s ease-out; }
        .user-bubble { animation: slideInRight 0.3s ease-out; }
        @keyframes slideInLeft { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes slideInRight { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-100 shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <img src="uploads/logo/logo.png" alt="Logo" class="h-10 w-auto" />
                    <span class="text-2xl font-display font-bold tracking-tight text-primary-700">SobatVit</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php#home" class="font-medium hover:text-primary-600 transition-colors">Home</a>
                    <a href="index.php#catalogue" class="font-medium hover:text-primary-600 transition-colors">Catalogue</a>
                    <a href="index.php#about-us" class="font-medium hover:text-primary-600 transition-colors">About Us</a>
                </div>

                <div class="flex items-center gap-4">
                    <form method="POST" class="m-0">
                        <button type="submit" name="clear_chat" class="text-xs font-bold text-slate-400 hover:text-red-500 transition-colors uppercase tracking-widest">
                            Hapus Chat
                        </button>
                    </form>
                    <div class="h-6 w-px bg-slate-200 mx-2"></div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="bg-slate-900 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-slate-800 transition-all">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="bg-primary-600 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-primary-700 transition-all">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Chat Area -->
    <main class="flex-1 overflow-hidden relative flex justify-center py-6 px-4 pb-0 md:pb-6">
        <!-- Background Decor -->
        <div class="absolute top-1/4 left-10 w-64 h-64 bg-primary-200/20 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-1/4 right-10 w-64 h-64 bg-primary-300/20 rounded-full blur-3xl -z-10"></div>

        <div class="w-full max-w-3xl bg-white rounded-[2.5rem] shadow-2xl shadow-primary-900/5 border border-slate-100 flex flex-col overflow-hidden">
            <!-- Header -->
            <div class="p-6 bg-gradient-to-r from-primary-600 to-primary-500 text-white flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center p-2 backdrop-blur-md">
                            <img src="uploads/logo/logo.png" alt="Bot" class="w-full h-auto brightness-0 invert">
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 border-2 border-primary-600 rounded-full"></div>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg leading-none mb-1">SobatVit Assistant</h3>
                        <p class="text-primary-100 text-xs font-medium">Online & Ready to Help</p>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6 chat-scroll bg-slate-50/50" id="chat-body">
                <?php
                foreach ($_SESSION['chat_history'] as $chat) {
                    if ($chat['type'] == 'user') {
                        echo "
                        <div class='flex justify-end user-bubble'>
                            <div class='bg-primary-600 text-white px-5 py-3 rounded-2xl rounded-tr-none shadow-lg shadow-primary-600/10 max-w-[80%]'>
                                <p class='text-[15px] leading-relaxed'>{$chat['message']}</p>
                            </div>
                        </div>";
                    } else {
                        echo "
                        <div class='flex justify-start bot-bubble'>
                            <div class='bg-white border border-slate-100 text-slate-800 px-5 py-3 rounded-2xl rounded-tl-none shadow-sm max-w-[85%]'>
                                <div class='text-[15px] leading-relaxed'>{$chat['message']}</div>
                            </div>
                        </div>";
                    }
                }
                ?>
            </div>

            <!-- Input Area -->
            <div class="p-6 bg-white border-t border-slate-100 shrink-0">
                <form method="POST" action="" class="flex gap-3 mb-4">
                    <div class="relative flex-1">
                        <input type="text" name="message" id="chat-input" placeholder="Tuliskan keluhan anda (misal: pusing, sariawan)..." required
                               class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 focus:ring-2 focus:ring-primary-500/20 transition-all font-medium text-slate-600 placeholder:text-slate-400 outline-none">
                    </div>
                    <button type="submit" class="bg-primary-600 text-white p-4 rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-600/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    </button>
                </form>
                
                <div class="flex gap-2 items-center">
                    <div class="flex-1 relative">
                        <input type="text" id="live-chat-input" placeholder="Tanya admin langsung..." 
                               class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus:border-primary-500 transition-colors">
                    </div>
                    <button onclick="sendLiveMessage()" class="bg-slate-900 text-white px-4 py-2 rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-slate-800 transition-all">Kirim</button>
                    <button onclick="startLiveChat()" class="bg-primary-100 text-primary-700 px-4 py-2 rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-primary-200 transition-all whitespace-nowrap">Live Chat</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-6 px-4 shrink-0 mt-auto">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <img src="uploads/logo/logo.png" alt="Logo" class="h-6 brightness-0 invert" />
                <span class="text-lg font-display font-bold tracking-tight">SobatVit</span>
            </div>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest leading-none">
                &copy; 2024 Ultigar. Preserving Health, Every Day.
            </p>
        </div>
    </footer>

    <!-- Vitamin Modal -->
    <div id="vitamin-modal" class="modal flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md p-8 md:p-10 rounded-[2.5rem] shadow-2xl relative scale-100 transition-transform">
            <button onclick="closeModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 transition-colors p-2 bg-slate-50 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
            <div class="text-center space-y-6">
                <div class="w-32 h-32 bg-primary-50 rounded-[2rem] mx-auto flex items-center justify-center p-4 shadow-inner">
                    <img id="modal-image" src="" alt="" class="max-h-full drop-shadow-xl">
                </div>
                <div>
                    <h2 id="modal-title" class="text-2xl font-display font-bold text-slate-900 mb-2"></h2>
                    <p id="modal-description" class="text-slate-500 leading-relaxed text-sm"></p>
                </div>
                <div class="pt-4">
                    <a id="modal-link" href="https://collshp.com/apylotaa" target="_blank" class="block w-full bg-primary-600 text-white py-4 rounded-2xl font-bold hover:bg-primary-700 shadow-xl shadow-primary-600/20 transition-all flex items-center justify-center gap-3">
                        Cari di Toko Resmi
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showModal(title, description, imageUrl) {
            $('#modal-title').text(title);
            $('#modal-description').text(description);
            $('#modal-image').attr('src', imageUrl);
            $('#vitamin-modal').fadeIn(300).css('display', 'flex');
        }

        function closeModal() {
            $('#vitamin-modal').fadeOut(300);
        }

        function sendLiveMessage() {
            const message = $('#live-chat-input').val();
            if(!message.trim()) return;
            
            const chatBody = document.getElementById('chat-body');
            $(chatBody).append(`<div class='flex justify-end user-bubble'><div class='bg-slate-900 text-white px-5 py-3 rounded-2xl rounded-tr-none shadow-lg max-w-[80%]'><p class='text-[15px]'>${message}</p></div></div>`);
            $('#live-chat-input').val('');
            chatBody.scrollTop = chatBody.scrollHeight;

            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'message=' + encodeURIComponent(message)
            }).then(r => r.json()).then(data => {
                if(data.success) {
                    $(chatBody).append(`<div class='flex justify-start bot-bubble'><div class='bg-white border border-slate-100 text-slate-800 px-5 py-3 rounded-2xl rounded-tl-none shadow-sm max-w-[85%]'><p class='text-[15px]'>${data.response}</p></div></div>`);
                    chatBody.scrollTop = chatBody.scrollHeight;
                }
            });
        }

        function startLiveChat() {
            const chatBody = document.getElementById('chat-body');
            $(chatBody).append(`<div class='flex justify-start bot-bubble'><div class='bg-primary-50 border border-primary-100 text-primary-800 px-5 py-3 rounded-2xl rounded-tl-none shadow-sm max-w-[85%]'><p class='text-[15px] font-medium'>Menghubungkan Anda dengan admin...</p></div></div>`);
            chatBody.scrollTop = chatBody.scrollHeight;

            fetch('start_live_chat.php', {
                method: 'POST'
            }).then(r => r.json()).then(data => {
                if(data.success) {
                    $(chatBody).append(`<div class='flex justify-start bot-bubble'><div class='bg-white border border-slate-100 text-slate-800 px-5 py-3 rounded-2xl rounded-tl-none shadow-sm max-w-[85%] font-medium'><p class='text-[15px]'>${data.message}</p></div></div>`);
                    chatBody.scrollTop = chatBody.scrollHeight;
                }
            });
        }

        function loadChat() {
            $.ajax({
                url: 'fetch_chat.php',
                method: 'GET',
                success: function (data) {
                    if(data.trim().length > 0) {
                        // Logic to append only new messages can be added here
                    }
                }
            });
        }

        $(document).ready(function () {
            const chatBody = document.getElementById('chat-body');
            chatBody.scrollTop = chatBody.scrollHeight;
            setInterval(loadChat, 3000);
        });

        window.onclick = function(event) {
            if (event.target == document.getElementById('vitamin-modal')) closeModal();
        }
    </script>
</body>
</html>