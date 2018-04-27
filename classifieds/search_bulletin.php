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
        while ($bulletin = $bulletinSearchQueryResult->fetch_assoc()) {
            $title = $bulletin['Title'];
            $description = $bulletin['Description'];
            $createdDate = $bulletin['CreatedDate'];

            //Style and format each individual pod
            echo <<< HTML
            <div class="col-sm-4">
                <div class="panel panel-default" style="min-height: 450px">
                    <div class="panel-heading panelHeading">$title</div>
                    <div class="panel-body">
                        <img class="img-responsive" src="../img/no-img-available.jpg" alt=""" style="width: 350px; height: 250px">
                        <br>
                        <p><b>Description: </b> $description</p>
                        <h5><b>User: </b>User Name (Placeholder)</h5>  
                        <h5><b>Created: </b>$createdDate</h5>  
                    </div>
                </div>                        
            </div>       
HTML;
        }
    }
}

mysqli_close($dbLogin);
?>     