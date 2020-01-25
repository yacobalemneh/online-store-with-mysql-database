<?php
// Initialize the session
session_start();
require_once "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;   
}
$user = $_SESSION["user"];
$permission = $_SESSION["permission"];
$idNO = $_SESSION["id"];

?>
<?php


if($_SERVER["REQUEST_METHOD"] == "POST"){ 

        $name = $_POST["hidden_name"];
        $price = $_POST["hidden_price"];
        $quantity= $_POST["quantity"];
        $discount = $_POST["hidden_discount"];
        
        $pID = $_POST["hidden_ID"];
        $sql = "INSERT INTO Cart (uid, pID, pname, pquantity, pprice, pdiscount) 
            VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE pquantity = pquantity+1;";

        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iisidd", $param_uid, $param_pid, $param_pname, 
                                    $param_pquantity, $param_pprice, $param_discount);
            $param_uid = $idNO;
            $param_pid = $pID;
            $param_pname = $name;
            $param_pquantity = $quantity;
            $param_pprice = $price;
            $param_discount = $discount;

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
           
        }

        $delete = "DELETE FROM ITEMS WHERE (Product_Quantity <= 0)";
        $delete_statement = mysqli_prepare($link, $delete);
        mysqli_stmt_execute($delete_statement);
        mysqli_stmt_close($delete_statement);
        
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    
</head>
<style>
    #thumbnail {border: 2px, solid #333; background-color:#f1f1f1; border-radius: 10px; padding:16px;}
    #nav {font-size: 20px;}

</style>

<body>
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
              <a class="navbar-brand" href="#">TozRUs</a>
          </div>
          <ul class="nav navbar-nav">
              <li class="active"><a href="#">Home</a></li>
              <?php if ($permission == 3)  
              echo '<li><a href="manager.php">Manager Page</a></li>
              <li><a href="orders.php">Orders</a></li>
              <li><a href="statistics.php">Statistics</a></li>';
              if ($permission == 2)
                echo '<li><a href="staff.php">Inventory</a></li>
              <li><a href="order.php">Orders</a></li>';
              if ($permission == 1)
                echo '<li><a href="customer_order.php">Order Status</a></li>';
               ?>
               <li><a href="cart.php">Cart</a></li>
              
              
          </ul>
          <ul class="nav navbar-nav navbar-right">
             <li><a  href="reset-password.php" class="btn btn-outline-danger">Reset Password</a></li>
              <li><a  href="logout.php" class="btn btn-outline-danger">Sign Out</a></li>

          </ul>


      </div>
    </nav>
    <br/>


    <div class="conatainer" style = "width: 1200px;" >
        <h2 align = "center"> Shop Toys </h2> <br/>
        <?php

        $sql = "SELECT * FROM Items ORDER BY Product_Id ASC";
        $result = $link->query($sql);
        while($row = $result->fetch_assoc()) {
            ?>
            <div class = "col-md-4" >
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div id ="thumbnail" align = "center">
                        
                        <h4 class= "test-info"> <?php echo $row["Product_name"]; ?> </h4>
                        <h4 class = "text-danger">$ <?php echo $row["Product_Price"];?> </h4>
                        <?php if ($row["Discount"] !== NULL && $row["Discount"] != 0) { ?>
                        <h4 class = "text-danger">Discount Get $<?php echo $row["Discount"];?> Off </h4>
                        
                        <h4 class = "text-danger">New Price: $<?php echo $row["Product_Price"]-$row["Discount"];?> </h4>
                        <?php } ?>

                          <?php if (($permission === 2 || $permission == 3) && ($row["Product_Quantity"] > 10)) { ?>
                        <h4 class = "text-success">Stock Quantity: <?php echo $row["Product_Quantity"];?> </h4>
                        <?php }  ?>
                    
                        <?php if (($permission === 2 || $permission == 3) && ($row["Product_Quantity"] <= 10)) {
                        ?>
                        <h4 class = "text-warning">(Alert) Low Stock: <?php echo $row["Product_Quantity"];?> </h4>
                        <?php }  ?>

                        <input type = "text" name = "quantity" class = "form-control" value = "1" 
                        style = "width: 40px;"/>

                        <input type="hidden" name= "hidden_name" value="<?php echo $row["Product_name"]; ?> " />
                        <input type="hidden" name= "hidden_price" value="<?php echo $row["Product_Price"]; ?> " />
                        <input type="hidden" name= "hidden_ID" value="<?php echo $row["Product_Id"]; ?> " />
                        <input type="hidden" name= "hidden_discount" value="<?php echo $row["Discount"]; ?> " />
                        <input type = "submit" name = "add_to_cart" style = "margin-top: 5px" class="btn btn-primary" 
                        value = "Add to Cart" />

                        <br><br>
                    </div>
                    <br>
                </form> 
            </div>

            <?php
        }
        
        ?>

    </div>
    <br>
</body>
</html>
