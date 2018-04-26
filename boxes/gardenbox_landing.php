<?php
require_once "../common/SLCCGAuthenticate.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>SLCC Gardens - </title>
<style type="text/css">
nav * {vertical-align:middle;}
div.login {float:right; margin:1em; padding:1em; border-radius:1em;
  border:1px solid #000000; background-color:#ff3333;}
div.login a {margin:1em; padding:0.5em; color:#000000; text-decoration:none;}
div.login:hover a {background-color:#ffcccc;}
table {border-collapse:separate; border:1px solid #000000;}
td {border:1px solid #000000;}
</style>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
</head>
<body>
<nav>
  <img src="images/static/slccg.png" />
  <a href="classifieds_landing.php">Classifieds</a> 
  | <a href="gardenbox_landing.php">Garden Box Logs</a> 
  | <a href="users.php">Manage Members</a> 
  | <a href="userSettings.php">User Settings</a> 
  | <a href="https://www.slccgardens.com/">Back to Main Site</a>
</nav>
<h1>This is the garden box logs landing page.</h1>
<h2>Table of garden boxes</h2>
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
</body>
</html>
