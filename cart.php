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
        $sub_total = $_POST["hidden_subtotal"];
        $total = $_POST["hidden_grand_total"];
        $name = $_POST["hidden_name"];
        $price = $_POST["hidden_price"];
        $quantity= $_POST["quantity"];
        $procuct_id = $_POST["hidden_pid"];
        $order_complete = FALSE;

        if ($_POST["less"]) {
            $decrease = "UPDATE Cart SET 
                pquantity = 
                pquantity - 1 
                WHERE (pID = $procuct_id) and pquantity >= 0";

        $decrement = mysqli_prepare($link, $decrease);
        mysqli_stmt_execute($decrement);
        mysqli_stmt_close($decrement);

        }
        if ($_POST["more"]) {
                $increase = "UPDATE Cart SET 
                pquantity = 
                pquantity + 1 
                WHERE (pid = $procuct_id ) and pquantity >= 0";

        $increment = mysqli_prepare($link, $increase);
        mysqli_stmt_execute($increment);
        mysqli_stmt_close($increment);

        }
        if ($_POST["remove_from_cart"]) {
            $delete = "DELETE FROM Cart WHERE (pid = $procuct_id)";
            $delete_statement = mysqli_prepare($link, $delete);
            mysqli_stmt_execute($delete_statement);
            mysqli_stmt_close($delete_statement);

        }

        $delete = "DELETE FROM Cart WHERE (pquantity <= 0)";
        $delete_statement = mysqli_prepare($link, $delete);
        mysqli_stmt_execute($delete_statement);
        mysqli_stmt_close($delete_statement);

        if ($_POST["add_to_order"]) {

            $sql = "SELECT * FROM Cart WHERE uid = $idNO;";
            $result = $link->query($sql);
            $insert_query = "INSERT INTO Pending (uid, productName, productID, pquantity, total, shipped) 
                    VALUES (?, ?, ? , ?, ?, ?) ";
            $insert_statement = mysqli_prepare($link, $insert_query);
            while($row = $result->fetch_assoc()) { 
                    
                    mysqli_stmt_bind_param($insert_statement,"isiidi", $param_uid, $param_pname, 
                        $param_pid, $param_quantity, $param_total, $param_shipped);
                    $param_uid = $row["uid"];
                    $param_pname = $row["pname"];
                    $param_pid = $row["pid"];
                    $param_quantity = $row["pquantity"];
                    $param_total = $row["pquantity"] * $row["pprice"];
                    $param_shipped = FALSE;
                    mysqli_stmt_execute($insert_statement);

            }
            mysqli_stmt_close($insert_statement);
            $empty_cart = "DELETE FROM Cart WHERE uid = $idNO;";
            $empty_statement = mysqli_prepare($link, $empty_cart);
            mysqli_stmt_execute($empty_statement);
            mysqli_stmt_close($empty_statement); 
            $order_complete = TRUE;
            
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
              <li><a href="orders.php">Orders</a></li>
              <li><a href="statistics.php">Statistics</a></li>';
              else if ($permission == 2)
                echo '<li><a href="staff.php">Inventory</a></li>
              <li><a href="orders.php">Orders</a></li>'; 
              else if ($permission == 1)
                echo '<li><a href="customer_order.php">Order Status</a></li>';
               ?>
               <li class="active"><a href="#">Cart</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
              <li><a  href="logout.php" class="btn btn-outline-danger">Sign Out</a></li>
          </ul>
      </div>
    </nav>
    <br/>

    <div>
        <h2 align = "center"> Shopping Cart </h2> <br/>
        <?php if ($order_complete == TRUE) { ?>
        <br> <br> <br>
        <h2 align = "center"> Order Complete </h2> <br/>
            <a style = "position: sticky; margin-left: 800px;"href="items.php" class="btn btn-success">Go To Shopping Cart</a>
       <?php
   }
        $sql = "SELECT * FROM Cart WHERE uid = $idNO;";
        $result = $link->query($sql);
        while($row = $result->fetch_assoc()) {
            
            ?>
        <div class="conatainer" style = "width: 800px;" >
            <div class = "col-md-4" >
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div id ="thumbnail" align = "center">
                        <h4 class= "test-info"> <?php echo $row["pname"]; ?> </h4>
                        <h4 class = "text-danger">$ <?php echo $row["pprice"];?> </h4>
                        <?php if ($row["pdiscount"] !== NULL && $row["pdiscount"] != 0) { ?>
                        <h4 class = "text-danger">Discount Get $<?php echo $row["pdiscount"];?> Off </h4>
                        
                        <h4 class = "text-danger">New Price: $<?php echo $row["pprice"]-$row["pdiscount"];?> </h4>
                        <?php } ?>
                        <h4 class = "text"><?php echo $row["pquantity"];?> </h4>
                        <input type="hidden" name= "hidden_name" value="<?php echo $row["pname"]; ?> " />
                        <input type="hidden" name= "hidden_price" value="<?php echo $row["pprice"]; ?> " />
                        <input type="hidden" name= "hidden_pid" value="<?php echo $row["pid"]; ?> " />
                        <input type = "submit" name = "less" style = "margin-top: 5px" class="btn btn-outline-info" 
                        value = "<" />
                        <input type = "submit" name = "more" style = "margin-top: 5px" class="btn btn-outline-info" 
                        value = ">" />
                        <br>
                        <input type = "submit" name = "remove_from_cart" style = "margin-top: 5px" class="btn btn-danger" 
                        value = "Remove" />
                        <br><br>
                    </div>
                    <br>      
            </div>
        </div>
        </form>

        
      <?php  
    } 
    
    ?>
            <div class = "container-right" style = "width: 400px; position: sticky; margin-left: 1200px">
            <table class="table table-hover" align="right">
                <tr>
                    <td>Total</td>
                </tr>

    <?php
        $sql = "SELECT * FROM Cart WHERE uid = $idNO;";
        $result = $link->query($sql);
        $grandtotal = 0;
        while($row = $result->fetch_assoc()) {
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <tr>
                    <td> <?php echo $row["pname"]; ?> </td>
                    <td>$<?php echo $row["pprice"]; ?></td>
                    <td>Quantity: <?php echo $row["pquantity"]; ?></td>
                    <?php $subtotal = $row["pquantity"]*$row["pprice"]; ?>
                    <td>Sub Total: $<?php echo $row["pquantity"]*$row["pprice"]; ?></td>
                    <?php $grandtotal = $grandtotal + $row["pquantity"]*$row["pprice"]; ?>
                    <input type="hidden" name= "hidden_grand_total" value="<?php echo $grandtotal; ?> " />
          <?php  
    }
    ?>
          </tr>
          <td> Grand Total: $<?php echo $grandtotal ?> </td>
            </table>
            <input type = "submit" name = "add_to_order" style = "margin-top: 5px" class="btn btn-primary" 
                        value = "Place Order" />
            
        </div>
    </form>
   <div>
<br>
   
</body>
</html>
