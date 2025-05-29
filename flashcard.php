<?php
header('Content-Type: application/json');
include "config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable mysqli exceptions

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {

        // Upload file to create flashcards
        case 'upload':
            if (!isset($_FILES['file'])) {
                throw new Exception("No file uploaded.");
            }

            $fileName = $_FILES['file']['name'];
            $tmpName = $_FILES['file']['tmp_name'];
            $destination = "uploads/" . basename($fileName);

            if (!move_uploaded_file($tmpName, $destination)) {
                throw new Exception("Failed to upload file.");
            }

            // Simulate flashcard creation
            $stmt = $conn->prepare("INSERT INTO flashcards (topic, file_path) VALUES (?, ?)");
            $topic = $_POST['topic'] ?? 'Untitled';
            $stmt->bind_param("ss", $topic, $destination);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'Flashcards created successfully.',
                'file' => $fileName
            ]);
            break;

        // Select a random topic
        case 'random_topic':
            $result = $conn->query("SELECT DISTINCT topic FROM flashcards ORDER BY RAND() LIMIT 1");

            if ($result->num_rows == 0) {
                throw new Exception("No topics found.");
            }

            $row = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'topic' => $row['topic']
            ]);
            break;

        // Count flashcards
        case 'count':
            $result = $conn->query("SELECT COUNT(*) AS total FROM flashcards");
            $row = $result->fetch_assoc();

            echo json_encode([
                'success' => true,
                'count' => $row['total']
            ]);
            break;

        default:
            throw new Exception("Unknown action.");
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
