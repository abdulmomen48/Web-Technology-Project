<?php
// Database connection settings
$host = "localhost";
$dbname = "scrapbhai";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];

   
        if ($password !== $confirm_password) {
            die(" Passwords do not match!");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die(" Invalid email format!");
        }

       
        $check_sql = "SELECT COUNT(*) FROM users WHERE email = :email OR phone = :phone";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([':email' => $email, ':phone' => $phone]);
        $exists = $check_stmt->fetchColumn();

        if ($exists > 0) {
            if ($exists > 0) {
    echo "This email or phone number is already registered!";
    exit();
}

            die(" This email or phone number is already registered!");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      
        $sql = "INSERT INTO users (name, email, phone, password) 
                VALUES (:name, :email, :phone, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':password' => $hashed_password
        ]);

        header("Location: thankyou.html");
        exit();
    }
} catch (PDOException $e) {
    echo " Database Error: " . $e->getMessage();
}

header("Location: login.html");
exit();

?>
