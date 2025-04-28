<?php
// Include database connection
require_once 'db.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$itemType = $_GET['type'] ?? '';
$itemId = $_GET['id'] ?? '';
$redirect = $_GET['redirect'] ?? 'index.php';

// Validate input
if (empty($itemType) || empty($itemId)) {
    header("Location: $redirect?error=invalid_item");
    exit;
}

// Check if item is already saved
$existingItem = fetchOne(
    "SELECT * FROM saved_items WHERE user_id = ? AND item_type = ? AND item_id = ?",
    [$userId, $itemType, $itemId]
);

if ($existingItem) {
    // Item already saved, remove it (toggle functionality)
    try {
        deleteData(
            "DELETE FROM saved_items WHERE user_id = ? AND item_type = ? AND item_id = ?",
            [$userId, $itemType, $itemId]
        );
        header("Location: $redirect?success=item_removed");
        exit;
    } catch (Exception $e) {
        header("Location: $redirect?error=db_error");
        exit;
    }
} else {
    // Save the item
    try {
        insertData(
            "INSERT INTO saved_items (user_id, item_type, item_id) VALUES (?, ?, ?)",
            [$userId, $itemType, $itemId]
        );
        header("Location: $redirect?success=item_saved");
        exit;
    } catch (Exception $e) {
        header("Location: $redirect?error=db_error");
        exit;
    }
}
?>
