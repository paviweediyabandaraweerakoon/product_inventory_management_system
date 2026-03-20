<?php
/**
 * Main Layout Responsibility: 
 * Assemble the core structural components (Header, Sidebar, Footer) 
 * and inject the dynamic page content.
 */

// 1. Load the HTML Head and CSS
include __DIR__ . '/header.php'; 

// 2. Load the Navigation Sidebar
include __DIR__ . '/sidebar.php'; 
?>

<main class="ml-64 flex-1 min-h-screen p-8">
    <?= $content ?>
</main>

<?php 
// 4. Load Scripts and close HTML tags
include __DIR__ . '/footer.php'; 
?>