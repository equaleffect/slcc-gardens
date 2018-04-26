<?php
require_once "../common/SLCCGAuthenticate.php";
$userInfo = array();
$err = "";
$msg = "";

// change information
if(!empty($_POST['submch'])){if($_POST['submch'] == "SubmCh"){
$msg = makeChanges($dbcxn);}}

// get user information for the currently logged in person
$userInfo = getUserInfo($dbcxn, $err);


function makeChanges($dbcxn){
$msg = "";

// make changes to everything except password
$tf = makeContactChanges($dbcxn, $msg);
if(empty($tf)){return $msg;}

// make password changes
$tf = changeUsersPassword($dbcxn, $msg2);

// return success message
$rv = "Your contact information has been updated.";
if(!empty($msg2)){$rv .= " {$msg2}";}
if(empty($tf)){$rv .= " Your password was NOT changed.";}
return $rv;
}  // end fcn makeChanges


function changeUsersPassword($dbcxn, &$msg){
// skip this section if all of the three pw fields are empty
if(empty($_POST['currpw'])){
  if(empty($_POST['newpw'])){
    if(empty($_POST['confpw'])){return true;}}}

// if you get here, at least one of the pw boxes is populated
// give an error if any is populated while any other is not
if(empty($_POST['currpw'])){
  $msg = "The current password field was blank.";
  return false;}
if(empty($_POST['newpw'])){
  $msg = "The new password field was blank.";
  return false;}
if(empty($_POST['confpw'])){
  $msg = "The confirm new password field was blank.";
  return false;}

// make sure the new and conf passwords match and are both at least 8 chars long
if($_POST['newpw'] != $_POST['confpw']){
  $msg = "The new password does not match the confirm password.";
  return false;}
if(strlen($_POST['newpw']) < 8){
  $msg = "The new password must be at least 8 characters long.";
  return false;}

// make the username SQL-safe
$un = $dbcxn->real_escape_string($_POST['username']);

// format query to get current salted hash
$q = "SELECT Password FROM Users WHERE UserName = '{$un}'";

// get current salted hash
$rs = $dbcxn->query($q);
if(!$rs){
  $dberr = $dbcxn->error;
  $s = "userSettings.php.changeUsersPassword Error: {$dberr}. Query: {$q}.";
  error_log($s);
  $msg = "Your records could not be retrieved due to a database error.";
  return false;}

// check that only a single record was returned
$nr = $rs->num_rows;
if($nr != 1){
  $s = "Username {$_POST['username']} query returned {$nr} rows.";
  error_log($s);
  $msg = "Your records could not be retrieved because of a database error.";
  return false;}

// get the expected salted hash from the database
$row = $rs->fetch_array();
$expHash = $row['Password'];

// salt and hash the new pw
$newHash = returnSaltedHash($_POST['newpw']);

// make new and old pw sql-safe
$newpw = $dbcxn->real_escape_string($newHash);
$currpw = $dbcxn->real_escape_string($expHash);

// build update query
$q = "UPDATE Users SET Password = '{$newpw}' " . 
"WHERE UserName = '{$un}' AND Password = '{$currpw}'";

// run update query
$rs = $dbcxn->query($q);
if(empty($rs)){
  $dberr = $dbcxn->error;
  $s = "userSettings.php.changeUsersPassword Error: {$dberr}. Query: {$q}.";
  error_log($s);
  $msg = "Your records could not be retrieved due to a database error.";
  return false;}

// check that only a single record was updated
$nr = $dbcxn->affected_rows;
if($nr != 1){
  $s = "Password update query for username {$_POST['username']} affected {$nr} rows.";
  error_log($s);
  $msg = "There was a database error while attempting to update your password.";
  return false;}

// update session variables and return success
$_SESSION['psswd'] = $newHash;
$msg = "Your password has been updated.";
return true;
} // end fcn changeUsersPassword


function makeContactChanges($dbcxn, &$msg){
$csv = array();
$w = array();

// make fields SQL-safe
if(!empty($_POST['username'])){
  $un = $dbcxn->real_escape_string($_POST['username']);
  $csv[] = "UserName='{$un}'";}
if(!empty($_POST['emailaddr'])){
  $em = $dbcxn->real_escape_string($_POST['emailaddr']);
  $csv[] = "Email='{$em}'";}
if(!empty($_POST['phonenum'])){
  $ph = $dbcxn->real_escape_string($_POST['phonenum']);
  $csv[] = "PhoneNumber='{$ph}'";}
else {$csv[] = "PhoneNumber=Null";}

// check for changes
if(empty($csv)){
  $msg = "There were no changes to save.";
  return false;}

// add date updated
$csv[] = "LastModifiedDate=NOW()";

// create set string
$set = implode(", ", $csv);

// create filter
$un = $dbcxn->real_escape_string($_SESSION['uname']);
$key = (int)$_SESSION['userid'];
$w[] = "UserName='{$un}'";
$w[] = "UserKey={$key}";
$where = implode(" AND ", $w);

// assemble query
$q = "UPDATE Users SET {$set} WHERE {$where} LIMIT 1";

// run query
$rs = $dbcxn->query($q);
if(empty($rs)){
 $dberr = $bdcxn->error;
  $s = "userSettings.php.makeContactChanges Error: {$dberr}. Query: {$q}.";
  error_log($s);
  $msg = "The updated was not completed due to a database error.";
  return false;}

// check for number of rows updated
$nr = $dbcxn->affected_rows;
if($nr != 1){
  $msg = "The updated was not completed due to a database error.";
  return false;}

// update session if username was changed
if(!empty($_POST['username'])){$_SESSION['uname'] = $_POST['username'];}

// if you get here all (non-password) changes were made.
// return no message to indicate success
return true;
}  // end fcn makeContactChanges


function getUserInfo($dbcxn, &$err){
// format query to get user info
$sqlun = $dbcxn->real_escape_string($_SESSION['uname']);
$q = "SELECT u.*, p.Role FROM Users u " .
"LEFT JOIN UserPermissions p " . 
"ON p.UserPermissionKey = u.UserPermissionKey " . 
"WHERE UserName = '{$sqlun}'";

// run query
$rs = $dbcxn->query($q);
if(!$rs){
  $dberr = $dbcxn->error;
  $s = "userSettings.php.getUserInfo Error: {$dberr}. Query: {$q}.";
  error_log($s);
  $err = "Settings are not available because of a database problem.";
  return false;}

// check the number of rows returned
$nr = $rs->num_rows;
if($nr != 1){
  $s = "userSettings.php.getUserInfo. Query should have " . 
  "returned 1 row but returned {$nr}. Query: {$q}.";
  error_log($s);
  $err = "Settings are not available due to a database problem.";
  return false;}

// get and return the row of information
$row = $rs->fetch_assoc();
return $row;
}  // end fcn getUserInfo

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>SLCC Gardens - Demo site</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
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
label {margin:1em 1em 1rem 1rem;}
input {display:block; margin:0.25em 1em 1.5rem 1rem;}
button[type="submit"] {margin:1rem; display:block;}
fieldset {border-style:groove; display:inline-block; margin:1rem;}

#errMsg {margin:1rem; color:#800000; font-weight:bold;}
#userMsg {margin:1rem; color:#003f8f; font-weight:bold;}

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
    /*/*height: 200px;  Should be removed. Only for demonstration */
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
  <a href="#">Link 1</a>
  <a href="#">Link 2</a>
  <a href="#">Link 3</a>
</div>

<div class="content">
  <h2>User Settings</h2>
  <p>Change your password or contact information</p>
  <hr />

<div id="errMsg">
<?php if(!empty($err)){echo $err;} ?>
</div>

<div id="userMsg">
<?php if(!empty($msg)){echo $msg;} ?>
</div>


<form id="userSettingsForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<label for="username">Username:</label>
<input type="text" reqired="required" id="username" name="username"
value="<?php if(!empty($userInfo['UserName'])){echo $userInfo['UserName'];} ?>" />

<label for="emailaddr">Email address:</label>
<input type="email" reqired="required" id="emailaddr" name="emailaddr"
value="<?php if(!empty($userInfo['Email'])){echo $userInfo['Email'];} ?>" />

<label for="phonenum">Phone number:</label>
<input type="text" id="phonenum" name="phonenum"
value="<?php if(!empty($userInfo['PhoneNumber'])){echo $userInfo['PhoneNumber'];} ?>" />

<fieldset>
<label for="currpw">Current password:</label>
<input type="password" id="currpw" name="currpw" />

<label for="newpw">New password:</label>
<input type="password" id="newpw" name="newpw" />

<label for="confpw">Confirm new password:</label>
<input type="password" id="confpw" name="confpw" />
</fieldset>

<button type="submit" id="submch" name="submch" value="SubmCh">Submit</button>
</form>

<!-- end content div -->
</div>

<div class="footer">
  <p>&copy; 2018 SLCC Gardens</p>
</div>

</body>
</html>

