<?php

/*
POSTValidator operates on a single field at a time.  Use reset function
to assign a new field to the validator object.  Then run the validate
function for the data type of the field.

The reset function is passed the variable to validate and the name 
of the variable (for error messages). These are saved as class 
variables. A second function is called to perform the validation: 
validateDate,
validateInt, 
validateFloat, 
validateEmail, 
validatePhone,
validateString
*/

class POSTValidator {
private $ele;
private $dataName;


public function reset($d, $n){
/* Used to set up the POSTValidator for field-by-field mode of
validation.
PARAMS:
$d: the variable to validate (e.g., $_POST['a']).
$n: The name of the field to validate (used for error messages).
RETURNS: Nothing.
*/
$this->ele = $d;
$this->dataName = $n;
}  // end public fcn reset


public function validateDate($req, $mindate, $maxdate){
/* Checks morphology of a date.
PARAMS:
$req: boolean.  True if the field is required.
$mindate: The earliest legal date.  May be a date/time string
          or a numeric timestamp. 
$maxdate: The latest legal date.  May be a date/time string
          or a numeric timestamp.
RETURNS: null on success or an error message.
*/

// if data is not required AND not populated, return no error
// if the data is populated OR if it is required, it must be valid
if(!isset($this->ele)){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required, but is missing.";
    return $rv;}}
elseif($this->ele == ""){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required but is not present.";
    return $rv;}}

// convert the date to be checked to a ts
if(is_numeric($this->ele)){
  $ts = $this->ele;
  $dastr = date("n/j/Y", $ts);}
else {
  $ts = strtotime($this->ele);
  $dastr = $this->ele;}

// make sure the timestamp is valid
if($ts === false){
  $rv = "The {$dataName} is not a valid date.";
  return $rv;}

// convert the mindate to a ts
if(!empty($mindate)){
if(is_numeric($mindate)){
  $tsmin = $mindate;
  $dastrmin = date("n/j/Y", $tsmin);}
else {
  $tsmin = strtotime($mindate);
  $dastrmin = $mindate;}

// validate against the minimum date (requires that mindate 
// could be converted to a valid ts).
if(is_numeric($tsmin)){
if($ts < $tsmin){
  $rv = "The {$dataName} must be on or after {$dastrmin}.";
  return $rv;}}}

// convert the mindate to a ts
if(!empty($maxdate)){
if(is_numeric($maxdate)){
  $tsmax = $maxdate;
  $dastrmiax = date("n/j/Y", $tsmax);}
else {
  $tsmax = strtotime($maxdate);
  $dastrmax = $maxdate;}

// validate against the minimum date (requires that mindate 
// could be converted to a valid ts).
if(is_numeric($tsmax)){
if($ts > $tsmax){
  $rv = "The {$dataName} must be on or before {$dastrmax}.";
  return $rv;}}}

// all checks passed
return null;
}  // end public fcn validateDate


public function validateInt($req, $minint, $maxint, $allowNegTF){
// if data is not required AND not populated, return no error.
// if data is required OR is populated, it needs to be validated.
if(!isset($this->ele)){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required but is missing.";
    return $rv;}}
elseif($this->ele == ""){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required but not present.";
    return $rv;}}

// make sure the data is a valid integer
if(!is_numeric($this->ele)){
  $rv = "{$this->dataName} is not a whole number.";
  return $rv;}
if((int)($this->ele) != $this->ele){
  $rv = "{$this->dataName} is not a whole number.";
  return $rv;}

// make sure data is not negative
if(empty($allowNegTF)){if($this->ele < 0){
  $rv = "{$this->dataName} may not be negative.";
  return $rv;}}

// make sure data is not < min
if(!is_null($minint)){
if($this->ele < $minint){
  $rv = "{$this->dataName} may not be less than {$minint}.";
  return $rv;}}

// make sure data is not > max
if(!is_null($maxint)){
if($this->ele > $maxint){
  $rv = "{$this->dataName} may not be greater than {$maxint}.";
  return $rv;}}

// all checks passed
return null;
}  // end public fcn validateInt


public function validateFloat($req, $minfl, $maxfl, $allowNegTF){

// if data is not required AND not populated, return no error
// if the data is populated OR if it is required, check for errors
if(!isset($this->ele)){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required but is missing.";
    return $rv;}}
elseif($this->ele == ""){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required but is not present.";
    return $rv;}}

// make sure the data is a valid float
if(!is_numeric($this->ele)){
  $rv = "{$this->dataName} is not a valid number.";
  return $rv;}

// make sure data is not negative
if(empty($allowNegTF)){if($this->ele < 0){
  $rv = "{$this->dataName} may not be negative.";
  return $rv;}}

// make sure data is not < min
if(!is_null($minfl)){
if($this->ele < $minfl){
  $rv = "{$this->dataName} may not be less than {$minfl}.";
  return $rv;}}

// make sure data is not > max
if(!is_null($maxfl)){
if($this->ele > $maxfl){
  $rv = "{$this->dataName} may not be greater than {$maxfl}.";
  return $rv;}}

// all checks passed
return null;
}  // end fcn validateFloat


public function validateEmail($req, $minL, $maxL){

// if data is not required AND not populated, return no error
// if the data is populated OR if it is required, check for errors
if(empty($this->ele)){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required but is missing.";
    return $rv;}}

// check for valid string first
$rv = $this->validateString($req, $minL, $maxL, null);
if(!empty($rv)){return $rv;}

// check for match with email format
$n = preg_match("/^[^@]+@{1}[^@\s]+\.{1}[\w]{2,5}$/", $this->ele);
if(empty($n)){
  $rv = "{$this->dataName} is not a valid email address.";
  return $rv;}

// all checks passed
return null;
}  // end fcn validateEmail


public function validatePhone($req, $minL, $maxL){
// if data is not required AND not populated, return no error
// if the data is populated OR if it is required, check for errors
if(empty($this->ele)){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required but is missing.";
    return $rv;}}

// check for valid string first
$rv = $this->validateString($req, $minL, $maxL, null);
if(!empty($rv)){return $rv;}

// check for match with phone format
$n = preg_match("/[^0-9\-]/", $this->ele);
if(!empty($n)){
  $rv = "{$this->dataName} is not a valid phone number.";
  return $rv;}

// all checks passed
return null;
}  // end fcn validatePhone


public function validateString($req, $minL, $maxL, $illegalChars){

// if data is not required AND not populated, return no error
// if the data is populated OR if it is required, check for errors
if(!isset($this->ele)){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required but is missing.";
    return $rv;}}
elseif($this->ele == ""){
  if(empty($req)){return null;}
  else {
    $rv = "{$this->dataName} is required but is missing.";
    return $rv;}}

// get string length
$L = strlen($this->ele);

// check that string is not too short
if(!empty($minL)){
if($L < $minL){
$rv = "{$this->dataName} must have at least {$minL} characters.";
return $rv;}}

// check that string is not too long
if(!empty($maxL)){
if($L > $maxL){
$rv = "{$this->dataName} exceeds the maximum length of {$maxL} characters.";
return $rv;}}

// check that string does not contain illegal characters
if(!empty($illegalChars)){
$pattern = "/[" . preg_quote($illegalChars, "/") . "]/";
$n = preg_match($pattern, $this->ele);
if(!empty($n)){
$rv = "{$this->dataName} contains one or more of the following illegal characters: {$illegalChars}";
return $rv;}}

// all checks pass
return null;
}  // end fcn validateString
}  // end class POSTValidator


function validateEmail($m){
if(empty($m)){return false;}
$tf = preg_match("/^[^@]+@{1}[^@\s]+\.{1}[\w]{2,5}$/", $m);
if(empty($tf)){return false;}
return true;
}  // end fcn validateEmail


function cleanPhone($ph){
if(empty($ph)){return "";}
$ph = preg_replace("/[^0-9\-]/", "-", $ph);
$ph = preg_replace("/\-{2,}/", "-", $ph);
$ph = preg_replace("/^\-+|\-+$/", "", $ph);
return $ph;
}  // end fcn cleanPhone


function wrapArray($R, $tag, $p){
/* PARAMS:
$R: the array to be flattened.
$tag: The tag (without brackets) to wrap each element in.  It has
      three special cases: "ul", "ol", and "br".  The lists will
      automatically include the list item tags.  br will insert breaks with-
      out any encapsulation.
$p (optional): An introductory heading that will be included in
   <p> tags.  (If you want a heading that is in a tag other than p,
   you can always add it to the string returned by this fcn
RETURNS: An html string containing the array elements wrapped in
         the appropriate tags. */

// set the implode set char (the check for t == li is to check
// for a common error: use of li instead of ul)
$t = strtolower($tag);
if($t == "ul" || $t == "li"){
  $a = "<ul><li>";
  $b = "</li><li>";
  $c = "</li></ul>";}
elseif($t == "ol"){
  $a = "<ol><li>";
  $b = "</li><li>";
  $c = "</li></ol>";}
elseif($t == "br"){
  $a = "";
  $b = "<br />";
  $c = "";}
else {
  $a = "<{$t}>";
  $b = "</{$t}><{$t}>";
  $c = "</{$t}>";}

// implode the array (and wrap in outer tags if applicable)
$s = implode($b, $R);
$u = "{$a}{$s}{$c}";

// prepend a paragraph if one was sent
if(empty($p)){return $u;}
$rv = "<p>{$p}</p>{$u}";
return $rv;
}  // end fcn wrapArray


function elide($s, $maxi, $cutAt){
/* Returns a truncated string with an ellipsis.
PARAMS:
$s The string to elide
$maxi: Max string length allowed without ellision. If the string
       is this number of chars long, or less, it is not elided.
$cutAt: the leftmost number of characters to keep during ellision.
        This should be less than $maxi to make room for the ellipsis
RETURNS: The original string (if <= $maxi) or the elided string.
*/

// return string as-is if it is not GT $maxi
$L = strlen($s);
if($L <= $maxi){return $s;}

// truncate at $cutAt, add ellipsis, and return
$t = trim(substr($s, 0, $cutAt));
$rv = "{$t}...";
return $rv;
}  // end fcn elide


function exitWithJSONStr($err, $warn, $note, $mm, $nvp){
$csv = array();
/* Writes to error log and exits with a JSON string returned to the
calling XMLHttpRequest object. 
PARAMS:
$err:  Error message array or string. Returned as JSON "failmess" 
       property, with an array of plain-text strings as its value.
$mm:   Management message array or string.  Concatenated with line 
       breaks then written to error log.
$warn: User message array or string. These are non-fatal error 
       warnings or informational notices. Returned as JSON "warn" 
       property with an array of plain-text strings as its value.
$note: User notice array or string.  These are non-error 
       notifications for confirmation of expected events or any
       other user message.
$nvp:  Array of name-value pairs. The name is the associated array 
       element index and the value is the array element value.
RETURNS: Exits PHP and returns a JSON string that can be converted
into a JS object using JSON.parse.
*/

// management message to error log
if(!empty($mm)){
if(is_array($mm)){
  $s = implode(" \n", $mm);
  error_log($s);}
else {error_log($mm);}}

// package error message(s)
if(!empty($err)){$csv[] = packArrayAsJSONNvp("failmess", $err);}

// pagage user notices or warnings
if(!empty($warn)){$csv[] = packArrayAsJSONNvp("warn", $warn);}

// pagage user notices or warnings
if(!empty($note)){$csv[] = packArrayAsJSONNvp("note", $note);}

// package nvp
if(!empty($nvp)){$csv[] = packArrayAsJSONNvp(null, $nvp);}

// assemble and return JSON string
header("Content-Type:application/json");
$s = implode(", ", $csv);
$rv = "{" . $s . "}";
exit($rv);
}  // end fcn exitWithJSONStr


function exitWithText($s){
header("Content-Type:text/plain");
exit($s);
}  // end fcn exitWithText


function exitWithHTML($s){
header("Content-Type:text/html");
exit($s);
}  // end fcn exitWithText


function packArrayAsJSONNvp($na, $R){
$A = array();
/*Packages array values as a name-value pair suitable for
JSON string. 
PARAMS:
$na: The name part of the nvp. If empty, use the index of the associated 
     array, $R
$R: A single string, an array of strings without text-based indexes,
    or an array of strings with test based indexes. 
RETURNS: A JSON string of the form na:va or na:[va, va, va]

Logic of this function. Escape and wrap na. Then wrap and escape R
based on 3, mutually exclusive use cases:
1) R is not array (and therefore presumably a string): na:str
2) na is set (and R is an array): na:[v1, v2, v3,...]
3) na is not set (and R is an assoc array): n1:v1, n2:v2, n3:v3...
*/

// if na has special chars, they would have been escaped once
// when the string was packed as the $na variable.
// Escape them again (JIC) so they are escaped once for PHP and
// again for JS
// characters that may be escaped in JSON:
// "\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c"
$jsonesc = <<<ENd4Xxu
/\n\r\t\x08\x0c"\\
ENd4Xxu;
if(!empty($na)){$na = addcslashes($na, $jsonesc);}

// IF $R IS A STRING: format as $NA:$R
if(!is_array($R)){
  // if $R is a string, it would have had special characters
  // escaped once. Escape them again to they are escaped for both
  // js and php
  $s = addcslashes($R, $jsonesc);

  // wrap the name and value string in js and php outer delimiters
  $rv = "\"{$na}\":\"{$s}\"";}

// IF $R IS AN ARRAY...
// if $na is set, format is: $NA:[V1, V2, V3,...]
elseif(!empty($na)){
  foreach($R as $v){
    // if $v had any special chars, they would have been escaped
    // once when $v was stored.
    // escape them again so they are escaped once for JS string
    // and again for PHP
    $t = addcslashes($v, $jsonesc); 

    // add php and js outer delimiters
    $s = "\"{$t}\"";
    $A[] = $s;}
    
  // concatenate the array elements
  $t = implode(", ", $A);
  $rv = "\"{$na}\":[{$t}]";}

// IF $NA IS NOT SET. USE INDEX FROM $R AS NAME
// format is: N1:V1, N2:V2, N3:V3,...
else {
  foreach($R as $k => $v){
    // if $k or $v had any special chars, they would have been 
    // escaped once when they were stored in the db.
    // escape them again so they are escaped once for JS string
    // and again for PHP
    $t = addcslashes($k, $jsonesc); 
    $u = addcslashes($v, $jsonesc);
    // add php and js outer delimiters
    $A[] = "\"{$t}\":\"{$u}\"";}

  // concatenate and return the nvp
  $rv = implode(", ", $A);}

// return nvp(s)
return $rv;
}  // end fcn packJSONArray


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
?>