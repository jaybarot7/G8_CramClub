<?php

require_once 'dbinit.php';

class ProductManager
{
    private $conn;
    private $errors = [];
    private $product;

    public function __construct()
    {
        $this->connectToDatabase();
    }

    private function connectToDatabase()
    {
        $this->conn = new mysqli("localhost:3307", "root", "", "cakezone_db");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function fetchProduct($productId)
    {
        if (!$productId) {
            return "Product ID is not provided.";
        }

        $query = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $this->product = $result->fetch_assoc();
        } else {
            return "Product not found.";
        }

        $stmt->close();
    }

    public function validateInput($formData, $fileData)
    {
        if (empty($formData['name'])) {
            $this->errors['name'] = "Product name is required.";
        } elseif (preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $formData['name'])) {
            $this->errors['name'] = "Special characters are not allowed in the product name.";
        }

        if (empty($formData['description'])) {
            $this->errors['description'] = "Description is required.";
        }

        if (empty($formData['category'])) {
            $this->errors['category'] = "Select a category.";
        }

        if (empty($formData['price'])) {
            $this->errors['price'] = "Price is required.";
        } elseif (!is_numeric($formData['price'])) {
            $this->errors['price'] = "Price must be numeric.";
        }

        if (empty($formData['quantity'])) {
            $this->errors['quantity'] = "Quantity is required.";
        } elseif (!is_numeric($formData['quantity'])) {
            $this->errors['quantity'] = "Quantity must be numeric.";
        }

        if (isset($fileData["img_url"]) && !empty($fileData["img_url"]["tmp_name"])) {
            $check = getimagesize($fileData["img_url"]["tmp_name"]);
            if ($check === false) {
                $this->errors['img_url'] = "Uploaded file is not an image.";
            }
        } else {
            $this->errors['img_url'] = "Image is required.";
        }
    }

    public function uploadImage($fileData)
    {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($fileData["img_url"]["name"]);
        if (move_uploaded_file($fileData["img_url"]["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            $this->errors['img_url'] = "Failed to upload the image.";
            return null;
        }
    }

    public function saveProduct($formData, $fileData)
    {
        $this->validateInput($formData, $fileData);

        if (!empty($this->errors)) {
            return false;
        }

        $imgUrl = $this->uploadImage($fileData);
        if (!$imgUrl) {
            return false;
        }

        $sql = "INSERT INTO products (name, description, category, price, quantity, img_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssdis",
            $formData['name'],
            $formData['description'],
            $formData['category'],
            $formData['price'],
            $formData['quantity'],
            $imgUrl
        );

        $stmt->execute();
        $stmt->close();
        header("Location: add_success_page.php");
        exit;
    }

    public function updateProduct($formData, $fileData)
    {
        $this->validateInput($formData, $fileData);

        if (!empty($this->errors)) {
            return false;
        }

        $imgUrl = $this->product['img_url'];
        if (isset($fileData["img_url"]) && !empty($fileData["img_url"]["tmp_name"])) {
            $imgUrl = $this->uploadImage($fileData);
        }

        $query = "UPDATE products SET name = ?, description = ?, category = ?, price = ?, quantity = ?, img_url = ? WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "sssdisi",
            $formData['name'],
            $formData['description'],
            $formData['category'],
            $formData['price'],
            $formData['quantity'],
            $imgUrl,
            $formData['product_id']
        );

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: manage_products.php");
            exit;
        }

        $stmt->close();
        return false;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}