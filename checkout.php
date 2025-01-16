<?php
session_start();
include('includes/dbconnection.php');

if (!isset($_SESSION['bpmsuid'])) {
    echo "<script>alert('Please log in to proceed with checkout.');window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['bpmsuid'];
if (isset($_POST['checkout'])) {
    $billing_address = $_POST['billing_address'] ?? '';
    $shipping_address = $_POST['shipping_address'];
    $contact_person = $_POST['contact_person'];
    $address = $_POST['address'] ?? '';
    $apartment = $_POST['apartment'];
    $zip_code = $_POST['zip_code'] ?? '';
    $city = $_POST['city'] ?? '';
    $contact_number = $_POST['contact_number'];
    $shipping_zip_code = $_POST['shipping_zip_code'];
    $shipping_city = $_POST['shipping_city'];
    $message_to_seller = $_POST['message_to_seller'];
    $payment_option = $_POST['payment_option'];
    $order_status = 'pending';
    $added_at = date("Y-m-d H:i:s");

    $total_cost = 0;
    foreach ($_SESSION['cart'] as $cart_item) {
        $total_cost += isset($cart_item['cost']) ? $cart_item['cost'] : 0;
    }

    $sql = "INSERT INTO tblodr (user_id, total_cost, billing_address, shipping_address, order_date, payment_option, contact_person, address, apartment, zip_code, city, contact_number, shipping_zip_code, shipping_city, message_to_seller, order_status, service_id, cart_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);

    $cart_item = reset($_SESSION['cart']);
    $service_id = isset($cart_item['id']) ? $cart_item['id'] : null;
    $cart_id = isset($cart_item['cart_id']) ? $cart_item['cart_id'] : 0;

    $stmt->bind_param("iissssssssssssssii", $user_id, $total_cost, $billing_address, $shipping_address, $added_at, $payment_option, $contact_person, $address, $apartment, $zip_code, $city, $contact_number, $shipping_zip_code, $shipping_city, $message_to_seller, $order_status, $service_id, $cart_id);
    $stmt->execute();

    // Clear cart after checkout
    $_SESSION['cart'] = [];
    echo "<script>alert('Order Placed successfully Let the Approved from Seller!');window.location.href='index.php';</script>";
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Checkout Page</title>
    <link rel="stylesheet" href="assets/css/style-starter.css">
</head>
<body>
<?php include_once('includes/header.php');?>

<section class="w3l-inner-banner-main">
    <div class="about-inner services">
        <div class="container">   
            <div class="main-titles-head text-center">
                <h3 class="header-name ">Checkout</h3>
            </div>
        </div>
    </div>
</section>

<section class="w3l-recent-work-hobbies">
    <div class="recent-work">
        <div class="container">
            <form method="POST">
                <h4>Order Summary</h4>
                <ul>
                    <?php
                    $total_cost = 0;
                    foreach ($_SESSION['cart'] as $item) {
                        echo "<li>{$item['name']} - \${$item['cost']}</li>";
                        $total_cost += $item['cost'];
                    }
                    ?>
                </ul>
                
                <p><strong>Total: $<?php echo number_format($total_cost, 2); ?></strong></p>
                <h4 style="margin-top: 20px;">Shipping Information</h4>
                <div class="form-group">
                    <label for="contact_person">Contact Person*</label>
                    <input type="text" name="contact_person" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number*</label>
                    <input type="text" name="contact_number" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="shipping_address">Shipping Address*</label>
                    <textarea name="shipping_address" required class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="apartment">Apartment/Unit*</label>
                    <input type="text" name="apartment" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="shipping_zip_code">Shipping Zip Code*</label>
                    <input type="text" name="shipping_zip_code" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="shipping_city">Shipping City*</label>
                    <input type="text" name="shipping_city" required class="form-control">
                </div>

                <!-- Message for Seller -->
                <div class="form-group">
                    <label for="message_to_seller">Leave a Message for the Seller</label>
                    <textarea name="message_to_seller" class="form-control"></textarea>
                </div>
                <!-- Payment Options -->
                <h4>Payment Options</h4>
                <div class="form-group">
                    <label><input type="radio" name="payment_option" value="COD" checked> Cash On Delivery</label>
                </div>

                <!-- Terms Agreement -->
                <div class="form-group">
                    <input type="checkbox"> I agree with the terms and conditions
                </div>
                <button type="submit" name="checkout" class="btn btn-primary">Confirm Purchase</button>
            </form>
        </div>
    </div>
</section>

<?php include_once('includes/footer.php');?>
</body>
</html>
