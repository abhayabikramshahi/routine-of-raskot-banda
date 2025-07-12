<?php
header('Location: login.php');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Routine of Raskot Banda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black dark:bg-black dark:text-white min-h-screen flex items-center justify-center">
    <div class="bg-white text-black dark:bg-black dark:text-white rounded-xl shadow-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Admin Login</h1>
        <form action="login.php" method="post" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium" for="username">Username</label>
                <input type="text" id="username" name="username" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-400 bg-gray-100 dark:bg-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block mb-1 font-medium" for="password">Password</label>
                <input type="password" id="password" name="password" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-400 bg-gray-100 dark:bg-gray-900 dark:text-white">
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-black text-white dark:bg-white dark:text-black rounded font-semibold hover:bg-gray-800 dark:hover:bg-gray-200 transition">Login</button>
        </form>
    </div>
</body>
</html> 