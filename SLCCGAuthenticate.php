<?php
/*
AuthMode is a variable that can be defined prior to the include
for AdobeAuthenticate that can modify AdobeAuthenticate behavior:

$AuthMode = 0: Regular behavior. If authentication fails, a 
               login form is shown. (Also applies if $AuthMode 
               is empty.)

$AuthMode = 1: Silent behavior. If authentication fails, an error 
               message is returned instead of showing login page
              (Used for AJAX calls where the user should already be 
              logged in.)
*/

// abort if any GET variables are non-numeric
if(!empty($_GET)){
  foreach($_GET as $gk => $g){
    if(!empty($g)){
      if(!is_numeric($g)){
      
      // abort page load due to potential hacking
      // wait a few seconds before replying to slow down hackers
      error_log("GET param is non-numeric: {$g}");
      $i = mt_rand (24, 54);
      sleep($i);
      exit(0);
}}}}

// initialize global variables
$tf = session_start();
$dbcxn = null;

// save the outer PHP filename (not this include) for use in the 
// login form
$protectedPage = $_SERVER['PHP_SELF'];  

// run authentication. $msg contains error messages. If it is 
// empty, authentication passed. If it is populated (and AuthMode is 
// empty) then the login form is shown, inclding $msg
$msg = authenticate();

// if there is no error message, then authentication passed. Return
// from the include and continue to the originally requested page
if(empty($msg)){return;}


function authenticate(){
global $AuthMode, $dbcxn;

// get a database connection
$dbcxn = getMysqliConnection($errmsg);

// check database connection. if it failed, clear session
if(!empty($errmsg)){
  clearSessionPermissions(); 
  return $errmsg;}

// authenticate the user. 
$msg = authenticateUser($dbcxn);

// if $msg is empty, authentication passed. Return nothing.  This will
// cause this include to exit early, and pass control to the including file
// without any login page
if(empty($msg)){return null;}

// if you get here, there is an error message...
// If authmode is empty, return the error message so that the login
// page is shown
if(empty($AuthMode)){
  clearSessionPermissions();
  return $msg;}
  
// if authmode is 1, this is an ajax call where the user should already
// ge logged in.  Return generic error message by existing.
elseif($AuthMode == 1){
  clearSessionPermissions();
  exit("Error: Authentication failed.");}
  
// if authmode is anything else, it has been incorrectly set
// so show login page regardless
clearSessionPermissions();
$s = "Authentication Mode contains an unexpected value.";
return $s;
} // end fcn authenticate


function authenticateUser($dbcxn){
// Returns an error message (which causes login screen to be shown)
// or null (if authentication is complete).

// resolve username and password: Returns a 3 element array:
// 0un, 1PostedPw, 2saltedHash
$unpw = resolveUsernameAndPassword();
if(empty($unpw)){
$msg = "Enter a username and password.";
return $msg;}

// clear session & post JIC
clearSessionPermissions();

$msg = validateUsernameAndPassword($unpw, $dbcxn);
if(!empty($msg)){return $msg;}
}  // end fcn authenticateUser


function resolveUsernameAndPassword(){
$rv = array();  // 0un, 1PostedPw, 2SessionPw(salted hash)

// check for username.  If it has been saved to session, use it.
// Otherwise, see if a username has been posted from the 
// login form.  If both of these are emtpy, the user is not already
// logged in and the login form has not been submitted.
if(!empty($_SESSION['uname'])){$rv[0] = $_SESSION['uname'];}
elseif(!empty($_POST['uname'])){$rv[0] = $_POST['uname'];}
else {$rv[0] = null;}

// check for password. If session is populated, the user is already
// logged in.  Otherwise, check for a password from a login form
// submission.  If both of these are empty, the user is not already
// signed in and has not yet submitted the login form.
if(!empty($_SESSION['psswd'])){
  $rv[1] = null;
  $rv[2] = $_SESSION['psswd'];}
elseif(!empty($_POST['psswd'])){
  $rv[1] = $_POST['psswd'];
  $rv[2] = null;}
else {return false;}

// return the username and password pair
return $rv;
}  // end fcn resolveUsernameAndPassword


function validateUsernameAndPassword($unpw, $dbcxn){
// $unpw is 3 ele array: 0un, 1PlainTextPw (posted), 2saltedHash

// check that username is populated
if(empty($unpw[0])){
$s = "Error: A username was not provided.";
return $s;}

// check that username is correct length
$L = strlen($unpw[0]);
if($L < 1){
  $s = "Error: A username was not provided.";
  return $s;}
elseif($L > 255){
  $s = "Error: The username exceeds the max length of 255 characters.";
  return $s;}

// check that pw is populated
if(empty($unpw[1]) && empty($unpw[2])){
$s = "Error: A password was not provided.";
return $s;}

// validate pw length 
if(!empty($unpw[1])){$L = strlen($unpw[1]);}
elseif(!empty($unpw[2])){strlen($unpw[2]);}
else {$L = 0;}

// validate password length
if($L < 1){
  $s = "Error: A password was not provided.";
  return $s;}
elseif($L > 255){
  $s = "Error: The password exceeds the max length of 255 characters.";
  return $s;}

// check for emerg login
$q = <<<ENdXxu
SELECT Count(*) AS Cuantos 
FROM Users u
WHERE u.ArchivedDate IS NULL
ENdXxu;

// if the users table is empty, this is first use of the system,
// or the user table has been wiped. Check for the system setup login
$rs = $dbcxn->query($q);
if(empty($rs)){
  $wtf = $dbcxn->error;
  error_log("System setup query failed: {$wtf}");}
else {
  $nr = $rs->num_rows;
  if($nr == 1){
    $row = $rs->fetch_array();
    if(isset($row['Cuantos'])){
      if($row['Cuantos'] == 0){
        if($unpw[0] == "slcc"){
        if($unpw[1] == "gardens" || $unpw[2] == md5("gardens")){
          $_SESSION['uname'] = $unpw[0];
          $_SESSION['psswd'] = md5("gardens");
          return null;
}}}}}}

// format query to get this user
$sqlun = $dbcxn->real_escape_string($unpw[0]);
$q = <<<ENdXxu
SELECT u.UserName, u.Password, up.Role 
FROM Users u
LEFT JOIN UserPermissions up ON up.UserPermissionKey = u.UserPermissionKey
WHERE u.ArchivedDate IS NULL
AND u.UserName = '$sqlun'
ENdXxu;

// run the query
$rs = $dbcxn->query($q);
if(empty($rs)){
  $s = "Error: The username or password was not found.";
  return $s;}

// get the number of rows
$nr = $rs->num_rows;
if(empty($nr)){
  $s = "Error: The username and password combination was not found.";
  return $s;}
if($nr != 1){
  $s = "Error: The username or password is not valid.";
  return $s;}

// get the row
$row = $rs->fetch_array();

// validate the salt/hash combo from the database
if(empty($row['Password'])){
  $s = "Error: The user or password was not found.";
  return $s;}

// get the expected salt/hash combination from the db user record
$expectedHash = $row['Password'];

// calculate the actual hash from a login form submission
// or store the session hash to actualHash
// FYI: The PHP manual of crypt function is not complete.
// The second param can be a salt, or it can be the entire string
// returned from crypt (which concatenates salt with hash,
// and algorithm code
if(!empty($unpw[1])){$actualHash = crypt($unpw[1], $expectedHash);}
elseif(!empty($unpw[2])){$actualHash = $unpw[2];}
else {
  $s = "Enter your username and password.";
  return $s;}

// compare the two hashes
$n = hash_equals($expectedHash, $actualHash);
if(empty($n)){
  $s = "Error: The username and password combination was not valid.";
  return $s;}

// the hash is valid, store successful login to session
$_SESSION['uname'] = $unpw[0];
$_SESSION['psswd'] = $actualHash;
$_SESSION['permrole'] = $row['Role'];
}  // end fcn validateUsernameAndPassword


function getRandomPassword(){
$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ" . 
"23456789!@#$%^&*()_-=+?";
$s = str_shuffle($chars);
$L = strlen($chars);
$sta = mt_rand(0, $L - 6);
$a = substr($s, $sta, 4);

// encode timestamp
$ts = time();
$b = base_convert($ts, 10, 36);
$b = substr($b, 0, 4);

// encode random number
$r = mt_rand(100000, 999999);
$t = base_convert($r, 10, 36);
$c = substr($t, 0, 4);

// construct password
$s = trim($a) . trim($b) . trim($c);
$rv = str_shuffle($s);
return $rv;
}  // end fcn getRandomPassword


function getMysqliConnection(&$errmsg){
$mysqlun = "isaac";
$mysqlpw = "vk8y4uBu5Pgu";
$dbhost = "localhost";
$dbname = "SLCCGardens";

// make database connection
$dbcxn = new mysqli($dbhost, $mysqlun, $mysqlpw, $dbname);

// if constructor failed, try one more time
if(empty($dbcxn)){
  sleep(2);
  $dbcxn = new mysqli($dbhost, $mysqliun, $mysqlipw, $dbname);
    if(empty($dbcxn)){
      $errmsg = "A connection to the database could not be made.";
      return null;
}}

// check for successful constructor, try one more time if needed
if(!($dbcxn instanceof mysqli)){
  sleep(2);
  $dbcxn = new mysqli($dbhost, $mysqliun, $mysqlipw, $dbname);
  if(!($dbcxn instanceof mysqli)){
    $errmsg = "A database connection could not be made.";
    return null;
}}

// check for connection error
$err = $dbcxn->connect_error;
if($err){
  error_log("MySQLi connection error: {$err}");
  $errmsg = "The connection to the database failed.";
  return null;}

// return the database connection
$dbcxn->set_charset("utf8");
return $dbcxn;
}  // end fcn getMysqliConnection


function clearSessionPermissions(){
$_SESSION['uname'] = null;
$_SESSION['psswd'] = null;
$_SESSION['permrole'] = null; 
}  // end fcn clearSessionPermissions
?>


<!DOCTYPE html>
<html>
<head>
<title>SLCC Gardens - Login</title>
<style type="text/css">
body {font-size:11pt;}
#loginForm {display:inline-block; border:solid 2px #2F441C; background-color:#B58E50;
     padding:2em; text-align:center; margin:0 2em 0 0; vertical-align:top;}
input {display:block; margin:1em 0; background-color:#ffffff; 
     padding-left:0.25em; width:15em;}
input:focus {outline:2px solid #d19165; background-color:#fafffd;}
#loginForm button {color:#2F441C; background-color:#BB5303; padding:0.5em 1em;
     display:block; font-weight:bold; margin:2em auto 1em; 
     border:2px solid #2F441C; border-radius:5px;}
#loginForm button:hover {background-color:#2F441C; color:#BB5303; 
     border:2px solid #BB5303;}
h2, h3, h4 {color:#2F441C; margin:0;}
#errmsg {margin:2em; padding:0.5em; background-color:#D6D4D6; box-sizing:border-box;}
</style>
<link rel="shortcut icon" type="image/x-icon" href="images/static/slccgardensfave.ico" />
<meta charset="utf8" />
<meta name="viewport" content="width=device-width; initial-scale=1" />
</head>
<body>

<!-- login form -->
<form id="loginForm" method="post" action="<?php echo $protectedPage; ?>">
<input id="uname" name="uname" type="text" placeholder="username" maxlength="255" />
<input id="psswd" name="psswd" type="password" placeholder="password" maxlength="255" />
<p id="errmsg">
<?php echo $msg; ?>
</p>
<button id="loginFormsubm" name="loginFormsubm" type="button">Login</button>
</form>


<script type="text/javascript">
window.onload = windowOnload;
var Surgir = new PopupMsg();


function windowOnload(){
assignEventHandlers();
document.getElementById("uname").focus();
}  // end fcn windowOnload


function assignEventHandlers(){
document.getElementById("loginFormsubm").onclick = validateAndSubmitLogin;
document.onkeyup = clickEnter;
}  // end fcn assignEventHandlers



function clickEnter(evt){
var e = evt || window.evt;
var charCode = evt.keyCode || evt.which;
if(charCode == 13){validateAndSubmitLogin();}
}  // end fcn clickEnter


function validateAndSubmitLogin(){
// reference form and controls
var u = document.getElementById("uname");
var p = document.getElementById("psswd");
var f = document.getElementById("loginForm");

// make sure username is populated
if(!u){
  alert("A username is required.");
  u.focus();
  return;}
else if(!u.value){
  alert("A username is required.");
  u.focus();
  return;}

// make sure username is within length limits
var s = u.value;
var L = s.length;
if(L < 1){
  alert("A username must have between 1 and 255 characters.");
  u.focus();
  return;}
else if(L > 255){
  alert("The username length (" + L + ") is longer than the max length of 255 characters.");
  u.focus();
  return;}

// make sure pw is populated
if(!p){
  alert("A password is required.");
  p.focus();
  return;}
else if(!p.value){
  alert("A password is required.");
  p.focus();
  return;}

// get pw length
s = p.value;
L = s.length;

// make sure pw is within length limits
if(L < 1){
  alert("A password must have between 1 and 255 characters.");
  p.focus();
  return;}
else if(L > 255){
  alert("The password length (" + L + ") is longer than the max length of 255 characters.");
  p.focus();
  return;}

// all checks passed. Submit form
f.submit();
}  // end fcn validateAndSubmitForm
</script>
</form>
</body>
</html>
<?php exit(); ?>
