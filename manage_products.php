<?php
$userType = "admin";
error_reporting(require('dbinit.php'));

class ProductManager
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllProducts()
    {
        $sql = "SELECT * FROM products";
        $result = $this->conn->query($sql);

        if ($result === false) {
            throw new Exception("Error fetching products: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function closeConnection()
    {
        $this->conn->close();
    }
}


$userType = "admin";
error_reporting(E_ALL);

$productManager = new ProductManager($conn);
$products = [];

try {
    $products = $productManager->getAllProducts();
} catch (Exception $e) {
    $error = $e->getMessage();
} finally {
    $productManager->closeConnection();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Zone: Manage Products</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="container mt-4 update-product-container">
        <div class="row main-page-wrapper">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./<?php echo htmlspecialchars($product["img_url"]); ?>" class="card-img-top product-image" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo ucfirst(htmlspecialchars($product["name"])); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product["description"]); ?></p>
                                <p class="card-text">Price: $<?php echo htmlspecialchars($product["price"]); ?></p>
                                <p class="card-text">Quantity: <?php echo htmlspecialchars($product["quantity"]); ?></p>
                            </div>
                            <div class="card-footer row card-footer-row">
                                <form action="product_update_details.php" method="GET" class="col-md-6">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product["product_id"]); ?>">
                                    <input type="submit" name="submit" class="btn btn-update update-product-button" value="Update">
                                </form>
                                <form class="delete-form col-md-6" action="delete.php" method="GET">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product["product_id"]); ?>">
                                    <input type="submit" name="submit" class="btn btn-delete update-product-button" value="Delete">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <div class="no-products">
                        <h4>No products found</h4>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>

</html>