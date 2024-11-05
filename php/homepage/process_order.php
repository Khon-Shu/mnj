<?php
session_start();
include('../connect.php');

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch cart items
$sql = "SELECT c.*, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = '$user_id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Insert each item into the orders table
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $price = $row['price'];
        $status = 'Pending'; // Set initial status for the order
        $order_date = date('Y-m-d H:i:s');

        // Insert into orders table, associating with user_id
        $sql_order = "INSERT INTO orders (user_id, product_id, quantity, status, order_date) VALUES ('$user_id', '$product_id', '$quantity', '$status', '$order_date')";
        $conn->query($sql_order);
    }

    // Clear the cart after order is processed
    $sql_clear_cart = "DELETE FROM cart WHERE user_id = '$user_id'";
    $conn->query($sql_clear_cart);

    echo "<script>alert('Order has been placed successfully!'); window.location.href='home.html';</script>";
} else {
    echo "<script>alert('Your cart is empty.'); window.location.href='cart.php';</script>";
}
?>
