<?php
// Database connection
$host = 'localhost';
$dbname = 'dbbcufioqhuqzf';
$username = 'uklz9ew3hrop3';
$password = 'zyrbspyjlzjb';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions for database operations
function executeQuery($sql, $params = []) {
    global $conn;
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

function insertData($sql, $params = []) {
    global $conn;
    executeQuery($sql, $params);
    return $conn->lastInsertId();
}

function updateData($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}

function deleteData($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}
?>
