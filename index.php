<?php
$userType = "user";
error_reporting(require('dbinit.php'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Zone</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <main class="container mt-4 update-product-container">
        <div class="row">
            <h1 class="add-product-heading">Featured Products</h1><br>
        </div>
        <div class="row home-page-wrapper">
            <?php
            $sql = "SELECT * FROM products LIMIT 3";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                        <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./' . $row["img_url"] . '" class="card-img-top product-image" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title">' . ucfirst($row["name"]) . '</h5>
                                <p class="card-text">' . $row["description"] . '</p>
                                <p class="card-text">Price: $' . $row["price"] . '</p>
                                <p class="card-text">Quantity: ' . $row["quantity"] . '</p>
                                </div>
                                </div>
                                </div>';
                }
            } else {
                echo '
                    <div class="col-md-12">
                        <div class="no-products">
                        <h4>No products found.</h4>
                        </div>
                    </div>';
            }
            ?>
        </div>
        <div class="row">
            <h1 class="add-product-heading">Latest Coupons
            </h1><br>
        </div>
        <div class="row home-page-wrapper">
            <?php
            $sql = "SELECT * FROM coupons LIMIT 3";
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
                        <p class="card-text">Discount: ' . $row["discount"] . '%</p>
                        <p class="card-text">Status: ' . $row["Status"] . '</p>
                        </div>
                        </div>
                        </div>';
                }
            } else {
                echo '
                    <div class="col-md-12">
                        <div class="no-products">
                        <h4>No coupons found.</h4>
                        </div>
                    </div>';
            }
            $conn->close();
            ?>
        </div>
        <div id="contact-section">
            <h1 class="add-product-heading">Location</h1>
            <div id="map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d11580.395921845182!2d-80.5180089!3d43.4794047!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882bf31d0cec9491%3A0x8bf5f60c306d2207!2sConestoga%20College%20Waterloo%20Campus!5e0!3m2!1sen!2sca!4v1733356646066!5m2!1sen!2sca"
                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </main>


    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>

</html>