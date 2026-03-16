<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Pro</title>
    
    <link rel="stylesheet" href="/assets/css/tailwind-output.css">
    
    <script src="/assets/js/lucide.min.js"></script>

    <style>
        /* Custom glassmorphism - CSS variable widiyata thiyaganna eka lesiyi */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#0f172a] p-4 font-sans text-white">

    <div class="w-full max-w-6xl grid lg:grid-cols-2 gap-12 items-center">
        
        <div class="hidden lg:block space-y-8">
            <div class="flex items-center space-x-4 mb-10">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-2xl shadow-xl">
                    <i data-lucide="box" class="w-10 h-10"></i>
                </div>
                <div>
                    <h1 class="text-4xl font-bold tracking-tight">Inventory Pro</h1>
                    <p class="text-slate-400">Professional Management System</p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-card p-4 rounded-2xl flex items-start space-x-4">
                    <div class="bg-emerald-500/20 p-3 rounded-xl text-emerald-400">
                        <i data-lucide="shield-check"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Secure & Reliable</h3>
                        <p class="text-slate-400 text-sm">Enterprise-grade security with encrypted data protection</p>
                    </div>
                </div>

                <div class="glass-card p-4 rounded-2xl flex items-start space-x-4">
                    <div class="bg-purple-500/20 p-3 rounded-xl text-purple-400">
                        <i data-lucide="zap"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Fast & Efficient</h3>
                        <p class="text-slate-400 text-sm">Real-time inventory tracking and analytics dashboard</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full flex justify-center lg:justify-end">
            <div class="bg-white w-full max-w-md p-10 rounded-[2rem] shadow-2xl text-slate-800">
                
                <div class="mb-8">
                    <h2 class="text-3xl font-bold mb-2">Welcome Back</h2>
                    
                    <?php if (empty($_GET['registered']) && empty($error)): ?>
                        <p class="text-slate-500">Sign in to access your inventory dashboard</p>
                    <?php endif; ?>

                    <?php if (isset($_GET['registered'])): ?>
                        <div class="mt-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm rounded flex items-center">
                            <i data-lucide="check-circle" class="mr-2 w-4 h-4"></i>
                            <span>Registration successful! Please check email to verify.</span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded flex items-center">
                            <i data-lucide="alert-circle" class="mr-2 w-4 h-4"></i>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <form action="/login" method="POST" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Email Address</label>
                        <input type="email" name="email" required 
                               value="<?= isset($data['email']) ? htmlspecialchars($data['email']) : '' ?>"
                               placeholder="admin@inventory.com" 
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Password</label>
                        <input type="password" name="password" required 
                               placeholder="Enter your password" 
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    </div>

                    <button type="submit" 
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center justify-center space-x-2">
                        <span>Sign In</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-slate-500 text-sm">
                        Don't have an account? 
                        <a href="/register" class="text-indigo-600 font-bold hover:underline">Create account</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
      lucide.createIcons();
    </script>
</body>
</html>