<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration | Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        .registration-card {
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
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-control, .form-select {
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .gender-options {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }
        
        .gender-options .form-check {
            display: flex;
            align-items: center;
        }
        
        .gender-options .form-check-input {
            margin-right: 8px;
        }
        
        .file-upload {
            position: relative;
            overflow: hidden;
        }
        
        .file-upload-input {
            position: absolute;
            font-size: 100px;
            opacity: 0;
            right: 0;
            top: 0;
        }
        
        .file-upload-label {
            display: block;
            padding: 10px 15px;
            background-color: #e9ecef;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            border: 1px dashed #adb5bd;
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
                <div class="card registration-card">
                    <div class="card-header text-center">
                        <h3>Student Registration</h3>
                    </div>
                    <div class="card-body p-4">
                        <form action="submit.php" method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <div class="gender-options">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="male" value="Male" required>
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="female" value="Female">
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="other" value="Other">
                                            <label class="form-check-label" for="other">Other</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="course" class="form-label">Course</label>
                                    <select class="form-select" id="course" name="course" required>
                                        <option value="">Select Course</option>
                                        <option value="Computer Science">Computer Science</option>
                                        <option value="Information Technology">Information Technology</option>
                                        <option value="Artificial Intelligence">Artificial Intelligence</option>
                                        <option value="Data Science">Data Science</option>
                                        <option value="Cyber Security">Cyber Security</option>
                                    </select>
                                </div>
                                
                                <div class="col-12">
                                    <label for="profile_picture" class="form-label">Profile Picture</label>
                                    <div class="file-upload">
                                        <input type="file" class="form-control file-upload-input" id="profile_picture" name="profile_picture" accept="image/*" required>
                                        <label for="profile_picture" class="file-upload-label">
                                            <i class="bi bi-cloud-arrow-up"></i> Choose a file...
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 py-2">Register Student</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="students.php" class="text-decoration-none">View Registered Students</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update file upload label with selected file name
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Choose a file...';
            document.querySelector('.file-upload-label').textContent = fileName;
        });
    </script>
</body>
</html>