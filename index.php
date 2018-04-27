<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>SLCC Gardens Classifieds</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
<?php
require 'common/header.php';
?>
<!--split main content area-->
<div class="container">
    <!--left side is for garden boxes-->
    <div class="split left">
        <h1>Garden Boxes</h1>
        <a href="boxes/gardenbox_landing.php" class="btn-link">Record Progress</a>
    </div>
    <!--right side is for classifieds-->
    <div class="split right">
        <h1>Classifieds</h1>
        <a href="classifieds/classifieds_landing.php" class="btn-link">Buy/Sell/Trade</a>
    </div>
</div>
<!--end split main content area-->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/landing.js"></script>
<script src="js/header.js"></script>
<script src="js/nav.js"></script>
<script>
    (function () {
        <?php
        if (isset($_SESSION['uname'])){
            echo "localStorage.user='".$_SESSION['uname']."';";
        }
        ?>
    }())
</script>
</body>
</html>
