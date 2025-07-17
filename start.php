<?php
session_start();

// Handle login first before any HTML output
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $db = "lakshani";
    $user = "root";
    $pass = "";

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';


    // Check user credentials
    $stmt = $conn->prepare("SELECT full_name, email, password_hash, role FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Simple password comparison (INSECURE)
        if ($password === $user['password_hash']) {
            // Set session variables
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
           
            
            // Redirect based on role
            header("Location: " . ($user['role'] === 'admin' ? 'admin_dashboard.html' : 'index.html'));
            exit();
        }
    }
    
    // Generic error message
    $loginError = "Invalid email or password";
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Faculty Resources Reservation System</title>
    <link rel="stylesheet" href="css/login_styles.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/features.css">
    <style>
        .page-header {
            text-align: center;
            margin: 2rem 0;
            color: var(--light-color);
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .page-header h3 {
            font-size: 1.5rem;
            font-weight: normal;
        }
        
        .error-message {
            color: var(--error-color);
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Faculty Resources Reservation System</h1>
        <h3>Welcome</h3>
    </div>
    <main class="auth-container">
        <div class="auth-form">
            <h1>Login to Your Account</h1>
            <?php if (!empty($loginError)): ?>
                <div class="error-message"><?php echo htmlspecialchars($loginError); ?></div>
            <?php endif; ?>
            <form id="loginForm" action="start.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="ds.fogotpassword.html" class="forgot-password">Forgot password?</a>
                </div>
                <button type="submit" class="btn-primary">Login</button>
            </form>
            <div class="auth-footer">
                <p>Don't have an account? <a href="dsregister.php">Register here</a></p>
            </div>
        </div>
    </main>

    <script src="../components/login.js"></script>
</body>
</html>