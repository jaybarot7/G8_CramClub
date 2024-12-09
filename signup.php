<?php
include 'dbinit.php';

class UserSignup {
    private $conn;
    private $username;
    private $email;
    private $password;
    private $userType;
    public $usernameError = "";
    public $passwordError = "";
    public $emailError = "";
    public $signupSuccess = "";

    // Constructor to initialize the database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Validate user inputs
    private function validateInputs() {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->emailError = "Invalid email format";
        }
        if (!preg_match("/^[a-zA-Z0-9]{5,}$/", $this->username)) {
            $this->usernameError = "Username must be alphanumeric and at least 5 characters long";
        }
        if (!preg_match("/^[a-zA-Z0-9]{8,}$/", $this->password)) {
            $this->passwordError = "Password must be alphanumeric and at least 8 characters long.";
        }
    }

    // Check if the username or email already exists
    private function checkDuplicate() {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $this->username, $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $this->usernameError = "Username or email already exists";
        }
        $stmt->close();
    }

    // Register the user in the database
    private function registerUser() {
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $this->username, $this->email, $hashedPassword, $this->userType);

        if ($stmt->execute()) {
            $this->signupSuccess = "Sign Up successful!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    // Handle the user signup process
    public function signup($username, $email, $password, $userType) {
        $this->username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $this->email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $this->password = $password; 
        $this->userType = htmlspecialchars($userType, ENT_QUOTES, 'UTF-8');

        $this->validateInputs();
        $this->checkDuplicate();

        if (empty($this->usernameError) && empty($this->passwordError) && empty($this->emailError)) {
            $this->registerUser();
        }
    }
}

// Create a new instance of UserSignup
$userSignup = new UserSignup($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userSignup->signup($_POST["username"], $_POST["email"], $_POST["password"], $_POST["usertype"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cram Club: Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container mt-4 main-page-wrapper add-product-container">
        <h1 class="add-product-heading">Sign Up</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">User Name:</label><br />
                <input type="text" class="form-control" placeholder="Username" id="name" name="username" required>
                <span class="error"><?php echo htmlspecialchars($userSignup->usernameError, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label><br />
                <input type="email" class="form-control" placeholder="Email" name="email" required>
                <span class="error"><?php echo htmlspecialchars($userSignup->emailError, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label><br />
                <input type="password" class="form-control" placeholder="Password" name="password" required>
                <span class="error"><?php echo htmlspecialchars($userSignup->passwordError, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="form-group">
                <label for="type">User Type:</label><br />
                <select name="usertype" class="form-control">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <input type="submit" name="submit" class="btn mx-auto add-product-button" value="Sign Up">
        </form>
        <span class="success"><?php echo htmlspecialchars($userSignup->signupSuccess, ENT_QUOTES, 'UTF-8'); ?></span>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>

<script>
    <?php
    if (!empty($userSignup->usernameError) || !empty($userSignup->passwordError) || !empty($userSignup->emailError)) {
        echo "alert('Please fill the form correctly')";
    }
    ?>
</script>