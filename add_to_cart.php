<?php
$userType = "user";
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "cakezone_db";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();


if (isset($_POST['product_id']) && isset($_POST['product_name']) && isset($_POST['product_image']) && isset($_POST['product_price'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_image = $_POST['product_image'];
    $product_price = $_POST['product_price'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $product_in_cart = false;

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $product_id) {
            $item['quantity'] += 1; 
            $product_in_cart = true;
            break;
        }
    }

    if (!$product_in_cart) {
        $_SESSION['cart'][] = [
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_image' => $product_image,
            'product_price' => $product_price,
            'quantity' => 1, 
        ];
    }
}


if (isset($_POST['action']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                if ($_POST['action'] == 'increase') {
                    $item['quantity'] += 1;
                } elseif ($_POST['action'] == 'decrease' && $item['quantity'] > 1) {
                    $item['quantity'] -= 1;
                }
                break;
            }
        }
        unset($item); 
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .product-container {
            margin: 20px auto;
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-items: center;
        }

        .product-card {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            margin: 10px;
            width:100%;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 2);
            text-align: left;
        }

        .product-card img {
            width: 450px;
            height: 250px;
            margin-right: 20px;
            border-radius: 8px;
        }

        .product-card .product-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-card h2 {
            font-size: 20px;
            margin: 0 0 10px 0;
        }

        .product-card p {
            font-size: 16px;
            margin: 0 0 5px 0;
        }

        .product-card form {
            margin-top: 10px;
        }

        .cart-header {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            margin-bottom: 20px;
        }

        .cart-heading {
            font-size: 24px;
            font-weight: bold;
        }

        .btn {
            background-color: #6A2509;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btnCou {
            background-color:#6A2509 ;
            color: black;
            padding: 3px 3px;
            border-radius: 50px;
        }
        

        .empty-cart {
            padding: 20px;
            font-size: 18px;
            text-align: center;
            color: #555;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="product-container">
        <div class="cart-header">
            <h2>Your Cart</h2>
            <div>
                <form action="checkout.php" method="post" style="display: inline-block; margin-right: 10px;">
                    <button type="submit" class="btn btn-primary">Buy Now</button>
                </form>
                <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        </div>
        <?php
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            echo '<div class="product-container">';
            foreach ($_SESSION['cart'] as $item) {
                echo '<div class="product-card">';
                echo '<img src="' . $item['product_image'] . '" alt="' . $item['product_name'] . '">';
                echo '<div class="product-info">';
                echo '<h2>' . $item['product_name'] . '</h2>';
                echo '<p>Price: $' . $item['product_price'] . '</p>';
                echo '<p>Quantity: ' . $item['quantity'] . '</p>';
                echo '<form method="post" class="quantity-controls">';
                echo '<input type="hidden" name="product_id" value="' . $item['product_id'] . '">';
                echo '<button type="submit" name="action" value="decrease" class="btnCou btn-secondary">-</button>';
                echo '<span>' . $item['quantity'] . '</span>';
                echo '<button type="submit" name="action" value="increase" class="btnCou btn-secondary">+</button>';
                echo '</form>';
                echo '</div>';
                echo '<form action="remove_from_cart.php" method="post">';
                echo '<input type="hidden" name="product_id" value="' . $item['product_id'] . '">';
                echo '<button type="submit" class="btn btn-danger">Remove</button>';
                echo '</form>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>Your cart is empty.</p>';
        }
        ?>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>