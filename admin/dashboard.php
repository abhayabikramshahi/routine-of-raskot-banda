<?php
session_start();
include '../config/config.php';

// Handle track upload
$upload_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['track_file'])) {
    $track_name = trim($_POST['track_name']);
    $file = $_FILES['track_file'];
    if ($file['error'] === UPLOAD_ERR_OK && $track_name !== '') {
        $allowed_types = ['audio/mpeg', 'audio/wav', 'audio/mp3'];
        if (in_array($file['type'], $allowed_types)) {
            $uploads_dir = '../uploads/';
            if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);
            $filename = uniqid() . '_' . basename($file['name']);
            $target_path = $uploads_dir . $filename;
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $stmt = $conn->prepare("INSERT INTO tracks (name, file_path) VALUES (?, ?)");
                $stmt->bind_param('ss', $track_name, $filename);
                $stmt->execute();
                $stmt->close();
            } else {
                $upload_error = "Failed to move uploaded file.";
            }
        } else {
            $upload_error = "Invalid file type. Only MP3 and WAV allowed.";
        }
    } else {
        $upload_error = "Please provide a track name and select a file.";
    }
}

// Handle news/article upload
$news_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['news_title'])) {
    $news_title = trim($_POST['news_title']);
    $news_content = trim($_POST['news_content']);
    $news_category = intval($_POST['news_category']);
    $image_filename = null;
    if (!empty($_FILES['news_image']['name'])) {
        $img = $_FILES['news_image'];
        $allowed_img_types = ['image/jpeg', 'image/png', 'image/gif'];
        if ($img['error'] === UPLOAD_ERR_OK && in_array($img['type'], $allowed_img_types)) {
            $uploads_dir = '../uploads/';
            if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);
            $image_filename = uniqid() . '_' . basename($img['name']);
            $target_path = $uploads_dir . $image_filename;
            if (!move_uploaded_file($img['tmp_name'], $target_path)) {
                $news_error = 'Failed to upload image.';
            }
        } else {
            $news_error = 'Invalid image type. Only JPG, PNG, GIF allowed.';
        }
    }
    if (!$news_error && $news_title && $news_content && $news_category) {
        $stmt = $conn->prepare("INSERT INTO articles (title, content, image, category_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $news_title, $news_content, $image_filename, $news_category);
        if (!$stmt->execute()) {
            $news_error = 'Failed to post news.';
        }
        $stmt->close();
    } elseif (!$news_error) {
        $news_error = 'Please fill all fields.';
    }
}

// Fetch categories for news form
$news_categories = [];
$cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
if ($cat_result) {
    while ($row = $cat_result->fetch_assoc()) {
        $news_categories[] = $row;
    }
}

// Handle news delete
if (isset($_GET['delete_news'])) {
    $news_id = intval($_GET['delete_news']);
    $stmt = $conn->prepare("SELECT image FROM articles WHERE id = ?");
    $stmt->bind_param('i', $news_id);
    $stmt->execute();
    $stmt->bind_result($img);
    if ($stmt->fetch() && $img) {
        $img_path = '../uploads/' . $img;
        if (file_exists($img_path)) unlink($img_path);
    }
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->bind_param('i', $news_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

// ✅ Fetch tracks before count
$tracks = [];
$result = $conn->query("SELECT * FROM tracks ORDER BY uploaded_at DESC");
if ($result && $result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $tracks[] = $row;
    }
}

// Fetch all news/articles
$news_list = [];
$news_sql = "SELECT articles.*, categories.name AS category_name FROM articles LEFT JOIN categories ON articles.category_id = categories.id ORDER BY created_at DESC";
$news_result = $conn->query($news_sql);
if ($news_result) {
    while ($row = $news_result->fetch_assoc()) {
        $news_list[] = $row;
    }
}

// ✅ Now safe to count
$total_news = count($news_list);
$total_tracks = count($tracks);

// Handle track delete
if (isset($_GET['delete'])) {
    $track_id = intval($_GET['delete']);
    $stmt = $conn->prepare("SELECT file_path FROM tracks WHERE id = ?");
    $stmt->bind_param('i', $track_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    if ($stmt->fetch()) {
        $full_path = '../uploads/' . $file_path;
        if (file_exists($full_path)) unlink($full_path);
    }
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM tracks WHERE id = ?");
    $stmt->bind_param('i', $track_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Dashboard - Routine of Raskot Banda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-8">
    <!-- Professional Navbar -->
    <nav class="bg-white dark:bg-black shadow sticky top-0 z-50 w-full mb-8">
        <div class="max-w-4xl mx-auto flex items-center justify-between px-4 h-16">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="flex items-center font-bold text-xl text-black dark:text-white gap-2 select-none">
                    <span class="w-8 h-8 flex items-center justify-center rounded-full bg-gradient-to-br from-black to-gray-800 dark:from-white dark:to-gray-400 text-white dark:text-black">📰</span>
                    Admin Panel
                </a>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <a href="dashboard.php" class="px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white font-medium">Dashboard</a>
                <a href="../index.php" class="px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white font-medium">View Site</a>
                <a href="logout.php" class="px-4 py-2 rounded hover:bg-red-100 dark:hover:bg-red-800 text-red-600 dark:text-red-400 font-medium">Logout</a>
                <div class="relative group">
                    <button class="px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white font-medium flex items-center gap-1 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-40 bg-white dark:bg-black rounded shadow-lg opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity z-50 invisible group-hover:visible group-focus:visible">
                        <a href="create_admin.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white">Create Admin</a>
                        <a href="dashboard.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white">Manage Tracks</a>
                    </div>
                </div>
            </div>
            <button class="md:hidden flex flex-col justify-center items-center w-10 h-10" id="adminNavHamburger" onclick="toggleAdminNavLinks()">
                <span class="block w-7 h-1 bg-black dark:bg-white rounded mb-1"></span>
                <span class="block w-7 h-1 bg-black dark:bg-white rounded mb-1"></span>
                <span class="block w-7 h-1 bg-black dark:bg-white rounded"></span>
            </button>
        </div>
        <div class="md:hidden px-4 pb-2" id="adminNavLinksMobile" style="display:none;">
            <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white font-medium">Dashboard</a>
            <a href="../index.php" class="block px-4 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white font-medium">View Site</a>
            <a href="logout.php" class="block px-4 py-2 rounded hover:bg-red-100 dark:hover:bg-red-800 text-red-600 dark:text-red-400 font-medium">Logout</a>
            <a href="create_admin.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white">Create Admin</a>
            <a href="dashboard.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 text-black dark:text-white">Manage Tracks</a>
        </div>
    </nav>
    <script>
    function toggleAdminNavLinks() {
        var links = document.getElementById('adminNavLinksMobile');
        links.style.display = links.style.display === 'none' || links.style.display === '' ? 'block' : 'none';
    }
    </script>
    <div class="w-full max-w-4xl mx-auto bg-white rounded-xl shadow-lg px-4 md:px-8 py-12 space-y-12">
        <h1 class="text-3xl font-extrabold text-center mb-8 flex items-center justify-center gap-2"><span class="text-blue-600">🛠️</span> Admin Dashboard</h1>

        <!-- Analytics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-blue-50 dark:bg-gray-800 rounded-xl p-4 flex flex-col items-center shadow">
                <span class="text-2xl mb-1">📰</span>
                <span class="text-lg font-bold"><?= $total_news ?></span>
                <span class="text-xs text-gray-500">Total News</span>
            </div>
            <div class="bg-green-50 dark:bg-gray-800 rounded-xl p-4 flex flex-col items-center shadow">
                <span class="text-2xl mb-1">🎵</span>
                <span class="text-lg font-bold"><?= $total_tracks ?></span>
                <span class="text-xs text-gray-500">Total Tracks</span>
            </div>
            <!-- Add more analytics as needed -->
        </div>

        <!-- News List -->
        <div class="mb-12">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl">📰</span>
                <h2 class="text-2xl font-bold">All News / Articles</h2>
            </div>
            <?php if (count($news_list) === 0): ?>
                <div class="text-gray-500 text-center">No news posted yet.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-900 rounded-xl shadow divide-y divide-gray-200 dark:divide-gray-800">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">Image</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">Title</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">Category</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">Date</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news_list as $news): ?>
                        <tr class="hover:bg-blue-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-2">
                                <?php if (!empty($news['image'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($news['image']) ?>" alt="" class="w-16 h-12 object-cover rounded shadow" />
                                <?php else: ?>
                                    <span class="text-gray-400">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 font-medium text-black dark:text-white">
                                <?= htmlspecialchars($news['title']) ?>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <?= htmlspecialchars($news['category_name']) ?>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                <?= htmlspecialchars(date('M d, Y', strtotime($news['created_at']))) ?>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <a href="dashboard.php?delete_news=<?= $news['id'] ?>" onclick="return confirm('Delete this news article?')" class="text-red-600 hover:underline font-semibold">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- News Upload Form -->
        <div class="mb-12">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl">📰</span>
                <h2 class="text-2xl font-bold">Post News / Article</h2>
            </div>
            <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="space-y-4 bg-gray-50 p-6 rounded-xl shadow">
                <div>
                    <label class="block mb-1 font-medium">Title</label>
                    <input type="text" name="news_title" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Content</label>
                    <textarea name="news_content" rows="4" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                </div>
                <div>
                    <label class="block mb-1 font-medium">Category</label>
                    <select name="news_category" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Select Category</option>
                        <?php foreach ($news_categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 font-medium">Image (optional)</label>
                    <input type="file" name="news_image" accept="image/*" class="w-full" />
                </div>
                <?php if ($news_error): ?>
                    <div class="text-red-600 font-medium text-center"><?= htmlspecialchars($news_error) ?></div>
                <?php endif; ?>
                <button type="submit" class="w-full px-6 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-800 transition">Post News</button>
            </form>
        </div>

        <hr class="my-8 border-t-2 border-gray-200" />

        <!-- Track Upload Form -->
        <div class="mb-12">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl">🎵</span>
                <h2 class="text-2xl font-bold">Upload Track</h2>
            </div>
            <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="space-y-4 bg-gray-50 p-6 rounded-xl shadow">
                <div>
                    <label class="block mb-1 font-medium">Track Name</label>
                    <input type="text" name="track_name" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-gray-700" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Audio File (MP3/WAV)</label>
                    <input type="file" name="track_file" accept=".mp3,.wav,audio/*" required class="w-full" />
                </div>
                <?php if ($upload_error): ?>
                    <div class="text-red-600 font-medium text-center"><?= htmlspecialchars($upload_error) ?></div>
                <?php endif; ?>
                <button type="submit" class="w-full px-6 py-2 bg-black text-white rounded font-semibold hover:bg-gray-800 transition">Upload Track</button>
            </form>
        </div>

        <hr class="my-8 border-t-2 border-gray-200" />

        <!-- Track List -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl">📂</span>
                <h2 class="text-2xl font-bold">Uploaded Tracks</h2>
            </div>
            <?php if (count($tracks) === 0): ?>
                <div class="text-gray-500 text-center">No tracks uploaded yet.</div>
            <?php else: ?>
                <ul class="divide-y">
                    <?php foreach ($tracks as $track): ?>
                        <li class="py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                            <div>
                                <span class="font-medium text-lg">🎵 <?= htmlspecialchars($track['name']) ?></span>
                                <span class="text-xs text-gray-500 ml-2">(<?= htmlspecialchars($track['uploaded_at']) ?>)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <audio controls src="../uploads/<?= htmlspecialchars($track['file_path']) ?>" class="h-8"></audio>
                                <a href="dashboard.php?delete=<?= $track['id'] ?>" onclick="return confirm('Delete this track?')" class="text-red-600 hover:underline font-semibold">Delete</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 