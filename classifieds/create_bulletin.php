<html>
    <head>
        <title>Create Bulletin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link href="../css/style.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="../css/header.css">
    </head>
    <body class="classifiedsContainer">
        <!--top navigation bar -->
        <?php
        require("../common/header.php"); 
        ?>
        <!--end navigation bar --
        <!--begin form for bulletin creation -->
        <div class="overlayContainerFull" style="height: 100% !important">
            <div class="bulletinFormContainer">
                <h2>Create A New Classified Bulletin</h2>
                <br>
                <div>
                    <form name="CreateBulletin" action="create_bulletin_results.php" method="post">
                        <fieldset style="width: 50%">
                            <legend>*Please Fill Out Completely</legend>
                            <label>Title:</label>
                            <input type="text" name="Title" required style="width: 385px;">
                            <br><br>
                            <label>Description:</label>
                            <input type="textarea" name="Description" required style="width: 385px; height: 200px">
                            <br><br>
                            <input type="submit" value="Add" name="Save" class="bulletinFormButton">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
        <!--end bulletin creation form -->
        <script src="../js/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="../js/header.js" type="text/javascript"></script>
        <script src="../js/nav.js" type="text/javascript"></script>
    </body>
</html>
