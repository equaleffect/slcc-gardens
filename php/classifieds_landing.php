<?php
require_once "SLCCGAuthenticate.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>SLCC Gardens - Classifieds</title>
<style type="text/css">
html {font-size:16px; font-family:Helvetica, Arial, sans-serif;}
div.memmen {float:right;}
table {border-collapse:separate; border:1px solid #000000;}
td {border:1px solid #000000; vertical-align:top;}
td:first-child img {float:left; overflow:auto; margin:1em;}
nav * {vertical-align:middle;} 
td:last-child a {display:block; text-align:center;}
</style>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<link rel="shortcut icon" type="image/x-icon" href="images/slccgardensfave.ico" />
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
</head>
<body>

<!-- Members Menu -->
<nav>
  <img src="images/static/slccg.png" />
  <a href="classifieds_landing.php">Classifieds</a> 
  | <a href="gardenbox_landing.php">Garden Box Logs</a> 
  | <a href="manage_members.php">Manage Members</a> 
  | <a href="../index.html">Back to Main Site</a>
</nav>

<h1>Classifieds</h1>
<p>TThis would be the main dashboard used to manage and navigate classified ads.</p>

<table>
<tr>
<td>
<img src="images/classifieds/cuke.jpg"/>
All about these tasty cucumbers...
</td>
<td>
Price, contact, what-have-you
<a href="classified_item.php">Details</a>
</td>
</tr>
<tr>
<td>
<img src="images/classifieds/cuke.jpg"/>
All about these tasty cucumbers...
</td>
<td>
Price, contact, what-have-ou
<a href="classified_item.php">Details</a></td>
</tr>
<tr>
<td>
<img src="images/classifieds/cuke.jpg"/>
All about these tasty cucumbers...
</td>
<td>
Price, contact, what-have-you
<a href="classified_item.php">Details</a>
</td>
</tr>
<tr>
<td>
<img src="images/classifieds/cuke.jpg"/>
All about these tasty cucumbers...
</td>
<td>
Price, contact, what-have-you
<a href="classified_item.php">Details</a>
</td>
</tr>
</table>
</body>
</html>
