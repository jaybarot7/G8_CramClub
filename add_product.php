<?php
$userType = "admin";
require_once 'ProductManager.php';

$productManager = new ProductManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productManager->validateInput($_POST, $_FILES);

    if (empty($productManager->errors)) {
        $imgUrl = $productManager->uploadImage($_FILES);
        $productManager->saveProduct($_POST, $imgUrl);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Zone: Add Products</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <main class="container mt-4 main-page-wrapper add-product-container">
        <h1 class="add-product-heading">Add Product</h1>
        <?php if (isset($productManager->errors['general'])) { ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($productManager->errors['general']); ?>
            </div>
        <?php } ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($sanitizedPost['name']) ? htmlspecialchars($sanitizedPost['name']) : ''; ?>">
                <?php if (isset($productManager->errors['name'])) { ?>
                    <p class="product-error"><?php echo htmlspecialchars($productManager->errors['name']); ?></p>
                <?php } ?>
            </div>

            <div class="form-group">
                <label for="description">Product Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($sanitizedPost['description']) ? htmlspecialchars($sanitizedPost['description']) : ''; ?></textarea>
                <?php if (isset($productManager->errors['description'])) { ?>
                    <p class="product-error"><?php echo htmlspecialchars($productManager->errors['description']); ?></p>
                <?php } ?>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Select</option>
                    <?php
                    $categories = ['Fruit Cake', 'Wedding Cake', 'Pound cake', 'BirthDay Cake', 'Cup Cakes', 'Pastry'];
                    foreach ($categories as $category) {
                        $selected = isset($sanitizedPost['category']) && $sanitizedPost['category'] === $category ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($category) . "\" $selected>" . htmlspecialchars($category) . "</option>";
                    }
                    ?>
                </select>
                <?php if (isset($productManager->errors['category'])) { ?>
                    <p class="product-error"><?php echo htmlspecialchars($productManager->errors['category']); ?></p>
                <?php } ?>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="<?php echo isset($sanitizedPost['price']) ? htmlspecialchars($sanitizedPost['price']) : ''; ?>">
                <?php if (isset($productManager->errors['price'])) { ?>
                    <p class="product-error"><?php echo htmlspecialchars($productManager->errors['price']); ?></p>
                <?php } ?>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="<?php echo isset($sanitizedPost['quantity']) ? htmlspecialchars($sanitizedPost['quantity']) : ''; ?>">
                <?php if (isset($productManager->errors['quantity'])) { ?>
                    <p class="product-error"><?php echo htmlspecialchars($productManager->errors['quantity']); ?></p>
                <?php } ?>
            </div>

            <div class="form-group">
                <label for="img_url">Upload Image:</label>
                <input type="file" class="form-control-file" id="img_url" name="img_url">
                <?php if (isset($productManager->errors['img_url'])) { ?>
                    <p class="product-error"><?php echo htmlspecialchars($productManager->errors['img_url']); ?></p>
                <?php } ?>
            </div>

            <input type="submit" class="btn d-block mx-auto" name="submit" value="Add Product">
        </form>
    </main>

    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>