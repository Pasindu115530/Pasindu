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

// Fetch users from database
$sql1 = "SELECT full_name, email, role, registration_no FROM user";
$result = $conn->query($sql1);

$users = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Add a default status (you might want to store this in your database)
        $row['status'] = 'Active'; // You can modify this based on your actual status field
        $users[] = $row;
    }
}

// Fetch all reservations from bookings table only
$sql = "SELECT * FROM bookings ORDER BY date DESC, start_time DESC";
$result = $conn->query($sql);

$reservations = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Management & Reservations</title>
  <link rel="stylesheet" href="css/User_Management.css" /><script src="js/admin.js"></script>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/adminbody.css">
    <link rel="stylesheet" href="css/footeradmin.css">
</head>
<body>
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
                <li class="nav-item active">
                    <a href="#" class="nav-link" data-page="users">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="bookingadmin.php" class="nav-link" data-page="reservations">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">Bookings</span>
                    </a>
                </li>
              
            </ul>
        </nav>
    </aside>
  <div class="container">
    <h1>User Management</h1>

    <div class="form-card">
      <h2>Add New User</h2>
      <div class="form-group">
        <input type="text" id="name" placeholder="Name" />
        <input type="email" id="email" placeholder="Email" />
        <select id="role">
          <option value="student">Student</option>
          <option value="staff">Staff</option>
          <option value="admin">Admin</option>
        </select>
        <button class="add-btn" onclick="addUser()">Add User</button>
      </div>
    </div>

    <div class="table-section">
      <h2>All Users and Admins</h2>
      <table id="userTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Registration No</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo htmlspecialchars($user['full_name']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['role']); ?></td>
              <td><?php echo htmlspecialchars($user['registration_no'] ?? '—'); ?></td>
              <td>
                <span class="status-badge status-<?php echo strtolower($user['status']); ?>">
                  <?php echo ucfirst($user['status']); ?>
                </span>
              </td>
              <td class="action-buttons">
                <button class="action-btn edit-btn">Edit</button>
                <button class="action-btn delete-btn">Delete</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="container">
    <h1>Booking Management</h1>

    <div class="table-section">
      <h2>All Reservations</h2>
      <table id="reservationTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Resource</th>
            <th>Faculty</th>
            <th>Date</th>
            <th>Time</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservations as $reservation): ?>
            <tr>
              <td><?php echo htmlspecialchars($reservation['name']); ?></td>
              <td><?php echo htmlspecialchars($reservation['email']); ?></td>
              <td><?php echo htmlspecialchars($reservation['resource']); ?></td>
              <td><?php echo htmlspecialchars($reservation['faculty'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($reservation['date']); ?></td>
              <td><?php echo htmlspecialchars($reservation['start_time'] . ' - ' . $reservation['end_time']); ?></td>
              <td><?php echo htmlspecialchars($reservation['purpose']); ?></td>
              <td>
                <span class="status-badge status-<?php echo strtolower($reservation['status'] ?? 'pending'); ?>">
                  <?php echo ucfirst($reservation['status'] ?? 'pending'); ?>
                </span>
              </td>
              <td class="action-buttons">
                <?php if (($reservation['status'] ?? 'pending') === 'pending'): ?>
                  <form action="update_booking_status.php" method="post" style="display: inline;">
                    <input type="hidden" name="booking_id" value="<?php echo $reservation['id']; ?>">
                    <button type="submit" name="action" value="approve" class="action-btn approve-btn">Approve</button>
                  </form>
                  <form action="update_booking_status.php" method="post" style="display: inline;">
                    <input type="hidden" name="booking_id" value="<?php echo $reservation['id']; ?>">
                    <button type="submit" name="action" value="reject" class="action-btn reject-btn">Reject</button>
                  </form>
                <?php else: ?>
                  <span>—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>