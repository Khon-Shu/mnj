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

// Fetch cart items for order summary
$sql = "SELECT c.*, p.name, p.price, p.image FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = '$user_id'";
$result = $conn->query($sql);
$orderItems = [];
$totalAmount = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderItems[] = $row;
        $totalAmount += $row['price'] * $row['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
</head>
<body>
    <h1>Order Summary</h1>
    <table>
        <tr>
            <th>Product Image</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($orderItems as $item): ?>
            <tr>
                <td><img src="../admin/uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" width="100"></td>
                <td><?php echo $item['name']; ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <h3>Grand Total: $<?php echo number_format($totalAmount, 2); ?></h3>
    
    <form method="post" action="process_order.php">
        <button type="submit">Confirm Order</button>
    </form>
</body>
</html>
