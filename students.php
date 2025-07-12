<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "test1");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$notification = '';
if (isset($_GET['delete_success']) && $_GET['delete_success'] == 1) {
    $notification = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        Student record deleted successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
} elseif (isset($_GET['delete_error']) && $_GET['delete_error'] == 1) {
    $notification = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        Error deleting student record.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
} elseif (isset($_GET['not_found']) && $_GET['not_found'] == 1) {
    $notification = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        Student record not found.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
} elseif (isset($_GET['invalid_id']) && $_GET['invalid_id'] == 1) {
    $notification = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        Invalid student ID provided.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$start = ($page - 1) * $limit;

$filter = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = '';
$params = [];
$types = '';

if ($filter) {
    $whereClause = "WHERE CONCAT(firstname, ' ', lastname) LIKE ? OR course LIKE ? OR email LIKE ?";
    $searchTerm = "%$filter%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = 'sss';
}

$countSql = "SELECT COUNT(*) AS total FROM registration $whereClause";
$countStmt = $conn->prepare($countSql);
if ($whereClause) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalResult = $countStmt->get_result();
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);


$sql = "SELECT * FROM registration $whereClause ORDER BY id DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$params[] = $start;
$params[] = $limit;
$types .= 'ii';

if ($whereClause) {
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $start, $limit);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records | Student Management System</title>
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
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 1.25rem 1.5rem;
        }
        
        .table th {
            background-color: var(--dark-color);
            color: white;
            font-weight: 500;
            vertical-align: middle;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #dee2e6;
        }
        
        .search-box {
            max-width: 500px;
            margin: 0 auto 20px;
        }
        
        .action-link {
            margin: 0 5px;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .action-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .page-link {
            color: var(--primary-color);
        }
        
        .empty-state {
            padding: 40px 0;
            text-align: center;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Student Records</h4>
                <a href="register.php" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-lg"></i> Add New
                </a>
            </div>
            
            <div class="card-body">
                <?php echo $notification; ?>
                
                <form method="GET" action="students.php" class="search-box">
                    <div class="input-group mb-4">
                        <input type="text" class="form-control" name="search" placeholder="Search by name, email or course..." value="<?= htmlspecialchars($filter) ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Gender</th>
                                <th>Course</th>
                                <th>Registered At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= htmlspecialchars($row['profile_picture']) ?>" alt="Profile" class="profile-img">
                                        </td>
                                        <td><?= htmlspecialchars($row['firstname']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['phone']) ?></td>
                                        <td><?= htmlspecialchars($row['gender']) ?></td>
                                        <td><?= htmlspecialchars($row['course']) ?></td>
                                        <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="action-link" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="delete.php?id=<?= $row['id'] ?>" class="action-link text-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this student?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <i class="bi bi-people"></i>
                                            <h5>No students found</h5>
                                            <p>Try adjusting your search or add a new student</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($filter) ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($filter) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($filter) ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>