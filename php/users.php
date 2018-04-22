<?php
require_once "SLCCGAuthenticate.php";
require_once "slccgutilities.php";
$errmsg = array();
$nvp = array();
$ttrow = array();
$tf = false;
$aum = null;

// check for admin permissions
if(empty($_SESSION['permrole']) || $_SESSION['permrole'] != "Admin"){
   exit("Your role setting does not allow you to view this page.");}

// check for AJAX calls
if(isset($_POST['a'])){processAJAXCall($dbcxn);}

// distinguish regular page load vs. form submission for new user
if(!empty($_POST['addNewUser'])){
if($_POST['addNewUser'] == "addNewUser"){
  $aum = addNewUser($dbcxn);
}}

// get dropdown list of roles
$rolelist = getRoleList($dbcxn);

// get users table (uses a mysqli db cxn from SLCCGAuth)
// $tt[0] = html, $tt[1] = js, $tt[2] = user message
$tt = getUsers($dbcxn);


function getUsers($dbcxn){
global $msg;
$tableRows = array();
$s = null;
$JS = array();
$rv = array();
$dt = array();
/* returns 3 element array:
0 HTML table
1 JS USERS array
2 text for message bar*/

// open js USERS array
$JS[] = "var USERS = new Array();";

// format query to get events
$q = <<<ENdXxu
SELECT * 
FROM Users u
LEFT JOIN UserPermissions p ON p.UserPermissionKey = u.UserPermissionKey
ORDER BY UserName
ENdXxu;

// run query to get event info
$rs = $dbcxn->query($q);
if(empty($rs)){
  $dberr = $dbcxn->error;

  // log error
  $err = "Error. users.php.getUsers. Query: {$q}. Error: {$dberr}";
  error_log($err);

  // return error message for user
  $rv[2] = "The table of users is not available due to a database problem.";
  $rv[1] = implode("\n", $JS);
  $rv[0] = null;
  return $rv;
}

// check number of rows
$nr = $rs->num_rows;
if($nr < 1){
  $rv[2] = "Users (0):";
  $rv[1] = implode("\n", $JS);
  $rv[0] = null;
  return $rv;}
else {$rv[2] = "Users ({$nr}):";}

// scan the result set
while($row = $rs->fetch_array()){

// store row results as html- and js-safe strings..
// user key
if(!empty($row['UserKey'])){
  $key = htmlspecialchars($row['UserKey']);
  $jskey = addslashes($row['UserKey']);}
else {
  $key = "";
  $jskey = "";} 

// username
if(!empty($row['UserName'])){
  $un = htmlspecialchars($row['UserName']);
  $jsun = addslashes($row['UserName']);}
else {
  $un = "";
  $jsun = "";} 

// email address
if(!empty($row['Email'])){
  $em = htmlspecialchars($row['Email']);
  $jsem = addslashes($row['Email']);}
else {
  $em = "";
  $jsem = "";} 

// phone
if(!empty($row['PhoneNumber'])){
  $ph = htmlspecialchars($row['PhoneNumber']);
  $jsph = addslashes($row['PhoneNumber']);}
else {
  $ph= "";
  $jsph = "";} 

// created date
if(!empty($row['CreatedDate'])){
  $dt = explode(" ", $row['CreatedDate']);
  $cd = htmlspecialchars($dt[0]);
  $ct = htmlspecialchars($dt[1]);
  $jscd = addslashes($row['CreatedDate']);}
else {
  $cd = "";
  $ct = "";
  $jscd = "";}

// last modified date
if(!empty($row['LastModifiedDate'])){
  $dt = explode(" ", $row['LastModifiedDate']);
  $md = htmlspecialchars($dt[0]);
  $mt = htmlspecialchars($dt[1]);
  $jsmd = addslashes($row['LastModifiedDate']);}
else {
  $md = "";
  $mt = "";
  $jsmd = "";}

// archived date and activate/inactivate buttons
if(!empty($row['ArchivedDate'])){
  $ad= htmlspecialchars($row['ArchivedDate']);
  $jsad = addslashes($row['ArchivedDate']);}
else {
  $ad = "";
  $jsad = "";}

// role
if(!empty($row['Role'])){
  $role= htmlspecialchars($row['Role']);
  $jsrole = addslashes($row['Role']);}
else {
  $role = "";
  $jsrole = "";}

// open table row
// the row with the currently logged in user gets no pw reset button
if($_SESSION['uname'] == $row['UserName']){
  $tableRows[] = "<tr class=\"currus\" data-uid=\"{$key}\">";
  $rsButton = "";}
else {
  $tableRows[] = "<tr data-uid=\"{$key}\">";
  $rsButton = "<button type=\"button\" class=\"rs\">Reset</button>";}

// add data cells
$tableRows[] = <<<ENdXxu
<td>{$un}</td>
<td>{$em}</td>
<td>{$ph}</td>
<td title="{$ct}">{$cd}</td>
<td title="{$mt}">{$md}</td>
ENdXxu;

// add password reset button
$tableRows[] = "<td>{$rsButton}</td>";

// add role cell
if(!empty($row['ArchivedDate'])){
  $tableRows[] = "<td class=\"inact\" title=\"{$row['ArchivedDate']}\">Inactive</td>";}
elseif($row['Role'] == "Admin"){
  $tableRows[] = "<td class=\"admin\">Admin</td>";}
elseif($row['Role'] == "Staff"){
  $tableRows[] = "<td class=\"staff\">Staff</td>";}
else {
  $tableRows[] = "<td class=\"user\">User</td>";}

// close table row
$tableRows[] = "</tr>";

// store js
$JS[] = <<<ENd4Xxu
USERS[{$jskey}] = new Array();
USERS[{$jskey}]['UserName'] = "{$jsun}";
USERS[{$jskey}]['Email'] = "{$jsem}";
USERS[{$jskey}]['PhoneNumber'] = "{$jsph}";
USERS[{$jskey}]['CreatedDate'] = "{$jscd}";
USERS[{$jskey}]['LastModifiedDate'] = "{$jsmd}";
USERS[{$jskey}]['ArchivedDate'] = "{$jsad}";
USERS[{$jskey}]['Role'] = "{$jsrole}";
ENd4Xxu;
}  // end while

// serialize table rows
if(empty($tableRows)){$rv[0] = null;} 
else {$rv[0] = implode("\n", $tableRows);}

// serialize js
if(empty($JS)){$rv[1] = null;}
else {$rv[1] = implode("\n", $JS);}

// return all parts
return $rv;
}  // end fcn getUsersTable


function addNewUser($dbcxn){
global $errmsg;

// check parameter morphology
$tf = validateUserMorphology($dbcxn);
if(empty($tf)){return;}

// validate business rules
$tf = validateUserBusinessRules($dbcxn);
if(empty($tf)){return;}

// create insert query
$q = createNewUserInsertQuery($dbcxn);

// run insert query
$rs = $dbcxn->query($q);
if(empty($rs)){
  $err = $dbcxn->error;
  $err = htmlspecialchars($err);
  $hq = htmlspecialchars($q);
  $errmsg[] = "There was a database error while adding the " . 
  "user. Error: {$err}. Query: {$hq}.";
  return;}

// check number of affected rows
$nr = $dbcxn->affected_rows;
if($nr != 1){
  $errmsg[] = "There was a database error while adding the " .
  "user. Number of affected rows: {$nr}.";
  return;}

$rv = "New user {$_POST['uname']} ({$_POST['emaddr']}) has been added.";
return $rv;
}  // addNewUser


function processAJAXCall($dbcxn){
global $errmsg;
$aviso = array();

// validate row and field to validate
$tf = validateEditParams($dbcxn);
if(empty($tf)){exitWithJSONStr($errmsg, null, null, null, null);}

// validate the new value
$tf = validateNewValue($errmsg, $dbcxn);
if(empty($tf)){exitWithJSONStr($errmsg, null, null, null, null);} 

// update the volunteers table in the db
$tf = updateUsersTable($errmsg, $aviso, $dbcxn);
if(empty($tf)){exitWithJSONStr($errmsg, null, null, null, null);}

// make and return array of extended values
$nvp = getExtendedValues($errmsg, $dbcxn);
exitWithJSONStr($errmsg, null, $aviso, null, $nvp);
}  // end fcn processAJAXCall


function createNewUserInsertQuery($dbcxn){
$set = array();

// make fields SQL-safe
if(!empty($_POST['uname'])){
  $s = $dbcxn->real_escape_string($_POST['uname']);
  $set[] = "UserName = '{$s}'";}
if(!empty($_POST['emaddr'])){
  $s = $dbcxn->real_escape_string($_POST['emaddr']);
  $set[] = "Email = '{$s}'";}
if(!empty($_POST['phone'])){
  $s = $dbcxn->real_escape_string($_POST['phone']);
  $set[] = "PhoneNumber = '{$s}'";}
if(!empty($_POST['role'])){
  $s = $dbcxn->real_escape_string($_POST['role']);
  $set[] = "UserPermissionKey = {$s}";}

// add date/time created
$set[] = "CreatedDate = NOW()";

// encrypt password
// $hash = password_hash($_POST['psswd'], PASSWORD_DEFAULT);
$salt = getRandomPassword();
$nacl = preg_replace("/[^A-Za-z]/", "", $salt);
$hash = crypt($_POST['psswd'], $nacl);
$s = $dbcxn->real_escape_string($hash);
$set[] = "Password = '{$s}'";

// concatenate nvp
$csv = implode(",", $set);

// make and return query
$q = "INSERT INTO Users SET {$csv}";
return $q;
}  // end fcn createNewUserInsertQuery


function validateUserBusinessRules($dbcxn){
global $errmsg;
$rv = true;

// role code cannot be zero
if(empty($_POST['role'])){
  $errmsg[] = "A role must be selected.";
  return false;}

// make username SQL-safe and lower cae
$un = $dbcxn->real_escape_string(strtolower($_POST['uname']));

// format query to look for duplicate username or email address
$q = "SELECT * FROM Users WHERE Lower(UserName) = '{$un}'";

// run the query
$rs = $dbcxn->query($q);
if(empty($rs)){return true;}

// check number of rows
$nr = $rs->num_rows;
if(empty($nr)){return true;}

// get results
while($row = $rs->fetch_array()){

// check for duplicate username
if(!empty($row['UserName'])){
  if(strtolower($row['UserName']) == strtolower($_POST['uname'])){
  $s = htmlspecialchars($_POST['uname']);
  $errmsg[] = "The new user could not be saved because the " .
  "username ({$s}) is already in use.";
  return false;}}
}

// return error (this is a backup since function should have 
// already returned if query returned no rows
return false;
}  // end fcn validateBusinessRules


function validateUserMorphology($dbcxn){
global $errmsg;
$PV = new POSTValidator();
$tf = true;

// validate username morphology
$PV->reset($_POST['uname'], "Username");
$s = $PV->validateString(true, 1, 255, null);
if($s){
  $errmsg[] = $s;
  $tf = false;}

// validate email address morphology
$PV->reset($_POST['emaddr'], "Email Address");
$s = $PV->validateEmail(true, 5, 255);
if($s){
  $errmsg[] = $s;
  $tf = false;}

// if a phone number is provided, clean it then validate it
if(!empty($_POST['phone'])){
  // clean phone number
  $_POST['phone'] = cleanPhone($_POST['phone']);

  // validate phone number
  $PV->reset($_POST['phone'], "Phone Number");
  $s = $PV->validatePhone(false, 7, 255);
  if($s){
  $errmsg[] = $s;
  $tf = false;}
}

// validate password
$PV->reset($_POST['psswd'], "Password");
$s = $PV->validateString(true, 8, null, null);
if($s){
  $errmsg[] = $s;
  $tf = false;}

// validate role
$PV->reset($_POST['role'], "Role");
$s = $PV->validateInt(true, null, null, true);
if($s){
  $errmsg[] = $s;
  $tf = false;}

// all checks passed
return $tf;
}  // end fcn validateUserMorphology


function validateEditParams($dbcxn){
global $errmsg;

// check that acn code is present
if(!isset($_POST['a'])){
  $errmsg[] = "Error 1. Edit user failed due to invalid " .
  "change code.";
  return false;}

// check that acn code is numeric
if(!is_numeric($_POST['a'])){
  $errmsg[] = "Error 2. Edit user failed due to invalid " .
  "change code.";
  return false;}

// check that acn is in range
if($_POST['a'] < 0){
  $errmsg[] = "Error 3. Edit user failed due to invalid change code.";
  return false;}
elseif($_POST['a'] > 7){
  $errmsg[] = "Error 4. Edit user failed due to invalid change code.";
  return false;}

// check that usid is present
if(empty($_POST['b'])){
  $errmsg[] = "Error 5. The user to edit could not be determined.";
  return false;}

// check that usid is numeric
if(!is_numeric($_POST['b'])){
  $errmsg[] = "Error 6. The user to edit is not valid.";
  return false;}

// for password change, make sure username and email address were sent
if($_POST['a'] == 5){
  if(empty($_POST['c'])){
    $errmsg[] = "Error 39. The email address is missing.";
    return false;}
  if(empty($_POST['d'])){
    $errmsg[] = "Error 40. The username is missing.";
    return false;}}

// make sure usid is SQL-safe (JIC)
$acn = $_POST['a'];
$usid = $dbcxn->real_escape_string($_POST['b']);

// format query to make sure usid exists
$q = <<<ENdXxu
SELECT * 
FROM Users u 
LEFT JOIN UserPermissions p ON p.UserPermissionKey = u.UserPermissionKey
WHERE UserKey = {$usid}
ENdXxu;

// run query
$rs = $dbcxn->query($q);
if(empty($rs)){
  $dberr = $dbcxn->error;
  $errmsg[] = "Error 8. The user could not be edited because " .
  "of a database error. Error: {$dberr}. Query: {$q}.";
  return false;}

// make sure a single row was returned
$nr = $rs->num_rows;
if($nr != 1){
  $errmsg[] = "Error 9. The user to edit could not retrieved " .
  "from the database.";
  return false;}

// get row values
$row = $rs->fetch_array();

// all checks passed
return true;
}  // end fcn validateEditParams


function validateNewValue(&$err, $dbcxn){
$acn = (int)($_POST['a']);
$PV = new POSTValidator();
$s = "";

// validate morphology
// username
if($acn == 0){
  $PV->reset($_POST['c'], "Username");
  $s = $PV->validateString(true, 1, 255, null);}

// email address
elseif($acn == 1){
  $PV->reset($_POST['c'], "Email Address");
  $s = $PV->validateEmail(true, 5, 255);}

// phone number
elseif($acn == 2){
  $PV->reset($_POST['c'], "Phone");
  $s = $PV->validatePhone(false, 7, 255);}

elseif($acn == 3){
  $PV->reset($_POST['c'], "Username");
  $s = $PV->validateString(true, 1, 150, null);}

elseif($acn == 5){
  // validate email address morphology
  $PV->reset($_POST['c'], "Email Address");
  $s = $PV->validateEmail(true, 5, 255);

  // validate username morphology
  $PV->reset($_POST['d'], "Username");
  $s = $PV->validateString(true, 1, 150, null);}

elseif($acn == 6){
  $PV->reset($_POST['c'], "Role");
  $s = $PV->validateString(true, 4, 12, null);}

elseif($acn == 7){
  // inactivate acn.  No further validation needed
  $s = null;}

// acn not found
else {
  $err[] = "Error 10. The update code failed validation.";
  return false;}

// return error if morphology failed validation
if(!empty($s)){
  $err[] = $s;
  return false;}

// For username update, make sure new value is unique..
// Otherwise, exit this function.
if($acn == 0){

// format query to find duplicates
$s = $dbcxn->real_escape_string($_POST['c']);
$q = "SELECT * FROM Users " . 
     "WHERE LCASE(UserName) = LCASE('{$s}')";

// run query to find duplicates
$rs = $dbcxn->query($q);
if(empty($rs)){
$dberr = $dbcxn->error;
$err[] = "Error 11. This edit could not be completed because of " .
"a database error. Error: {$dberr}. Query: {$q}.";
return false;}

// check that number of rows returned is zero
$nr = $rs->num_rows;
if(!empty($nr)){
  $s = htmlspecialchars($_POST['c']);
  $err[] = "Error 12. This edit could not be completed because " .
  "a {$n} of {$s} is already in use by another user.";
  return false;}}

// all checks passed
return true;
}  // end fcn validateNewValue


function updateUsersTable(&$err, &$aviso, $dbcxn){

// get acn, userid, and new value and make them all SQL-safe
$acn = (int)($_POST['a']);
$usid = (int)($_POST['b']);
$nv = $dbcxn->real_escape_string($_POST['c']);

// determine fields to update
if($acn == 0){
  $f = "UserName";
  $v = "'{$nv}'";}
elseif($acn == 1){
  $f = "Email";
  $v = "'{$nv}'";}
elseif($acn == 2){
  $f = "PhoneNumber";
  $v = "'{$nv}'";}

// reset password is shuttled through the changePw function 
// (from AdobeAuthenticate)
elseif($acn == 5){
  // make sure an administrator is logged in
  if($_SESSION['permrole'] != "Admin"){
    $err[] = "You do not have the necessary permissions to change a user's password.";
    return false;}
  
  // run pw change query
  $tf = changePw($usid, $_POST['d'], $_POST['c'], $dbcxn, $pwmsg);
  if(empty($tf)){
    $err[] = "Error 36: {$pwmsg}";
    return false;}
  $aviso[] = $pwmsg;
  return true;}

// change role
elseif($acn == 6){$v = "'{$nv}'";}

// inactivate user
elseif($acn == 7){$v = "NOW()";}

// action code not found
else {
  $err[] = "Error 13. The update code is not valid.";
  return false;}

// format query
if($acn == 6){
  $q = "UPDATE Users SET UserPermissionKey = " . 
  "(SELECT UserPermissionKey FROM UserPermissions " . 
  "WHERE Role = {$v}), " . 
  "LastModifiedDate = NOW(), ArchivedDate = Null " . 
  "WHERE UserKey = {$usid} LIMIT 1";}
elseif($acn == 7){
  $q = "UPDATE Users SET ArchivedDate = {$v}, " . 
  "LastModifiedDate = {$v} WHERE UserKey = {$usid} LIMIT 1";}
else {
  $q = "UPDATE Users SET {$f} = {$v}, LastModifiedDate = NOW() " .
  "WHERE UserKey = {$usid} LIMIT 1";}

// run query
$rs = $dbcxn->query($q);
if(empty($rs)){
  $dberr = $dbcxn->error;
  $err[] = "Error 14. The update was not completed due to a " . 
  "database error. Error: {$dberr}. Query: {$q}.";
  return false;}

// check number of rows updated
$nr = $dbcxn->affected_rows;
if(empty($nr)){
  $err[] = "Error 14: No database rows were updated.";
  return false;}
elseif($nr != 1){  
  $err[] = "Error 15: {$nr} database rows were updated.";
  return false;}

// if current username was updated, update the session
if($acn == 0){
  if($usid == $_SESSION['userid']){
  $_SESSION['uname'] = $_POST['c'];}}

// if current role was update, update the session role
elseif($acn == 6){
  if($usid == $_SESSION['userid']){
  $_SESSION['permrole'] = $_POST['c'];}}

// if current status was inactivated, clear session variables
// related to the current login
elseif($acn == 7){
  if($usid == $_SESSION['userid']){
  clearSessionPermissions();}}

// all checks passed
return true;
}  // end fcn updateUsersTable


function getExtendedValues(&$err, $dbcxn){
$nvp = array();

// get acn & usid, make usid SQL-safe
$acn = (int)($_POST['a']);
$usid = (int)($_POST['b']);
$qusid = $dbcxn->real_escape_string($usid);

// format query to get database row
$q = "SELECT * FROM Users u LEFT JOIN UserPermissions p " .
"ON p.UserPermissionKey = u.UserPermissionKey " . 
"WHERE UserKey = {$qusid}";

// run query
$rs = $dbcxn->query($q);
if(empty($rs)){
  $dberr = $dbcxn->error;
  $err[] = "Error 16. Unable to verify that change was made " . 
  "properly. Error: {$dberr}. Query: {$q}.";
  return false;}

// validate number of rows retrieved
$nr = $rs->num_rows;
if($nr != 1){
  $err[] = "Error 17. Unable to verify that change was made " .
  "property. Number of rows ({$nr}) changed is not correct.";
  return;}

// get the values
$row = $rs->fetch_array();

// add ancillary values and return the array
$row['usid'] = $usid;
$row['acn'] = $acn;
$row['nv'] = $_POST['c'];
return $row;
}  // end fcn getExtendedValues


function getRoleList($dbcxn){
$opt = array();

// format query
$q = "SELECT * FROM UserPermissions ORDER BY Role";

// run query
$rs = $dbcxn->query($q);
if(empty($rs)){
  $err = $dbcxn->error;
  $s = "users.php.getRoleList error: {$err}. Query: {$q}.";
  error_log($s);
  $s = "<option>Database error</option>";
  return $s;}

// scan result set, make option elements
while($row = $rs->fetch_array()){
  $opt[] = "<option value=\"{$row['UserPermissionKey']}\">{$row['Role']}</option>";
} // end while

// join and return option list
$rv = implode("\n", $opt);
return $rv;
}  // end fcn getRoleList


function changePw($userid, $username, $email, $dbcxn, &$pwmsg){
// make sure user has admin privs
if($_SESSION['permrole'] != "Admin"){
  $pwmsg = "Password was not changed. You do not have the required privileges.";
  return false;}

// get a new password
$newpw = getRandomPassword();
$salt = getRandomPassword();
$nacl = preg_replace("/[^A-Za-z]/", "", $salt);
// $pwhash = password_hash($newpw, PASSWORD_DEFAULT);
$pwhash = crypt($newpw, $nacl);

// make password, username and email SQL-safe
$un = $dbcxn->real_escape_string($username);
$em = $dbcxn->real_escape_string($email);
$pw = $dbcxn->real_escape_string($pwhash);

// construct query
$q = "UPDATE Users SET Password = '{$pw}', LastModifiedDate = NOW() " . 
"WHERE UserKey = $userid AND UserName = '{$un}' AND Email = '{$em}' LIMIT 1";

// run query
$rs = $dbcxn->query($q);
if(empty($rs)){
  $dberr = $mysql->error;
  $pwmsg = "Password was not changed due to a database error: {$dberr}.";
  return false;}

// check that exactly 1 row was affected
$nr = $dbcxn->affected_rows;
if($nr != 1){
  $pwmsg = "Password was not changed due to a database error.  Affected rows: {$nr}.";
  return false;}

// return success
$pwmsg = "The password for {$username} has been changed to:\n\n" . 
"{$newpw}\n\nInform them by email at {$email}.";
return true;
}  // end fcn changePw


?>
<!DOCTYPE html>
<html>
<head>
<title>SLCC Gardens - User Accounts</title>
<style type="text/css">
html {margin:0; padding:0; font-family:"Franklin Gothic Medium", 
  Arial, sans-serif; font-size:10pt; width:100%;}
body {margin:0; padding:0; font-size:10pt; min-width:100%;}

/* popup for new text */
#getNewText {position:absolute; border:1px solid #54664f; 
  box-shadow:2px 2px 3px #808080; padding:0.25em; color:#0a0400;
  background-image:linear-gradient(135deg, #fdf5e6, #fffaf0); 
  display:none; min-width:20em;}
#getNewText img {float:left; margin:0 1em 1em 0.5em;}
#gntTitle {background-image:linear-gradient(90deg, #54664f, #5f8040, #ba5000);
  color:#ffffff; margin:0.25em 0.25em 1em 0.25em; font-weight:bold; 
  padding:0.25em 1em; text-shadow:1px 1px #000000; 
  border:1px solid #001008;}
#gntPrompt {margin:1.5em 0.5em 1em 1em; color:#0a0400;}
#gntInput {box-sizing:border-box; width:100%; margin:1em 0;}
#getNewText div.gntButtons {text-align:center; margin:1em 0;}
#getNewText div.gntButtons button {margin:0 1em;}

#uta {padding:0; margin:1em; border-collapse:separate; 
     border-spacing:1px; border:solid 1px #000000;}
#uta tr.currus td {font-weight:bold;}
#uta tr:nth-of-type(5n+4){background-color:#f5f0af;}
#uta tr:nth-of-type(5n+5){background-color:#f5f0af;}
#uta th {background-color:#1d403d; color:#f8f8f8; padding:0.1em 0.3em; 
     font-weight:bold; border:solid 1px #222222; text-align:center;}
#uta th.ux {background-color:#800000; text-align:center;}
#uta td {padding:0 0.1em 0 0.1em; border:solid 1px #333333; 
     text-align:left;}
#uta td.ux a {color:#800000; text-align:center;}
#uta td:nth-of-type(1) {text-align:left;}
#uta td:nth-of-type(2) {text-align:left;}
#uta td:nth-of-type(3) {text-align:left;}
#uta td:nth-of-type(4) {text-align:center;}
#uta td:nth-of-type(5) {text-align:center;}
#uta td:nth-of-type(6) {text-align:center;}
#uta td:nth-of-type(7) {text-align:center; padding:0.1em 0.25em;
  font-weight:bold;}
#uta tr:nth-last-child(1) {background-color:#dfdfdf;}

/* last table cell shows roles */
#uta td.inact {background-color:#333333; color:#ffffff;}
#uta td.user {background-color:#662020; color:#ffffff;}
#uta td.staff {background-color:#242466; color:#ffffff;}
#uta td.admin {background-color:#b37d12; color:#ffffff;}

#uta button.rs {border:1px solid #4682b4; border-radius:5px; 
     color:#ffffff; background-color:#4682b4; margin:2px; 
     padding:0.25em 0.5em; font-weight:bold; cursor:pointer;}
#uta button.rs:hover {background-color:#f0f0f0; color:#4682b4;}

#ufn {width:8em;}
#uln {width:10em;}
#uem {width:15em;}
#uun {width:8em;}
#upw {width:8em;}

#addNewUser {border:1px solid #b8860b; border-radius:5px; 
     color:#ffffff; background-color:#b8860b; margin:0 0 0 1em; 
     padding:0.25em 0.75em; font-weight:bold; cursor:pointer;}
#addNewUser:hover {background-color:#f0f0f0; color:#b8860b;}

/* change role/inactivate popup */
#changerole {position:absolute; box-shadow:2px 2px 2px 1px #808080;
  padding:0; border:1px; display:none;}
#changerole div {margin:0; padding:0.5em 1em; border:1px solid #000000;
  color:#000000; text-align:center;}
#changerole div:hover {text-shadow:0 0 8px #ffffff; text-align:center;
  box-shadow:inset 0 0 4px #ffffff;}
#role1 {background-color:#ccaa66;}
#role2 {background-color:#3d3d66;}
#role3 {background-color:#945959;}
#role4 {background-color:#666666;}

/* messaging div */
#messaging {font-weight:bold;}
#messaging div.err {margin:0; padding:0; color:#660000;}
#messaging p {margin:1em 0 0.25em 1em;}
#messaging ul {margin:0 0 1em 0;}
																
</style>
<link rel="shortcut icon" type="image/x-icon" href="../img/stafficon.ico" />
</head>

<body>
<!-- change role popup -->
<div id="changerole">
  <div id="role1">Admin</div>
  <div id="role2">Staff</div>
  <div id="role3">User</div>
  <div id="role4">Inactivate</div>
</div>

<!-- new text value popup -->
<div id="getNewText">
<div id="gntTitle"></div>
<img src="../img/slccgsm.png" />
<p id="gntPrompt"></p>
<input id="gntInput" type="text" />
<div class="gntButtons">
<button type="button" id="gntOk">OK</button>
<button type="button" id="gntCa">Cancel</button>
</div>
</div>

<!-- users table -->
<form id="nuevo" name="nuevo" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<!-- messages above users table -->
<div id="messaging">
<?php 
if(!empty($errmsg)){
  $s = wrapArray($errmsg, "ul", "Errors:");
  echo "<div class=\"err\">$s</div>";}
if(!empty($aum)){echo "<p>{$aum}</p>";}
if(!empty($tt[2])){echo "<p>{$tt[2]}</p>";} 
?>
</div>

<!-- users table -->
<table id="uta" name="uta">
<tr>
<th>UserName</th>
<th>Email address</th>
<th>Phone</th>
<th>Created</th>
<th>Modified</th>
<th>Password</th>
<th>Role</th>
</tr>

<!-- table data -->
<?php if(!empty($tt[0])){echo $tt[0];} ?>

<!-- add new user row -->
<tr id="newu">
<td>
<input id="uname" name="uname" type="text" placeholder="Username" maxlength="255" />
</td>
<td>
<input id="emaddr" name="emaddr" type="text" placeholder="Email" maxlength="255" />
</td>
<td>
<input id="phone" name="phone" type="text" placeholder="Phone" maxlength="255" />
</td>
<td>
(Date Created)
</td>
<td>
(Date Modified)
</td>
<td>
<input id="psswd" name="psswd" type="password" placeholder="Password" maxlength="255" />
</td>
<td>
<select id="role" name="role">
  <option value="0">Select a role...</option>
  <?php if(!empty($rolelist)){echo $rolelist;} ?>
</select>
</td>
</tr>
</table>

<!-- add new user button -->
<button id="addNewUser" type="button">Add</button>
<input type="hidden" name="addNewUser" value="addNewUser" />

</form>
<script type="text/javascript" src="../js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../js/slccgutilities.js"></script>
<script type="text/javascript">
<?php if(!empty($tt[1])){echo $tt[1];} ?>
window.onload = windowOnload;
var VM = new ValidationManager();


function windowOnload(){
assignEventHandlers();
}  // end fcn windowOnload


function assignEventHandlers(){

// edit cell event handlers
assignEditCellHandlers();

// others
document.getElementById("addNewUser").onclick = addNewUser;
}  // end fcn assignEventHandlers


function addNewUser(){
// validate input morphology
trimAllInput();
var tf = validateUserMorphology();
if(!tf){return;}

// validate input business rules
var tf = validateUserBusinessRules();
if(!tf){return;}

// submit form
var f = document.getElementById("nuevo");
f.submit();
}  // end fcn addNewUser


function validateUserBusinessRules(){

// reference username element
var g = document.getElementById("uname");
if(!g){
alert("The new user could not be added because " + 
"of a problem on this web page. Refresh your browser and " + 
"try again.");
return false;}

// get the new username and email address
var un = g.value;
un = un.trim();

// make sure username is unique
var k = null;
for(k in USERS){

  // check for duplicate username
  if(un.toLowerCase() == USERS[k]['UserName'].toLowerCase()){
  alert("The username '" + un + "' is already " + 
  "in use and cannot be used for a new user.");
  return false;}
}  // end for

// make sure a role has been selected
g = document.getElementById("role");
if(!g){
  alert("A role must be selected.");
  return false;}
if(!g.value){
  alert("A role must be selected.");
  g.focus();
  return false;}
if(g.value == "0"){
  alert("A role must be selected.");
  g.focus();
  return false;}

// checks passed
return true;
}  // end fcn validateUserBusinessRules


function validateUserMorphology(){
alert("FYI: Client side validation needs to be written.");

// validate username
// validate email address
// validate phone number
// validate password
// all checks passed
return true;
}  // end fcn vaidateUserMorphology


function assignEditCellHandlers(){
// reference event table and rows
var T = document.getElementById("uta");
if(!T){return;}
var trnl = T.rows;
if(!trnl){return;}
var k = trnl.length;
if(k < 2){return;}

// initialize 
var tr = null;
var i = 0;
var j = 0;
var usid = null;
var cnl = null;
var L = 0;
var anl = null;
var n = 0;
var cba = null;
var cbb = null;

// scan table rows
for(i = 0; i < k; i++){
tr = trnl[i];
usid = tr.getAttribute("data-uid");
if(!usid){continue;}

// reference node list of row cells
cnl = tr.cells;
if(!cnl){continue;}
L = cnl.length;
if(!L){return;}

// assign an event handler to each cell...

// enable editing of the username
cnl[0].onclick = returnEditTextHandler(usid, cnl[0], 0, true, 1, 255, "Username");
cnl[0].style.cursor = "pointer";

// enable editing of the email address
cnl[1].onclick = returnEditTextHandler(usid, cnl[1], 1, true, 5, 255, "Email Address");
cnl[1].style.cursor = "pointer";

// enable editing of the phone number
cnl[2].onclick = returnEditTextHandler(usid, cnl[2], 2, false, 7, 255, "Phone Number");
cnl[2].style.cursor = "pointer";

// assign event handler for password reset
var bnl = cnl[5].getElementsByTagName("button");
if(!bnl){continue;}
n = bnl.length;
if(n == 1){
if(bnl[0].className == "rs"){bnl[0].onclick = returnPwResetHandler(cnl[5], tr, usid);}}

// enable editing of the role
cnl[6].onclick = returnEditRoleHandler(usid, cnl[6]);
cnl[6].style.cursor = "pointer";
}  // next i
}  // end fcn assignEditCellHandlers


function editText(evt, usid, td, acn, reqtf, minL, maxL, fiena){
var tibar = "";
var msg = "";
var s = "";
var tf = false;

// style the cell that was clicked
tf = styleCell(td, true);
if(!tf){
  msg = "The " + fiena + " cannot be edited while another edit " + 
  "is in progress.";
  alert(msg);
  return;}

// validate usid...
// alert if usid is not available
if(!usid){
  msg = "The " + fiena + " cannot be edited due to a web page " + 
  "error.\n\nRefresh your browser and try again.";
  alert(msg);
  tf = styleCell(td, false);
  return;}

// alert if usid is not a number
if(isNaN(usid)){
  msg = "The " + fiena + " cannot be edited due to a web page " + 
  "error.\n\nPlease refresh your browser and try again.";
  alert(msg);
  tf = styleCell(td, false);
  return;}

// get the field name for action code
var fina = acnToFieldName(acn);
if(!fina){
  tf = styleCell(td, false);
  return;}

// get the current value
var ti = USERS[usid][fina];
if(!ti){ti = "";}

// make ok and cancel callback functions
var cbca = function(){abortEdit(td, this);}
var cbok = function(){submitNewText(evt, usid, td, acn, reqtf, minL, maxL, fiena, this);}

// set up the new value popup
var tibar = "Enter New " + fiena;
var msg = "Enter a new " + fiena + ".";
document.getElementById("gntTitle").textContent = tibar;
document.getElementById("gntPrompt").textContent = msg;
document.getElementById("gntOk").onclick = cbok;
document.getElementById("gntCa").onclick = cbca;
document.getElementById("gntInput").value = ti;

// show the new value popup
var PUAC = new PopupAtCursor();
var g = document.getElementById("getNewText");
PUAC.positionElement(evt, g);
$("#getNewText").show(300);
}  // end fcn editText


function editRole(evt, usid, td){
var tibar = "";
var msg = "";
var s = "";
var tf = false;

// style the cell that was clicked
tf = styleCell(td, true);
if(!tf){
  msg = "The Role cannot be edited while another edit " + 
  "is in progress.";
  alert(msg);
  return;}

// validate usid...
// alert if usid is not available
if(!usid){
  msg = "The Role cannot be edited due to a web page " + 
  "error.\n\nRefresh your browser and try again.";
  alert(msg);
  tf = styleCell(td, false);
  return;}

// alert if usid is not a number
if(isNaN(usid)){
  msg = "The Role cannot be edited due to a web page " + 
  "error.\n\nPlease refresh your browser and try again.";
  alert(msg);
  tf = styleCell(td, false);
  return;}

// set up callback functions
var f = function(e){submitNewRole(e, usid, td, this);}
document.getElementById("role1").onclick = f;
document.getElementById("role2").onclick = f;
document.getElementById("role3").onclick = f;
document.getElementById("role4").onclick = f;

// show the new role popup
var PUAC = new PopupAtCursor();
var g = document.getElementById("changerole");
PUAC.positionElement(evt, g);
$(g).show(300, focusInput);
}  // end fcn editRole


function focusInput(){
var g = document.getElementById("gntInput");
g.focus();
}  // end fcn focusInput


function formatAJAXPost(acn, usid, td, newtext, vard){
var a = encodeURIComponent(acn);
var b = encodeURIComponent(usid);
var c = encodeURIComponent(newtext);
var poststr = "a=" + acn + "&b=" + b + "&c=" + newtext;

// add d param if sent
var tov = typeof vard;
if(tov != "undefined"){if(tov != "null"){
var d = encodeURIComponent(vard);
var s = "&d=" + d;
poststr += s;}}

// post request
sendAJAXEditRequest(td, poststr);
}  // end fcn formatAJAXPost


function submitNewText(evt, usid, td, acn, reqtf, minL, maxL, fiena, button){
var tf = false;

// reference the input dialog
var ipdialog = button.parentNode.parentNode;

// get the new text 
var newtext = document.getElementById("gntInput").value;
newtext = newtext.trim();
if(!newtext){if(reqtf){
  alert("A value for " + fiena + " is required.");
  return;}}

// strip any newlines from new text
newtext = newtext.replace(/\n/g, "");

// get and clean old text
var fina = acnToFieldName(acn);
var oldtextUSERS = USERS[usid][fina];
oldtext = oldtextUSERS.toString();
oldtext = oldtext.replace(/\n/g, "");
oldtext = oldtext.trim();

// check that text has changed
if(oldtext == newtext){
  ipdialog.style.display = "none";
  tf = styleCell(td, false);
  return;}

// validate morphology of new text
var FV = new FieldValidator(null, "value", fiena, newtext);
tf = FV.validateString(reqtf, minL, maxL, false);
if(!tf){return;}

// make sure username is not re-used
if(acn == 0){
for(var k in USERS){
if(k != usid){
if(USERS[k][fina] == newtext){
  alert("A " + fiena + " of " + newtext + " is already in use " + 
  "and cannot be used for a second account.");
  return;}}}}

// make sure email address is valid
if(acn == 1){
  FV.reset(null, "value", "Email Address", newtext);
  tf = FV.validateEmail(true, 5, 255, false);
  if(!tf){return;}}

// make sure phone number is valid
if(acn == 2){
  // clean the phone number
  newtext = cleanPhone(newtext);
  FV.reset(null, "value", "Phone Number", newtext);
  tf = FV.validateString(false, 7, 255, false);
  if(!tf){return;}}

// all checks have passed. Hide dialog
ipdialog.style.display = "none";

// create poststr and send AJAX request
formatAJAXPost(acn, usid, td, newtext, oldtextUSERS)
}  // end fcn submitNewText


function submitNewRole(evt, usid, td, div){
var tf = false;

// get the current value
if(USERS[usid]['ArchivedDate']){var roleo = "Inactive";}
else if(USERS[usid]['Role']){var roleo = USERS[usid]['Role'];}
else {roleo = "";}

// get the new role
divid = div.id;
if(divid == "role1"){
  var rolef = "Admin";
  var acn = 6;}
else if(divid == "role2"){
  var rolef = "Staff";
  var acn = 6}
else if(divid == "role3"){
  var rolef = "User";
  var acn = 6;}
else if(divid == "role4"){
  var acn = 7;
  var rolef = "Inactive";}
else {
  alert("The web page has an error. Refresh your browser and try again.");
  tf = styleCell(td, false);
  return;}

// check that text has changed
if(roleo == rolef){
  div.parentNode.style.display = "none";
  tf = styleCell(td, false);
  return;}

// all checks have passed. Hide pick list
div.parentNode.style.display = "none";

// create poststr and send AJAX request
formatAJAXPost(acn, usid, td, rolef);
}  // end fcn submitNewRole


function styleCell(td, tf){
if(tf){
  // check for edit in progress
  if(styleCell.td){return false;}

  // store table cell and original background color
  var cs = window.getComputedStyle(td, null);
  var bc = cs.getPropertyValue("background-color");
  styleCell.bc = bc;
  styleCell.td = td;

  // highlight cell
  td.style.outline = "3px solid #ba5000";
  td.style.backgroundColor = "#e2eecd";}

// restore original style
else {
  styleCell.td = null;
  td.style.outline = "none";
  td.style.backgroundColor = styleCell.bc;
  styleCell.bc = null;}

// return success
return true;
}  // end fcn styleCell


function getCellEid(td){
var tr = td.parentNode;
var eid = tr.getAttribute("data-eid");
return eid;
}  // end fcn getCellEid


function sendAJAXEditRequest(td, poststr){
// create HTMHttpRequest object
var xreq = null;
try {xreq = new XMLHttpRequest();}
catch(e){
  try {xreq = new ActiveXObject("MSXML2.XMLHTTP");}
  catch(e){xreq = new ActiveXObject("Microsoft.XMLHTTP");}}
if(!xreq){
  alert("The table cell cannot be edited due to a web " +
  "page error.\n\nPlease refresh your browser and try again.");
  tf = styleCell(td, false);
  return;}

// configure request
xreq.open("POST", "<?php if(!empty($_SERVER['PHP_SELF'])){echo $_SERVER['PHP_SELF'];} ?>");
xreq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xreq.onreadystatechange = function(){processAJAXEditResponse(td, poststr, xreq);}
xreq.send(poststr);
}  // end fcn sendAJAXEditRequest


function processAJAXEditResponse(td, poststr, xreq){
if(xreq.readyState != 4){return;}
if(xreq.status != 200){return;}
var s = "";
var t = "";
var u = "";
var tf = null;
var rv = false;

// get return text and convert to an object
// returnText is populated from Census view (and a few augmented
// elements)
var t = xreq.responseText;
alert("This is responseText: " + t);

if(!t){
  alert("Error 18. The edit could not be completed because of " + 
  "a web page error.\n\nPlease refresh your browser and try again.");
  tf = styleCell(td, false);
  return;}

// parse JSON
try {var ro = JSON.parse(t);}
catch(e){
  ro = null;
  // alert("JSON.parse error. This is responseText: " + t);
  }

// validate result object
if(!ro){
  alert("Error 19. The edit could not be completed because of a " + 
  "web page error.\n\nPlease refresh your browser and try again.");
  tf = styleCell(td, false);
  return;}

// check for error
if(ro.err){
  s = arrayToNumberedList(ro.err);
  t = s + "\n\nThe update was not completed.";
  alert(t);
  tf = styleCell(td, false);
  return;}

// check for warning...
else if(ro.warn){
  var tibar = "Update Warning:\n\n";
  s = arrayToNumberedList(ro.warn);
  t = vibar + s + "\n\nIgnore this check and continue?";
  var rv = confirm(t);
  
  // if ignoring warning
  if(rv){
    poststr += "&e=86";
    sendAJAXEditRequest(td, poststr);
    return;}
  else {
    tf = styleCell(td, false);
    return;}}

// check for user message
else if(ro.note){
  s = arrayToNumberedList(ro.note);
  t = "OK. The update was completed with the following " + 
  "notice(s):\n\n" + s;
  alert(t);}

// process return
tf = updateUSERSArray(ro);
if(tf){updateUsersDisplay(ro, td);}
tf = styleCell(td, false);
}  // processAJAXEditResponse


function abortEdit(td, button){
var tf = styleCell(td, false);
button.parentNode.parentNode.style.display = "none";
}  // end fcn abortEdit


function trimAllInput(){
var ipnl = document.getElementsByTagName("input");
if(!ipnl){return;}
var L = ipnl.length;
if(!L){return;}
var g = null;
var s = "";
for(var i = 0; i < L; i++){
g = ipnl[i];
if(!g){continue;}
if(!g.type){continue;}
if(g.type.toLowerCase() != "text"){continue;}
g.value = g.value.trim();}
}  // end fcn trimAllInput


function updateUSERSArray(ro){
if(!ro.usid){
  alert("Error 21. Edit results were not returned from the " + 
  "server\n\nRefresh your browser, check results, and try again " + 
  "if changes were not saved.");
  return false;}

// store usid
var usid = parseInt(ro.usid, 10);
if(isNaN(usid)){
  alert("Error 22. Edit results were not returned from the " + 
  "server\n\nRefresh your browser, check results, and try again " + 
  "if changes were not saved.");
  return false;}

// make sure there is a USERS array
if(!USERS[usid]){USERS[usid] = new Array();}

// update params
if(typeof ro.UserName != "undefined"){USERS[usid]['UserName'] = ro.UserName;}
  else {USERS[usid]['UserName'] = null;}
if(typeof ro.Email != "undefined"){USERS[usid]['Email'] = ro.Email;}
  else {USERS[usid]['Email'] = null;}
if(typeof ro.PhoneNumber != "undefined"){USERS[usid]['PhoneNumber'] = ro.PhoneNumber;}
  else {USERS[usid]['PhoneNumber'] = null;}
if(typeof ro.LastModifiedDate != "undefined"){USERS[usid]['LastModifiedDate'] = ro.LastModifiedDate;}
  else {USERS[usid]['LastModifiedDate'] = null;}
if(typeof ro.ArchivedDate != "undefined"){USERS[usid]['ArchivedDate'] = ro.ArchivedDate;}
  else {USERS[usid]['ArchivedDate'] = null;}
if(typeof ro.Role != "undefined"){USERS[usid]['Role'] = ro.Role;}
  else {USERS[usid]['Role'] = null;}

// return true
return true;
}  // end fcn updateUSERSArray


function updateUsersDisplay(ro, td){
// validate usid
if(!ro.usid){
  alert("Error 23. Edit results were " + 
  "not returned from the server\n\nRefresh your " + 
  "browser, check results, and try again if changes were " + 
  "not saved.");
  return;}
var usid = parseInt(ro.usid, 10);
if(isNaN(usid)){
  alert("Error 24. Edit results were " + 
  "not returned from the server\n\nRefresh your " + 
  "browser, check results, and try again if changes were " + 
  "not saved.");
  return;}

// validate td
if(!td){
  alert("Error 26. Edit results were " + 
  "not returned from the server\n\nRefresh your " + 
  "browser, check results, and try again if changes were " + 
  "not saved.");
  return;}

// validate acn
if(typeof ro.acn == "undefined"){
  alert("Error 27. Edit results were " + 
  "not returned from the server\n\nRefresh your " + 
  "browser, check results, and try again if changes were " + 
  "not saved.");
  return;}

// save acn to var for convenience
var acn = parseInt(ro.acn);

// validate acn value
if(isNaN(acn)){
  alert("Error 28. Edit results were " + 
  "not returned from the server\n\nRefresh your " + 
  "browser, check results, and try again if changes were " + 
  "not saved.");
  return;}
if(acn < 0 || acn > 7){
  alert("Error 29. Edit results were " + 
  "not returned from the server\n\nRefresh your " + 
  "browser, check results, and try again if changes were " + 
  "not saved.");
  return;}

// get cell index
var ci = td.cellIndex;
ci = parseInt(ci);
if(isNaN(ci)){
  alert("Error 30. Edit results were " + 
  "not returned properly from the server\n\nRefresh " + 
  "your browser, check results, and try again if changes " + 
  "were not saved.");
  return;}

// exit early for those edits that do not update table cells
if(acn == 3){
  styleCell(td, false);
  return;}
else if(acn == 4){
  styleCell(td, false);
  return;}
else if(acn == 5){
  styleCell(td, false);
  return;}

// make sure acn matches cell index
if(acn != ci){
if(!(acn == 7 && ci == 6)){
  alert("Error 31. Edit results were " + 
  "not returned properly from the server\n\nRefresh " + 
  "your browser, check results, and try again if changes " + 
  "were not saved.");
  return;}}

// make sure usid is in USERS (this check has to happen after 
// deleteUserRow since this array entry has already been deleted
// during a user delete.
if(!USERS[usid]){
  alert("Error 25. Edit results were " + 
  "not returned from the server.\n\nRefresh your " + 
  "browser, check results, and try again if changes were " + 
  "not saved.");
  return;}

// get the associated USERS array value
if(acn == 7){var dv = "Inactive";}
else {
  var fina = acnToFieldName(acn)
  var dv = USERS[usid][fina];}

// make sure new value was sent
if(typeof ro.nv == "undefined"){
  alert("Error 32. Edit results were " + 
  "not returned properly from the server\n\nRefresh " + 
  "your browser, check results, and try again if changes " + 
  "were not saved.");
  return;}

// make sure new value matches USERS value
if(ro.nv != dv){
  alert("Error 33. Edit results were " + 
  "not returned properly from the server\n\nRefresh " + 
  "your browser, check results, and try again if changes " +
  "were not saved.");
  return;}

// always update modified date
var moddate = USERS[usid]['LastModifiedDate'];
var tr = td.parentNode;
var mdtd = tr.cells[4];
splitDate(moddate, mdtd);

// always update ardhived date
statd = tr.cells[6];
splitDate(USERS[usid]['ArchivedDate'], statd);

// update role cell with new value
if(acn == 6 || acn == 7){
  if(USERS[usid]['ArchivedDate']){
    statd.className = "inact";
    statd.textContent = "Inactive";}
  else if(USERS[usid]['Role'] == "Admin"){
    statd.className = "admin";
    statd.textContent = "Admin";}
  else if(USERS[usid]['Role'] == "Staff"){
    statd.className = "staff";
    statd.textContent = "Staff";}
  else {
    statd.className = "user";
    statd.textContent = "User";}}

// if update anything other than role/status
else {td.textContent = dv;}

// release update lock
styleCell(td, false);
}  // end fcn updateUsersDisplay


function returnEditTextHandler(id, td, acn, req, mi, ma, na){
var f = function(e){editText(e, id, td, acn, req, mi, ma, na);}
return f;
}  // end fcn returnEditTextHandler


function returnPwResetHandler(td, tr, usid){
var f = function(e){resetPassword(e, td, tr, usid);}
return f;
}  // end fcn returnPwResetHandler


function returnEditRoleHandler(usid, td){
var f = function(e){editRole(e, usid, td);}
return f;
}  // end fcn returnPwResetHandler


function resetPassword(evt, td, tr, usid){

// confirm password reset
var username = USERS[usid]['UserName'];
var email = USERS[usid]['Email'];
var rv = confirm("Confirm reset password for " + username + " (" + email + ")?");
if(!rv){return;}

// style the cell
var tf = styleCell(td, true);
if(!tf){
  msg = "Error 35. The password cannot be reset while another edit " + 
  "is in progress.";
  alert(msg);
  return;}

formatAJAXPost(5, usid, td, email, username);
}  // end fcn resetPassword


function acnToFieldName(n){
if(n == 0){return "UserName";}
else if(n == 1){return "Email";}
else if(n == 2){return "PhoneNumber";}
else if(n == 3){return "CreatedDate";}
else if(n == 4){return "LastModifiedDate";}
else if(n == 5){return "Password";}
else if(n == 6){return "Role";}
else if(n == 7){return "Inactive";}
else {
  alert("There is an error on the page. Refresh your browser " + 
  "and try again.");
  return "";}
}  // end fcn acnToFieldName


function splitDate(datistr, td){
// check for null values
if(!td){return;}
if(!datistr){
  td.textContent = "";
  td.title = "";
  return;}
datistr = datistr.trim();
if(!datistr){
  td.textContent = "";
  td.title = "";
  return;}

// split datistr at space
var R = datistr.split(" ");

// set date string
if(R[0]){td.textContent = R[0];}
else {td.textContent = "Error";}

// set time string
if(R[1]){td.title = R[1];}
else {td.title = "";}
}  // end fcn splitDate
</script>
</body>
</html>
