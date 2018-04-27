<html>
    <head>
        <title>SLCC Gardens Classifieds</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
    </head>

    <body class="classifiedsContainer" style="overflow: auto !important"> 
        <div class="overlayContainerFull">
            <?php
            require("../common/dbconfig.php");
            require("../common/header.php");

            //Query to get all bulletin posts and display them in a formatted view
            $bulletinSearchQuery = "SELECT * FROM SLCCGardens.Bulletins ORDER BY CreatedDate";
            $bulletinSearchQueryResult = $dbLogin->query($bulletinSearchQuery);
            if (!$bulletinSearchQueryResult) {
                die($dbLogin->error);
            }

            if ($bulletinSearchQueryResult->num_rows > 0) {
                //Table to store each bulletin
                echo "<table>";
                while ($bulletin = $bulletinSearchQueryResult->fetch_assoc()) {
                    $title = $bulletin['Title'];
                    $description = $bulletin['Description'];
                    $createdDate = $bulletin['CreatedDate'];

                    //Style and format each individual pod
                    echo <<< HTML
                    <tr>
                        <td>
                            <div style="color: white">
                            <div>
                            <img src="../img/no-img-available.jpg" alt=""" style="width: 300px; height: 300px">
                            <br>
                            <h5>Title: $title</h5>
                            <h5>Description: $description</h5>
                            <img src="../img/profile-holder.jpg" alt=""" style="width: 30px; height: 30px">
                            <h5>User Name (Placeholder)</h5>  
                            <h5>Created: $createdDate</h5>    
                            <br/>
                            </div>
                        </td>    
                    </tr>        
HTML;
                }
                //End Table
                echo "</table>";
            }
            ?>
        </div>
    </body>
</html>