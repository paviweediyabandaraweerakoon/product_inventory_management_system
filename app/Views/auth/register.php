<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Inventory Pro</title>
    
    <link rel="stylesheet" href="/assets/css/tailwind-output.css">
    
    <script src="/assets/js/lucide.min.js"></script>

    <style>
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
            <div class="flex items-center space-x-4 mb-6">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-4 rounded-2xl shadow-xl">
                    <i data-lucide="package" class="w-10 h-10 text-white"></i>
                </div>
                <div>
                    <h1 class="text-4xl font-bold tracking-tight">Join Inventory Pro</h1>
                    <p class="text-slate-400">Start managing smarter today</p>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-2xl font-semibold mb-6 text-white/90">What you'll get:</h2>
                
                <?php 
                $features = [
                    "Real-time inventory tracking and updates",
                    "Advanced analytics and reporting tools",
                    "Multi-user access with role management",
                    "Automated stock level alerts",
                    "Secure cloud-based data storage",
                    "Mobile-responsive dashboard access"
                ];
                foreach ($features as $feature): ?>
                <div class="glass-card p-4 rounded-xl flex items-center space-x-4 transition-transform hover:scale-[1.02]">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full border-2 border-emerald-500 flex items-center justify-center">
                        <i data-lucide="check" class="w-3 h-3 text-emerald-400"></i>
                    </div>
                    <p class="text-slate-300 font-medium"><?= htmlspecialchars($feature) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="w-full flex justify-center lg:justify-end">
            <div class="bg-white w-full max-w-md p-10 rounded-[2rem] shadow-2xl text-slate-800">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold mb-2 text-slate-900">Create Account</h2>
                    <p class="text-slate-500">Get started with your inventory management</p>

                    <?php if (!empty($error)): ?>
                        <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-lg flex items-center">
                            <i data-lucide="alert-triangle" class="mr-3 w-5 h-5 text-red-500"></i>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <form action="/register" method="POST" class="space-y-5">
                    
                    <?php 
                        // Assigning posted data to local variable for sanitization (Supervisor Feedback)
                        $formData = array_map(fn($v) => htmlspecialchars(trim((string)$v)), $_POST);
                    ?>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-slate-700">Full Name</label>
                        <input type="text" name="full_name" required 
                               value="<?= $formData['full_name'] ?? '' ?>"
                               placeholder="John Doe" 
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-slate-700">Email Address</label>
                        <input type="email" name="email" required 
                               value="<?= $formData['email'] ?? '' ?>"
                               placeholder="your@email.com" 
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-slate-700">Password</label>
                        <input type="password" name="password" required 
                               placeholder="Min 8 characters" 
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-slate-700">Confirm Password</label>
                        <input type="password" name="confirm_password" required 
                               placeholder="Re-enter password" 
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2 mt-4">
                        <span>Create Account</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-slate-500 text-sm">
                        Already have an account? 
                        <a href="/login" class="text-indigo-600 font-bold hover:underline">Sign in here</a>
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