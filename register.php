<?php
include('includes/config.php');

$status_message = "";
$status_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password for security

    // Check if username already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
    $check_stmt->bindParam(':username', $username);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        $status_message = "Username is already taken. Please try another one.";
        $status_type = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            $status_message = "Registration successful! You can now <a href='login.php' class='underline font-bold'>login here</a>.";
            $status_type = "success";
        } else {
            $status_message = "An error occurred during registration. Please try again.";
            $status_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Ultigar - Premium Wellness Community</title>
    
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
<body class="font-sans antialiased bg-slate-50 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">

    <!-- Background Patterns -->
    <div class="absolute inset-0 z-0 pointer-events-none opacity-40">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_20%_20%,_var(--tw-gradient-stops))] from-primary-100 via-transparent to-transparent"></div>
        <div class="absolute bottom-0 right-0 w-full h-full bg-[radial-gradient(circle_at_80%_80%,_var(--tw-gradient-stops))] from-primary-100 via-transparent to-transparent"></div>
    </div>
    
    <div class="w-full max-w-lg relative z-10">
        <!-- Logo / Brand -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-3xl shadow-xl p-3 mb-6 border border-slate-100">
                <img src="uploads/logo/logo.png" alt="Ultigar" class="w-full h-auto">
            </div>
            <h1 class="text-4xl font-display font-bold text-slate-900 tracking-tight mb-2">Create Account</h1>
            <p class="text-slate-500 font-medium">Start your journey to holistic wellness today.</p>
        </div>

        <!-- Register Card -->
        <div class="bg-white rounded-[3rem] p-10 md:p-14 shadow-2xl shadow-primary-900/10 border border-white">
            
            <!-- Alert Section -->
            <?php if ($status_message): ?>
                <div class="mb-10 p-5 rounded-2xl flex items-start gap-4 <?= $status_type === 'success' ? 'bg-primary-50 border border-primary-100 text-primary-700' : 'bg-red-50 border border-red-100 text-red-600' ?>">
                    <div class="mt-1">
                        <?php if($status_type === 'success'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm font-semibold leading-relaxed"><?= $status_message ?></p>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-8">
                <div class="grid grid-cols-1 gap-6">
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] px-1">Desired Username</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <input type="text" name="username" required placeholder="SobatSehat24" 
                                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-14 pr-6 py-4 focus:ring-4 focus:ring-primary-500/10 focus:bg-white focus:border-primary-200 transition-all outline-none font-medium text-slate-700">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] px-1">Secure Password</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-primary-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <input type="password" name="password" required placeholder="••••••••" 
                                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-14 pr-6 py-4 focus:ring-4 focus:ring-primary-500/10 focus:bg-white focus:border-primary-200 transition-all outline-none font-medium text-slate-700">
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium px-1">Must be at least 8 characters with numbers.</p>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-primary-600 text-white py-5 rounded-2xl font-bold hover:bg-primary-700 shadow-xl shadow-primary-600/30 transition-all active:scale-[0.98] text-lg flex items-center justify-center gap-3">
                        Register Now
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                </div>

                <div class="flex items-center gap-4 text-slate-200">
                    <div class="flex-1 h-px bg-slate-100"></div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-300">Fast Pass</span>
                    <div class="flex-1 h-px bg-slate-100"></div>
                </div>

                <p class="text-center text-slate-500 text-sm font-medium">
                    Already a member? 
                    <a href="login.php" class="text-primary-600 font-bold hover:text-primary-700 transition-colors ml-1">Sign In</a>
                </p>
            </form>
        </div>

        <!-- Footer Info -->
        <div class="mt-12 text-center space-y-4">
            <p class="text-slate-400 text-xs font-medium px-10 leading-relaxed italic">
                "Preserving health is a duty. Few are aware of it."
            </p>
            <div class="flex justify-center gap-4 opacity-30">
                <div class="w-2 h-2 rounded-full bg-primary-400"></div>
                <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                <div class="w-2 h-2 rounded-full bg-primary-600"></div>
            </div>
        </div>
    </div>
</body>
</html>