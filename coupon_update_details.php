<?php
include 'dbinit.php';
$userType = "admin";

session_start();

class CouponManager {
    private $conn;
    public $errors = array();
    public $coupon = array();

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCoupon($coupon_id) {
        $stmt = $this->conn->prepare("SELECT * FROM coupons WHERE coupon_id = ?");
        $stmt->bind_param("i", $coupon_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $this->coupon = $result->fetch_assoc();
        } else {
            $this->errors['coupon'] = "Coupon not found.";
        }

        $stmt->close();
    }

    public function validateAndUpdateCoupon($data) {
        $this->validateCouponData($data);

        if (empty($this->errors)) {
            $stmt = $this->conn->prepare("UPDATE coupons SET code = ?, description = ?, discount = ?, status = ? WHERE coupon_id = ?");
            $stmt->bind_param(
                "ssdii",
                $data['code'],
                $data['description'],
                $data['discount'],
                $data['status'],
                $data['coupon_id']
            );

            if ($stmt->execute()) {
                header("Location: manage_coupons.php");
                exit();
            } else {
                $this->errors['database'] = "Failed to update coupon.";
            }

            $stmt->close();
        }
    }

    private function validateCouponData($data) {
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

        if (empty($data['status']) && $data['status'] !== '0') {
            $this->errors['status'] = "Status is required.";
        } elseif (!in_array((int)$data['status'], [0, 1])) {
            $this->errors['status'] = "Status must be either 0 or 1.";
        }
    }
}

$couponManager = new CouponManager($conn);

if (isset($_GET['coupon_id'])) {
    $couponManager->getCoupon((int)$_GET['coupon_id']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $couponManager->validateAndUpdateCoupon($_POST);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Zone: Update Coupon Details</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('header.php'); ?>

    <main class="container mt-4 main-page-wrapper add-product-container">
        <h1 class="add-product-heading">Update Coupon</h1>
        <?php if (isset($couponManager->errors['coupon'])): ?>
            <p class="text-danger"><?php echo $couponManager->errors['coupon']; ?></p>
        <?php else: ?>
        <form action="" method="POST">
            <input type="hidden" class="form-control" id="id" name="coupon_id"
                value="<?php echo htmlspecialchars($couponManager->coupon['coupon_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-group">
                <label for="code">Code:</label>
                <input type="text" class="form-control" id="code" name="code"
                    value="<?php echo htmlspecialchars($couponManager->coupon['code'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($couponManager->errors['code'])): ?>
                    <p class="text-danger"><?php echo $couponManager->errors['code']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="discount">Discount:</label>
                <input type="number" class="form-control" id="discount" name="discount" min="0" step="0.01"
                    value="<?php echo htmlspecialchars($couponManager->coupon['discount'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($couponManager->errors['discount'])): ?>
                    <p class="text-danger"><?php echo $couponManager->errors['discount']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($couponManager->coupon['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if (isset($couponManager->errors['description'])): ?>
                    <p class="text-danger"><?php echo $couponManager->errors['description']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <input type="number" class="form-control" id="status" name="status" min="0" max="1"
                    value="<?php echo htmlspecialchars($couponManager->coupon['status'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($couponManager->errors['status'])): ?>
                    <p class="text-danger"><?php echo $couponManager->errors['status']; ?></p>
                <?php endif; ?>
            </div>
            <input type="submit" class="btn d-block mx-auto add-product-button" name="submit" value="Update Coupon">
        </form>
        <?php endif; ?>
    </main>

    <?php include('footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>