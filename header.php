<header>
    <nav class="navbar1">
        <a class="navbar-brand" href="index.php">
            <img src="./img/header-logo.png" alt="cake zone logo" class="header-logo-img">
        </a>
        <a class="navbar-brand" href="index.php"><span class="navbar-text">Cake Zone</span></a>
        <ul class="navbar-nav rtl-navbar">
            <?php
            $currentPage = basename($_SERVER['PHP_SELF']);

            if ($currentPage === "login.php" || $currentPage === "signup.php") {
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="signup.php">Sign Up</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Log In</a>
                </li>
                <?php
            } else {
                if ($userType === "admin") {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="add_product.php">Add Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_coupon.php">Add Coupon</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_products.php">Manage Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_coupons.php">Manage Coupons</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                    <?php
                } elseif ($userType === "user") {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </nav>
</header>

<style>
    .rtl-navbar {
        display: flex;
        flex-direction: row; /* Reverse the direction of nav items */
    }

    .rtl-navbar .nav-item {
        margin-left: 10px; /* Add spacing between items */
    }
</style>