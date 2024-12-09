<?php
require('dbinit.php');

if (isset($_GET['submit']) && isset($_GET['coupon_id'])) {
    $coupon_id = $_GET['coupon_id'];

    $delete_query = "DELETE FROM coupons WHERE coupon_id = $coupon_id";

    echo $delete_query;

    if ($conn->query($delete_query) === TRUE) {
        header("Location: manage_coupons.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close();
}

?>