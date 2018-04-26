<?php
require_once "../common/SLCCGAuthenticate.php";

// include management link for staff
$manmem = null;
if($_SESSION){
  if($_SESSION['permrole']){
    if($_SESSION['permrole'] == "Admin"){
      $manmem = "<a href=\"users.php\">Manage Members</a>";}}}

// save userid as js var
if(!empty($_SESSION['uname'])){
  $logun = "var logun = \"{$_SESSION['uname']}\";";}
else {$logun = null;}
?>
<! DOCTYPE html>
<html>
  <head>
  <meta charset="utf8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    nav * {vertical-align:middle;}
    body {max-width:65em; padding:1em;}
    #stats {float:right; margin:1em; border:1px solid #000000; padding:0.5em;}
    #vitals {margin:1em; border:1px solid #000000; padding:0.5em; 
      overflow:auto; background-color:#ffffff;}
    #stats label {width:7em; display:inline-block;}
#stats input {width:5em;}
    #photos figure {vertical-align:top; margin:1em; padding:0.5em; border:1px solid #000000;
      display:inline-block; background-color:#ffffff;
      overflow:auto;}
    #photos figure img {max-height:250px;}
    #photos p {margin:1em 0 0.25em;}
    #log {border:1px solid #000000; padding:0.5em; 
      background-color:#ffffff;}
    #cb {display:block; margin:1em;}
    #uploadpic {border:1px solid #000000; padding:1em; background-color:#ffffff;}
    #uploadpic p {margin:0 1em;}

* {
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
  background: #535353;
}

h2 {margin:1em 1rem 0.25em 1rem;}
h2+p {margin:0.25em 1rem 1rem 1rem;} 

.header{
background: #cdeb8e;
background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2NkZWI4ZSIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNhNWM5NTYiIHN0b3Atb3BhY2l0eT0iMSIvPgogIDwvbGluZWFyR3JhZGllbnQ+CiAgPHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
background: -moz-linear-gradient(top, #cdeb8e 0%, #a5c956 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#cdeb8e), color-stop(100%,#a5c956));
background: -webkit-linear-gradient(top, #cdeb8e 0%,#a5c956 100%);
background: -o-linear-gradient(top, #cdeb8e 0%,#a5c956 100%);
background: -ms-linear-gradient(top, #cdeb8e 0%,#a5c956 100%);
background: linear-gradient(to bottom, #cdeb8e 0%,#a5c956 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cdeb8e', endColorstr='#a5c956',GradientType=0 );
height: 140px;
}

.logo{
    background-image:url('../img/logox.png');
    width: 271px;
    height: 135px;
}

/* Style the top navigation bar */
.topnav {
    overflow: hidden;
    background-color: #446d32;
}

/* Style the topnav links */
.topnav a {
    float: left;
    display: block;
    color: #f2f2f2;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}

/* Change color on hover */
.topnav a:hover {
    background-color: #ddd;
    color: black;
}

/* Style the content */
.content {
    background-color: #d4dfcf;
    padding: 10px;
    /* height: 200px; Should be removed. Only for demonstration */
}

/* Style the footer */
.footer {
    background-color: #446d32;
    padding: 10px;
    color: #fff;
}
  </style>
  </head>
  <body>
   <div class="header">
    <div class="logo"></div>
</div>

<div class="topnav">
  <a href="#">Classifieds</a>
  <a href="gardenbox_landing.php">Box Logs</a>
  <?php if(!empty($manmem)){echo $manmem;} ?>
  <a href="userSettings.php">My Settings</a>
  <a href="https://www.slccgardens.com/">Main Site</a>
</div>

<div class="content">
  <h2>Your Garden Box Log</h2>
  <p>Keep track of your progress and get tips from our staff.</p>

  <div id="vitals">
    <div id="stats">
      <h3>Statistics:</h3>
      <p><label>pH</label> <input type="text" value="4.5" /> </p>
      <p><label>Conductivity:</label> <input type="text" value="12.2" /> </p>
      <p><label>Phosphorus:</label> <input type="text" value="6.7" /> </p>
      <p><label>Potassium:</label> <input type="text" value="12.3" /> </p>
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
  <textarea id="ta1" name="ta1" rows="5" cols="50"></textarea>
  <button id="cb">Add Comment</button>
  
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

  <div id="uploadpic">
  <p>Upload picture:</p>
  <input type="file" />
  </div>
  </div>

  <h2>Schematic</h2>
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ec/Garden_KR_Plan.png/296px-Garden_KR_Plan.png" />

</div>
<div class="footer">
  <p>&copy; 2018 SLCC Gardens</p>
</div>

<script>
window.onload = windowOnload;


function windowOnload(){
assignEventHandlers();
}  // end fcn windowOnload


function assignEventHandlers(){
document.getElementById("cb").onclick = addComment;
}  // end fcn assignEventHandlers


function addComment(){
// reference the text area and the log
var ta1 = document.getElementById("ta1");
var log = document.getElementById("log");
<?php if(!empty($logun)){echo $logun;}?>

// get value from text area
var v = ta1.value.trim();
if(!v){return;}

// get today's date
var D = new Date();
var da = D.getDate();
var m = D.getMonth() + 1;
var y = D.getYear();
var datestr = m + "/" + da + "/" + y;

// create a p element to contain message
var p = document.createElement("p");

// add new text to p and p to log
var t = datestr + " [" + logun + "]. " + v;
var u = t.replace(/[\n\r]+/g, "<br />");
p.innerHTML = u;
log.appendChild(p);
}  // end fcn addComment
</script>
  </body>
</html>
