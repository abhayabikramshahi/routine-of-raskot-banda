<?php
// Run this script ONCE to create or reset the default admin user, then delete it for security.
include '../config/config.php';

$username = 'admin';
$password = 'admin';
$hash = password_hash($password, PASSWORD_DEFAULT);

// Check if user exists
$stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    // Update password
    $stmt->close();
    $stmt = $conn->prepare('UPDATE users SET password = ? WHERE username = ?');
    $stmt->bind_param('ss', $hash, $username);
    if ($stmt->execute()) {
        echo 'Admin password reset to "admin".';
    } else {
        echo 'Error: ' . $stmt->error;
    }
} else {
    $stmt->close();
    $stmt = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $stmt->bind_param('ss', $username, $hash);
    if ($stmt->execute()) {
        echo 'Admin user created successfully.';
    } else {
        echo 'Error: ' . $stmt->error;
    }
}
$stmt->close();
$conn->close(); 