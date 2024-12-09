<?php
session_start();

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    $servername = "localhost:3307";
    $username = "root";
    $password = "";
    $dbname = "cakezone_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $delete_sql = "DELETE FROM cart_item WHERE product_id = '$product_id'";

    if ($conn->query($delete_sql) !== TRUE) {
        // Handle error
    }

    $conn->close();
}

header('Location: add_to_cart.php');
?>