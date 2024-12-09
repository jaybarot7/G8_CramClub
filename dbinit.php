<?php

$DB_SERVER = "localhost:3307";
$DB_USERNAME = "root";
$DB_PASSWORD = "";
$dbname = "cakezone_db";


$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD);

// Check connection
if (!$conn) {
    die("Database connection error");
}
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === true) {

} else {
    echo "Error creating database: " . $conn->error;
    exit();
}

$conn->select_db($dbname);

$sql = "CREATE Table if not exists products(
        product_id INt(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name Varchar(255) ,
        description Varchar(255),
        category varchar(100),
        quantity Int(55),
        price DOUBLE,
        img_url Varchar(55)
        )";

if ($conn->query($sql) === true) {
} else {
    echo "Error creating table: " . $conn->error;
    exit();
}

$sql = "CREATE TABLE IF NOT EXISTS users (
        user_id iNT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username varchar(50),
        email varchar(50),
        password varchar(255),
        type ENUM('admin','user')
        ) ";

if ($conn->query($sql) === true) {
} else {
    echo "Error creating table: " . $conn->error;
    exit();
}

$sql = "CREATE TABLE IF NOT EXISTS coupons(
        coupon_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50),
        discount INT(11),
        description VARCHAR(255),
        status boolean
        )";
if ($conn->query($sql) === true) {
} else {
    echo "Error creating table: " . $conn->error;
    exit();
}

$sql = "CREATE TABLE IF NOT EXISTS cart_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_image VARCHAR(255) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL
)";
if ($conn->query($sql) === true) {
} else {
    echo "Error creating table: " . $conn->error;
    exit();
}

$sql = "CREATE TABLE IF NOT EXISTS billing(
        bill_id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        amount Int(11),
        payment_method ENUM('credit','cod','debit')
        )";
if ($conn->query($sql) === true) {

} else {
    echo "Error creating table: " . $conn->error;
    exit();
}

$checkQuery = "SELECT COUNT(*) as count FROM products";
$result = $conn->query($checkQuery);
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $sql = "INSERT INTO products (name, description,category, quantity, price, img_url) VALUES 
    ('Fruit Cake', 'Delicious fruit cake made with rich fruits.','Fruit Cake', 20, 15.99, './img/fruit-cake.jpg'),
    ('Wedding Cake', 'Soft white wedding cake with cream cheese frosting.','Wedding Cake', 15, 20.99, './img/white-wedding-cake.jpg'),
    ('Small Cake', 'Small cake for small occasions.','Pound cake', 10, 12.99, './img/small-cake.jpg'),
    ('Wedding Cake', 'Layered cream cake with whipped cream.','Wedding Cake', 12, 20.49, './img/wedding-cake.jpg'),
    ('Birth-Day Cake', 'Birth Day Cake for someone special.','BirthDay Cake', 50, 21.99, './img/birthday-cake.jpg'),
    ('Cup Cake', 'Fresh fruit cup cake with seasonal fruits.','Cup Cakes', 8, 5.00, './img/cup-cake.jpg'),
    ('Coffee Pastry', 'Soft and moist coffee-flavored pastry.','Pastry', 10, 7.99, './img/pastry.jpg')";

    if ($conn->query($sql) === true) {
    } else {
        echo "Error creating table: " . $conn->error;
        exit();
    }
} else {
}

?>