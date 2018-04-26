<html>
    <head>
        <title>Create Bulletin</title>
        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
    </head>

    <body class="classifiedsContainer"> 
<?php
    require("../common/dbconfig.php");  

    //top navigation bar
    require("../common/header.php");     
    
    $title = clean_input($_POST['Title']);    
    $description = clean_input($_POST['Description']);
    $userKey = 2; //TODO: Use SESSION UserKey from Valid Authentication to save the correct user to bulletin post. Using temp key for testing
    $createdDate = date("Y-m-d H:i:s");
    $expirationDate = date("Y-m-d H:i:s"); // We will need to look into this value. Maybe add logic on the initial bulletin page if dateToday > expiratetionDate don't display
    
    if(isset($_POST['Title'], $_POST['Description']))
    {                              
        $newBulletinInsertQuery = $dbLogin->prepare("INSERT INTO SLCCGardens.Bulletins (UserKey, Title, Description, CreatedDate, ExpirationDate) VALUES ('$userKey', '$title', '$description', '$createdDate', '$expirationDate')");
        $newBulletinInsertQuery->execute();
            if ($dbLogin->query($newBulletinInsertQuery) === FALSE) {
                echo "Error: " . $newBulletinInsertQuery . "<br>" . $dbLogin->error;
            }                        

        echo "<div class=\"overlayContainerFull\">";    
            if ($newBulletinInsertQuery->affected_rows > 0)
            {            
            echo "<h1>Bulletin Successfully Created!</h1>";
            }
            else 
            {
                echo "<h1>There was a problem creating the bulletin! Please go back and try again. For additional support please contact our Technical Help Desk</h1>"; 
            }
        echo "</div>";
    }
    
    function clean_input($var)
    {
        $var = trim($var);
        $var = stripslashes($var);
        $var = htmlspecialchars($var);

        return $var;
    }
    
    mysqli_close($dbLogin);
?>    
    </body>    
</html>

