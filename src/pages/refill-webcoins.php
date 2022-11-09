<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION["email"])) {
    header("Location: signin.php");
}

/**
 *  IMPLEMENT REFILL BY AMOUNT
 *     Caroline and Thanh Vu 11/03/2022
 */

$userId = $_SESSION['userid'];

try {
    if (!empty($_GET['amounts'])) {
        $refillAmount = $_GET['amounts'] ?? '0';

        $conn->beginTransaction(); 
        $sql = ("UPDATE User SET webstoreBalance = webstoreBalance + ? where id = ?");
        $statement = $conn->prepare($sql);
        $statement->bindValue(1, $refillAmount);
        $statement->bindValue(2, $userId);
        $statement->execute();
        $conn->commit(); 
    
        $messageRefilled =  "<div class='alert alert-warning alert-dismissable'>
        <a href='signin.php' class='close' data-dismiss='alert' aria-label='close'>&times;</a> 
        &curren; $refillAmount added successfully 
    </div>";
    }   

    $stmt = $conn->query("SELECT webstoreBalance FROM User where id = $userId");
    $result = $stmt->fetch();
    $userBalance = $result['webstoreBalance'] ?? -1;
} catch(PDOException $e) {
    header("Location: error.php?error=Connection failed:" . $e->getMessage());
  }

// close db connection to save resources
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<!-- AVOID FORM RESUBMISSION WARNING UPON PAGE REFRESH-->
<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>
<!-- END SCRIPT - PAGE REFRESH-->
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/account.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" />
    <title>Webstore</title>
</head>

<body>
    <div id="root">
        <?php include("partials/header.php") ?>
        <div class="catalog">
            <div>
                <?php include("../pages/partials/sidebar.php") ?>
            </div>

            <div>
                <strong><p>Remaining Balance:</strong>&curren; <?php echo $userBalance ?></p>
                <form action="refill-webcoins.php" method="get">
                    <label for="amount">Add coins to the account:</label>
                    <select name="amounts" id="amount">
                        <option value=500>500</option>
                        <option value=200>200</option>
                        <option value=100>100</option>
                    </select>
                    <br>
            <!-- IF THERE IS AN ERROR for the user or password information, then display this --> 
            <?php 
              echo (!empty($messageRefilled)) ? $messageRefilled : '';
            ?>
            <!-- END display error -->
                    
                    <button type="submit" value="Submit" class="btn btn-secondary" style="margin-top: 10px">Add</button>
                </form>
            </div>
        </div>
        <?php include("partials/footer.php") ?>
    </div>

    <!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>