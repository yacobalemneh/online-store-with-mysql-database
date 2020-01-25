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

  if ($_POST["ship_order"]) {
        $item_name = $_POST["hidden_pname"];
        $item_quantity = trim($_POST["hidden_quantity"]);
        $an_order = $_POST["order_id"];
        // SET SHIPPED BOOL TO TRUE
        $ship_order = "UPDATE Pending SET 
                    shipped = 1
                    WHERE (orderID = $an_order);";

        $shipped = mysqli_prepare($link, $ship_order);
        mysqli_stmt_execute($shipped);
        mysqli_stmt_close($shipped);
        // DECREASE STOCK WHEN ORDER IS SHIPPED
        $decrease_stock = "UPDATE Items SET 
                    Product_Quantity = Product_Quantity - $item_quantity
                    WHERE (Product_name = '$item_name')";
                  
        $item_removed = mysqli_prepare($link, $decrease_stock);
        mysqli_stmt_execute($item_removed);
        mysqli_stmt_close($item_removed);
        // QUERY TO SEND SHIPPED ITEMS TO TABLE SHIPPED
        $sql = "SELECT * FROM Pending WHERE shipped = 1";
        $result = $link->query($sql);
        $insert_query = "INSERT IGNORE INTO Shipped (uid, orderID, quantity, productID, date) 
                        VALUES (?, ?, ?, ?, ?) ";
        $insert_statement = mysqli_prepare($link, $insert_query);
        while($row = $result->fetch_assoc()) { 
            $date = date('Y-m-d H:i:s');
            mysqli_stmt_bind_param($insert_statement,"iiiis", $param_uid, $param_oid, $param_quantity, $param_pid, 
                    $param_date);
            $param_uid = $row["uid"];
            $param_oid = $row["orderID"];
            $param_quantity = $row["pquantity"];
            $param_pid = $row["productid"];
            $param_date = $date;
            mysqli_stmt_execute($insert_statement);
        }
    mysqli_stmt_close($insert_statement);
}
    if ($_POST["clear_shipped"]) {
          $clear_orders = "DELETE FROM Pending WHERE shipped = 1;";
          $clear_statement = mysqli_prepare($link, $clear_orders);
          mysqli_stmt_execute($clear_statement);
          mysqli_stmt_close($clear_statement); 

    }

}

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
              <li class="active"><a href="#">Orders</a></li>
              <li><a href="statistics.php">Statistics</a></li>';
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

        <h2 align = "center"> Orders </h2> <br/>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type = "submit" name = "clear_shipped" style = "margin-top: 5px; width: 200px; margin-left: 100px" class="btn btn-success" 
                        value = "Clear Shipped" /> 
                        <a href="items.php" class="btn btn-success">Go To Shopping Cart</a>
        </form>
        <br> <br>
       <?php 
        $sql = "SELECT * FROM Pending ORDER BY total ASC";
        $result = $link->query($sql);
        while($row = $result->fetch_assoc()) {
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class = "container-right" style = "width: 1000px; position: sticky; margin-left: 100px">
            <table class="table table-hover" align="right">
                <tr>
                    <td>Order No. <?php echo $row["orderID"]; ?> </td>
                </tr>
                    <td>User ID: <?php echo $row["uid"]; ?></td>
                    <td>Product: <?php echo $row["productName"]; ?></td>
                    <input type="hidden" name= "order_id" value="<?php echo $row["orderID"]; ?> " />
                    <input type="hidden" name= "hidden_uid" value="<?php echo $row["uid"]; ?> " />
                    <input type="hidden" name= "hidden_pname" value="<?php echo $row["productName"]; ?> " />
                    <input type="hidden" name= "hidden_quantity" value="<?php echo $row["pquantity"]; ?> " />
                    <td>Amount Paid: $ <?php echo $row["total"]; ?></td>
                    </table>
                    <?php if ($row["shipped"] == 0) {?>
                    <input type = "submit" name = "ship_order" style = "margin-top: 5px" class="btn btn-danger" 
                        value = "Ship" />
        <?php
                }
                else {
            ?>     <h4>Order Has Been Shipped</h4> <?php
                }
                  ?>

        </div>
        </form>
   
     <?php  
    } 
    
    ?> 
    <br>

</body>
</html>
