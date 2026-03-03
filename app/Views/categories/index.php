<h1>Categories</h1>
<a href="/categories/create">Add New</a>
<?php if (!empty($categories) && is_array($categories)): ?>
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th></tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= htmlspecialchars($cat['id']) ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No categories found.</p>
<?php endif; ?>

