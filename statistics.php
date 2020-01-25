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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
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
                <li><a href="items.php">Home</a></li>
                 <?php if ($permission == 3)  
                echo '<li><a href="manager.php">Manager Page</a></li>
                <li ><a href="orders.php">Orders</a></li>
                <li class= "active"><a href="#">Statistics</a></li>';
                else if ($permission == 2)
                  echo '<li><a href="staff.php">Inventory</a></li>
                <li class="active"><a href="#">Orders</a></li>';
                 ?>
                <li ><a href="cart.php">Cart</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a  href="logout.php" class="btn btn-outline-danger">Sign Out</a></li>
            </ul>
        </div>
      </nav>
      <br/>
   
        <h2 align = "center"> Statistics </h2> <br/>
        <br> <br>
       <?php 
        $sql = "SELECT * FROM Shipped INNER JOIN Items ON Items.Product_ID = Shipped.productID";
        $result = $link->query($sql);
      while($row = $result->fetch_assoc()) {
    ?>
     <div class = "container-right" style = "width: 1000px; position: sticky; margin-left: 100px">
            <table class="table table-hover" align="right">
                
                    <td>Order No. <?php echo $row["orderID"]; ?> </td>
                    <td>Product ID: <?php echo $row["productID"]; ?></td>
                    <td>Product Name: <?php echo $row["Product_name"]; ?></td>
                    <td>Amount Sold: <?php echo $row["quantity"]; ?></td>
                    <td>Customer Paid: $<?php echo $row["quantity"] * $row["Product_Price"]; ?></td>
                    <td>Amount in Stock: <?php echo $row["Product_Quantity"]; ?></td>
                    <input type="hidden" name= "order_id" value="<?php echo $row["orderID"]; ?> " />
                    <input type="hidden" name= "hidden_uid" value="<?php echo $row["uid"]; ?> " />
                    <input type="hidden" name= "hidden_pname" value="<?php echo $row["productName"]; ?> " />
                    <td>Ordered On: <?php echo $row["date"]; ?></td>
                    </table>

     </div>
        
   
     <?php  
    } 
    
    ?> 
 
  
    <br>
      
     

</body>
</html>
