<?php
include 'header.php';
include 'config/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT articles.*, categories.name AS category_name FROM articles LEFT JOIN categories ON articles.category_id = categories.id WHERE articles.id = $id";
$result = $conn->query($sql);
$article = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? htmlspecialchars($article['title']) : 'Article Not Found'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black dark:bg-black dark:text-white">
    <div class="max-w-2xl mx-auto mt-10 bg-white text-black dark:bg-black dark:text-white rounded-xl shadow-lg px-2 sm:px-4 md:px-8 py-8">
        <a href="index.php" class="inline-block mb-4 hover:underline">&larr; Back to Home</a>
        <?php if ($article): ?>
            <div class="bg-gray-100 text-black dark:bg-gray-900 dark:text-white rounded-lg shadow p-8">
                <?php if (!empty($article['image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full max-h-80 object-cover rounded mb-4" />
                <?php endif; ?>
                <span class="block text-black dark:text-white text-sm font-semibold mb-1">Category: <?php echo htmlspecialchars($article['category_name']); ?></span>
                <span class="block text-gray-500 dark:text-gray-300 text-xs mb-2"><?php echo $article['created_at']; ?></span>
                <h1 class="text-2xl font-bold mb-4 text-black dark:text-white"><?php echo htmlspecialchars($article['title']); ?></h1>
                <p class="text-black dark:text-white leading-relaxed text-lg"><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 dark:text-gray-300">No article found.</p>
        <?php endif; ?>
    </div>
</body>
</html> 