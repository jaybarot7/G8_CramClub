<?php
require_once 'dbinit.php';

class ProductManager {
    private $conn;
    public $errors = [];
    private $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxFileSize = 2 * 1024 * 1024; 

    public function __construct() {
        $this->connectToDatabase();
    }

    private function connectToDatabase() {
        $dbHost = getenv('DB_HOST') ?: 'localhost:3307';
        $dbUser = getenv('DB_USER') ?: 'root';
        $dbPass = getenv('DB_PASS') ?: '';
        $dbName = getenv('DB_NAME') ?: 'cakezone_db';

        $this->conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        if ($this->conn->connect_error) {
            die("Connection failed: " . htmlspecialchars($this->conn->connect_error));
        }
    }

    public function validateInput($data, $file) {
        if (empty($data['name'])) {
            $this->errors['name'] = "Product name is required.";
        } elseif (preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $data['name'])) {
            $this->errors['name'] = "Special characters are not allowed in the product name.";
        }

        if (empty($data['description'])) {
            $this->errors['description'] = "Description is required.";
        }

        if (empty($data['category'])) {
            $this->errors['category'] = "Select a category.";
        }

        if (empty($data['price'])) {
            $this->errors['price'] = "Price is required.";
        } elseif (!is_numeric($data['price'])) {
            $this->errors['price'] = "Price must be numeric.";
        }

        if (empty($data['quantity'])) {
            $this->errors['quantity'] = "Quantity is required.";
        } elseif (!is_numeric($data['quantity'])) {
            $this->errors['quantity'] = "Quantity must be numeric.";
        }

        if (!isset($file["img_url"]) || empty($file["img_url"]["tmp_name"])) {
            $this->errors['img_url'] = "Image is required.";
        } else {
            $fileType = mime_content_type($file["img_url"]["tmp_name"]);
            if (!in_array($fileType, $this->allowedFileTypes)) {
                $this->errors['img_url'] = "Only JPG, PNG, and GIF files are allowed.";
            }
            if ($file["img_url"]["size"] > $this->maxFileSize) {
                $this->errors['img_url'] = "File size exceeds the maximum limit of 2MB.";
            }
        }
    }

    public function uploadImage($file) {
        $targetDir = "uploads/";
        $uniqueName = uniqid() . "_" . basename($file["img_url"]["name"]);
        $targetFile = $targetDir . $uniqueName;

        if (!move_uploaded_file($file["img_url"]["tmp_name"], $targetFile)) {
            throw new Exception("Failed to upload image.");
        }

        return $targetFile;
    }

    public function saveProduct($data, $imgUrl) {
        $sql = "INSERT INTO products (name, description, category, price, quantity, img_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . htmlspecialchars($this->conn->error));
        }

        $stmt->bind_param(
            "sssdis",
            $data['name'],
            $data['description'],
            $data['category'],
            $data['price'],
            $data['quantity'],
            $imgUrl
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to execute query: " . htmlspecialchars($stmt->error));
        }

        $stmt->close();
        header("Location: add_success_page.php");
        exit;
    }

    public function __destruct() {
        $this->conn->close();
    }
}

?>