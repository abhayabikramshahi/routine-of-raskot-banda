<?php
session_start();
include '../config/config.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Only allow "admin" to login
            if ($user['username'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = '❌ Access denied. Admins only.';
            }
        } else {
            $error = '❌ Invalid username or password.';
        }
    } else {
        $error = '❌ Invalid username or password.';
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login - Routine of Raskot Banda</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/3596/3596093.png" type="image/x-icon" />
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-300 dark:from-gray-900 dark:to-black min-h-screen flex items-center justify-center px-4">
  <div class="w-full max-w-sm p-8 rounded-3xl shadow-xl bg-white dark:bg-gray-950 space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col items-center gap-2">
      <div class="bg-black dark:bg-white text-white dark:text-black rounded-full p-3 text-xl shadow-md">
        🛡️
      </div>
      <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Admin Panel</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400">Only authorized admin can login</p>
    </div>

    <!-- Error Display -->
    <?php if ($error): ?>
      <div class="text-center text-sm text-red-700 bg-red-100 dark:bg-red-800/30 p-3 rounded-lg border border-red-300 dark:border-red-600 font-medium">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form action="login.php" method="POST" class="space-y-4">
      <div>
        <label for="username" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
        <input type="text" id="username" name="username" required
          class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-black dark:focus:ring-white focus:outline-none" />
      </div>
      <div>
        <label for="password" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
        <input type="password" id="password" name="password" required
          class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-black dark:focus:ring-white focus:outline-none" />
      </div>
      <button type="submit"
        class="w-full py-2 bg-black dark:bg-white text-white dark:text-black font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition">
        Login
      </button>
    </form>

    <!-- Footer -->
    <div class="text-center text-xs text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-800">
      &copy; <?= date("Y") ?> Raskot Banda. All rights reserved.
    </div>
  </div>
</body>
</html>
