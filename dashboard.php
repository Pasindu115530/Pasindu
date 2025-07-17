<?php
// Start session at the VERY TOP (before any output)
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Consider using environment variables for credentials
$dbname = "lakshani";

// Create connection with error handling
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Function to safely fetch count
function getStatusCount($conn, $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE status = ?");
    if (!$stmt) {
        // Handle prepare error
        error_log("Prepare failed: " . $conn->error);
        return 0;
    }
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['count'] ?? 0; // Return 0 if count not found
}

function getNameFromEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT full_name FROM user WHERE email = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return null;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['full_name'] ?? null;
}

// Get counts for each status
$pendingCount = getStatusCount($conn, 'Pending');
$approvedCount = getStatusCount($conn, 'Approved');
$rejectedCount = getStatusCount($conn, 'Rejected');
$getname = getNameFromEmail($conn, $email);

// Get total count (using prepared statement for consistency)
$totalCount = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalCount = $row['total'] ?? 0;
    $stmt->close();
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="CSS/global.css">
    <link rel="stylesheet" href="CSS/navbar.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/userdashboard.css">
    <link rel="stylesheet" href="CSS/quickactions.css">
    <link rel="stylesheet" href="CSS/stats.css">
    <link rel="stylesheet" href="CSS/bookinglist.css">
    <link rel="stylesheet" href="CSS/recentactivity.css">
    <link rel="stylesheet" href="CSS/availability.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="dashboard-body">
    <header>
        <nav class="navbar" id="navbar">
            <img src="images/uni_logo.png" class="img-shrink-on-scroll" alt="Website Logo" width="80" height="80">
            <div class="logo">Faculty Resources Reservation</div>
            <ul class="nav-links" id="nav-links">
                <div class="nav-indicator" id="nav-indicator"></div>
                <li><a href="index.html" class="nav-item" data-section="home">Home</a></li>
                <li><a href="" class="nav-item active" data-section="dashboard">Dashboard</a></li>
                <li><a href="reservationPage.html" class="nav-item" data-section="resources">Reservation</a></li>
                <li><a href="Booking history.php" class="nav-item" data-section="booking">My Booking</a></li>
                <li><a href="start.php" class="login-btn">Sign Out</a></li>
            </ul>
            <div class="burger" id="burger">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </nav>
    </header>

    <main class="dashboard-main">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="welcome">
                <h1>Welcome Back,<?= $getname ?></h1>
                <p>Faculty of Computing - Computer Science Department</p>
                <div class="time" id="currentTime"></div>
            </div>
            <div class="useravatar">
                <i class="fas fa-user-circle"></i>
            </div>
        </section>

        <!-- Stats Cards -->
        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $approvedCount ?></h3>
                        <p>Active Bookings</p>
                    </div>
                    
                </div>

                <div class="stat-card dashboard-stable">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $pendingCount ?></</h3>
                        <p>Pending Approvals</p>
                    </div>
                  
                </div>

                <div class="stat-card dashboard-stable">
                    <div class="stat-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $totalCount ?></h3>
                        <p>Total Reservations</p>
                    </div>
                    
                </div>

                <div class="stat-card dashboard-stable">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <h3>4.8</h3>
                        <p>Rating Score</p>
                    </div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i> 0.2
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="quick-actions-section dashboard-stable">
            <h2>Quick Actions</h2>
            <div class="actions-grid">
                <div class="action-card" onclick="location.href='reservationPage.html'">
                    <i class="fas fa-plus-circle"></i>
                    <h3>New Booking</h3>
                    <p>Reserve a resource</p>
                </div>
                <div class="action-card" onclick="location.href='Check Availability _Halls.html'">
                    <i class="fas fa-search"></i>
                    <h3>Check Availability</h3>
                    <p>View available resources</p>
                </div>
                <div class="action-card" onclick="location.href='Booking history.php'">
                    <i class="fas fa-list"></i>
                    <h3>My Bookings</h3>
                    <p>Manage reservations</p>
                </div>
            </div>
        </section>

        <!-- Dashboard Content Grid -->
        <section class="usercontent">
            <!-- Upcoming Bookings -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> Upcoming Bookings</h3>
                    <a href="mybooking.html" class="view-all">View All</a>
                </div>
                <div class="bookings-list">
                    <div class="booking-item">
                        <div class="booking-info">
                            <h4>Computer Lab A-301</h4>
                            <p>Programming Workshop</p>
                            <span class="booking-time">Today, 2:00 PM - 4:00 PM</span>
                        </div>
                        <div class="booking-status approved">Approved</div>
                    </div>
                    <div class="booking-item">
                        <div class="booking-info">
                            <h4>Lecture Hall B-205</h4>
                            <p>Data Structures Class</p>
                            <span class="booking-time">Tomorrow, 10:00 AM - 12:00 PM</span>
                        </div>
                        <div class="booking-status pending">Pending</div>
                    </div>
                    <div class="booking-item">
                        <div class="booking-info">
                            <h4>Auditorium Main</h4>
                            <p>Department Seminar</p>
                            <span class="booking-time">Dec 20, 3:00 PM - 5:00 PM</span>
                        </div>
                        <div class="booking-status approved">Approved</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-bell"></i> Recent Activity</h3>
                </div>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="activity-info">
                            <p>Booking approved for Lab A-301</p>
                            <span class="activity-time">2 hours ago</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon info">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="activity-info">
                            <p>New booking created for Auditorium</p>
                            <span class="activity-time">5 hours ago</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="activity-info">
                            <p>Booking reminder: Meeting in 1 hour</p>
                            <span class="activity-time">1 day ago</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resource Availability -->
            <div class="content-card full-width">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> Resource Availability Today</h3>
                    <button class="btn-secondary" onclick="location.href='availability.html'">View Details</button>
                </div>
                <div class="availability-grid">
                    <div class="availability-item">
                        <div class="resource-info">
                            <h4>Computer Labs</h4>
                            <p>8 available out of 12</p>
                        </div>
                        <div class="availability-bar">
                            <div class="bar-fill" style="width: 67%"></div>
                        </div>
                        <span class="availability-percent">67%</span>
                    </div>
                    <div class="availability-item">
                        <div class="resource-info">
                            <h4>Lecture Halls</h4>
                            <p>15 available out of 20</p>
                        </div>
                        <div class="availability-bar">
                            <div class="bar-fill" style="width: 75%"></div>
                        </div>
                        <span class="availability-percent">75%</span>
                    </div>
                    <div class="availability-item">
                        <div class="resource-info">
                            <h4>Equipment</h4>
                            <p>28 available out of 35</p>
                        </div>
                        <div class="availability-bar">
                            <div class="bar-fill" style="width: 80%"></div>
                        </div>
                        <span class="availability-percent">80%</span>
                    </div>
                    <div class="availability-item">
                        <div class="resource-info">
                            <h4>Study Rooms</h4>
                            <p>6 available out of 10</p>
                        </div>
                        <div class="availability-bar">
                            <div class="bar-fill" style="width: 60%"></div>
                        </div>
                        <span class="availability-percent">60%</span>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>Faculty Resources</h3>
                <ul>
                    <li><a href="reservationPage.html">Hall Booking</a></li>
                    <li><a href="equipments.html">Equipment Checkout</a></li>
                   
                    <li><a href="help.html">Support</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: support@facultyreserve.edu</p>
                <p>Phone: +1 (800) 123-4567</p>
                <p>Hours: Mon–Fri, 8am–6pm</p>
            </div>

            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><img src="https://th.bing.com/th/id/OIP.QHODby_bS81-x2of8vCIhgHaHa?w=189&h=189&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3" alt="Facebook"></a>
                    <a href="#"><img src="https://static.vecteezy.com/system/resources/previews/018/910/704/original/tiktok-logo-tiktok-symbol-tiktok-icon-free-free-vector.jpg" alt="TikTok"></a>
                    <a href="#"><img src="https://th.bing.com/th/id/OIP.NUFU5mhqhqOr82Ge-CwjawHaHv?rs=1&pid=ImgDetMain" alt="Instagram"></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Faculty Resources Reservation System. All rights reserved.</p>
        </div>
    </footer>

    <script src="JS/mobile-nav.js"></script>
    <script src="JS/1dashboard.js"></script>
    <script src="JS/navbar.js"></script>
    
</body>
</html>
