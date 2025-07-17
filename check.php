<?php
header('Content-Type: text/html');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lakshani";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed");
}

// Get input data from POST
$faculty = $conn->real_escape_string($_POST['faculty'] ?? '');
$resource = $conn->real_escape_string($_POST['resource'] ?? '');
$date = $conn->real_escape_string($_POST['date'] ?? '');
$start_time = $conn->real_escape_string($_POST['start_time'] ?? '');
$end_time = $conn->real_escape_string($_POST['end_time'] ?? '');

// 1. Check if faculty has booked this hall before
$checkHistoryQuery = "SELECT * FROM bookings 
                     WHERE faculty = '$faculty' 
                     AND resource = '$resource' 
                     ORDER BY date DESC, start_time DESC 
                     LIMIT 1";
$historyResult = $conn->query($checkHistoryQuery);
$hasPreviousBooking = $historyResult->num_rows > 0;
$lastBooking = $hasPreviousBooking ? $historyResult->fetch_assoc() : null;

// 2. Check for time conflicts
$conflictQuery = "SELECT * FROM bookings 
                 WHERE resource = '$resource'
                 AND date = '$date'
                 AND (
                     (start_time < '$end_time' AND end_time > '$start_time')
                     OR (start_time = '$start_time' AND end_time = '$end_time')
                 )";
$conflictResult = $conn->query($conflictQuery);

$conflicts = [];
while($row = $conflictResult->fetch_assoc()) {
    $conflicts[] = $row;
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halls Availability Results</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/hero.css">
    <link rel="stylesheet" href="css/features.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/animations.css">
    <style>
        :root {
            --primary-color: #6C5CE7;
            --secondary-color: #00CEFF;
            --accent-color: #FF7675;
            --dark-color: #2D3436;
            --light-color: #FDFDFD;
            --success-color: #00B894;
            --warning-color: #FDCB6E;
            --primary-hover: #5a4dcc;
        }
        
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74EBD5 0%, #9FACE6 100%);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin-top: 80px;
            padding-bottom: 60px;
        }
        
        /* Results Container Styles */
        .results-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            transition: all 0.4s ease;
            background: linear-gradient(to bottom right, rgba(255,255,255,0.95), rgba(255,255,255,0.9));
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.3);
            animation: fadeInUp 0.5s ease-out forwards;
            transform: translateY(20px);
            opacity: 0;
        }
        
        .results-container.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .results-container:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }
        
        .results-container h2 {
            text-align: center;
            color: var(--dark-color);
            margin-bottom: 25px;
            font-size: 28px;
            position: relative;
            padding-bottom: 10px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }
        
        .results-container h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 5px;
        }
        
        .available {
            color: var(--success-color);
            font-weight: bold;
            background-color: rgba(0, 184, 148, 0.1);
            padding: 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid var(--success-color);
            transition: all 0.3s ease;
        }
        
        .available:hover {
            transform: translateX(5px);
            background-color: rgba(0, 184, 148, 0.15);
        }
        
        .not-available {
            color: var(--accent-color);
            font-weight: bold;
            background-color: rgba(255, 118, 117, 0.1);
            padding: 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid var(--accent-color);
            transition: all 0.3s ease;
        }
        
        .not-available:hover {
            transform: translateX(5px);
            background-color: rgba(255, 118, 117, 0.15);
        }
        
        .previous-booking {
            background-color: rgba(253, 203, 110, 0.15);
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid var(--warning-color);
            transition: all 0.3s ease;
        }
        
        .previous-booking:hover {
            transform: translateX(5px);
            background-color: rgba(253, 203, 110, 0.2);
        }
        
        .conflict-list {
            margin-top: 25px;
        }
        
        .conflict-list h4 {
            color: var(--dark-color);
            margin-bottom: 15px;
            font-size: 18px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        
        .conflict-item {
            margin-bottom: 15px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
        }
        
        .conflict-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .conflict-item p {
            margin: 8px 0;
            color: var(--dark-color);
        }
        
        .conflict-item strong {
            color: var(--primary-color);
        }
        
        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 25px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-align: center;
            width: 100%;
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
        }
        
        .back-btn:hover {
            background: linear-gradient(45deg, var(--primary-hover), #00b7e6);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.4);
        }
        
        .back-btn:active {
            transform: translateY(-1px);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .results-container {
                padding: 20px;
                margin: 0 15px;
            }
            
            .results-container h2 {
                font-size: 24px;
            }
        }

        /* Header and Footer styles remain the same as your original */
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar" id="navbar">
            <img src="images/uni_logo.png" class="img-shrink-on-scroll" alt="Website Logo" width="80" height="80">
            <div class="logo">Faculty Resources Reservation</div>
            <ul class="nav-links" id="nav-links">
                <div class="nav-indicator" id="nav-indicator"></div>
                <li><a href="index.html" class="nav-item " data-section="home">Home</a></li>
                <li><a href="dashboard.html" class="nav-item" data-section="dashboard">Dashboard</a></li>
                <li><a href="Booking history.php" class="nav-item" data-section="booking">My Booking</a></li>
            
                <li><a href="dslogin.php" class="login-btn">Sign Out</a></li>
            </ul>
            </ul>
            <div class="burger" id="burger">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </nav>
    </header> 
    <main>
        <div class="results-container visible">
            <h2>Availability Results</h2>
            
            <?php if (empty($conflicts)): ?>
                <p class="available">✅ The hall is available for your selected time slot!</p>
            <?php else: ?>
                <p class="not-available">❌ The hall is not available during your selected time.</p>
            <?php endif; ?>
            
            <?php if ($hasPreviousBooking): ?>
                <div class="previous-booking">
                    <p>📅 You have booked this hall before on <?= $lastBooking['date'] ?> 
                    from <?= $lastBooking['start_time'] ?> to <?= $lastBooking['end_time'] ?></p>
                </div>
            <?php else: ?>
                <p style="padding: 15px; background-color: rgba(0, 184, 148, 0.1); border-radius: 10px;">ℹ️ You have not booked this hall before.</p>
            <?php endif; ?>
            
            <?php if (!empty($conflicts)): ?>
                <div class="conflict-list">
                    <h4>Existing bookings during this time:</h4>
                    <?php foreach ($conflicts as $conflict): ?>
                        <div class="conflict-item">
                            <p><strong>Date:</strong> <?= $conflict['date'] ?></p>
                            <p><strong>Time:</strong> <?= $conflict['start_time'] ?> to <?= $conflict['end_time'] ?></p>
                            <p><strong>Booked by:</strong> <?= $conflict['faculty'] ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <a href="javascript:history.back()" class="back-btn">Back to Booking Form</a>
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
        // Add animation class when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const resultsContainer = document.querySelector('.results-container');
            resultsContainer.classList.add('visible');
        });
    </script>
    <script src="js/navbar.js"></script>
    <script src="js/carousel.js"></script>
    <script src="js/animations.js"></script>
    <script src="js/mobile-nav.js"></script>
</body>
</html>