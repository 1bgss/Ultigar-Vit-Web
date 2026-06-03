<?php
session_start();
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Arahkan berdasarkan role
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = "The password you entered is incorrect.";
        }
    } else {
        $error = "Username not found in our records.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ultigar Premium Wellness</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    
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
<body class="font-sans antialiased bg-primary-900 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">

    <!-- Decorative elements -->
    <div class="absolute top-0 right-0 w-full h-full opacity-20 pointer-events-none">
        <div class="absolute -top-48 -right-48 w-full h-full bg-primary-400/30 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-48 -left-48 w-full h-full bg-primary-600/20 rounded-full blur-[120px]"></div>
    </div>
    
    <div class="w-full max-w-md relative z-10">
        <!-- Logo / Brand -->
        <div class="text-center mb-10 space-y-4">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-xl rounded-[2rem] p-4 border border-white/20 shadow-2xl">
                <img src="uploads/logo/logo.png" alt="Ultigar" class="w-full h-auto brightness-0 invert">
            </div>
            <h1 class="text-4xl font-display font-bold text-white tracking-tight">Welcome Back</h1>
            <p class="text-primary-200/60 font-medium">Please sign in to access your dashboard.</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-black/40 border border-white/10 relative overflow-hidden">
            <!-- Alert Section -->
            <?php if (isset($error)): ?>
                <div class="mb-8 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-3 animate-bounce">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <p class="text-red-600 text-xs font-bold uppercase tracking-wider"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Username</label>
                    <div class="relative">
                        <input type="text" name="username" required placeholder="Enter username" 
                               class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 focus:ring-2 focus:ring-primary-500/20 transition-all outline-none font-medium text-slate-700 placeholder:text-slate-300">
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">Password</label>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" required placeholder="••••••••" 
                               class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 focus:ring-2 focus:ring-primary-500/20 transition-all outline-none font-medium text-slate-700 placeholder:text-slate-300">
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-primary-600 text-white py-5 rounded-2xl font-bold hover:bg-primary-700 shadow-xl shadow-primary-600/30 transition-all active:scale-[0.98] text-lg">
                        Access Account
                    </button>
                </div>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-50 text-center">
                <p class="text-slate-400 text-sm font-medium">
                    Don't have an account? 
                    <a href="register.php" class="text-primary-600 font-bold hover:underline decoration-primary-500/30 underline-offset-4 ml-1">Create Now</a>
                </p>
            </div>
        </div>

        <!-- Simple Footer -->
        <div class="mt-10 text-center">
            <p class="text-primary-200/30 text-[10px] font-bold uppercase tracking-[0.3em]">
                &copy; 2024 Ultigar Wellness &bull; Secure Authentication
            </p>
        </div>
    </div>
</body>
</html>
