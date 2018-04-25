<?php 
$AuthMode = 1;
require_once "SLCCGAuthenticate.php";
require_once "slccgutilities.php";
$errmsg = array();

// check for AJAX calls
if(isset($_POST['a'])){processAJAXCall($dbcxn);}


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

// For username update, make sure new value is unique.
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

?>