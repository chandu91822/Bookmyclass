<?php
// post_announcement.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']); // This might be 'description' in form input name based on prev file view
    // Actually the form has 'description', let's check input name again or stick to previous file
    // The previous file view showed <textarea name="description">. I will use $_POST['description'] as message.
    $message = mysqli_real_escape_string($conn, $_POST['description']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    
    $sql = "INSERT INTO announcements (title, message, year, section, type, created_at) VALUES ('$title', '$message', '$year', '$section', '$type', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        $msg = "Announcement posted successfully!";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Announcement | BookMyClass</title>
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
                <a href="manage_assignments.php">Assignments</a>
                <a href="post_announcement.php" style="color: var(--secondary);">Announcements</a>
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
                <h2>Post Announcement</h2>
            </header>

            <div class="form-container" style="max-width: 800px; margin: 0 auto;">
                <?php if($msg): ?>
                    <div style="color: green; margin-bottom: 20px;"><?php echo $msg; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-input" required placeholder="e.g. Mid-term Exam Schedule">
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-input" rows="5" required placeholder="Enter announcement details..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary form-btn">Post Announcement</button>
                </form>
            </div>

            <div style="margin-top: 3rem;">
                <h3 style="margin-bottom: 1rem;">My Announcements</h3>
                <?php
                    $my_anns = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC");
                    if(mysqli_num_rows($my_anns) > 0):
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
                            <?php while($ann = mysqli_fetch_assoc($my_anns)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ann['title']); ?></td>
                                <td><?php echo date("M d, Y", strtotime($ann['created_at'])); ?></td>
                                <td>
                                    <form method="POST" action="delete_announcement.php" onsubmit="return confirm('Delete this announcement?');">
                                        <input type="hidden" name="id" value="<?php echo $ann['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline" style="color: #dc3545; border-color: #dc3545;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted">No announcements posted.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="js/landing.js"></script>
</body>
</html>
