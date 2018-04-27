<html>
    <head>
        <title>SLCC Gardens Classifieds</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <script src="../js/jquery-3.3.1.min.js" type="text/javascript"></script>
        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
        <link href="../css/header.css" rel="stylesheet" type="text/css"/>

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
        <div class="overlayContainerFull">
            <br>
            <h2 style="color: white">SLCC Gardens Classifieds</h2>
            <h3 style="color: white">Search Classifieds</h3>

            <!--Seach form to filter bulletins-->
            <form name="SearchForm" onsubmit="return SearchBulletins()" style="color: white">
                <label>Title: </label>
                <input type="text" name="SearchTitle">
                <label style="margin-left: 25px">Description: </label>
                <input type="text" name="SearchDescription">
                <input type="submit" value="Search" name="Search" style="margin-left: 25px">
            </form>
            <br>          
            <form style="color: white" action="create_bulletin.php">
                <label>Create New Classifieds: </label>
                <input type="submit" value="Create" name="Create" style="margin-left: 25px">
            </form>


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
        </div>
    </body>
</html>