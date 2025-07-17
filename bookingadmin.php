<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lakshani";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];
    
    // Validate action
    if (!in_array($action, ['approve', 'reject'])) {
        die("Invalid action");
    }
    
    // Update booking status
    $status = ($action == 'approve') ? 'approved' : 'rejected';
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    
    if ($stmt->execute()) {
        $message = "Booking request " . $status . " successfully!";
    } else {
        $message = "Error updating booking: " . $stmt->error;
    }
    
    $stmt->close();
}

// Fetch pending booking requests
$pending_bookings = [];
$sql = "SELECT * FROM bookings WHERE status IS NULL OR status = 'pending' ORDER BY date, start_time";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pending_bookings[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Approvals</title>
      <script src="js/admin.js"></script>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/adminbody.css">
    <link rel="stylesheet" href="css/footeradmin.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74EBD5 0%, #9FACE6 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 80px auto 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 30px;
            overflow: hidden;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
            position: relative;
            padding-bottom: 15px;
        }
        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            border-radius: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e6ed;
        }
        table th {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            color: #fff;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        table tr:hover {
            background-color: #f0f7ff;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-approve {
            background-color: #2ecc71;
            color: white;
        }
        .btn-approve:hover {
            background-color: #27ae60;
        }
        .btn-reject {
            background-color: #e74c3c;
            color: white;
        }
        .btn-reject:hover {
            background-color: #c0392b;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- HEADER SECTION -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <a href="https://www.sjp.ac.lk" target="_blank">
                    <img src="images/logo.png" alt="University Logo" />
                </a>
                <h1>Faculty Resources Reservation</h1>
                
            </div>
            <div class="user-info">
                <span>Welcome, Administrator</span>
                <a href="user.html" class="profile">
                    <button class="your-profile">Your profile</button>
                </a>
                <a href="start.php"><button class="logout-btn">Logout</button></a>
            </div>
        </div>
    </header>

    
    <!-- SIDEBAR NAVIGATION -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item ">
                    <a href="admin_dashboard.html" class="nav-link" data-page="dashboard">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="User_Management.php" class="nav-link" data-page="users">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">Users</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="#" class="nav-link" data-page="reservations">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#resources" class="nav-link" data-page="resources">
                        <span class="nav-icon">🏢</span>
                        <span class="nav-text">Resources</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <div class="container">
        <h2>Pending Booking Requests</h2>
        
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($pending_bookings)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Requester</th>
                        <th>Email</th>
                        <th>Resource</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Purpose</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['email']); ?></td>
                            <td><?php echo htmlspecialchars($booking['resource']); ?></td>
                            <td><?php echo htmlspecialchars($booking['date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['start_time'] . ' - ' . $booking['end_time']); ?></td>
                            <td><?php echo htmlspecialchars($booking['purpose']); ?></td>
                            <td>
                                <form method="post" class="action-buttons">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center;">No pending booking requests found.</p>
        <?php endif; ?>
    </div>
</body>
</html>