<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "lakshani";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$registrationError = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullname = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $registration_no = htmlspecialchars($_POST['facultyId']);
    $department = htmlspecialchars($_POST['department']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validate password match
    if ($password !== $confirmPassword) {
        $registrationError = "Passwords do not match.";
    } else {
        // Check if email or registration number already exists
        $checkSql = "SELECT * FROM user WHERE email = ? OR registration_no = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $email, $registration_no);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            $registrationError = "Email or Registration Number already exists.";
        } else {
            // Hash password
            // $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Set default role
            $role = "user";
            
            // Insert into database
            $insertSql = "INSERT INTO user (full_name, email, registration_no, department, password_hash, created_at, role)
                          VALUES (?, ?, ?, ?, ?, NOW(), ?)";
            
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ssssss", $fullname, $email, $registration_no, $department, $password, $role);
            
            if ($insertStmt->execute()) {
                $successMessage = "Registration successful!";
                // You can redirect to login page after successful registration
            header("Location: start.php");
                exit();
            } else {
                $registrationError = "Error: " . $insertStmt->error;
            }
            
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Faculty Resources Reservation System</title>
    <link rel="stylesheet" href="css/login_styles.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    

    <main class="auth-container">
        <div class="auth-form">
            <h1>Create an Account</h1>
            
            <?php if (!empty($registrationError)): ?>
                <div class="error-message"><?php echo $registrationError; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($successMessage)): ?>
                <div class="success-message"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            
            <form id="registerForm" action="dsregister.php" method="POST">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required value="<?php echo isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="facultyId">Registration No</label>
                    <input type="text" id="facultyId" name="facultyId" required value="<?php echo isset($_POST['facultyId']) ? htmlspecialchars($_POST['facultyId']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <option value="computer_science" <?php echo (isset($_POST['department']) && $_POST['department'] == 'computer_science') ? 'selected' : ''; ?>>Computer Science</option>
                        <option value="mathematics" <?php echo (isset($_POST['department']) && $_POST['department'] == 'mathematics') ? 'selected' : ''; ?>>Software Engineering</option>
                        <option value="physics" <?php echo (isset($_POST['department']) && $_POST['department'] == 'physics') ? 'selected' : ''; ?>>Information Systems</option>
                        <option value="chemistry" <?php echo (isset($_POST['department']) && $_POST['department'] == 'chemistry') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>
                <button type="submit" class="btn-primary">Register</button>
            </form>
            <div class="auth-footer">
                <p>Already have an account? <a href="start.php">Login here</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2023 Faculty Resources Reservation System. All rights reserved.</p>
    </footer>

    <script src="../components/login.js"></script>
</body>
</html>

<?php
$conn->close();
?>