<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'StockFlow | Inventory System' ?></title>
    
    <link href="/assets/css/output.css" rel="stylesheet">
    <script src="/assets/js/lucide.js"></script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        body { overflow-x: hidden; }
    </style>
</head>
<body class="bg-gray-50 flex">

    <aside class="w-64 h-screen bg-[#0f172a] text-slate-300 border-r border-slate-800 fixed flex flex-col no-scrollbar z-50">
        <div class="p-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <i data-lucide="box" class="text-white size-6"></i>
                </div>
                <span class="font-bold text-2xl text-white italic tracking-tight">StockFlow</span>
            </div>
        </div>

        <nav class="mt-6 px-4 flex-1">
            <?php 
                // Determine the current URI for active link highlighting
                $current_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); 
            ?>

            <a href="/dashboard" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl mb-2 transition-all group <?= ($current_uri == '/dashboard' || $current_uri == '/') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i data-lucide="layout-dashboard" class="size-5 <?= ($current_uri == '/dashboard' || $current_uri == '/') ? 'text-white' : 'group-hover:text-blue-400' ?>"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="/products" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl mb-2 transition-all group <?= str_contains($current_uri, '/products') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i data-lucide="package" class="size-5 <?= str_contains($current_uri, '/products') ? 'text-white' : 'group-hover:text-blue-400' ?>"></i>
                <span class="font-medium">Products</span>
            </a>

            <a href="/categories" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl mb-2 transition-all group <?= str_contains($current_uri, '/categories') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                <i data-lucide="layers" class="size-5 <?= str_contains($current_uri, '/categories') ? 'text-white' : 'group-hover:text-blue-400' ?>"></i>
                <span class="font-medium">Categories</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 mb-4">
            <div class="flex items-center gap-3 px-2 py-3 rounded-xl bg-slate-800/50">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm">
                    SJ
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-medium text-white truncate">Steve Johnson</p>
                    <p class="text-xs text-slate-400 truncate">Manager</p>
                </div>
            </div>
        </div>
    </aside>

    <main class="ml-64 flex-1 min-h-screen p-8">
