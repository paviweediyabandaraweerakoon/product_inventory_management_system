<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Inventory System</title>
    
    <link href="/assets/css/output.css" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 flex">
    <aside class="w-64 h-screen bg-white border-r border-gray-200 fixed">
        <div class="p-6 font-bold text-2xl text-blue-600 italic">StockFlow</div>
        <nav class="mt-6 px-4">
            <a href="/dashboard" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-xl mb-2 transition-all">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>
            <a href="/products" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-xl mb-2 transition-all">
                <i data-lucide="package"></i> Products
            </a>
            <a href="/categories" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-xl mb-2 transition-all">
                <i data-lucide="layers"></i> Categories
            </a>
        </nav>
    </aside>
    <main class="ml-64 w-full p-8">