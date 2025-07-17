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

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Your existing booking submission code...
}

// Fetch booking history from database
$bookings = [];
$sql = "SELECT * FROM bookings ORDER BY date DESC, start_time DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/hero.css">
    <link rel="stylesheet" href="css/features.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/animations.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74EBD5 0%, #9FACE6 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        main {
            flex: 1 0 auto;
            display: flex;
            justify-content: center;
            padding: 20px;
            margin-top: 80px;
            padding-bottom: 60px;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            border: 5px solid black;
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
            text-align: center;
            border: none;
        }
        table th {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
        }
        table tr {
            border-bottom: 1px solid #e0e6ed;
            transition: all 0.3s ease;
        }
        table tr:last-child {
            border-bottom: none;
        }
        table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        table tr:hover {
            background-color: #f0f7ff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .status-approved {
            color: #27ae60;
            font-weight: bold;
            background-color: rgba(39, 174, 96, 0.1);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        .status-pending {
            color: #e67e22;
            font-weight: bold;
            background-color: rgba(230, 126, 34, 0.1);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        .status-rejected {
            color: #e74c3c;
            font-weight: bold;
            background-color: rgba(231, 76, 60, 0.1);
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-classroom {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .badge-lab {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        .badge-audi {
            background-color: #fff3e0;
            color: #ff6d00;
        }
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .footer {
            flex-shrink: 0;
            background: var(--dark-color);
            color: white;
            padding: 40px 0 20px;
            margin-top: auto;
            width: 100%;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .footer-section h3 {
            color: white;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: var(--secondary-color);
        }

        .footer-section p {
            color: #ddd;
            margin: 5px 0;
        }

        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icons img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .social-icons img:hover {
            transform: scale(1.1);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #aaa;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar" id="navbar">
            <img src="images/uni_logo.png" class="img-shrink-on-scroll" alt="Website Logo" width="80" height="80">
            <div class="logo">Faculty Resources Reservation</div>
            <ul class="nav-links" id="nav-links">
                <div class="nav-indicator" id="nav-indicator"></div>
                <li><a href="index.html" class="nav-item active" data-section="home">Home</a></li>
                <li><a href="dashboard.html" class="nav-item" data-section="dashboard">Dashboard</a></li>
                <li><a href="resources.html" class="nav-item" data-section="resources">Resources</a></li>
                <li><a href="#" class="nav-item active"  data-section="booking">My Booking</a></li>
           
            </ul>
            <div class="burger" id="burger">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </nav>
    </header> 
    <main>
    <div class="container">
        <h2>Your Booking History</h2>
        <!--Here I add some table data for examples-->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Resource</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $index => $booking): ?>
                            <?php 
                            // Determine badge class based on resource type
                            $badgeClass = '';
                            if (strpos(strtolower($booking['resource']), 'lab') !== false) {
                                $badgeClass = 'badge-lab';
                            } elseif (strpos(strtolower($booking['resource']), 'auditorium') !== false || strpos(strtolower($booking['resource']), 'audi') !== false) {
                                $badgeClass = 'badge-audi';
                            } else {
                                $badgeClass = 'badge-classroom';
                            }
                            
                            // Determine status class
                            $statusClass = 'status-pending';
                            if (isset($booking['status'])) {
                                $statusText = strtolower($booking['status']);
                                if ($statusText === 'approved') {
                                    $statusClass = 'status-approved';
                                } elseif ($statusText === 'rejected') {
                                    $statusClass = 'status-rejected';
                                }
                            }
                            ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($booking['resource']); ?></span></td>
                                <td><?php echo htmlspecialchars($booking['date']); ?></td>
                                <td><?php echo htmlspecialchars($booking['start_time'] . ' - ' . $booking['end_time']); ?></td>
                                <td><?php echo htmlspecialchars($booking['purpose']); ?></td>
                                <td class="<?php echo $statusClass; ?>">
                                    <?php echo isset($booking['status']) ? htmlspecialchars(ucfirst($booking['status'])) : 'Pending'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No bookings found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
        </table>
    </div>
    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>Faculty Resources</h3>
                <ul>
                    <li><a href="#">Hall Booking</a></li>
                    <li><a href="#">Equipment Checkout</a></li>
                    <li><a href="#">Schedule Calendar</a></li>
                    <li><a href="#">Support</a></li>
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
    <script>
    document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(el => {
    el.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
        this.parentElement.style.zIndex = '1';
    });
    
    el.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
        this.parentElement.style.zIndex = '0';
    });
    });
    </script>
    <script src="js/navbar.js"></script>
    <script src="js/carousel.js"></script>
    <script src="js/animations.js"></script>
    <script src="js/mobile-nav.js"></script>
</body>
</html>