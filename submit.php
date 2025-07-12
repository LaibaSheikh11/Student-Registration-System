<?php
// Connect to MySQL database
$conn = new mysqli("localhost", "root", "", "test1");

if ($conn->connect_error) {
    die("<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>");
}

$message = "";
$success = false;
$studentId = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $gender = $_POST["gender"];
    $course = $_POST["course"];


    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    
    $img_name = $_FILES["profile_picture"]["name"];
    $img_tmp = $_FILES["profile_picture"]["tmp_name"];
    $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($img_ext, $allowed_ext)) {
        $message = "Invalid image format. Only JPG, JPEG, PNG & GIF are allowed.";
    } else {
      
        $new_filename = uniqid('profile_', true) . '.' . $img_ext;
        $img_path = "uploads/" . $new_filename;

        if (move_uploaded_file($img_tmp, $img_path)) {
            
            $sql = "INSERT INTO registration (firstname, email, phone, gender, course, profile_picture) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $name, $email, $phone, $gender, $course, $img_path);
            
            if ($stmt->execute()) {
                $success = true;
                $message = "Student registered successfully!";
                $studentId = $stmt->insert_id;
            } else {
                $message = "Database Error: " . $stmt->error;
                
                unlink($img_path);
            }
            
            $stmt->close();
        } else {
            $message = "Failed to upload profile picture. Please try again.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status | Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .result-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .result-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .result-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 1.5rem;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 25px;
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .status-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="result-container">
            <div class="card result-card">
                <div class="card-header result-header text-center">
                    <h3>Registration Status</h3>
                </div>
                <div class="card-body text-center p-4">
                    <?php if ($success): ?>
                        <i class="bi bi-check-circle-fill text-success status-icon"></i>
                        <div class="alert alert-success">
                            <h5 class="alert-heading">Success!</h5>
                            <p><?= $message ?></p>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="register.php" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle"></i> Register Another
                            </a>
                            <?php if ($studentId): ?>
                                <a href="students.php" class="btn btn-success">
                                    <i class="bi bi-people-fill"></i> View Students
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <i class="bi bi-x-circle-fill text-danger status-icon"></i>
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">Error!</h5>
                            <p><?= $message ?></p>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="register.php" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Try Again
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>