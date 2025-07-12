<?php
session_start();
include '../config/config.php';

// Handle file upload
$upload_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['track_file'])) {
    $track_name = trim($_POST['track_name']);
    $file = $_FILES['track_file'];

    if ($file['error'] === UPLOAD_ERR_OK && $track_name !== '') {
        $allowed_types = ['audio/mpeg', 'audio/wav', 'audio/mp3'];
        if (in_array($file['type'], $allowed_types)) {
            $uploads_dir = '../uploads/';
            if (!is_dir($uploads_dir)) {
                mkdir($uploads_dir, 0777, true);
            }
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

// Handle delete
if (isset($_GET['delete'])) {
    $track_id = intval($_GET['delete']);
    $stmt = $conn->prepare("SELECT file_path FROM tracks WHERE id = ?");
    $stmt->bind_param('i', $track_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    if ($stmt->fetch()) {
        $full_path = '../uploads/' . $file_path;
        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM tracks WHERE id = ?");
    $stmt->bind_param('i', $track_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

// Fetch all tracks
$tracks = [];
$result = $conn->query("SELECT * FROM tracks ORDER BY uploaded_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tracks[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Track Manager - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-8">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg p-8 space-y-8">
        <h1 class="text-2xl font-bold text-center mb-4">Track Manager</h1>

        <!-- Upload Form -->
        <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Track Name</label>
                <input type="text" name="track_name" required class="w-full px-3 py-2 border rounded" />
            </div>
            <div>
                <label class="block mb-1 font-medium">Audio File (MP3/WAV)</label>
                <input type="file" name="track_file" accept=".mp3,.wav,audio/*" required class="w-full" />
            </div>
            <?php if ($upload_error): ?>
                <div class="text-red-600 font-medium"><?= htmlspecialchars($upload_error) ?></div>
            <?php endif; ?>
            <button type="submit" class="px-6 py-2 bg-black text-white rounded hover:bg-gray-800">Upload Track</button>
        </form>

        <!-- Track List -->
        <div>
            <h2 class="text-xl font-semibold mb-2">Uploaded Tracks</h2>
            <?php if (count($tracks) === 0): ?>
                <div class="text-gray-500">No tracks uploaded yet.</div>
            <?php else: ?>
                <ul class="divide-y">
                    <?php foreach ($tracks as $track): ?>
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <span class="font-medium"><?= htmlspecialchars($track['name']) ?></span>
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