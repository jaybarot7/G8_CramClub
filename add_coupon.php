
<?php
require('dbinit.php'); 

class CouponManager
{
    private $conn;
    public $errors = [];

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function validateCoupon($data)
    {
 
        $data['code'] = htmlspecialchars(strip_tags($data['code']));
        $data['description'] = htmlspecialchars(strip_tags($data['description']));
        $data['discount'] = filter_var($data['discount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $data['status'] = filter_var($data['status'], FILTER_VALIDATE_INT);

       
        if (empty($data['code'])) {
            $this->errors['code'] = "Code is required.";
        } elseif (preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $data['code'])) {
            $this->errors['code'] = "Special characters are not allowed in the coupon code.";
        }

        if (empty($data['description'])) {
            $this->errors['description'] = "Description is required.";
        }

        if (empty($data['discount'])) {
            $this->errors['discount'] = "Discount is required.";
        } elseif (!is_numeric($data['discount'])) {
            $this->errors['discount'] = "Discount must be numeric.";
        }

        if ($data['status'] === false) {
            $this->errors['status'] = "Status is required and must be either 0 or 1.";
        }

        return empty($this->errors);
    }

    public function addCoupon($data)
    {
        $sql = "INSERT INTO coupons (code, discount, description, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("SQL Error: " . htmlspecialchars($this->conn->error));
        }

        $stmt->bind_param(
            "sisi",
            $data['code'],
            $data['discount'],
            $data['description'],
            $data['status']
        );

        if (!$stmt->execute()) {
            $this->errors['database'] = "Failed to add coupon: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

error_reporting(E_ALL);
$couponManager = new CouponManager($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($couponManager->validateCoupon($_POST)) {
        $couponManager->addCoupon($_POST);

        if (empty($couponManager->errors)) {
            header("Location: add_success_page_coupons.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Zone: Add Coupon</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="container mt-4 main-page-wrapper add-product-container">
        <h1 class="add-product-heading">Add Coupon</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="code">Coupon Code:</label>
                <input type="text" class="form-control" id="code" name="code">
                <?php if (isset($couponManager->errors['code'])): ?>
                    <p class="product-error"><?php echo $couponManager->errors['code']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="discount">Discount:</label>
                <input type="number" class="form-control" id="discount" name="discount" min="0" step="0.01">
                <?php if (isset($couponManager->errors['discount'])): ?>
                    <p class="product-error"><?php echo $couponManager->errors['discount']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                <?php if (isset($couponManager->errors['description'])): ?>
                    <p class="product-error"><?php echo $couponManager->errors['description']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <input type="number" class="form-control" id="status" name="status" min="0" max="1">
                <?php if (isset($couponManager->errors['status'])): ?>
                    <p class="product-error"><?php echo $couponManager->errors['status']; ?></p>
                <?php endif; ?>
            </div>
            <input type="submit" class="btn d-block mx-auto" name="submit" value="Add Coupon">
        </form>
    </main>

    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>