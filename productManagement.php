<?php
class ProductManager
{
    private $conn;
    private $errors = [];
    private $product;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
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

    public function validateAndProcessForm($formData, $fileData)
    {
        $this->validateForm($formData, $fileData);

        if (empty($this->errors)) {
            return $this->updateProduct($formData, $fileData);
        }

        return false;
    }

    private function validateForm($formData, $fileData)
    {
        $name = $formData["name"];
        $description = $formData["description"];
        $price = $formData["price"];
        $quantity = $formData["quantity"];

        if (empty($name)) {
            $this->errors['name'] = "Product name is required.";
        } elseif (preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $name)) {
            $this->errors['name'] = "Special characters are not allowed in the product name.";
        }

        if (empty($description)) {
            $this->errors['description'] = "Description is required.";
        }

        if (empty($price)) {
            $this->errors['price'] = "Price is required.";
        } elseif (!is_numeric($price)) {
            $this->errors['price'] = "Price must be numeric.";
        }

        if (empty($quantity)) {
            $this->errors['quantity'] = "Quantity is required.";
        } elseif (!is_numeric($quantity)) {
            $this->errors['quantity'] = "Quantity must be numeric.";
        }

        if (isset($fileData["img_url"]) && !empty($fileData["img_url"]["tmp_name"])) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($fileData["img_url"]["name"]);
            $check = getimagesize($fileData["img_url"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($fileData["img_url"]["tmp_name"], $targetFile)) {
                    $this->product['img_url'] = $targetFile; // Update with new file path
                } else {
                    $this->errors['img_url'] = "Failed to upload the file.";
                }
            } else {
                $this->errors['img_url'] = "File is not an image.";
            }
        } else {
            $this->product['img_url'] = $this->product['img_url']; // Retain the existing image
        }
    
    }

    private function updateProduct($formData, $fileData)
    {
        $query = "UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, img_url = ? WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            "ssdsdi",
            $formData['name'],
            $formData['description'],
            $formData['price'],
            $formData['quantity'],
            $this->product['img_url'],
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

    public function getProduct()
    {
        return $this->product;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
?>