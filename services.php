
<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Add to cart functionality
if (isset($_POST['add_to_cart'])) {
  $service_id = $_POST['service_id'];
  $service_name = $_POST['service_name'];
  $service_cost = $_POST['service_cost'];
  $service_image = $_POST['service_image'];
  $service_description = $_POST['service_description'];
  $user_id = $_SESSION['bpmsuid'];
  
  // Set quantity to 1 by default when adding a service to the cart
  $quantity = 1;

  // Add service to the cart session
  $_SESSION['cart'][] = [
      'id' => $service_id,
      'name' => $service_name,
      'cost' => $service_cost,
      'image' => $service_image,
      'description' => $service_description,
      'quantity' => $quantity,
      'user_id' => $user_id,
  ];

  // Insert the service into the cart table in the database
  try {
      $sql = "INSERT INTO tblcart (user_id, service_id, quantity, added_at) 
              VALUES (?, ?, ?, NOW())";
      $stmt = $con->prepare($sql);
      $stmt->bind_param("iii", $user_id, $service_id, $quantity); // 'i' for integer
      $stmt->execute();
  } catch (Exception $e) {
      echo "Error: " . $e->getMessage();
  }
}

// Get the search query if available
$search_query = isset($_GET['query']) ? '%' . trim($_GET['query']) . '%' : '';

// Fetch services based on search query
if (!empty($search_query)) {
    try {
        // Prepare and bind the search query
        $sql = "SELECT * FROM tblservices WHERE ServiceName LIKE ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $search_query);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any results were returned
        if ($result->num_rows > 0) {
            $services = [];
            while ($row = $result->fetch_assoc()) {
                $services[] = $row;
            }
        } else {
            $services = [];
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // If no search query, display all services
    try {
        $sql = "SELECT * FROM tblservices";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            $services = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $services[] = $row;
            }
        } else {
            $services = [];
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    

    <title>Beauty Parlour Management System | service Page </title>

    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  </head>
  <body id="home">
<?php include_once('includes/header.php');?>

<script src="assets/js/jquery-3.3.1.min.js"></script> <!-- Common jquery plugin -->
<!--bootstrap working-->
<script src="assets/js/bootstrap.min.js"></script>
<!-- //bootstrap working-->
<!-- disable body scroll which navbar is in active -->
<script>
$(function () {
  $('.navbar-toggler').click(function () {
    $('body').toggleClass('noscroll');
  })
});
</script>
<!-- disable body scroll which navbar is in active -->

<!-- breadcrumbs -->
<section class="w3l-inner-banner-main">
    <div class="about-inner services ">
        <div class="container">   
            <div class="main-titles-head text-center">
            <h3 class="header-name ">
                Our Service
            </h3>
        </div>
</div>
</div>
<div class="breadcrumbs-sub">
<div class="container">   
<ul class="breadcrumbs-custom-path">
    <li class="right-side propClone"><a href="index.php" class="">Home <span class="fa fa-angle-right" aria-hidden="true"></span></a> <p></li>
    <li class="active ">Services</li>
</ul>
</div>
</div>
    </div>
</section>

<!-- Services Section -->
    <section class="w3l-recent-work-hobbies">
        <div class="recent-work">
            <div class="container">
                <div class="row about-about">
                    <?php
                    if (!empty($services)) {
                        foreach ($services as $service) {
                    ?>
                        <div class="col-lg-4 col-md-6 col-sm-6 propClone">
                            <img src="admin/images/<?php echo $service['Image']; ?>" alt="product" height="200" width="400" class="img-responsive about-me">
                            <div class="about-grids ">
                                <hr>
                                <h5 class="para"><?php echo $service['ServiceName']; ?></h5>
                                <p class="para "><?php echo $service['ServiceDescription']; ?></p>
                                <p class="para" style="color: hotpink;"> Cost of Service: $<?php echo $service['Cost']; ?></p>
                                <form method="POST">
                                    <input type="hidden" name="service_id" value="<?php echo $service['ID']; ?>">
                                    <input type="hidden" name="service_name" value="<?php echo $service['ServiceName']; ?>">
                                    <input type="hidden" name="service_cost" value="<?php echo $service['Cost']; ?>">
                                    <input type="hidden" name="service_image" value="<?php echo $service['Image']; ?>">
                                    <input type="hidden" name="service_description" value="<?php echo $service['ServiceDescription']; ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-success">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    <?php
                        }
                    } else {
                        echo "<p>No services found matching your search.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

<?php include_once('includes/footer.php');?>
<!-- move top -->
<button onclick="topFunction()" id="movetop" title="Go to top">
	<span class="fa fa-long-arrow-up"></span>
</button>
<script>
	// When the user scrolls down 20px from the top of the document, show the button
	window.onscroll = function () {
		scrollFunction()
	};

	function scrollFunction() {
		if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
			document.getElementById("movetop").style.display = "block";
		} else {
			document.getElementById("movetop").style.display = "none";
		}
	}

	// When the user clicks on the button, scroll to the top of the document
	function topFunction() {
		document.body.scrollTop = 0;
		document.documentElement.scrollTop = 0;
	}
</script>
<!-- /move top -->
</body>

</html>