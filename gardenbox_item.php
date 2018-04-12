<?php
require_once "SLCCGAuthenticate.php";
?>
<! DOCTYPE html>
<html>
  <head>
  <meta charset="utf8" />
  <meta name="viewport" content="width=device-width, initial-scale="1" />
  <style>
    nav * {vertical-align:middle;}
    body {max-width:65em; padding:1em;}
    #stats {float:right; margin:1em; border:1px solid #000000; padding:0.5em;}
    #vitals {margin:1em; border:1px solid #000000; padding:0.5em; overflow:auto;}
    #photos figure {vertical-align:top; margin:1em; padding:0.5em; border:1px solid #000000;
      max-height:250px; display:inline-block;}
    #log {border:1px solid #000000; padding:0.5em;}
  </style>
  </head>
  <body>
    <nav>
      <img src="images/static/slccg.png" />
      <a href="classifieds_landing.php">Classifieds</a> 
      | <a href="gardenbox_landing.php">Garden Box Logs</a> 
      | <a href="manage_members.php">Manage Members</a> 
      | <a href="index.php">Back to Main Site</a> 
    </nav>

  <div id="vitals">
    <div id="stats">
      <h3>Statistics:</h3>
      <p><b>pH</b> 4.5</P>
      <p><b>Conductivity:</b> 12.2</p>
      <p><b>Phosphorus:</b> 6.7</p>
      <p><b>Potassium:</b> 12.3</p>
    </div>

    <p><b>Plot #:</b> 123</p>
    <p><b>Site:</b> Taylorsville</p>
    <p><b>UserID:</b> Green_Thumb_Tom</p>
    <p><b>Expires:</b> 7/31/2018</p>
  </div>

  <h2>Log</h2>
  <div id="log">
  <p>1/27/2018. Box is ready but you won't be able to plant until the snow melts.</p>
  <p>2/13/2018. We've added new borders to the boxes.  Enjoy!</p>
  <p>2/17/2018. Pre-emergent will be available for pickup beginning March 1.</p>
  <p>3/4/2018. pH was tested today. You're in a good range for roses.</p>
  </div>

  <h2>Add comment to log:</h2>
  <textarea rows="5" cols="50"></textarea>
  <button>Add Comment</button>
  
  <h2>Photos<h2>
  <div id="photos">
  <figure>
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/18_Pflanzen.jpg/320px-18_Pflanzen.jpg" />
    <figcaption>Close up of Herbie.</figcaption>
  </figure>
  <figure>
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/85/Rosa_%27Aushunter%27_Jubilee_Celebration%2C_at_RHS_Garden_Hyde_Hall%2C_Essex%2C_England.jpg/320px-Rosa_%27Aushunter%27_Jubilee_Celebration%2C_at_RHS_Garden_Hyde_Hall%2C_Essex%2C_England.jpg" />
    <figcaption>Are these leaves too red?</figcaption>
  </figure>
  <figure>
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/1_Cactus%2C_2_cactus_and_3_Cactus_%2837786982512%29.jpg/255px-1_Cactus%2C_2_cactus_and_3_Cactus_%2837786982512%29.jpg" />
    <figcaption>Cactus coming along nicely.</figcaption>
  </figure>
  </div>

  <h2>Schematic</h2>
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ec/Garden_KR_Plan.png/296px-Garden_KR_Plan.png" />
  </body>
</html>
