<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "autozoneparts"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$nameErr = $emailErr = $phoneErr = $passErr = $confirmPassErr = "";
$name = $email = $phone = "";
$successMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
        $valid = false;
    } else {
        $name = trim($_POST["name"]);
        if (strlen($name) < 2) {
            $nameErr = "Name must be at least 2 characters";
            $valid = false;
        }
    }

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

    if (empty($_POST["phone"])) {
        $phoneErr = "Phone number is required";
        $valid = false;
    } else {
        $phone = trim($_POST["phone"]);
        if (!preg_match('/^\d{10,}$/', $phone)) {
            $phoneErr = "Phone number must be at least 10 digits and numbers only";
            $valid = false;
        }
    }

    if (empty($_POST["password"])) {
        $passErr = "Password is required";
        $valid = false;
    } else {
        $passwordInput = $_POST["password"];
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $passwordInput)) {
            $passErr = "Password must be at least 8 chars with uppercase, lowercase, number & special char";
            $valid = false;
        }
    }

    if (empty($_POST["confirm-password"])) {
        $confirmPassErr = "Confirm Password is required";
        $valid = false;
    } else {
        $confirm_password = $_POST["confirm-password"];
        if (isset($passwordInput) && $confirm_password !== $passwordInput) {
            $confirmPassErr = "Passwords do not match";
            $valid = false;
        }
    }

    if ($valid) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $emailErr = "Email already registered";
        } else {
            $stmt->close();

            $hashedPass = password_hash($passwordInput, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashedPass);

            if ($stmt->execute()) {
                $successMsg = "Welcome to AutoZone Parts! Your account has been created.";
                $name = $email = $phone = "";
            } else {
                $successMsg = "Error: " . $conn->error;
            }
            $stmt->close();
        }
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
<title>Register - AutoZone Parts</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f1f1f1;
    padding: 30px;
  }
  .register-section {
    background: #fff;
    max-width: 420px;
    margin: 0 auto;
    padding: 30px 40px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    border-top: 6px solid #e31837; /* AutoZone Red */
  }
  .register-section h2 {
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
  input[type="text"], input[type="email"], input[type="tel"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
  }
  input[type="text"]:focus, input[type="email"]:focus, input[type="tel"]:focus, input[type="password"]:focus {
    border-color: #e31837;
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
  .success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
  }
  p.login-link {
    margin-top: 20px;
    text-align: center;
    font-size: 14px;
  }
  p.login-link a {
    color: #e31837;
    text-decoration: none;
  }
  p.login-link a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>

<section class="register-section">
  <h2>Create Your AutoZone Account</h2>

  <?php if($successMsg): ?>
    <div class="success-message"><?php echo $successMsg; ?></div>
  <?php endif; ?>

  <form action="" method="post" class="register-form" novalidate>

    <label for="name">Full Name:</label>
    <input type="text" id="name" name="name" required 
      value="<?php echo htmlspecialchars($name); ?>" 
      class="<?php echo errorClass($nameErr); ?>">
    <div class="error-message"><?php echo $nameErr; ?></div>

    <label for="email">Email Address:</label>
    <input type="email" id="email" name="email" required 
      value="<?php echo htmlspecialchars($email); ?>" 
      class="<?php echo errorClass($emailErr); ?>">
    <div class="error-message"><?php echo $emailErr; ?></div>

    <label for="phone">Phone Number:</label>
    <input type="tel" id="phone" name="phone" required 
      value="<?php echo htmlspecialchars($phone); ?>" 
      class="<?php echo errorClass($phoneErr); ?>">
    <div class="error-message"><?php echo $phoneErr; ?></div>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required 
      class="<?php echo errorClass($passErr); ?>">
    <div class="error-message"><?php echo $passErr; ?></div>

    <label for="confirm-password">Confirm Password:</label>
    <input type="password" id="confirm-password" name="confirm-password" required 
      class="<?php echo errorClass($confirmPassErr); ?>">
    <div class="error-message"><?php echo $confirmPassErr; ?></div>

    <button type="submit">Register</button>
  </form>
  <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
</section>

</body>
</html>
