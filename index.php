<?php
include 'header.php';
include 'config/config.php';

// Fetch latest articles
$sql = "SELECT articles.*, categories.name AS category_name FROM articles LEFT JOIN categories ON articles.category_id = categories.id ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($sql);

// Fetch categories for sidebar
$sidebar_cat_sql = "SELECT * FROM categories ORDER BY name ASC";
$sidebar_cat_result = $conn->query($sidebar_cat_sql);
$sidebar_categories = [];
if ($sidebar_cat_result && $sidebar_cat_result instanceof mysqli_result) {
    while ($row = $sidebar_cat_result->fetch_assoc()) {
        $sidebar_categories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>News Website</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black dark:bg-black dark:text-white overflow-x-hidden">
  <div class="w-full flex flex-col md:flex-row gap-8 px-0" style="max-width:100vw;">
    <main class="flex-1 bg-white text-black dark:bg-black dark:text-white rounded-xl shadow-lg p-0 md:p-8 w-full">
      <!-- Hero Section Removed -->

      <!-- Articles Section -->
      <section id="articles">
        <?php if ($result && $result->num_rows > 0): ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php while($article = $result->fetch_assoc()): ?>
              <div class="group bg-gray-50 dark:bg-gray-900 rounded-xl shadow-lg overflow-hidden flex flex-col hover:shadow-2xl transition-shadow border border-gray-100 dark:border-gray-800">
                <?php if (!empty($article['image'])): ?>
                  <a href="article.php?id=<?= $article['id'] ?>">
                    <img src="uploads/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300" />
                  </a>
                <?php endif; ?>
                <div class="flex-1 p-6 flex flex-col justify-between">
                  <div>
                    <div class="flex items-center gap-2 mb-2">
                      <span class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded"><?= htmlspecialchars($article['category_name']) ?></span>
                      <span class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(date('M d, Y', strtotime($article['created_at']))) ?></span>
                    </div>
                    <a href="article.php?id=<?= $article['id'] ?>" class="block text-2xl font-bold mb-2 hover:text-blue-700 dark:hover:text-blue-300 text-black dark:text-white transition"><?= htmlspecialchars($article['title']) ?></a>
                    <p class="text-gray-700 dark:text-gray-300 mb-4"><?= nl2br(htmlspecialchars(mb_strimwidth($article['content'], 0, 180, '...'))) ?></p>
                  </div>
                  <div>
                    <a href="article.php?id=<?= $article['id'] ?>" class="inline-block px-4 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-800 transition">Read More</a>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <p class="text-center text-gray-500 dark:text-gray-300">No posts yet.</p>
        <?php endif; ?>
      </section>
    </main>

    <aside class="w-full md:w-80 flex-shrink-0 bg-white dark:bg-gray-900 text-black dark:text-white rounded-xl shadow-lg p-0 h-fit overflow-auto" style="max-width:100vw;">
      <div class="p-6 pb-4">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xl">📚</span>
          <h3 class="text-lg font-semibold">Categories</h3>
        </div>
        <div class="flex gap-4 overflow-x-auto pb-2 mb-6 custom-scrollbar">
          <?php if (count($sidebar_categories) > 0): ?>
            <?php foreach ($sidebar_categories as $cat): ?>
              <?php
                // Simple icon mapping by category name
                $icon = '📁';
                if (stripos($cat['name'], 'tech') !== false) $icon = '💻';
                elseif (stripos($cat['name'], 'world') !== false) $icon = '🌍';
                elseif (stripos($cat['name'], 'sports') !== false) $icon = '🏅';
                elseif (stripos($cat['name'], 'entertain') !== false) $icon = '🎬';
              ?>
              <a href="category.php?id=<?= $cat['id'] ?>" class="flex flex-col items-center justify-center min-w-[110px] h-24 rounded-lg bg-blue-50 dark:bg-gray-800 hover:bg-blue-100 dark:hover:bg-gray-700 text-blue-900 dark:text-white shadow transition p-2">
                <span class="text-2xl mb-1"><?= $icon ?></span>
                <span class="font-medium text-sm text-center"><?= htmlspecialchars($cat['name']) ?></span>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <hr class="border-t border-gray-200 dark:border-gray-700 mx-6" />
      <div class="p-6 pt-4">
        <div class="flex items-center gap-2 mb-2">
          <span class="text-xl">⭐</span>
          <h3 class="text-lg font-semibold">Top News</h3>
        </div>
        <ul class="space-y-2 mb-2">
          <li>
            <a href="#" class="flex items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 transition font-medium text-black dark:text-white shadow">
              <span class="text-lg">📰</span> Sample Top News 1
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-blue-50 dark:hover:bg-gray-700 transition font-medium text-black dark:text-white shadow">
              <span class="text-lg">📰</span> Sample Top News 2
            </a>
          </li>
        </ul>
      </div>
<style>
  /* Custom scrollbar for horizontal categories */
  .custom-scrollbar::-webkit-scrollbar {
    height: 8px;
  }
  .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
  }
  .custom-scrollbar {
    scrollbar-color: #cbd5e1 transparent;
    scrollbar-width: thin;
  }
</style>
    </aside>
  </div>
</body>
</html>
