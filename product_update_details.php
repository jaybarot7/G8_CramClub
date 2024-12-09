<?php
require 'dbinit.php';
$userType = "admin";

class ProductManager
{
    private $conn;
    private $errors = [];
    private $product = [];

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function fetchProduct($productId)
    {
        $query = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $this->product = $result->fetch_assoc();
        } else {
            throw new Exception("Product not found.");
        }
    }

    public function validateAndProcessForm($formData, $fileData)
    {
        $this->errors = [];

        // Validate inputs
        $this->validateInputs($formData, $fileData);

        if (!empty($this->errors)) {
            return false;
        }

        // Update the database
        return $this->updateProduct($formData, $fileData);
    }

    private function validateInputs($formData, $fileData)
    {
        if (empty($formData["name"])) {
            $this->errors['name'] = "Product name is required.";
        } elseif (preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $formData["name"])) {
            $this->errors['name'] = "Special characters are not allowed in the product name.";
        }

        if (empty($formData["description"])) {
            $this->errors['description'] = "Description is required.";
        }

        if (empty($formData['category'])) {
            $this->errors['category'] = "Select a category.";
        }

        if (empty($formData["price"])) {
            $this->errors['price'] = "Price is required.";
        } elseif (!is_numeric($formData["price"])) {
            $this->errors['price'] = "Price must be numeric.";
        }

        if (empty($formData["quantity"])) {
            $this->errors['quantity'] = "Quantity is required.";
        } elseif (!is_numeric($formData["quantity"])) {
            $this->errors['quantity'] = "Quantity must be numeric.";
        }

        if (isset($fileData["img_url"]) && !empty($fileData["img_url"]["tmp_name"])) {
            $check = getimagesize($fileData["img_url"]["tmp_name"]);
            if ($check === false) {
                $this->errors['img_url'] = "File is not an image.";
            }
        }
    }

    private function updateProduct($formData, $fileData)
    {
        $productId = (int)$formData["product_id"];
        $name = htmlspecialchars(trim($formData["name"]));
        $description = htmlspecialchars(trim($formData["description"]));
        $category = htmlspecialchars(trim($formData["category"]));
        $price = (float)$formData["price"];
        $quantity = (int)$formData["quantity"];

        $imgUrl = $this->product['img_url'];
        if (isset($fileData["img_url"]) && !empty($fileData["img_url"]["tmp_name"])) {
            $targetDir = "uploads/";
            $safeFileName = $targetDir . uniqid() . "_" . basename($fileData["img_url"]["name"]);
            move_uploaded_file($fileData["img_url"]["tmp_name"], $safeFileName);
            $imgUrl = htmlspecialchars($safeFileName);
        }

        $query = "UPDATE products SET name=?, description=?, category=?, price=?, quantity=?, img_url=? WHERE product_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssdisi", $name, $description, $category, $price, $quantity, $imgUrl, $productId);
        return $stmt->execute();
    }
}

$productManager = new ProductManager($conn);
$errors = [];
$product = [];

try {
    if (isset($_GET['product_id'])) {
        $productId = (int)$_GET['product_id'];
        $productManager->fetchProduct($productId);
        $product = $productManager->getProduct();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $success = $productManager->validateAndProcessForm($_POST, $_FILES);
        if ($success) {
            header("Location: manage_products.php");
            exit;
        } else {
            $errors = $productManager->getErrors();
            $product = $_POST;
        }
    }
} catch (Exception $e) {
    $errors['general'] = "An error occurred. Please try again.";
    error_log($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
    <main class="container mt-4">
        <h1>Update Product</h1>
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id'] ?? ''; ?>">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $product['name'] ?? ''; ?>">
                <?php if (isset($errors['name'])): ?>
                    <p class="text-danger"><?php echo $errors['name']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description"><?php echo $product['description'] ?? ''; ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <p class="text-danger"><?php echo $errors['description']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Select</option>
                    <option value="Fruit Cake" <?php echo ($product['category'] == 'Fruit Cake') ? 'selected' : ''; ?>>Fruit Cake</option>
                    <option value="Wedding Cake" <?php echo ($product['category'] == 'Wedding Cake') ? 'selected' : ''; ?>>Wedding Cake</option>
                    <option value="Pound cake" <?php echo ($product['category'] == 'Pound cake') ? 'selected' : ''; ?>>Pound cake</option>
                    <option value="BirthDay Cake" <?php echo ($product['category'] == 'BirthDay Cake') ? 'selected' : ''; ?>>Birth-Day Cake</option>
                    <option value="Cup Cakes" <?php echo ($product['category'] == 'Cup Cakes') ? 'selected' : ''; ?>>Cup Cakes</option>
                    <option value="Pastry" <?php echo ($product['category'] == 'Pastry') ? 'selected' : ''; ?>>Pastry</option>
                </select>
                <?php if (isset($errors['category'])) { ?>
                    <p class="product-error"><?php echo $errors['category']; ?></p>
                <?php } ?>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo $product['price'] ?? ''; ?>" min="0" step="0.01">
                <?php if (isset($errors['price'])): ?>
                    <p class="text-danger"><?php echo $errors['price']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $product['quantity'] ?? ''; ?>" min="0">
                <?php if (isset($errors['quantity'])): ?>
                    <p class="text-danger"><?php echo $errors['quantity']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="image_upload">Upload Image:</label>
                <input type="file" class="form-control-file" id="image_upload" name="img_url">
                <?php if (!empty($product['img_url'])): ?>
                    <p>Current Image:</p>
                    <img src="<?php echo htmlspecialchars($product['img_url']); ?>" alt="Product Image" style="max-width: 200px; max-height: 200px;">
                <?php endif; ?>
                <?php if (isset($errors['img_url'])): ?>
                    <p class="text-danger"><?php echo $errors['img_url']; ?></p>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn d-block mx-auto add-product-button" >Update Product</button>
        </form>
    </main>
</body>
</html>