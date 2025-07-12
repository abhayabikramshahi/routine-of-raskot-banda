<?php
include 'header.php';
include 'config/config.php';

$cat_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cat_sql = "SELECT * FROM categories WHERE id = $cat_id";
$cat_result = $conn->query($cat_sql);
$category = $cat_result && $cat_result->num_rows > 0 ? $cat_result->fetch_assoc() : null;

$sql = "SELECT * FROM articles WHERE category_id = $cat_id ORDER BY created_at DESC";
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
    <title><?php echo $category ? htmlspecialchars($category['name']) : 'Category Not Found'; ?> - News</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black dark:bg-black dark:text-white">
    <div class="max-w-7xl mx-auto mt-10 flex flex-col md:flex-row gap-8 px-4">
        <main class="flex-1 bg-white text-black dark:bg-black dark:text-white rounded-xl shadow-lg p-8">
            <a href="index.php" class="inline-block mb-4 hover:underline">&larr; Back to Home</a>
            <h1 class="text-2xl font-bold text-center mb-8"><?php echo $category ? htmlspecialchars($category['name']) : 'Category Not Found'; ?></h1>
            <p class="text-center text-gray-500 dark:text-gray-300">No posts yet in this category.</p>
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
        </aside>
    </div>
</body>
</html> 