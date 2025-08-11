<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scrapbhai";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$emailErr = $passErr = "";
$email = "";
$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $valid = false;
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            $valid = false;
        }
    }

    if (empty($_POST["password"])) {
        $passErr = "Password is required";
        $valid = false;
    } else {
        $passwordInput = $_POST["password"];
    }

    if ($valid) {
        $stmt = $conn->prepare("SELECT password, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($hashedPassword, $name);
            $stmt->fetch();

            if (password_verify($passwordInput, $hashedPassword)) {
                $successMsg = "Welcome, " . htmlspecialchars($name) . "! You are logged in.";
                // Here you can start a session or redirect to a dashboard page
            } else {
                $passErr = "Incorrect password";
            }
        } else {
            $emailErr = "No account found with that email";
        }

        $stmt->close();
    }
}

$conn->close();

function errorClass($error) {
    return $error ? "input-error" : "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" >
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Scrap Bhai</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f4f7f8;
    padding: 30px;
  }
  .login-section {
    background: #fff;
    max-width: 400px;
    margin: 0 auto;
    padding: 30px 40px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
  .login-section h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
  }
  label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: #555;
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
  input[type="email"]:focus, input[type="password"]:focus {
    border-color: #007BFF;
    outline: none;
  }
  .input-error {
    border-color: #d93025 !important;
  }
  .error-message {
    color: #d93025;
    font-size: 13px;
    margin-top: 4px;
  }
  button {
    width: 100%;
    padding: 12px;
    background: #007BFF;
    color: white;
    font-size: 16px;
    border: none;
    margin-top: 25px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  button:hover {
    background: #0056b3;
  }
  .success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
  }
  p.register-link {
    margin-top: 20px;
    text-align: center;
    font-size: 14px;
  }
  p.register-link a {
    color: #007BFF;
    text-decoration: none;
  }
  p.register-link a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>

<section class="login-section">
  <h2>Login to Your Account</h2>

  <?php if($successMsg): ?>
    <div class="success-message"><?php echo $successMsg; ?></div>
  <?php endif; ?>

  <form action="" method="post" novalidate>
    <label for="email">Email Address:</label>
    <input type="email" id="email" name="email" required
      value="<?php echo htmlspecialchars($email); ?>"
      class="<?php echo errorClass($emailErr); ?>">
    <div class="error-message"><?php echo $emailErr; ?></div>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required
      class="<?php echo errorClass($passErr); ?>">
    <div class="error-message"><?php echo $passErr; ?></div>

    <button type="submit">Login</button>
  </form>
  <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
</section>

</body>
</html>
