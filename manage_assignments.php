<?php
// manage_assignments.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

$msg = "";
// Handle Upload (Existing Logic assumed, but writing simpler version for rewrite)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['assignment_file'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $faculty_id = $_SESSION['user_id'];
    
    $target_dir = "uploads/";
    $file_name = basename($_FILES["assignment_file"]["name"]);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO assignments (title, description, file_path, faculty_id, year, section, uploaded_at) VALUES ('$title', '$description', '$file_name', '$faculty_id', '$year', '$section', NOW())";
        if (mysqli_query($conn, $sql)) {
            $msg = "Assignment uploaded successfully!";
        } else {
            $msg = "Database Error: " . mysqli_error($conn);
        }
    } else {
        $msg = "File upload failed.";
    }
}

// Handle Delete
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $faculty_id = $_SESSION['user_id'];
    $del_query = "DELETE FROM assignments WHERE id='$delete_id' AND faculty_id='$faculty_id'";
    if(mysqli_query($conn, $del_query)) {
        $msg = "Assignment deleted.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Assignments | BookMyClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Background Theme -->
    <div class="hero-bg" style="position: fixed; z-index: -2;"></div>
    <div class="hero-overlay" style="position: fixed; z-index: -1;"></div>
    
    <!-- Top Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-shapes"></i>
                BookMyClass
            </a>
            <div class="nav-links">
                <a href="faculty_dashboard.php">Dashboard</a>
                <a href="book_class.php">Book Class</a>
                <a href="faculty_schedule.php">My Schedule</a>
                 <a href="view_students.php">Students</a>
                <a href="manage_assignments.php" style="color: var(--secondary);">Assignments</a>
                <a href="post_announcement.php">Announcements</a>
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 10px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Faculty'); ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;">Faculty</span>
                </div>
                <a href="logout.php" class="btn btn-primary" style="padding: 8px 18px; font-size: 0.9rem;">Logout</a>
            </div>
            <div class="mobile-menu-toggle">
                <i class="fa-solid fa-bars" style="color:var(--text-main); font-size:1.5rem; cursor:pointer;"></i>
            </div>
        </div>
    </nav>

    <div class="dashboard-layout">
        <main class="main-content fade-in-up glass-panel">
            <header class="dashboard-header">
                <h2>Manage Assignments</h2>
            </header>

            <div class="form-container" style="margin: 0; max-width: 600px;">
                <?php if($msg): ?>
                    <div style="color: green; margin-bottom: 20px;"><?php echo $msg; ?></div>
                <?php endif; ?>

                <form action="manage_assignments.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-input" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>File</label>
                        <input type="file" name="assignment_file" class="form-input" required>
                    </div>
                    <button type="submit" class="btn btn-primary form-btn">Upload</button>
                </form>
            </div>

            <div style="margin-top: 3rem;">
                <h3>Mapped Assignments</h3>
                <?php
                    $fac_id = $_SESSION['user_id'];
                    $assigns = mysqli_query($conn, "SELECT * FROM assignments WHERE faculty_id='$fac_id' ORDER BY uploaded_at DESC");
                    if(mysqli_num_rows($assigns) > 0):
                ?>
                <div class="table-container">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($assigns)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo date("M d, Y", strtotime($row['uploaded_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this assignment?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline" style="color: #dc3545; border-color: #dc3545;">Delete</button>
                                    </form>
                                    <a href="uploads/<?php echo $row['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline">View</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted">No assignments uploaded yet.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="js/landing.js"></script>
</body>
</html>
