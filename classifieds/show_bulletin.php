<html>
    <head>
        <title>Create Bulletin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="../css/header.css">
        <script src="../js/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="../js/header.js"></script>
    </head>
    <body class="classifiedsContainer">
    <!--top navigation bar -->

<?php
    require("../common/dbconfig.php");
    require("../common/header.php"); // end navigation bar
    
    $uName = $_SESSION['uname'];
    
    $search = "SELECT * from SLCCGardens.Bulletins where uname Like '%$uName%'";
    $return = $con->query($search);
    
    if (!$return) 
    {
        $message = "Whole query " . $search;
        echo $message;
        die('Invalid query: ' . mysqli_error($con));
    }
    
    echo "<h2>The following contact(s) were found: </h2><br>";
    echo "<table class='table'><thead><th>Title</th><th>Description</th></thead><tbody>";
    while ($row = $return->fetch_assoc()) 
    {
        echo "<tr><td> " . $row['Title']
        . "</td><td>   " . $row['Description']
        . "</td></tr>";
    }
    echo "</tbody></table>";
    
    
    $con->close;
?>
