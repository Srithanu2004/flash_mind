<?php
header('Content-Type: application/json');
include "config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable exceptions for mysqli

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method.");
    }

    // Get and sanitize input
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    // Validate inputs
    if (empty($username) || empty($password)) {
        throw new Exception("Username and password are required.");
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        throw new Exception("Invalid username or password.");
    }

    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if (!password_verify($password, $hashed_password)) {
        throw new Exception("Invalid username or password.");
    }

    $stmt->close();
    $conn->close();

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Login successful.',
        'user_id' => $user_id,
        'username' => $username
    ]);

} catch (Exception $e) {
    // Error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
