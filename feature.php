<?php
header('Content-Type: application/json');
include "config.php";

try {
    // Read flashcards
    $flashcards = [];
    $result = $conn->query("SELECT id, topic, file_path FROM flashcards LIMIT 10");
    while ($row = $result->fetch_assoc()) {
        $flashcards[] = $row;
    }

    // Read smart study rooms
    $smart_study = [];
    $result = $conn->query("SELECT id, room_name FROM smart_study LIMIT 10");
    while ($row = $result->fetch_assoc()) {
        $smart_study[] = $row;
    }

    // Read study groups
    $study_groups = [];
    $result = $conn->query("SELECT id, group_name FROM study_groups LIMIT 10");
    while ($row = $result->fetch_assoc()) {
        $study_groups[] = $row;
    }

    // Return all data
    echo json_encode([
        'success' => true,
        'data' => [
            'flashcards' => $flashcards,
            'smart_study' => $smart_study,
            'study_groups' => $study_groups
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
