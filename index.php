<?php
include 'header.php';
include 'config/config.php';

// Fetch latest articles
$sql = "SELECT articles.*, categories.name AS category_name FROM articles LEFT JOIN categories ON articles.category_id = categories.id ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($sql);

// Fetch categories for sidebar
$sidebar_cat_sql = "SELECT * FROM categories ORDER BY name ASC";
$sidebar_cat_result = $conn->query($sidebar_cat_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black dark:bg-black dark:text-white">
    <div class="max-w-7xl mx-auto mt-10 flex flex-col md:flex-row gap-8 px-4">
        <main class="flex-1 bg-white text-black dark:bg-black dark:text-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-8">Routine of Raskot Banda</h1>
            <p class="text-center text-gray-500 dark:text-gray-300">No posts yet.</p>
        </main>
        <aside class="w-full md:w-80 flex-shrink-0 bg-white text-black dark:bg-black dark:text-white rounded-xl shadow-lg p-6 h-fit">
            <h3 class="text-lg font-semibold mb-4">Categories</h3>
            <ul class="mb-8 space-y-2">
                <?php if ($sidebar_cat_result && $sidebar_cat_result->num_rows > 0): ?>
                    <?php while($cat = $sidebar_cat_result->fetch_assoc()): ?>
                        <li><a href="category.php?id=<?php echo $cat['id']; ?>" class="hover:underline font-medium text-black dark:text-white"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
            <h3 class="text-lg font-semibold mb-4">Trending</h3>
            <ul class="space-y-2">
                <li><a href="#" class="hover:underline font-medium text-black dark:text-white">Sample Trending 1</a></li>
                <li><a href="#" class="hover:underline font-medium text-black dark:text-white">Sample Trending 2</a></li>
            </ul>
        </aside>
    </div>
</body>
</html> 