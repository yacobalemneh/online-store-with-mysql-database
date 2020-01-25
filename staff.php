<?php
// Initialize the session
session_start();
require_once "config.php";
 
// Check if the user is logged in, if not then redirect him to login page 
// Also check if user has permission to access page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["permission"] !== 2){ 
    header("location: login.php");
    exit;
}

$item_name = $price = $quantity = "";
$item_name_err = $price_err = $quantity_err = "";
$to_staff = $grant_err = "";
$access_success_msg = "";
$success_msg = "";
$idNO = $_SESSION["idno"];


if($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["grant_access"] || $_POST["deny_access"]) {

        if ((empty(trim($_POST["to_staff"]))))
            $grant_err = "Please enter staff email. ";
        else
            $to_staff = trim($_POST["to_staff"]);

        $sql = "UPDATE Users SET Permission = ? WHERE Email = '$to_staff'";

           if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_permission);

                if ($_POST["grant_access"]) 
                    $param_permission = '2';
                else 
                    $param_permission = '1';
                 
                if(mysqli_stmt_execute($stmt)) {
                    if(mysqli_stmt_num_rows($stmt) == 1)
                        $access_success_msg = "Successfully Submitted! ";
                    else
                        $access_success_msg = "Couldn't find email. Try Again ";
                }
                
            }
            mysqli_stmt_close($stmt);
        
        mysqli_close($link);
            
    }


    if ($_POST["stock_up"]) { 

        if ((empty(trim($_POST["item_name"]))))  // get Item name and store it in variable
            $item_name_err = "Please enter Item Name.";
        else 
            $item_name = trim($_POST["item_name"]);

        if (empty(trim($_POST["quantity"]))) 
            $quantity_err = "Please enter Quantity of items.";
        else  
            $quantity = trim($_POST["quantity"]);
    // get price and store it in variable
        if (empty(trim($_POST["price"]))) 
            $price_err = "Please enter price of item.";
        else  
            $price = trim($_POST["price"]);

        if (empty(trim($_POST["product_id"])))
            $product_id_err = "Please enter Product ID.";
        else  
            $product_id = trim($_POST["product_id"]);

        $image = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));

        if(empty($item_name_err) && empty($quantity_err) && empty($price_err)){
            $sql = "INSERT INTO Items 
                    (Product_Id, Product_Name, Product_Quantity, Product_Price, Image) 
                    VALUES (?, ?, ?, ?,?)"  ;
                
            if($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "isidb", $product_id, $product_name, $product_quantity, 
                                        $product_price, $product_image);
                $product_id = (int)$product_id;
                $product_name = $item_name;
                $product_quantity = (int) $quantity;
                $product_price = (double)$price;
                $product_image = $image;

                if(mysqli_stmt_execute($stmt))
                    $success_msg = "Successfully Submitted! ";
                else
                    $success_msg = "Something went wrong. Please try again later.";
                
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
    }
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to ToyzRuz</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        #mydiv{ font: 14px sans-serif; text-align: center; }
        #login { font: 14px sans-serif; text-align: center; vertical-align: top; position: sticky; 
                left: 500px; bottom: 20px;}
        .wrapper{ width: 400px; padding: 20px; position: relative;}
        #additem {vertical-align: top; bottom: 180px;}
        #privilege { text-align: center; vertical-align: top; position: sticky; left: 1500px}
    </style>
</head>
<body>
    <body>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
              <a class="navbar-brand" href="#">TozRUs</a>
          </div>
          <ul class="nav navbar-nav">
              <li ><a href="items.php">Home</a></li>
              <li class="active"><a  href="#">Inventory </a></li>
              <li><a href="orders.php">Orders</a></li>
              <li><a href="cart.php">Cart</a></li>
              
          </ul>
        </div>
    </nav>
    <br/>

    <div class="page-header" id = "mydiv">
        <h1> <b><?php echo htmlspecialchars($_SESSION["user"]); ?></b>'s  Staff Page</h1>
    </div>


 <br> <br> <br> <br> <br><br>
    <div class="wrapper" id = "additem">
        <p>Please add Items into inventory.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <div class="form-group <?php echo (!empty($item_name_err)) ? 'has-error' : ''; ?>">
                <label>Item Name</label>
                <input type="text" name="item_name" class="form-control" value="<?php echo $item_name; ?>"> 
                <span class="help-block"><?php echo $item_name_err; ?></span>
            </div> 
            <div class="form-group <?php echo (!empty($item_name_err)) ? 'has-error' : ''; ?>">
                <label>Product ID</label>
                <input type="text" name="product_id" class="form-control" value="<?php echo $product_id; ?>"> 
                <span class="help-block"><?php echo $product_id_err; ?></span>
            </div> 
            <div class="form-group <?php echo (!empty($quantity_err)) ? 'has-error' : ''; ?>">
                <label>Item Quantity</label>
                <input type="input" name="quantity" class="form-control" value="<?php echo $quantity; ?>">
                <span class="help-block"><?php echo $quantity_err; ?></span>
            </div>   
             <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                <label>Item Price</label>
                <input type="input" name="price" class="form-control" value="<?php echo $price; ?>">
                <span class="help-block"><?php echo $price_err; ?></span>
            </div>  
            <label for="file-upload" class="custom-file-upload"> Item Picture </label>
            <input id="file-upload" name = "image" type="file" accept="image/*" enctype="multipart/form-data" value = "<?php echo $image; ?>"/>
            <br> 
            <?php echo $success_msg ?>
            <br>
            <div class="form-group">
                <input type="submit" name = "stock_up" class="btn btn-primary" value="Add Item">
                <input type="reset" class="btn btn-warning" value="Reset"> <br> <br>
                <a href="items.php" class="btn btn-success">Go To Shopping Cart</a>
                
            </div>

</form>
</div>



<div id = "login">
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>

</div>
</body>
</html>