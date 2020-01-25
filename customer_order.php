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
              echo '<li><a href="manager.php">Admin Page</a></li>
              <li class="active"><a href="#">Orders</a></li>
              <li><a href="statistics.php">Statistics</a></li>';
              else if ($permission == 2)
                echo '<li><a href="staff.php">Inventory</a></li>
              <li class="active"><a href="#">Orders</a></li>';
               ?>
              
              <li class = "active"><a href="#">Order Status</a></li>
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
    
        <a href="items.php" class="btn btn-success" style = "position: sticky; margin-left: 800px;"href="items.php">
          Go To Shopping Page</a>
        </form>
        <br> <br>
       <?php 
        $sql = "SELECT * FROM Shipped WHERE (uid = $idNO)";
        $result = $link->query($sql);
        while($row = $result->fetch_assoc()) {
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class = "container-right" style = "width: 1000px; position: sticky; margin-left: 100px">
            <table class="table table-hover" align="right">
                <tr>
                    <td>Order No. <?php echo $row["orderID"]; ?> </td>
                </tr>
                
                    </table>
                    <?php if (empty($row["date"])) {?>
                    <h4>Order is Pending</h4>
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
