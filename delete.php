<?php
require('dbinit.php');

if (isset($_GET['submit']) && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $delete_query = "DELETE FROM products WHERE product_id = $product_id";

    echo $delete_query;

    if ($conn->query($delete_query) === TRUE) {
        header("Location: manage_products.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close();
}

?>