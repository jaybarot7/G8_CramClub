<?php
$userType = "admin";
error_reporting(require('dbinit.php'));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Zone</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .success-container {
            margin: 100px auto;
            max-width: 50%;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 2);
        }

        .success-container img {
            height: 350px;
        }

        .success-message {
            font-size: 24px;
            color: #6a2509;
        }

        .success-message span {
            font-weight: bold;
        }

        .main-page-wrapper {
            margin-top: -80px;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <main class="container mt-4">
        <div class="row main-page-wrapper">
            <div class="success-container">
                <img src="./img/success.jpg" alt="success">
                <div class="success-message">Coupon Added successfully!<br><span>Thank you!</span>
                </div>
            </div>
    </main>


    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>

</html>