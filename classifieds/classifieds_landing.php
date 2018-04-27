<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>SLCC Gardens Classifieds</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
        <link href="../css/header.css" rel="stylesheet" type="text/css"/>
        <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

        <script>
            //Script is used to call the search_bulletin.php file to retrieve bulletins based on search input
            function SearchBulletins()
            {
                var searchTitle = document.forms["SearchForm"]["SearchTitle"].value;
                var searchDescription = document.forms["SearchForm"]["SearchDescription"].value;
                if (searchTitle || searchDescription)
                {
                    $.ajax({
                        type: 'post',
                        url: '/classifieds/search_bulletin.php',
                        data: {searchTitle: searchTitle, searchDescription: searchDescription,
                        },
                        success: function (response) {
                            document.getElementById("BulletinSearch").innerHTML = response;
                        }
                    });

                    return false;
                }
            }
        </script>
    </head>

    <?php
    require("../common/dbconfig.php");
    require("../common/header.php");
    ?>

    <body class="classifiedsContainer" style="overflow: auto !important"> 

        <div class="col-sm-8 col-sm-offset-2" style="background-color: rgba(0,0,0,.6); padding: 15px">
            <div class="col-sm-12">
                <p class="col-sm-8" style="color: white; font-size: 28px;">SLCC Gardens Classifieds</p>

                <!-- Seach form to filter bulletins-->
                <form class="col-sm-12" name="SearchForm" onsubmit="return SearchBulletins()" style="margin-bottom: 15px">
                    <label style="color: white">Search Classifieds: </label>
                    <label style="color: white; margin-left: 25px">Title: </label>
                    <input type="text" name="SearchTitle">
                    <label style="color: white;margin-left: 25px">Description: </label>
                    <input type="text" name="SearchDescription">
                    <input type="submit" value="Search" name="Search" style="margin-left: 25px">
                </form>
            </div>

            <div id="BulletinSearch">
                <?php
                //Query to get all bulletin posts and display them in a formatted view
                $bulletinSearchQuery = "SELECT * FROM SLCCGardens.Bulletins ORDER BY CreatedDate";
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
                    //End Table
                    echo "</table>";
                }
                ?>
            </div>
        </div>
        <script src="../js/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="../js/header.js"></script>
        <script src="../js/nav.js"></script>
        <script src="../js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>