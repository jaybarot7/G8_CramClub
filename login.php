<?php
include 'dbinit.php';
session_start();

class UserLogin {
    private $conn;
    public $emailError = "";
    public $passwordError = "";
    public $loginError = "";

    // Constructor to initialize the database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Validate and process the login form
    public function login($email, $password) {
        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');

        // Use prepared statements to prevent SQL injection
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $row["password"])) {
                $_SESSION["user_id"] = $row["user_id"];

                // Redirect based on user type
                if ($row["type"] == "user") {
                    header("Location: index.php");
                } elseif ($row["type"] == "admin") {
                    header("Location: add_product.php");
                }
                exit();
            } else {
                $this->passwordError = "Invalid email or password";
            }
        } else {
            $this->loginError = "User not found";
        }
        $stmt->close();
    }
}

$userLogin = new UserLogin($conn);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userLogin->login($_POST["email"], $_POST["password"]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CramClub: Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <main class="container mt-4 main-page-wrapper add-product-container">
        <h1 class="add-product-heading">Log In</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="email">E-mail:</label><br />
                <input type="email" name="email" class="form-control" placeholder="Email" required />
                <span class="text-danger"><?php echo htmlspecialchars($userLogin->emailError, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label><br />
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <span class="text-danger"><?php echo htmlspecialchars($userLogin->passwordError, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="text-danger"><?php echo htmlspecialchars($userLogin->loginError, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <input type="submit" name="submit" class="btn mx-auto add-product-button" value="Log In">
        </form>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>