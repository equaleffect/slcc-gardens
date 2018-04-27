<?php

require("../common/dbconfig.php");

if (isset($_POST['searchTitle']) || isset($_POST['searchDescription'])) {
    $searchTitle = $_POST['searchTitle'];
    $searchDescription = $_POST['searchDescription'];

    //Query to search bulletins based on search input of title or description
    $bulletinSearchQuery = "SELECT * FROM SLCCGardens.Bulletins where Title like '%$searchTitle%' and Description like '%$searchDescription%' ORDER BY CreatedDate";
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
    }
}

mysqli_close($dbLogin);
?>     