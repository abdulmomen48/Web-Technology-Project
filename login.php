<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "autozoneparts";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$email = $passwordInput = "";
$emailErr = $passErr = "";
$loginError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $passwordInput = $_POST["password"];
    if (empty($email)) {
        $emailErr = "Email is required";
    }
    if (empty($passwordInput)) {
        $passErr = "Password is required";
    }
    if (empty($emailErr) && empty($passErr)) {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashedPass);
            $stmt->fetch();
            if (password_verify($passwordInput, $hashedPass)) {
                $_SESSION["user_id"] = $id;
                $_SESSION["user_name"] = $name;
                header("Location: dashboard.php");
                exit;
            } else {
                $loginError = "Invalid password.";
            }
        } else {
            $loginError = "No account found with that email.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - AutoZone Parts</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f1f1f1;
    padding: 30px;
  }
  .login-section {
    background: #fff;
    max-width: 420px;
    margin: 0 auto;
    padding: 30px 40px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    border-top: 6px solid #e31837;
  }
  .login-section h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #e31837;
  }
  label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: #333;
  }
  input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
  }
  input:focus {
    border-color: #e31837;
    outline: none;
  }
  .error-message {
    color: #d93025;
    font-size: 13px;
    margin-top: 4px;
  }
  .login-error {
    background-color: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
  }
  button {
    width: 100%;
    padding: 12px;
    background: #e31837;
    color: white;
    font-size: 16px;
    border: none;
    margin-top: 25px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  button:hover {
    background: #b31229;
  }
  p.register-link {
    margin-top: 20px;
    text-align: center;
    font-size: 14px;
  }
  p.register-link a {
    color: #e31837;
    text-decoration: none;
  }
  p.register-link a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>

<section class="login-section">
  <h2>Login to AutoZone</h2>

  <?php if($loginError): ?>
    <div class="login-error"><?php echo $loginError; ?></div>
  <?php endif; ?>

  <form action="" method="post">
    <label for="email">Email Address:</label>
    <input type="email" id="email" name="email" 
      value="<?php echo htmlspecialchars($email); ?>">
    <div class="error-message"><?php echo $emailErr; ?></div>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <div class="error-message"><?php echo $passErr; ?></div>

    <button type="submit">Login</button>
  </form>

  <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
</section>

</body>
</html>
