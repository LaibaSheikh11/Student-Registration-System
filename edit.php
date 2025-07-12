<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "test1");
if ($conn->connect_error) {
    die("<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>");
}

// Initialize variables
$student = [];
$error = '';

// Get student data if ID exists
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM registration WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if (!$student) {
        $error = "Student not found";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $course = $_POST['course'];

    // Handle image upload if new file is provided
    $img_path = $student['profile_picture']; // Keep old image by default
    
    if (!empty($_FILES["profile_picture"]["name"])) {
        $img_name = $_FILES["profile_picture"]["name"];
        $img_tmp = $_FILES["profile_picture"]["tmp_name"];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($img_ext, $allowed_ext)) {
            $new_filename = uniqid('profile_', true) . '.' . $img_ext;
            $new_img_path = "uploads/" . $new_filename;
            
            if (move_uploaded_file($img_tmp, $new_img_path)) {
                // Delete old image if it exists
                if (file_exists($img_path)) {
                    unlink($img_path);
                }
                $img_path = $new_img_path;
            }
        }
    }

    // Update record
    $stmt = $conn->prepare("UPDATE registration SET firstname=?, email=?, phone=?, gender=?, course=?, profile_picture=? WHERE id=?");
    $stmt->bind_param("ssssssi", $name, $email, $phone, $gender, $course, $img_path, $id);
    
    if ($stmt->execute()) {
        // Redirect back to students.php with success message
        header("Location: students.php?update_success=1");
        exit();
    } else {
        $error = "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student | Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .edit-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 1.5rem;
        }
        
        .profile-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #dee2e6;
            margin: 0 auto;
            display: block;
        }
        
        .file-upload-label {
            display: block;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            border: 1px dashed #adb5bd;
            margin-top: 10px;
        }
        
        .file-upload-label:hover {
            background-color: #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card edit-card">
                    <div class="card-header text-center">
                        <h3>Edit Student</h3>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if ($student): ?>
                        <form method="POST" action="edit.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $student['id'] ?>">
                            
                            <div class="text-center mb-4">
                                <img src="<?= htmlspecialchars($student['profile_picture']) ?>" alt="Profile" class="profile-preview" id="profilePreview">
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($student['firstname']) ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="male" value="Male" <?= $student['gender'] == 'Male' ? 'checked' : '' ?> required>
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="female" value="Female" <?= $student['gender'] == 'Female' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="other" value="Other" <?= $student['gender'] == 'Other' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="other">Other</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="course" class="form-label">Course</label>
                                    <select class="form-select" id="course" name="course" required>
                                        <option value="">Select Course</option>
                                        <option value="Computer Science" <?= $student['course'] == 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                                        <option value="Information Technology" <?= $student['course'] == 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
                                        <option value="Artificial Intelligence" <?= $student['course'] == 'Artificial Intelligence' ? 'selected' : '' ?>>Artificial Intelligence</option>
                                        <option value="Data Science" <?= $student['course'] == 'Data Science' ? 'selected' : '' ?>>Data Science</option>
                                    </select>
                                </div>
                                
                                <div class="col-12">
                                    <label for="profile_picture" class="form-label">Update Profile Picture</label>
                                    <input type="file" class="form-control d-none" id="profile_picture" name="profile_picture" accept="image/*">
                                    <label for="profile_picture" class="file-upload-label">
                                        <i class="bi bi-cloud-arrow-up"></i> Choose new file...
                                    </label>
                                    <div class="form-text">Leave blank to keep current image</div>
                                </div>
                                
                                <div class="col-12 mt-4 d-flex justify-content-between">
                                    <a href="students.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" name="update" class="btn btn-primary">Update Student</button>
                                </div>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update profile preview when new image is selected
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('profilePreview').src = event.target.result;
                }
                reader.readAsDataURL(file);
                
                // Update the file label
                document.querySelector('.file-upload-label').innerHTML = 
                    `<i class="bi bi-check-circle"></i> ${file.name}`;
            }
        });
    </script>
</body>
</html>