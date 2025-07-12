<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "test1");
if ($conn->connect_error) {
    die("<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>");
}

// Check if ID parameter exists
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // First, get the profile picture path to delete the file
    $sql = "SELECT profile_picture FROM registration WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $profile_picture = $row['profile_picture'];
        
        // Delete the student record
        $delete_sql = "DELETE FROM registration WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            // Delete the profile picture file if it exists
            if (!empty($profile_picture) && file_exists($profile_picture)) {
                unlink($profile_picture);
            }
            
            // Redirect back to students.php with success message
            header("Location: students.php?delete_success=1");
            exit();
        } else {
            // Redirect back with error message
            header("Location: students.php?delete_error=1");
            exit();
        }
    } else {
        // Student not found, redirect back
        header("Location: students.php?not_found=1");
        exit();
    }
} else {
    // No ID provided, redirect back
    header("Location: students.php?invalid_id=1");
    exit();
}

$conn->close();
?>