<?php
require_once "../common/SLCCGAuthenticate.php";

// add member management link for administrators
$manmem = null;
if($_SESSION){
  if($_SESSION['permrole']){
    if($_SESSION['permrole'] == "Admin"){
      $manmem = "<a href=\"users.php\">Manage Members</a>";}}}
?>
<!DOCTYPE html>
<html>
<head>
<title>SLCC Gardens - Garden Box List</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">
nav * {vertical-align:middle;}
div.login {float:right; margin:1em; padding:1em; border-radius:1em;
  border:1px solid #000000; background-color:#ff3333;}
div.login a {margin:1em; padding:0.5em; color:#000000; text-decoration:none;}
div.login:hover a {background-color:#ffcccc;}
table {border-collapse:separate; border:1px solid #000000;}
tr {background-color:#ffffff;}
td {border:1px solid #000000; padding:0.5em 1em;}

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
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
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
  <h2>List of Garden Boxes</h2>
  <p>(This list is a fake list for demo)</p>
<table>
<tr>
<th>Box #</th>
<th>Location</th>
<th>UserID</th>
<th>Expires</th>
<th>Details</th>
</tr>

<tr>
<td>123</td>
<td>Jordan</td>
<td>mmiller</td>
<td>6/31/2018</td>
<td><a href="gardenbox_item.php">Details</a></td>
</tr>

<tr>
<td>456</td>
<td>Taylorsville</td>
<td>amy_s</td>
<td>7/14/2018</td>
<td><a href="gardenbox_item.php">Details</a></td>
</tr>

<tr>
<td>789</td>
<td>Taylorsville</td>
<td>ttyler</td>
<td>6/31/2018</td>
<td><a href="gardenbox_item.php">Details</a></td>
</tr>
</table>

<p>If an Administrator or Staff is logged in, all garden boxes
will be shown.<br />
If a plain User is logged in they will see only their own boxes.
</p>

<p>When this page is built for real, the bottom row of the table
will let you add a new box. <br />
And you'll be able to click a table cell
to edit it.  <br />
See the user management table for a similar concept.</p>

</div>
<div class="footer">
  <p>&copy; 2018 SLCC Gardens</p>
</div>

</body>
</html>
