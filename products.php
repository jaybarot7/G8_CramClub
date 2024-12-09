<?php
$userType = "user";
require_once 'dbinit.php';

class ProductPage {
    private $conn;

    public function __construct() {
        $this->connectToDatabase();
    }

    private function connectToDatabase() {
        $this->conn = new mysqli("localhost:3307", "root", "", "cakezone_db");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function fetchProducts($filters) {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
            $types .= "s";
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= ?";
            $params[] = (float)$filters['min_price'];
            $types .= "d";
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= ?";
            $params[] = (float)$filters['max_price'];
            $types .= "d";
        }

        $stmt = $this->conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $products;
    }

    public function renderProductCard($product) {
        $escapedName = htmlspecialchars($product["name"], ENT_QUOTES, 'UTF-8');
        $escapedDesc = htmlspecialchars($product["description"], ENT_QUOTES, 'UTF-8');
        $escapedImg = htmlspecialchars($product["img_url"], ENT_QUOTES, 'UTF-8');
        $escapedPrice = htmlspecialchars(number_format($product["price"], 2), ENT_QUOTES, 'UTF-8');

        return '
            <div class="product-card-one">
                <img src="' . $escapedImg . '" alt="' . $escapedName . '" width="200">
                <h2 class="product-name">' . $escapedName . '</h2>
                <p>' . $escapedDesc . '</p>
                <p class="price">Price: $' . $escapedPrice . '</p>
                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="product_id" value="' . htmlspecialchars($product["product_id"], ENT_QUOTES, 'UTF-8') . '">
                    <input type="hidden" name="product_name" value="' . $escapedName . '">
                    <input type="hidden" name="product_image" value="' . $escapedImg . '">
                    <input type="hidden" name="product_price" value="' . $escapedPrice . '">
                    <input type="submit" class="btn" value="Add to Cart">
                </form>
            </div>';
    }

    public function renderProducts($filters) {
        $products = $this->fetchProducts($filters);
        if (count($products) > 0) {
            foreach ($products as $product) {
                echo $this->renderProductCard($product);
            }
        } else {
            echo "No Products Found.";
        }
    }

    public function __destruct() {
        $this->conn->close();
    }
}

$productPage = new ProductPage();
$filters = [
    'category' => htmlspecialchars($_GET['category'] ?? '', ENT_QUOTES, 'UTF-8'),
    'min_price' => htmlspecialchars($_GET['min_price'] ?? '', ENT_QUOTES, 'UTF-8'),
    'max_price' => htmlspecialchars($_GET['max_price'] ?? '', ENT_QUOTES, 'UTF-8')
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
       <style>
        .product-container {
            margin: 20px auto;
            height: auto;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
        }

        .product-card-one {
            padding: 30px;
            margin: 20px;
            width: 400px;
            height: 500px;
            background-color: white;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 2);
            text-align: center;
        }

        .product-card-one img {
            width: 300px;
            height: 200px;
            margin-bottom: 10px;
        }

        .product-card-one h2 {
            margin-top: 0;
        }

        .product-card-one p {
            margin-bottom: 10px;
            max-width: 350px;
            text-align: justified;
        }

        .product-card-one form {
            margin-top: 10px;
        }

        .product-name {
            font-size: 25px;
            margin-top: 20px;
        }

        .price {
            font-weight: bold;
        }

        .btn {
            background-color: #6A2509;
            color: #fff;
            padding: 10px 30px;
        }

        .filter-container {
        text-align: center;
        margin: 20px 0;
        display: flex;
        justify-content: center;
        }

        .filter-form {
            display: flex;
            gap: 20px;
            align-items: flex-end; 
        }

        .filter-form .form-group {
            margin: 0;
            display: flex;
            flex-direction: column; 
        }

        .filter-form .btn {
            height: 38px; 
            padding: 5px 20px;
            margin: 0; 
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="filter-container">
        <form class="filter-form" method="GET" action="">
            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category" id="category" class="form-control">
                    <option value="">All</option>
                    <option value="Fruit Cake">Fruit Cake</option>
                    <option value="Wedding Cake">Wedding Cake</option>
                    <option value="Pound cake">Pound cake</option>
                    <option value="BirthDay Cake">Birth-Day Cake</option>
                    <option value="Cup Cakes">Cup Cakes</option>
                    <option value="Pastry">Pastry</option>
                </select>
            </div>
            <div class="form-group">
                <label for="min_price">Min Price:</label>
                <input type="number" name="min_price" id="min_price" class="form-control">
            </div>
            <div class="form-group">
                <label for="max_price">Max Price:</label>
                <input type="number" name="max_price" id="max_price" class="form-control">
            </div>
            <button type="submit" class="btn">Apply Filters</button>
        </form>
    </div>

    <div class="product-container">
        <?php $productPage->renderProducts($filters); ?>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>