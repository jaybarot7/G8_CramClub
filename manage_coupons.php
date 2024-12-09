<?php
$userType = "admin";
error_reporting(require('dbinit.php'));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Zone: Manage Coupons</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <main class="container mt-4 update-product-container">
        <div class="row main-page-wrapper">
            <?php
            $sql = "SELECT * FROM coupons";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="./img/discount.jpg" class="card-img-top product-image" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title">' . ucfirst($row["code"]) . '</h5>
                            <p class="card-text">' . $row["description"] . '</p>
                            <p class="card-text">Discount: $' . $row["discount"] . '</p>
                            <p class="card-text">Status: ' . $row["status"] . '</p>
                        </div>
                        <div class="card-footer row card-footer-row">
                            <form action="coupon_update_details.php" method="GET" class="col-md-6">
                                <input type="hidden" name="coupon_id" value="' . $row["coupon_id"] . '">
                                <input type="submit" name="submit" class="btn btn-update update-product-button" value="Update">
                            </form>
                            <form class="delete-form col-md-6" action="delete_coupon.php" method="GET">
                                <input type="hidden" name="coupon_id" value="' . $row["coupon_id"] . '">
                                <input type="submit" name="submit" class="btn btn-delete update-product-button" value="Delete">
                            </form>
                        </div>
                    </div>
                    </div>';
                }
            } else {
                echo '
                <div class="col-md-12">
                    <div class="no-products" >
                        <h4>No coupons found</h4>
                    </div>
                </div>';
            }
            $conn->close();
            ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>