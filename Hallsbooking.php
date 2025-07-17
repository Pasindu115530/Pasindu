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
    // Validate and sanitize input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phonenumber = mysqli_real_escape_string($conn, $_POST['phonenumber']);
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $resource = mysqli_real_escape_string($conn, $_POST['resource']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start-time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end-time']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);

    // // Additional validation
    // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //     die("Invalid email format");
    // }

    // // Check if email ends with @foc.ac.sjp.lk domain
    // if (!preg_match("/@foc\.ac\.sjp\.lk$/", $email)) {
    //     die("Email must be from @foc.ac.sjp.lk domain");
    // }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO bookings (name, email, phonenumber, faculty, resource, date, start_time, end_time, purpose) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $name, $email, $phonenumber, $faculty, $resource, $date, $start_time, $end_time, $purpose);

    // Execute the statement
     if ($stmt->execute()) {
        // Redirect to booking history page after successful submission
        header("Location: booking history.php");
        exit(); // Make sure to exit after header redirect
    } else {
        echo "Error: " . $stmt->error;
    }


    // Close statement
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
    <title>Resource Booking</title>
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
            padding: 20px;
            margin-top: 80px;
            padding-bottom: 60px;
        }
        
        .booking-container {
            background: white;
            padding: 30px;
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: linear-gradient(to bottom right, rgba(255,255,255,0.9), rgba(255,255,255,0.8));
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.3);
            margin: 20px 0;
        }
        
        .booking-container:hover {
            transform: translateY(-5px) scale(1.005);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }
        
        .booking-container h2 {
            text-align: center;
            color: var(--dark-color);
            margin-bottom: 25px;
            font-size: 32px;
            position: relative;
            padding-bottom: 10px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }
        
        .booking-container h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 5px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .booking-container label {
            display: block;
            margin-bottom: 10px;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .booking-container input, 
        .booking-container select,
        .booking-container textarea {
            width: 90%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: rgba(255,255,255,0.8);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .booking-container input:focus, 
        .booking-container select:focus,
        .booking-container textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.2);
            background-color: white;
        }
        
        .booking-container textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .booking-container button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .booking-container button:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            box-shadow: 0 8px 25px rgba(0, 206, 255, 0.4);
            transform: translateY(-2px);
        }
        
        .booking-container button:active {
            transform: translateY(0);
        }
        
        .booking-container button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        
        .booking-container button:hover::before {
            left: 100%;
        }
        
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236C5CE7' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 18px;
        }
      
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .booking-container {
            animation: float 6s ease-in-out infinite;
        }
        
        .form-group:focus-within label {
            color: var(--primary-color);
        }
        
        @media (max-width: 480px) {
            .booking-container {
                padding: 25px 20px;
                border-radius: 15px;
            }
            
            .booking-container h2 {
                font-size: 26px;
            }
            
            .booking-container input, 
            .booking-container select,
            .booking-container textarea {
                padding: 12px 15px;
            }
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
                <li><a href="resources.html" class="nav-item" data-section="resources">Resources</a></li>
                <li><a href="booking history.html" class="nav-item" data-section="booking">My Booking</a></li>
                <li><a href="reports.html" class="nav-item" data-section="reports">Reports</a></li>
                <li><a href="start.php" class="login-btn">Sign Out</a></li>
            </ul>
            <div class="burger" id="burger">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </nav>
    </header>    
    
    <main>
        <div class="booking-container">
            <h2>Resource Booking</h2>
            <form action="Hallsbooking.php" method="post">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                </div> 
                  
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="fc123456@foc.ac.sjp.lk" required>
                </div>
                
                <div class="form-group">
                    <label for="phonenumber">Contact No:</label>
                    <input type="tel" id="phonenumber" name="phonenumber" placeholder="Phone Number" required>
                </div>
               
                <div class="form-group">
                    <label for="faculty">Select Faculty:</label>
                    <select id="faculty" name="faculty" required>
                        <option value="">-- Choose Faculty --</option>
                        <option value="management">Management Studies and Commerce</option>
                        <option value="applied">Applied Science</option>
                        <option value="art">Humanities and Social Sciences</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="resource">Select Place:</label>
                    <select id="resource" name="resource" required disabled>
                        <option value="">-- Choose a Resource --</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date">Booking Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                
                <div class="form-group">
                    <label for="start-time">Start Time:</label>
                    <input type="time" id="start-time" name="start-time" required>
                </div>

                <div class="form-group">
                    <label for="end-time">End Time:</label>
                    <input type="time" id="end-time" name="end-time" required>
                </div>
                
                <div class="form-group">
                    <label for="purpose">Purpose of Booking:</label>
                    <textarea id="purpose" name="purpose" rows="3" placeholder="E.g., Group study, Event, Meeting" required></textarea>
                </div>
                
                <button type="submit">Submit Booking Request</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>Faculty Resources</h3>
                <ul>
                    <li><a href="Hallsbooking.html">Hall Booking</a></li>
                    <li><a href="Check Availability _Equipments.html">Equipment Checkout</a></li>
                    <li><a href="#">Schedule Calendar</a></li>
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

    <script>
    const facultyPlaces = {
        management: [
            { value: "fayol_hall", label: "Fayol Hall" },
            { value: "maslow_hall", label: "Maslow Hall" },
            { value: "sbm_hall", label: "SBM Hall" },
            { value: "sbsf_hall", label: "SBSF Hall" },
            { value: "A11_lecture_hall", label: "A11 Lecture Hall" },
            { value: "A12_lecture_hall", label: "A12 Lecture Hall" },
            { value: "A13_lecture_hall", label: "A13 Lecture Hall" }
        ],
        applied: [
            { value: "computer_lab", label: "Computer Lab" },
            { value: "nfc_3_hall", label: "NFC 3 Hall" },
            { value: "nfc_4_hall", label: "NFC 4 Hall" },
            { value: "nfc_6_hall", label: "NFC 6 Hall" }
        ],
        art: [
            { value: "WD101_lecture_hall", label: "WD101 Lecture Hall" },
            { value: "WD102_lecture_hall", label: "WD102 Lecture Hall" },
            { value: "WD103_lecture_hall", label: "WD103 Lecture Hall" },
            { value: "WD204_lecture_hall", label: "WD204 Lecture Hall" },
            { value: "WD301_lecture_hall", label: "WD301 Lecture Hall" },
            { value: "wt_exam_hall_no_4", label: "WT Exam Hall No 4" }
        ]
    };

    const facultySelect = document.getElementById('faculty');
    const resourceSelect = document.getElementById('resource');

    facultySelect.addEventListener('change', function() {
        const faculty = this.value;
        resourceSelect.innerHTML = '<option value="">-- Choose a Resource --</option>';
        if (!faculty || !facultyPlaces[faculty]) {
            resourceSelect.disabled = true;
            return;
        }

        facultyPlaces[faculty].forEach(place => {
            const option = document.createElement('option');
            option.value = place.value;
            option.textContent = place.label;
            resourceSelect.appendChild(option);
        });
        resourceSelect.disabled = false;
    });
    </script>
    <script src="js/navbar.js"></script>
    <script src="js/carousel.js"></script>
    <script src="js/animations.js"></script>
    <script src="js/mobile-nav.js"></script>
</body>
</html>