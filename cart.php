<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if (isset($_POST['remove_from_cart'])) {
    $service_id_to_remove = $_POST['service_id'];
    $user_id = $_SESSION['bpmsuid'];
    try {
        $sql = "DELETE FROM tblcart WHERE user_id = ? AND service_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ii", $user_id, $service_id_to_remove); 
        $stmt->execute();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $service_id_to_remove) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
    echo "<script>window.location.href='cart.php';</script>";
}


?>
<!doctype html>
<html lang="en">
<head>
    <title>Beauty Parlour Management System | Cart Page</title>
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</head>
<body>
<?php include_once('includes/header.php');?>

<section class="w3l-inner-banner-main">
    <div class="about-inner services">
        <div class="container">   
            <div class="main-titles-head text-center">
                <h3 class="header-name ">Wishlist Shipping Cart</h3>
            </div>
        </div>
    </div>
</section>

<!-- Cart Section -->
<section class="w3l-recent-work-hobbies">
    <div class="recent-work">
        <div class="container">
            <div class="row about-about">
                <?php
                if (!empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $cart_item) {
                ?>
                <div class="col-lg-4 col-md-6 col-sm-6 propClone">
                    <img src="admin/images/<?php echo $cart_item['image']; ?>" alt="Service Image" height="200" width="400" class="img-responsive about-me">
                    <div class="about-grids">
                        <hr>
                        <h5 class="para"><?php echo $cart_item['name']; ?></h5>
                        <p class="para"><?php echo $cart_item['description']; ?></p>
                        <p class="para" style="color: hotpink;">Cost: $<?php echo $cart_item['cost']; ?></p>
                        <form method="POST">
                            <input type="hidden" name="cart_id" value="<?php echo $cart_item['cart_id']; ?>">
                            <input type="hidden" name="service_id" value="<?php echo $cart_item['id']; ?>">
                            <button type="submit" name="remove_from_cart" class="btn btn-danger">Remove from Wishlist Shipping Cart</button>
                        </form>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p>No items in the Wishlist Shipping Cart.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</section>

<!-- Checkout Button -->
<div class="text-center mt-4">
    <?php if (!empty($_SESSION['cart'])) { ?>
        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
    <?php } ?>
</div>

<?php include_once('includes/footer.php');?>
</body>
</html>
