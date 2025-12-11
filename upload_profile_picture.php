<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/session.php';

requireLogin();

$user_id = getCurrentUserId();
$response = ['success' => false, 'message' => ''];

// Create uploads directory if it doesn't exist
$uploads_dir = __DIR__ . '/assets/uploads/profile_pictures/';
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $response['message'] = 'Only JPEG, PNG, and GIF images are allowed.';
    } elseif ($file['size'] > $max_size) {
        $response['message'] = 'File size must be less than 5MB.';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'File upload failed.';
    } else {
        // Generate unique filename
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
        $filepath = $uploads_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Delete old profile picture if exists
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user['profile_picture']) {
                $old_file = $uploads_dir . $user['profile_picture'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            // Update database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $filename, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['user_profile_picture'] = $filename;
                $response['success'] = true;
                $response['message'] = 'Profile picture updated successfully!';
            } else {
                $response['message'] = 'Database update failed.';
                unlink($filepath);
            }
            
            $stmt->close();
            $conn->close();
        } else {
            $response['message'] = 'Failed to save file.';
        }
    }
} else {
    $response['message'] = 'No file uploaded.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
