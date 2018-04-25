<?php 
$AuthMode = 1;
require_once "SLCCGAuthenticate.php";
require_once "slccgutilities.php":

$errmsg = array();

if(isset($_POST['AJAXAdd'])){
  if($_POST['AJAXAdd'] == "AJAXAdd"){addAJAXUser();}}


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


function addAJAXUser(){



}  // end fcn addAJAXUser



?>