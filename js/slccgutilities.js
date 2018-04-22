/* TABLE SORTER */
/*
1. Create a new MesaOrdenar object.  The same object can be used to make 
   any number of tables sortable.
2. Call the makeSortable function of the object to make a table sortable.
   Pass in two params:
   a) The ID attribute (as a string) or an object reference for the table
      to make sortable.  (Required)
   b) A csv of single letter codes, representing the data types of each of the
      table columns: xNotSortable, nNumber, dDate, t or sString.  If the csv 
      has more codes than the table has columns, extra codes are ignored. If the 
      table has more columns than there are codes, the extra columns are not 
      sortable. (Required.)
3. The MesaOrdenar object has no other properties or methods.
*/

// constructor for MesaOrdenar
function MesaOrdenar(){

// inner fuction to make a table sortable
/* PARAMS:
tRef can be the id string of the table or the table object itself.
DaTyCSV: CSV of data type codes.  One code for each cell in the
         table header: xNotSortable, nNumber, dDate, tText or 
         sString. 

         If DaTyCSV has more values than there are cells in the 
         header, the extra values are ignored. If the table has 
         more header cells than there are CSV values, the extra 
         header cells will not sortable. */

this.makeSortable = function(tRef, DaTyCSV){
var A = new Array();
var C = null;
var k = 0;
var L = 0;
var T = null;
var rnl = null;
var R = null;

// make sure CSV is sent
if(!DaTyCSV){
  alert("Data type parameter is missing.  Table cannot be made sortable.");
  return;}

// reference the table element
if(typeof tRef == "string"){T = document.getElementById(tRef);}
else if(typeof tRef == "object"){T = tRef;}
if(!T){
  alert("Table reference could not be resolved.");
  return;}

// make sure T object really is a table
if(!T.tagName){
  alert("Table reference could not be resolved.");
  return;}
if(T.tagName != "TABLE"){
  alert("Table reference could not be resolved.");
  return;}

// reference the top row of the table and its cells
rnl = T.rows;
R = rnl[0];
C = R.cells;
k = C.length;

// split DaTyCSV into an array of data type codes
var s = DaTyCSV.replace(/\s+/, "");
A = s.split(",");
L = A.length;

// scan the header cells...
for(var i = 0; i < k; i++){

// set the data type for the sort
if(!C){continue;}
else if(!A[i]){continue;}
else if(A[i] == "x"){continue;}
else if(A[i] == "n"){C[i].onclick = sortTable;}
else if(A[i] == "t"){C[i].onclick = sortTable;}
else if(A[i] == "s"){C[i].onclick = sortTable;}
else if(A[i] == "d"){C[i].onclick = sortTable;}
else {continue;}

// add tool tip and initial a-to-z sort direction
C[i].title = "Click to sort.";
C[i].style.cursor = "pointer";
C[i].MOaz = -1;
C[i].MOdaty = A[i];}
}  // end inner function makeSortable


function sortTable(){
/* PARAM:n
1SortByNumber
2SortByText
3SortByDate*/
var A = new Array();
var az = -1;
var daty = "t";
var i = 0;
var j = 0;
var k = 0;
var L = 0;
var T = null;
var R = null;

// get the cell index of the header that was clicked
var j = this.cellIndex;

// get and toggle the sort order
if(!this.MOaz){this.MOaz = -1;}
this.MOaz = (-1) * (this.MOaz);
az = this.MOaz;

// get the data type
if(!this.MOdaty){this.MOdaty = "t";} 
daty = this.MOdaty;

// reference the table that owns the cell that was clicked
T = this.parentNode;
while(T.tagName != "TABLE"){T = T.parentNode;}

// reference table rows
R = T.rows;
L = R.length;

// scan the table rows...
// start from index = 1 to skip the header row
for(i = 1; i < L; i++){

// store the entire row (2nd index = 0) 
// and the cell content to sort on (2nd index = 1).
k = A.length;
A[k] = new Array();
A[k][0] = R[i];
A[k][1] = R[i].cells[j].textContent;}

// sort the array of row object
if(daty == "n"){A.sort(sortByNumber);}
else if(daty == "d"){A.sort(sortByDate);}
else {A.sort(sortByText);}

// repopulate the table from the rows array
// appendChild will MOVE an existing node
k = A.length;
for(i = 0; i < k; i++){
T.appendChild(A[i][0]);}



// store the sort functions inside this fcn, so they have
// access to the az variable
function sortByNumber(a, b){
var rv = 0;

// extract the contents of the cell to sort by
var c = a[1];
var d = b[1];

// convert each to a number
var e = parseFloat(c, 10);
var f = parseFloat(d, 10);

// if both conversions are a valid number, return the difference...
if(!isNaN(e) && !isNaN(f)){
  rv = az * (e - f);
  return rv;}

rv = sortByText(a, b);
return rv;
}  // end fcn sortByNumber


function sortByText(a, b){
// get the text to compare
var texta = a[1];
var textb = b[1];
var rv = 0;

// compare text and return determination
var rv = az * (texta.localeCompare(textb));
return rv;
}  // end fcn sortByText


function sortByDate(a, b){
var rv = 0;

// extract the contents of the cell to sort by
var c = a[1];
var d = b[1];

// convert each to a Date
var e = new Date(c);
var f = new Date(d);

// if both conversions are a valid date, sort by timestamp
if(!isNaN(e) && !isNaN(f)){
  var g = e.getTime();
  var h = f.getTime();
  rv = az * (g - h);
  return rv;}

// ... otherwise, sort by text
rv = sortByText(a, b);
return rv;
}  // end fcn sortByDate
}  // end fcn sortTable
}  // end fcn MesaOrdenar


/* POPUPATCURSOR CONSTRUCTOR */

/* This object is used to show a data entry box (like a date picker or
a dropdown) at the same screen position where a click event happens.  

1.  The popup element must be absolutely positioned, and this style 
    is set (if necessary) by the PopupAtCursor object.

2.  The popup element must be a direct child of the body element.
    The PopupAtCursor object will move the popup element if nec.
 
3.  The popup position is based on the screen quadrant that the 
    cursor is in, so the popup element stays within the current 
    screen bounds.  For example. if the click occurs in the 4th
    (lower right) quadrant, the bottom right corner of the popup
    is positioned at the click point.  If the click occurs in the 1st
    quadrant, the top right corner of the popup is positioned
    at the click point.

4.  The popup is made with a simple change in display style 
    from none to block.  There are no animations or transitions.

5.  The object has a single method that links the click event
    to a popup element: 

    setupPopup(clickTarget, popUpEle, positionStyle, preDisplayCallback, postDisplayCallback).

6.  The single PopupAtCursor object can be used to set up any number
    of different clickTarget/popupEle combinations.
*/
function PopupAtCursor(){

// function that links the element to click and the element to popup.
// PARAMS:
// 1. clickTarget (reqd): the id or obj ref for the element that gets clicked.
// 2. popUpEle (reqd): The id or obj ref for the element that appears. 
//    popUpEle is assumed to have style.display = none and 
//    style.position = absolute (or fixed.) On reveal, the display is set to block.
// 3. positionStyle (opt): if omitted, style.position is not changed.  If the
//    value is any string that starts with an "f" (case-insensitive), the
//    position is set to fixed.  If the string starts with an "a", style.position
//    is set to absolute. Any other value is ignored and style.position
//    is not changed.  If the position is already being set in the style sheet,
//    this parameter isn't needed.
// 4. cba and cbb (opt): Callback functions that are called before and after
//    popUpEle is shown. They can be used to read values from clickTarget and/or 
//    configure popUpEle before or after it is shown.  The "this" variable 
//    of cba and cbb is bound to clickTarget. The first function
//    param is set to popUpEle.  For cba only (pre-popup cb), a rv 
//    === boolean true (not just truthy) will cancel the popup (and 
//    also cancel cbb).
// RETURN: none

// Also has function PUAC.positionElement(evt, ele) for positioning 
// a popup after the click has occurred.

this.setupPopup = function(clickTarget, popUpEle, positionStyle, cba, cbb){

// resolve the clickTarget
if(typeof clickTarget == "string"){clickTarget = document.getElementById(clickTarget);}
if(typeof clickTarget != "object"){
  alert("The click target could not be determined.");
  return;}

// resolve the popUpElement
if(typeof popUpEle == "string"){popUpEle = document.getElementById(popUpEle);}
if(typeof popUpEle != "object"){
  alert("The click target could not be determined.");
  return;}

// make sure the popupElement is a direct child of the body element
if(popUpEle.parentNode != document.body){
  popUpEle = popUpEle.parentNode.removeChild(popUpEle);
  document.body.appendChild(popUpEle);}

// make sure the popUpEle is absolutely positioned and hidden
if(positionStyle){
if(typeof positionStyle == "string"){
var acn = positionStyle.substr(0, 1).toLowerCase();
if(acn == "a"){popUpEle.style.position = "absolute";}
else if(acn == "f"){popUpEle.style.position = "fixed";}}}

// make sure the popUpEle is not shown
popUpEle.style.display = "none";

// change the cursor of the click target
clickTarget.style.cursor = "pointer";

// create the custom popup function
var f = returnOnclickEventHandler(popUpEle, cba, cbb);

// attach custom popUp function to clickTarget
if(clickTarget.addEventListener){
  clickTarget.addEventListener("click", f, false);}
else if(clickTarget.attachEvent){
  clickTarget.attachEvent("onclick", f);}
else {clickTarget.onclick = f;}
}  // end method setupPopup


// inner function to create a popup function specific to th popUpEle
function returnOnclickEventHandler(pue, cba, cbb){

// create inner function (that does the popup work) 
// to be returned from this function
var f = function(evt){

// reference the event object
var E = new XBEO(evt);
if(!E){
alert("Error. Unable to show pop-up. An event object is not available.");
return;}

// cancel the default action (JIC)
if(E.quit){E.quit();}

// run cba
if(cba){if(typeof cba == "function"){
var cbarv = cba.call(this, pue);
if(cbarv === true){return;}}}

// position the element, based on click quadrant
posnEle(E, pue);

// show the popup
pue.style.display = "block";

// run cbb
if(cbb){if(typeof cbb == "function"){
cbb.call(this, pue);}}
}  // end inner fcn to be returned

// return the fcn that was just created
return f;
}  // end fcn returnPopupFunction


function posnEle(ev, ele){
/* Sets the left and top properties of an element, based on the 
quandrant that a click event occurred in.
PARAMS:
ev: An onclick event.  The event type is not checked by this fcn.
    Should be the XBEO produced by this page.
ele: The element to position. Must already be absolutely positioned
     and an immediate child of the <body> element. Can be an 
     object reference or the element id string. 
RETURN: Nothing. Only the style.top and style.left properties are 
        changed. The element's display and position properties are
        not changed. */

// resolve ele
if(typeof ele == "string"){ele = document.getElementById(ele);}
if(!ele){return;}

// get the event x-coordinate relative to document and window
var eventXwin = ev.clientX;
var eventXdoc = ev.pageX; 

// get the event y-coordinate relative to document and window
var eventYwin = ev.clientY;
var eventYdoc = ev.pageY;

// window size and half-way cutoff positions
var ww = window.innerWidth;
var wh = window.innerHeight;
var cox = parseInt(ww / 2);
var coy = parseInt(wh / 2);

// get the document dimensions and scroll
var dhw = getDocumentHW();
var docscr = getDocScroll();

// position the element vertically: Compare window coordinates
// but set page coordinates. For absolutely positioned elements
// that are children of the body element, top/right/bottom/left
// have to be specified relative to the INITIAL CONTAINING BLOCK
// i.e, the viewport with zero scroll.
if(eventYwin >= coy){
  ele.style.top = "auto";
  var b = wh - eventYwin - docscr.top;
  // var b = dhw.h - eventYdoc;
  ele.style.bottom = b + "px";}
else {
  ele.style.top = eventYdoc + "px";
  ele.style.bottom = "auto";}

// position the element horizontally: Compare window coordinates 
// but set page coordinates. For absolutely positioned elements
// that are children of the body element, top/right/bottom/left
// have to be specified relative to the INITIAL CONTAINING BLOCK
// i.e, the viewport with zero scroll.
if(eventXwin >= cox){
  ele.style.left = "auto";
  var r = ww - eventXwin - docscr.left;;
  ele.style.right = r + "px";}
else {
  ele.style.left = eventXdoc + "px";
  ele.style.right = "auto";}
}  // end inner fcn posnEle

// utility method to position an element manually (after click
// occurred and click event was captured. evt that is sent
// must be XBEO
this.positionElement = function(evt, ele){
if(!evt.xbeo){var E = new XBEO(evt);}
else {var E = evt;}
posnEle(E, ele);
}  // end utility method positionElement
}  // end constructor PopupAtCursor


/* Constructor for VALIDATIONMGR */

/* Performs validation on input elements during input keystrokes. 
Each method takes the input element to be validated and formatted,
along with other params such as max length, and another element 
that will contain a count of remaining characters. The 
ValidationManager assigns its own event handlers to the onkeyup
or onblue events.  One ValidationManager can be used to format
multiple input elements. 

stylo: Style object. An object of this format, used as last
param to multiple functions. Used to style the "characters left"
element when text gets close to the max limit
{limit:10 (chars),
 under:{style nvp},
 over:{style nvp},
 maxed:{style nvp}}
*/

function ValidationManager(){

// method to limit number of characters
// ele: id string or object reference for the input ele to validate
// maxlen (opt): max number of characters (0 for no limit). Cannot be negative.
// msg (opt): id or obj ref for element to contain error messages
// stylo: style object (see into to this constructor)
this.limitNumOfChars = function(ele, maxlen, msg, stylo){

// resolve input and message elements
var g = returnObjRef(ele);
var mensaje = returnObjRef(msg);

// create function call
var f = function(e){LimitChars(g, maxlen, mensaje, stylo);}

// add event listener
if(g.addEventListener){g.addEventListener("keyup", f, false);}
else if(g.attachEvent){g.attachEvent("onkeyup", f);}
else {g.onkeyp = f;}

// set initial message
var s = truncate(g.value, maxlen, mensaje, stylo);
}  // end method limitNumOfChars

// method to limit input to an integer
// ele: id string or object reference for the input ele to validate
// pm: boolean. true = allow plus and minus sign as first char only
// decpt: boolean. true = allow decimal point
// maxlen (opt): max number of characters (0 for no limit). Cannot be negative.
// msg (opt): id or obj ref for element to contain error messages
// cb (opt): a function to run after field content has been checked/limited.
//           "this" is set to the element being validated. 
// stylo: style object (see into to this constructor)
this.limitToNumber = function(ele, pm, decpt, maxlen, msg, cb, stylo){

// resolve input and message elements
var g = returnObjRef(ele);
var mensaje = returnObjRef(msg);

// create function call
var f = function(e){LimitToNum(g, pm, decpt, maxlen, mensaje, cb, stylo);}

// add event listener
if(g.addEventListener){g.addEventListener("keyup", f, false);}
else if(g.attachEvent){g.attachEvent("onkeyup", f);}
else {g.onkeyp = f;}

// set initial message
var s = truncate(g.value, maxlen, mensaje, stylo);
}  // end method limitToNumber


this.limitToWordChar = function(ele, alphaTF, numTF, spTF, uTF, maxlen, msg, cb, stylo){
// alphaTF: allow alphabetic.  numTF: allow 0-9. 
// spTF: allow spaces.  uTF: allow underscore

// resolve input and message elements
var g = returnObjRef(ele);
var mensaje = returnObjRef(msg);

// create function call
var f = function(e){LimitToChar(g, alphaTF, numTF, spTF, uTF, maxlen, mensaje, cb, stylo);}

// add event listener
if(g.addEventListener){g.addEventListener("keyup", f, false);}
else if(g.attachEvent){g.attachEvent("onkeyup", f);}
else {g.onkeyp = f;}

// set initial message
var s = truncate(g.value, maxlen, mensaje, stylo);
}  // end method limitToWordChar


this.limitToCurrency = function(ele, maxlen, msg, cb, stylo){

// resolve input and message elements
var g = returnObjRef(ele);
var mensaje = returnObjRef(msg);

// create function call
var f = function(e){LimitToCurr(g, maxlen, mensaje, cb, stylo);}

// add event listener
if(g.addEventListener){g.addEventListener("keyup", f, false);}
else if(g.attachEvent){g.attachEvent("onkeyup", f);}
else {g.onkeyp = f;}

// set initial message
var s = truncate(g.value, maxlen, mensaje, stylo);
}  // end method limitToCurrency


// method to limit input to a phone number
// ele: the input element to format
// maxlen (opt): max number of characters
// msg (opt): the element to show character remaining count
// cb (opt): function to run after field value has been checked/limited
//           "this" is set to the element being validated.
// stylo: style object (see into to this constructor)
this.limitToPhone = function(ele, maxlen, msg, cb, stylo){

// resolve object references
var g = returnObjRef(ele);
var mensaje = returnObjRef(msg);

// add onkeyup event listener
var f = function(){formatPhone(g, maxlen, mensaje, cb);}
if(g.addEventListener){g.addEventListener("keyup", f, false);}
else if(g.attachEvent){g.attachEvent("onkeyup", f);}
else {g.onkeyup = f;}

// add onblur event listener
f = function(){stripTrailingNonNumber(g);}
if(g.addEventListener){g.addEventListener("blur", f, false);}
else if(g.attachEvent){g.attachEvent("onblur", f);}
else {g.onblur = f;}

// set initial message
var s = truncate(g.value, maxlen, mensaje, stylo);
}  // end method limitToPhone


// ele: the element (usu. input) to validate
// maxlen: The max number of characters. ele.value is truncated if it has more.
// msg: id or obj ref for a element to show "chars left" message
// cb: function to call after input has been checked/limited.
//     "this" is set to ele.
// stylo: style object (see into to this constructor)
this.limitToTime = function(ele, maxlen, msg, cb, stylo){

// resolve input and message elements
var g = returnObjRef(ele);
var mensaje = returnObjRef(msg);

// create function call
var f = function(e){formatTime(g, maxlen, mensaje, cb, stylo);}

// add event listener
if(g.addEventListener){g.addEventListener("keyup", f, false);}
else if(g.attachEvent){g.attachEvent("onkeyup", f);}
else {g.onkeyp = f;}

// set initial message
var s = truncate(g.value, maxlen, mensaje, stylo);
}  // end method limitToTime


function truncate(v, maxlen, msgblock, stylo){
if(!maxlen){return v;}
var m = parseInt(maxlen);
if(isNaN(m)){return v;}
if(!m){return v;}
var s = v.substr(0, m);
if(msgblock){
var L = s.length;
var R = m - L;
if(R == 1){var t = R + " char left";}
else {var t = R + " chars left";}
fillEleWithText(msgblock, t);
if(typeof stylo == "object"){styleMsgblock(msgblock, R, stylo);}}
return s;
}  // end fcn truncate


function styleMsgblock(msgdiv, R, stylo){
/* See descr'n of stylo at top of ValidationMgr constructor 
R = number of characters remaining. */
// create default values for style objects
if(typeof stylo.under != "object"){stylo.under = null;}
if(typeof stylo.over != "object"){stylo.over = null;}
if(typeof stylo.maxed != "object"){stylo.maxed = null;}

// get the warn limit
if(!stylo.warnat){
  applyStyle(msgdiv, stylo.under);
  return;}
var warnlimit = parseInt(stylo.warnat, 10);
if(isNaN(warnlimit)){
  applyStyle(msgdiv, stylo.under);
  return;}

// style message block
if(R <= 0){applyStyle(msgdiv, stylo.maxed);}
else if(R <= warnlimit){applyStyle(msgdiv, stylo.over);}
else {applyStyle(msgdiv, stylo.under);}
}  // end fcn styleMsgblock


function applyStyle(g, nvp){
for(var n in nvp){g.style[n] = nvp[n];}
}  // end fcn applyStyle


// inner fcn to limit number of chars
function LimitChars(g, maxlen, msg, stylo){

// get the value from the input element
var v = g.value;

// truncate if there is a max number of characters
var s = truncate(v, maxlen, msg, stylo);

// reset input element value
if(s != v){g.value = s;}
}  // end inner fcn LimitChars


// inner function to limit input to an integer
// g=inputEle, pm=boolean to allow plus or minus; maxlen=max number of chars (0=no max)
function LimitToNum(g, pm, decpt, maxlen, msg, cb, stylo){

// get the value from the input element
var v = g.value;

// strip anything that is not a digit, decimal point, 
// plus sign or minus sign 
var s = v.replace(/[^0-9\-\+\.]/g, "");

// remove any plus and minus signs that are not the first character
var L = s.substr(0, 1);
var R = s.substr(1);
R = R.replace(/[\+\-]/g, "");

// remove all plus or minus signs if they are verboten
if(!pm){L = L.replace(/[\+\-]/g, "");}

// re-assemble the left and right side of the string
s = L + R;

// remove all decimal points if they are verboten
if(!decpt){s = s.replace(/\./g, "");}

// remove any duplicate decimal points
var i = s.indexOf(".");
if(i >= 0){
var j = i + 1;
L = s.substr(0, j);
R = s.substr(j);
R = R.replace(/\./g, "");
s = L + R;}

// truncate if there is a max number of characters
s = truncate(s, maxlen, msg, stylo);

// reset input element value
if(s != v){g.value = s;}

// run callback fcn
if(typeof cb == "function"){cb.call(g);}
}  // end inner fcn LimitToNum


function LimitToChar(g, alphaTF, numTF, spTF, uTF, maxlen, msg, cb, stylo){

// get the value from the input element
var v = g.value;

// strip evrything that is not a word character, space, or underscrore
var s = v.replace(/[^\w\s_]/ig, "");

// strip alphabetic chars
if(!alphaTF){s = s.replace(/[a-zA-Z]/ig, "");}

// strip numbers
if(!numTF){s = s.replace(/[0-9]/ig, "");}

// strip spaces
if(!spTF){s = s.replace(/\s/ig, "");}

// strip underscores
if(!uTF){s = s.replace(/_/ig, "");}

// truncate if there is a max number of characters
s = truncate(s, maxlen, msg, stylo);

// reset input element value
if(s != v){g.value = s;}

// run callback fcn
if(typeof cb == "function"){cb.call(g);}
}  // end inner fcn LimitToChar


// inner function to limit input to an integer
// g=inputEle, maxlen=max number of chars (0=no max)
function LimitToCurr(g, maxlen, msg, cb, stylo){

// get the value from the input element
var v = g.value;
var L = "";
var R = "";

// strip anything that is not a digit or decimal point, 
var s = v.replace(/[^0-9\.]/g, "");

// remove any duplicate decimal points
var i = s.indexOf(".");
if(i >= 0){
var j = i + 1;
L = s.substr(0, j);
R = s.substr(j);
R = R.replace(/\./g, "");

// only allow two digits after the decimal point
R = R.substr(0, 2);

// re-assemble the string
s = L + R;
} // end if i >= 0

// truncate if there is a max number of characters
s = truncate(s, maxlen, msg, stylo);

// reset input element value
if(s != v){g.value = s;}

// run callback fcn
if(typeof cb == "function"){cb.call(g);}
}  // end inner fcn LimitToCurr


function fillEleWithText(ele, txt){
ele = returnObjRef(ele);
if(!ele){return;}
if(ele.textContent){ele.textContent = txt;}
else if(ele.innerText){ele.innerText = txt;} 
else {
  var tn = document.createTextNode(txt);
  while(ele.firstChild){ele.removeChild(ele.firstChild);}
  ele.appendChild(tn);}
} // end inner fcn fillEleWithText


function formatPhone(g, maxlen, msg, cb, stylo){
// get the value from the input element
var v = g.value;

// replace any non-digit with a hyphen
var s = v.replace(/[^0-9\-]+/g, "-");

// strip leading hyphens and remove dupe hyphens
s = s.replace(/^\-+/g, "");
s = s.replace(/\-{2,}/, "-");

// truncate if there is a max number of characters
s = truncate(s, maxlen, msg, stylo);

// reset input element value
if(s != v){g.value = s;}

// run callback fcn
if(typeof cb == "function"){cb.call(g);}
}  // end fcn formatPhone


function formatTime(g, maxlen, msg, cb, stylo){
// get the value from the input element
var v = g.value;

// remove any non-time characters
var s = v.replace(/[^0-9apm: ]+/gi, "");

// collapse redundant spaces
s = s.replace(/ {2,}/gi, " ");
s = s.replace(/^\s+|]s+$/gi, "");

// allow only one letter a, p, or m
s = allowCharByCount(s, "ap", 1, false);
s = allowCharByCount(s, "m", 1, false);

// allow a max of two colons (but not back to back)
s = allowCharByCount(s, ":", 2, true);
s = s.replace(/:{2,}/gi, ":");

// truncate if there is a max number of characters
s = truncate(s, maxlen, msg, stylo);

// reset input element value
if(s != v){g.value = s;}

// run callback fcn
if(typeof cb == "function"){cb.call(g);}
}  // end fcn formatTime


function stripTrailingNonNumber(g){
var v = g.value;
var s = v.replace(/[^0-9]+$/g, "");
g.value = s;
}  // end fcn stripTrailingNonNumber


function allowCharByCount(s, ch, co, cs){
// s: the string to check
// ch: the character to limit. May be a string from which any single character
//     contributes to th char count (e.g. one a or one b but not both is "ab";
// co: max number of occurences
// cs: case senstive (boolean)
// RETURNS a string with all matching characters removed, except for the first co.
var B = new Array();
var t = "";
var u = "";

// if not case sensitive, convert ch to lower case
if(!cs){ch = ch.toLowerCase();}

// split the string into a character array
var A = s.split("");
var L = A.length;

// scan the array
for(var i = 0; i < L; i++){
t = A[i];

// convert t to lower case if not case sensitive
if(!cs){t = t.toLowerCase();}

// if the character does not match, add it to the destination array
if(ch.indexOf(t) == -1){B[B.length] = A[i];}

// since it does match, add char to destn array only if co > 0
// otherwise, do not xfer the char and move on to the next one
else if(co > 0){
  B[B.length] = A[i];
  co--;}
}  // end for

// join B array into a string and return it
t = B.join("");
return t;
}  // allowCharByCount
}  // end constructor ValidationMgr


function getElementText(ele){
var s = "";
var t = "";

// resolve object ref if necessary
if(typeof ele == "string"){ele = document.getElementById(ele);}
if(!ele){return "";}
if(typeof ele != "object"){return "";}

// normalilze text nodes
ele.normalize();

// check for non-whitespace W3C textContent
if("textContent" in ele){
  s = ele.textContent;
  t = s.replace(/^\s+|\s+$/g, "");
  if(t){return t;}}

// check for IE innerText
if("innerText" in ele){  
  s = ele.innerText;
  t = s.replace(/^\s+|\s+$/g, "");
  if(t){return t;}}

// scan descendants and return first non-whitespace text node
var s = getFirstTextDescendant(ele);
return s;
}  // end fcn getElementText


function getFirstTextDescendant(ele){
// normalize ele (since this fcn can be called recursively)
ele.normalize();

// get node list of child nodes
var nl = ele.childNodes;
if(!nl){return "";}
var noty = 0;
var s = "";
var t = "";

// get node list length
var L = nl.length;
if(!L){return "";}

// scan child nodes and get each node type
for(var i = 0; i < L; i++){
noty = nl[i].nodeType;

// if node is a non-empty, non-whitespace txt node, return its value
if(noty == 3){
if(nl[i].nodeValue){
s = nl[i].nodeValue;
t = s.replace(/^\s+|\s+$/, "");
if(t){return t;}}}

// otherwise, scan its children and return any non-white and non-empty text
s = getFirstTextDescendant(nl[i]);
s = s.replace(/^\s+|\s+$/, "");
if(s){return s;}
}  // end for i

// when you get here, you have scanned all elements and their descendants
// and found no text.  So return an empty string
return "";
}  // end fcn getFirstTextDescendant


function getFirstAncestorOfType(ele, anctype, allowEleMatchTF){
// PARAMS
// ele: the element to get the ancestor for (id string or obj ref)
// anctype: The tagName (case insensitive) of the ancestor type to return
// allowEleMatchTF (boolean): if true, allows ele to match anctype.
//      Otherwise the rv must be a true ancestor of ele.    
// RETURNS: Obj ref or null if none found
var tana = "";

// resolve ele
var g = returnObjRef(ele);

// conv anctype to all caps since that's how tagName
// returns a value
anctype = anctype.toUpperCase();

// loop through parent elements until a parent of type anctype is found,
// or the loop finds the top of the tree
if(!allowEleMatchTF){g = g.parentNode;}
do {
tana = g.tagName;
if(tana == anctype){return g;}
if(tana == "BODY"){return null;}
if(tana == "HTML"){return null;}
g = g.parentNode;
} while(tana != anctype);

// if the loop exits with no tr, return null
return null;
}  // end fcn getFirstAncestorOfType


function getFirstDescendantOfType(ele, descType, matchOnEleTF, clana){
/*
PARAMS:
ele: The parent to search within
descType: The tag name of the element to find. Case-insensitive.
matchOnEleTF: If set to true, return ele if it is of type descType
clana (opt): className to filter on. The descendant must be of this
       className in order to be returned. Clana IS case sensitive.
RETURNS: the first descenant of descType (tag name) 
         and className (if clana is set).  Returns null if there 
         are no matching ele. 
*/
var tana = "";
var s = "";
var t = "";
var k = 0;

// resolve ele
var g = returnObjRef(ele);
if(!g){return null;}

// make sure descType if set
if(!descType){return null;}
t = descType.toLowerCase();

// make sure the ele sent is not of the type to match on
if(matchOnEleTF){
s = ele.tagName;
tana = s.toLowerCase();
if(tana == t){return g;}}

// get a node list of matching elements
var nl =g.getElementsByTagName(t);
if(!nl){return null;}
k = nl.length;
if(!k){return null;}

// if clana is not set, return the first element in the node list
if(!clana){return nl[0];}

// scan the node list, checking for match on class name
for(var i = 0; i < k; i++){
t = nl[i].className;
if(t == clana){return nl[i];}}

// no match
return null;
}  // end fcn getFirstDescendantOfType


function returnObjRef(s){
var g = null;

// convert s param to an object if needed
if(!s){return null;}
else if(typeof s == "string"){g = document.getElementById(s);}
else if(typeof s == "object"){g = s;}
else {return null;}

// validate conversions
if(typeof g == "object"){return g;}
return null;
}  // end inner fcn returnObjRef


function validateEmailFormat(s){
var tf = s.match(/^[^@\n]+@[^\n@\s]+\.{1}\w{2,5}$/i);
if(!tf){return false;}
return true;
}  // end fcn validateEmailFormat


/* CROSS-BROWSER EVENT OBJECT */
// Meant to be called with New. Returns an event object with 
// custom fields and methods.

function XBEO(suceso){
// Cross-Browser Event Object.  This function has no return value,
//  and is meant to be called with new.
/*
.x      x position relative to document (alias: pageX)
.y      y position relative to document (alias: pageY)
.wx			x position relative to window (alias: clientX)
.wy     y position relative to window (alias: clientY)
.charNo ASCII code for character
.keyNo  Code for key pressed
.gob    target element
.quit() function to abort the default action
.button mouse button that was pressed:
        DOM: 0L, 1M, 2R
        IE: 0None, 1L, 2R, 4M
.shiftKey True if key was depressed when event was generated
.ctrlKey  True if key was depressed when event was generated
.altKey   True if key was depressed when event was generated
*/
var evt = null;
var x = null;
var y = null;
var wx = null;
var wy = null;
var charNo = null;
var keyNo = null;
var gob = null;
var quit = null;

// set property flag to identify this as xbeo
this.xbeo = "xbeo";

//set event
if(typeof suceso == "object"){evt = suceso;}
else if (window.event){evt = window.event;}

// make sure an event was sent
if(!evt){return {};}

//set horizontal document coordinates
if(evt.pageX){
  this.x = evt.pageX;
  this.y = evt.pageY;}
else {
  var docscr = getDocScroll();
  this.x = evt.clientX + docscr.left;
  this.y = evt.clientY + docscr.top;}

// set aliases
this.pageX = this.x;
this.pageY = this.y;

// set horizontal and vertical coordinates relative to window
this.wx = evt.clientX;
this.clientX = evt.clientX;
this.wy = evt.clientY;
this.clientY = evt.clientY;

//set charCode
if(evt.charCode){this.charNo = evt.charCode;}
else if (evt.keyCode) {this.charNo = evt.keyCode;}
else {this.charNo = 0;}

//set keyCode
if(evt.keyCode){this.keyNo = evt.keyCode;} 
else {this.keyNo = 0;}

//set object that initiated event
if(evt.target){this.gob = evt.target;}
else if (evt.srcElement){this.gob = evt.srcElement;}
this.target = this.gob;

// set aux key pressed
if(evt.button){this.button = evt.button;}
if(evt.shiftKey){this.shiftKey = evt.shiftKey;}
if(evt.ctrlKey){this.ctrlKey = evt.ctrlKey;}
if(evt.altKey){this.altKey = evt.altKey;}

// function to cancel default action
this.quit = function (){
if("preventDefault" in evt){evt.preventDefault();}
else if("returnValue" in evt){evt.returnValue = false;}
}  // end inner fcn quit
} //end Obj GetXBrowserEvent


/* Selects one or more options in a <select> input.
PARAMS: 
sel: The select object (id str or obj ref)
val: the string to match against <option> value properties.
RETURNS: Nothing.
*/

function selectOptions(sel, val){
// resolve select object
if(typeof sel == "string"){sel = document.getElementById(sel);}

// scan number of options
var nl = sel.options;
var L = sel.length;
for(var i = 0; i < L; i++){

// select any options that match the val
if(nl[i].value == val){nl[i].selected = true;}
else {nl[i].selected = false;}}
}  // end fcn selectOptions


/* FIELD VALIDATOR */

/*
Performs morphologic validation on request.  Does not validate
against any sophisticated business rules.

Meant to be called with new. Create a FieldValidator object by 
passing:
1. element to validate (id or objRef), 
2. Name of the property to read (str), and 
3. name to use in error messages (str). 
4. You can also pass a 4th optional variable for the actual value
   to validate.  This 4th value is only used if it is populated and
   the 1st variable is null.
   
Then call one of more of the validation methods on the FV object.

if the name to use in error messages if falsey, then no error 
messages (alerts) are shown.

The same FV object can be used to validate a different element
by sending a new ele/property/name to the reset method.*/

function FieldValidator(m, p, n, v){

// module level variables:
// ele: The element to be validated
// prop: the name of the propert element to be validated
// na: User-friendly name of the filed to be validated (used to 
//    generate error messages if a valid string)
// val: the property value
// ty: The typeof the property value.
var ele = null;
var prop = "";
var na = "";
var val = "";
var ty = "";
setValues(m, p, n, v);


// method to reset the element and propert to check
/* PARAMS:
m: The input or select object to valudate (or its id as a string)
p: The name of the property to be validate (usu. = "value")
n: Common name of the value to be validated (used in err msg)
v: An actual value to be validated (instead of the value from an
   element.) This is optional. It is only used if m == null and 
   v != null and v!= undefined.
*/
this.reset = function(m, p, n, v){setValues(m, p, n, v);}


/* sets paramater passed (either to constructor or reset function)
to the module-level variables*/
function setValues(m, p, n, v){
// if v is populated and m is null, create an object literal to 
// stand in for m
if(m == null){if(v != undefined){if(v != null){
m = {"value": v, "focus": function(){return;}};}}}

// resolve object reference
if(m == null){
  alert("Could not obtain a reference to the element to validate.");
  return;}
else if(typeof m == "object"){ele = m;}
else if(typeof m == "string"){ele = document.getElementById(m);}
else {
  ele = null;
  alert("Could not obtain a reference to the element to validate.");
  return;}
if(typeof ele != "object"){
  ele = null;
  alert("Could not obtain a reference to the element to validate.");
  return;}

// validate property name and get the current value
if(typeof p != "string"){
  ele = null;
  p = null;
  alert("Could not determine the property to validate.");
  return;}
else if(!(p in ele)){
  ele = null;
  p = null;
  alert("Could not find the property to validate.");
  return;}

// set module-level property variables
prop = p;
val = ele[prop];
ty = typeof(val);

// set the name to use in error message
if(n){na = n;}
}  // end inner fcn setValues;

// method to validate integer. Returns true or false
/* PARAMS:
reqTF: Truthy if the field cannot be empty.
minval: minimum acceptable value. Must be null if n/a.
maxval: maximum acceptable value. Must be null if n/a.
negTF: Truthy if negative values are allowed.
AOR (boolean): Allow user to ignore the error
RETURNS: True if all validation checks pass.
*/
this.validateInteger = function(reqTF, minval, maxval, negTF, AOR){
// if the field is populated (reqd or not) then it must be a valid
// integer.  The field may only be unpopulated (null, undef, "")
// if it is optional
if(!reqTF){
if(ty == "undefined"){return true;}
if(ty == "null"){return true;}
if(val == null){return true;}
if(val == ""){return true;}}

// if the field is required, or if it has any other typeof value,
// then it must be valid...

// convert value to an integer
var n = parseInt(val, 10);
var amsg = "";
var atf = true;

// check for valid integer. If n is NaN, there is no sense checking
// any of the checks that come after.  So always exit after 
// showValidationProblem.
if(isNaN(n)){
  amsg = "must be a valid whole number.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}

// make sure the integer created by parseInt is the same as val
// if it's not, parseInt truncated or converted it.
if(n != val){
  amsg = "(" + val + ") is not a valid whole number."
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}

// at this point you know that you have an integer. Now make sure
// it is in range
if(n < 0){if(!negTF){
  amsg = "may not be negative.";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}

// check for minimum violation
if(minval != null){
if(n < minval){
  amsg = "may not be less than " + minval + ".";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}


// check for maximum violation
if(maxval != null){
if(n > maxval){
  amsg = "may not be more than " + maxval + ".";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}

// all checks passed
return true;
}  // end method validateInteger


// method to validate float. Returns true or false
/* PARAMS:
reqTF: Truthy if the field cannot be empty.
minval: minimum acceptable value. Must be null if n/a.
maxval: maximum acceptable value. Must be null if n/a.
negTF: Truthy if negative values are allowed.
AOR (boolean): Allow user to override this error
RETURNS: True if all validation checks pass.
*/
this.validateFloat = function(reqTF, minval, maxval, negTF, AOR){
// if the field is populated (reqd or not) then it must be a valid
// integer.  The field may only be unpopulated (i.e., null, undef, "")
// if it is optional
if(!reqTF){
if(ty == "undefined"){return true;}
if(ty == "null"){return true;}
if(val == undefined){return true;}
if(val == null){return true;}
if(val == ""){return true;}}

// if you get to this point the field must be a valid float, 
// whether reqd or not...

// convert value to an float
var n = parseFloat(val);
var amsg = "";
atf = true;

// check for valid float. If not a valid float, no further checking
// can happen even if overrides are allowed.
if(isNaN(n)){
  amsg = "must be a valid number.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}

// make sure the float created by parsefloat is the same as val
// if it's not, parseFloat truncated val. If this check fails,
// perform no other checks even if overrides are allowed.
if(n != val){
  amsg = "(" + val + ") must be a valid number.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}

// at this point you know that you have an float. Now make sure
// it is in range
if(n < 0){if(!negTF){
  amsg = "may not be negative.";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}

// check for minimum violation
if(minval != null){
if(n < minval){
  amsg = "may not be less than " + minval + ".";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}

// check for maximum violation
if(maxval != null){
if(n > maxval){
  amsg = "may not be more than " + maxval + ".";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}

// all checks passed
return true;
}  // end method validateFloat


// method to validate String. Returns true (=valid) or false
/* PARAMS:
reqTF: Truthy if the field cannot be empty.
minlen: minimum string length. Only checked if field is req'd.
        Must be null if n/a.
maxlen: maximum string length. Must be null if n/a.
AOR (boolean): Allow user to override this error
RETURNS: True if all validation checks pass.
*/
this.validateString = function(reqTF, minlen, maxlen, AOR){
// if the field is populated (reqd or not) then it must be a valid
// integer.  The field may only be unpopulated (i.e., null, undef, "")
// if it is optional
if(!reqTF){
if(ty == "undefined"){return true;}
if(ty == "null"){return true;}
if(val == undefined){return true;}
if(val == null){return true;}
if(val == ""){return true;}}

// if you get to this point the field must be a valid (non-empty)
// string, whether reqd or not...

// convert value to an float
var s = new String(val);
var L = s.length;
var amsg = "";
var atf = true;

// make sure string is present. Exit after this check. If the string
// is absent, there is nother else to check, regardless of override
// status
if(reqTF){
if(!L){
  amsg = "may not be blank.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}}

// check for minimum length
if(reqTF){
if(minlen != null){
var minL = parseInt(minlen, 10);

if(isNaN(minL)){
  amsg = "The minimum length for " + na + "(" + minlen + ") is not valid.";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}
else if(L < minL){
  if(minL == 1){
    amsg = "must have at least 1 character.";}
  else {
    amsg = "must have at least " + minlen + " characters."}
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}}

// check for max length
if(maxlen != null){
var maxL = parseInt(maxlen, 10);

if(isNaN(maxL)){
  amsg = "The maximum length for " + na + "(" + maxlen + ") is not valid.";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}
else if(L > maxL){
  if(maxL == 1){
    amsg = "may not have more than 1 character.";}
  else {
    amsg = "may not have more than " + maxL + " characters.";}
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}

// all checks passed
return true;
}  // end method validateString


// method to validate Date. Returns true (=valid) or false
/* PARAMS:
reqTF: Truthy if the field cannot be empty.
mindate: minimum date. May be a date string or a ts. (Anthing that
         is valid for the Date constructor.) Must be null if n/a.
maxdate: maximum date. May be a date or a ts. (Anthing that
         is valid for the Date constructor.) Must be null if n/a.
AOR (boolean): Allow user to override this error
RETURNS: True if all validation checks pass.
*/
this.validateDate = function(reqTF, mindate, maxdate, AOR){
// The field may only be unpopulated (i.e., null, undef, "")
// if it is optional
if(!reqTF){
if(ty == "undefined"){return true;}
if(ty == "null"){return true;}
if(val == undefined){return true;}
if(val == null){return true;}
if(val == ""){return true;}}

// if you get to this point the field must be a valid (non-empty)
// date, whether reqd or not...
var amsg = "";
var atf = true;

// make sure date string is present
if(reqTF){
if(!val){
  amsg = "may not be blank.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}}

// convert value to an float
var s = new Date(val);
var dastr = "";
var md = null;

// check for failed date conversion
if(s == undefined){
  amsg = "is not a valid date.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}
else if(s == null){
  amsg = "is an invalid date.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}
else if(typeof s != "object"){
  amsg = "does not contain a valid date.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}
else if(!(s instanceof Date)){
  amsg = "contains an invalid date.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}
else if(isNaN(s)){
  amsg = "shows an invalid date.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}

// check for minimum date
if(mindate){
md = new Date(mindate);
if(md instanceof Date){
dastr = isNaN(mindate)? mindate: md.toString();
if(s < md){
  amsg = "may not be before " + dastr + ".";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}}

// check for minimum date
if(maxdate){
md = new Date(maxdate);
if(md instanceof Date){
dastr = isNaN(maxdate)? maxdate: md.toString();
if(s > md){
  amsg = "may not be after " + dastr;
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}}

// all checks passed
return true;
}  // end method validateDate


// method to validate email address. Returns true (=valid) or false
/* PARAMS:
reqTF: Truthy if the field cannot be empty.
minlen: minimum string length. Only checked if field is req'd.
        May be falsy if n/a.
maxlen: maximum string length. May be falsy if n/a.
AOR (boolean): Allow user to override this error
RETURNS: True if all validation checks pass.
*/
this.validateEmail = function(reqTF, minlen, maxlen, AOR){
// if the field is populated (reqd or not) then it must be a valid
// integer.  The field may only be unpopulated (i.e., null, undef, "")
// if it is optional
if(!reqTF){
if(ty == "undefined"){return true;}
if(ty == "null"){return true;}
if(val == undefined){return true;}
if(val == null){return true;}
if(val == ""){return true;}}

// if you get to this point the field must be a valid (non-empty)
// string, whether reqd or not...

// convert value to astring
var s = new String(val);
var L = s.length;
var amsg = ""
var atf = true;

// if the string is required
if(reqTF){

// make sure length is more than 0
if(!L){
  amsg = "may not be blank.";
  atf = showValidationProblem(amsg, AOR);
  return atf;}

// check for minimum length
if(minlen != null){
var minL = parseInt(minlen, 10);

// if minimum length is not a valid integer
if(isNaN(minL)){
  amsg = "The minimum length for " + na + "(" + minlen + ") is not valid.";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}
else if(L < minL){
  if(minL == 1){
    amsg = "must have at least 1 character.";}
  else {
    amsg = "must have at least " + minlen + " characters.";}
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}}

// check for max length
if(maxlen != null){
var maxL = parseInt(maxlen, 10);

// if maximum length is not valid
if(isNaN(maxL)){
  amsg = "The maximum length for " + na + "(" + maxlen + ") is not valid.";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}
else if(L > maxL){
  if(maxL == 1){
    amsg = "may not have more than 1 character.";}
  else {
    amsg = "may not have more than " + maxlen + " characters.";}
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}}

// make sure the string is a valid email address
var r = s.match(/^[^@\n]+@[^\n@\s]+\.{1}\w{2,5}$/i);
if(!r){
  amsg = "is not a valid email address.";
  atf = showValidationProblem(amsg, AOR);
  if(!atf){return false;}}

// all checks passed
return true;
}  // end method validateEmail


function showValidationProblem(s, AOR){
/* Allow Override = AOR (boolean) 
ele and na (field name) is a module level variable that is not 
sent in call*/

// if the field name was not included, there can't be an error 
// message
if(!na){
  ele.focus();
  return false;}

// if there is a field name, but allow override is not true,
// alert for the problem and return false
else if(!AOR){
  alert(na + " " + s);
  ele.focus();
  return false;}

// if there is a field name and override is allowed, prompt
else {
  var rv = confirm(na + " " + s + 
  "\n\nIgnore this check and continue?");
  if(!rv){
    ele.focus();
    return false;}}

// if you get here, the user opted to ignore the check
return true;
} // end fcn showValidationProblem 

this.returnFieldObject = function(){
if(typeof ele == "object"){return ele;}
return null;
}  // end method returnFieldObject
}  // end constructor FieldValidator


function cleanPhone(p){
var q = p.replace(/[^0-9\-]+/g, "-");
var r = q.replace(/^[^0-9]+|[^0-9]+$/g, "");
return r;
}  // end fcn cleanPhone


/* 
Deep clones an array.  Only actual arrays are interated
and copied.  Other non-primitives (objects) are copied by 
ref. Note that Array.isList does NOT consider a node list
to be an array. 
PARAMS: R: the array to clone;
        intOnly: if T, only integer indexes are copied.
RETURNS: A new array that is a copy of the original.
         Only leaf nodes with primitives are copied.
         Array elements that contain
         anything else (e.g., objects, node lists, functions, 
         etc.) are referenced.
         Undefined array elements are not copied.
*/
function deepCloneArray(R, intOnly){

// build a container for the clone. This function should only be
// sent an array or an object.  So the last else is JIC.
if(Array.isArray(R)){var RV = new Array();}
else if(typeof R == "object"){var RV = {};}
else {return R;}
var n = 0;

// scan the outer array
for(var i in R){
if(!R.hasOwnProperty(i)){continue;}
if(typeof R[i] == "undefined"){continue;}

//skip non-integer indexes
if(intOnly){
n = parseInt(i);
if(isNaN(n)){continue;}}

// if the element is another array or object: deep clone it
// otherwise just copy it
if(Array.isArray(R[i])){RV[i] = deepCloneArray(R[i]);}
else if(typeof R[i] == "object"){RV[i] = deepCloneArray(R[i]);}
else {RV[i] = R[i];}}

// return the cloned array
return RV;
}  // end fcn deepCloneArray


function elide(s, maxlen, cutat){
var t = s.toString();
var L = t.length;
var m = parseInt(maxlen);
var c = parseInt(cutat);
if(isNaN(m)){return s;}
if(L <= m){return s;}
if(isNaN(c)){return s;}
var u = t.substr(0, cutat);
var v = JStrim(u);
var w = v + "...";
return w;
}  // end fcn elide


function arrayToNumberedList(A){
var B = new Array();

// take care of edge cases
if(!A){return "";}
if(typeof A == "string"){return A;}
if(!Array.isArray(A)){return "";}

// check array length
var L = A.length;
if(!L){return "";}
if(L == 1){return A[0];}
var i = 0;
var j = 0;

// scan the array; add list number to each element
for(i = 0; i < L; i++){
j = i + 1;
B[i] = j + ". " + A[i];}

// concatenate and return array elements
var s = A.join("\n\n");
return s;
}  // end fcn arrayToNumberedList


function arrayToHTMLNumberedList(A){
var B = new Array();

// take care of edge cases
if(!A){return "";}
if(typeof A == "string"){return A;}
if(!Array.isArray(A)){return "";}

// check array length
var L = A.length;
if(!L){return "";}
if(L == 1){return A[0];}
var i = 0;
var j = 0;
var s = "";

// scan the array; wrap each element in li tags
for(i = 0; i < L; i++){
s = A[i].toString();
A[i] = s.trim();
B[i] = "<li>" + A[i] + "</li>";}

// concatenate and return array elements
var s = A.join("\n");
var rv = "<ol>\n" + s + "\n</ol>";
return rv;
}  // end fcn arrayToHTMLNumberedList


/* POPUP MESSAGE */
function PopupMsg(){

// stash x-index
if(!PopupMsg.zorder){PopupMsg.zorder = 600;}

// private inner fcn to create base dialog
function baseDialog(imgSrc){

// create and style containing div
var condiv = document.createElement("div");
condiv.style.display = "none";
condiv.style.position = "fixed";
condiv.style.boxSizing = "border-box";
condiv.style.maxWidth = "50%";
condiv.style.borderSize = "2px";
condiv.style.borderStyle = "outset";
condiv.style.backgroundColor = "#e2dccb";
condiv.style.margin = "0";
condiv.style.padding = "0.1em";
condiv.style.fontFamily = "Arial, sans-serif";
condiv.style.boxShadow = "#808080 2px 2px 8px";
PopupMsg.zorder++;
condiv.style.zIndex = PopupMsg.zorder;

// create, style, and add title bar
var tibar = document.createElement("div");
tibar.style.margin = "0.25em 0.25em 1.5em";
tibar.style.boxSizing = "border-box";
tibar.style.padding = "0.1em 0.25em";
tibar.style.fontWeight = "bold";
tibar.style.color = "#ffffff";
var ligra = "linear-gradient(90deg, #00baf3 10%, #9a9c83 80%, #cd6a2c 100%)";
tibar.style.backgroundImage = ligra;
tibar = condiv.appendChild(tibar);

// create, style, and add outer message div
var msgdiv = document.createElement("div");
msgdiv.style.margin = "1.5em 1em 1em";
msgdiv.style.padding = "0";
msgdiv.style.backgroundColor = "transparent";
msgdiv.style.borderStyle = "none";
msgdiv = condiv.appendChild(msgdiv);

// create, style, and add image to msgdiv
if(imgSrc){
var logo = document.createElement("img");
logo.src = imgSrc;
logo.style.float = "left";
logo.style.margin = "0 1em 1em 0";
msgdiv.appendChild(logo);}

// add a p element to msgdiv for actual text
var pmsg = document.createElement("p");
pmsg.style.backgroundColor = "transparent";
pmsg.style.borderStyle = "none";
pmsg.style.display = "block";
pmsg.style.marginLeft = "75px";
pmsg = msgdiv.appendChild(pmsg);

// create, style, and add a button div
var bdiv = document.createElement("div");
bdiv.style.margin = "2em 1em 1em";
bdiv.style.padding = "0";
bdiv.style.textAlign = "center";
bdiv.style.backgroundColor = "transparent";
bdiv.style.borderStyle = "none";
bdiv.style.whiteSpace = "nowrap"
bdiv = condiv.appendChild(bdiv);

// create, style, and add OK button
var okbut = document.createElement("button");
okbut.type = "button";
okbut.className = "pumsgok";
okbut.style.margin = "0 1.5em";
okbut.style.textAlign = "center";
okbut.style.fontWeight = "bold";
okbut = bdiv.appendChild(okbut);

// add OK text to OK button
var tn = document.createTextNode("OK");
okbut.appendChild(tn);

// return assoc array of elements
var rv = {"condiv":condiv, "tibar":tibar, "pmsg":pmsg, 
"bdiv":bdiv, "okbut":okbut, "msgdiv":msgdiv};
return rv; 
}  // end inner fcn baseDialog


// inner fcn to make an alert dialog
function makeAlert(imgSrc){
var ao = baseDialog(imgSrc);

// add dismiss handler to ok button
ao.okbut.onclick = dismiss;

// add div to the document
ao.condiv = document.body.appendChild(ao.condiv);

// make the dialog dragable
var DZ = new Dragalizer();
if(DZ){
DZ.makeDragable(ao.condiv);
ao.tibar.style.cursor = "move";}

// return the summary object
return ao;
}  // end fcn makeAlert


// inner fcn to make a confirm dialog
function makeConfirm(imgSrc){
var ao = baseDialog(imgSrc);

// create and add cancel button
var cabut = document.createElement("button");
cabut.type = "button";
cabut.className = "pumsgca";
cabut.style.margin = "0 1.5em";
cabut.style.textAlign = "center";
cabut.style.fontWeight = "bold";

// add the cancel button to the summary object and to the button div
cabut = ao.bdiv.appendChild(cabut);
ao.cabut = cabut;

// assign dismiss handler to cancel button. This is JIC, it should
// be overridden using returnDismissFcn(div, ip) once the input
// element to contain the result is known
cabut.onclick = dismiss;

// add Cancel text to cancel button
var tn = document.createTextNode("Cancel");
cabut.appendChild(tn);

// add div to the document
ao.condiv = document.body.appendChild(ao.condiv);

// make the dialog dragable
var DZ = new Dragalizer();
if(DZ){
DZ.makeDragable(ao.condiv);
ao.tibar.style.cursor = "move";}

// return the summary object
return ao;
}  // end fcn makeConfirm


// inner fcn to make a confirm dialog
function makePrompt(imgSrc){
var ao = makeConfirm(imgSrc);

// create and add input element for text
var ip = document.createElement("input");
ip.type = "text";
ip.style.boxSizing = "border-box";
ip.style.width = "99%";
ip.style.margin = "1em 0 0.25em";

// add the input element to the summary object and msgdiv
ip = ao.msgdiv.appendChild(ip);
ao.ipele = ip;

// create and style a p element for length limit feedback
var fbp = document.createElement("p");
fbp.style.position = "absolute";
fbp.style.color = "#303030";
fbp.style.fontSize = "0.75em";
fbp.style.margin = "0.1em 0 1em 1em";

// add to msgdiv below the input element
fbp = ao.msgdiv.appendChild(fbp);
ao.fbp = fbp;

// add div to the document
ao.condiv = document.body.appendChild(ao.condiv);

// make the dialog dragable
var DZ = new Dragalizer();
if(DZ){
DZ.makeDragable(ao.condiv);
ao.tibar.style.cursor = "move";}

// return the summary object
return ao;
}  // end fcn makePrompt


function dismiss(){
// reference the grandfather of the "cancel" button (the dialog)
var d = this.parentNode.parentNode

// hide and remove the dialog
d.style.display = "none";
d.parentNode.removeChild(d);
}  // end fcn dismiss


function returnDismissFcn(div, dataip, cba){
/* PARAMS:
div: The confirm or return dialog div
dataip: The input element to clear.
cba: A callback function to call after cancel is selected but before
     the dialog is hidden. The "this" variable is set to the input
     element that is to contain the selection.
RETURNS: Nothing. Sets the ip value to MT str and hides/removes 
the dialog from the DOM hierarchy. */

// create anonymous inner fcn to return
var f = function(){

// clear the input element
if(dataip){if("value" in dataip){dataip.value = "";}}

// call the callback fcn
if(cba){cba.call(dataip);}

// remove the div from DOM hierarchy
div.style.display = "none";
div.parentNode.removeChild(div);
}  // remove anonymous inner fcn

// return the anon fcn
return f;
}  // end fcn returnDismissFcn


// public method to show a centered alert
/* PARAMS
tibar: text for the title bar
msg: text for the message p
evt (opt): onclick event, if available, for quadrant positioning
           of the alert.
RETURNS: An object with the following properties:
condiv: The overall, abs positioned dialog.
tibar: A p element to contain dialog title.
pmsg: A p element containing the user message
ipele: An input element (prompt method only)
fbp: A div with 0.9em font giving feedback on ipele length limits
msgdiv: A containing div for img, pmsg, ipele, and fbp
bdiv: A div to contain centered buttons
okbut: OK button
cabut: Cancel button (not used on alert)
*/
this.alert = function(tibar, msg, evt){
// makeAlert creates a new dialog AND adds it to document
var ao = makeAlert("images/adobevsm.png");
// configure the alert dialog
ao.pmsg.innerHTML = msg;
ao.tibar.innerHTML = tibar;

// position dialog
quadrantPosition(evt, ao.condiv);

// show dialog, fix its size, and return it
ao.condiv.style.display = "block";
fixSize(ao.condiv);
return ao;
}  // end method alert


function fixSize(ele){
// alert("offsetHeight: " + ele.offsetHeight + "\noffsetWidth: " + ele.offsetWidth);
// return;

// prevent resizing when the dialog is moved (must be called after
// the dialog is displayed)...
// set max width
if(ele.offsetWidth){
  ele.style.width = ele.offsetWidth + "px";}
else if(ele.getBoundingClientRect){
  var br = ele.getBoundingClientRect();
  ele.style.weight = br.width + "px";}

// set max height
if(ele.offsetHeight){
  ele.style.maxHeight = ele.offsetHeight + "px";}
else if(br){
  ele.style.maxHeight = br.height + "px";}
else if(ele.getBoundingClientRect){
  var br = ele.getBoundingClientRect();
  ele.style.maxHeight = br.height + "px";}
}  // end fcn fixSize


// public method to show a centered confirm box
/* PARAMS:
tibar: The title bar text
msg: The prompt text
dataEle: The input element to be populated with the user selection.
         Maybe id string or object reference
         ok="1", ca="";
cbok: A function to be called after after the OK button is clicked 
     but before the dialog is hidden. The function "this" variable is
     set to dataEle.
cbca: A function to be called after the cancel button is clicked
     but before the dialog is hidden. The function "this" variable is
     set to dataEle.
evt (opt): event object from onclick or mouse event.  If included,
           the confirm box is positioned at the click point using
           quadrant positioning.
RETURNS: An object with the following properties:
condiv: The overall, abs positioned dialog.
tibar: A p element to contain dialog title.
pmsg: A p element containing the user message
ipele: An input element (prompt method only)
fbp: A div with 0.9em font giving feedback on ipele length limits
msgdiv: A containing div for img, pmsg, ipele, and fbp
bdiv: A div to contain centered buttons
okbut: OK button
cabut: Cancel button (not used on alert)
*/
this.confirm = function(tibar, msg, dataEle, cbok, cbca, evt){
var ao = makeConfirm("images/adobevsm.png");

// resolve dataEle reference
dataEle = returnObjRef(dataEle);

// assign onclick handler to buttons
ao.okbut.onclick = returnOkFunction(dataEle, cbok, null);
ao.cabut.onclick = returnDismissFcn(ao.condiv, dataEle, cbca);
ao.pmsg.innerHTML = msg;
ao.tibar.innerHTML = tibar;

// position dialog
quadrantPosition(evt, ao.condiv);

// show dialog, fix its size, and return it
ao.condiv.style.display = "block";
fixSize(ao.condiv);
return ao;
}  // end public method confirm 


// public method to show a centered confirm box
/* PARAMS:
tibar: The title bar text
msg: The prompt text (HTML)
defv: Default text for input box
dataEle: The input element to be populated with the user selection.
         Maybe id string or object reference
         ok="1", ca="";
cbok: A function to be called after after the OK button is clicked 
     but before the dialog is hidden. The function "this" variable is
     set to dataEle.
cbca: A function to be called after the cancel button is clicked
     but before the dialog is hidden. The function "this" variable is
     set to dataEle.
evt (opt): event object from onclick or mouse event.  If included,
           the confirm box is positioned at the click point using
           quadrant positioning.
RETURNS: An object with the following properties:
condiv: The overall, abs positioned dialog.
tibar: A p element to contain dialog title.
pmsg: A p element containing the user message
ipele: An input element (prompt method only)
fbp: A div with 0.9em font giving feedback on ipele length limits
msgdiv: A containing div for img, pmsg, ipele, and fbp
bdiv: A div to contain centered buttons
okbut: OK button
cabut: Cancel button (not used on alert)
*/
this.prompt = function(tibar, msg, defv, dataEle, cbok, cbca, evt){
var ao = makePrompt("images/adobevsm.png");

// make sure default is a string
var def = defv.toString();
def = def.replace(/^\s+|\s+$/g, "");
if(def){ao.ipele.value = def;}

// resolve dataEle reference
dataEle = returnObjRef(dataEle);

// assign onclick handler to buttons
ao.okbut.onclick = returnOkFunction(dataEle, cbok, ao.ipele);
ao.cabut.onclick = returnDismissFcn(ao.condiv, dataEle, cbca);
ao.pmsg.innerHTML = msg;
ao.tibar.innerHTML = tibar;

// position dialog
quadrantPosition(evt, ao.condiv);

// show dialog, fix its size, and return it
ao.condiv.style.display = "block";
fixSize(ao.condiv);
ao.ipele.select();
ao.ipele.focus();
return ao;
}  // end public method prompt 


// inner function to assign event handler to OK button
/* PARAM
dataEle (opt): An input element where the OK/Cancel results are 
               stored. May be an object variable or the element id 
               as a string. If null, the fact that the user selected
               OK can be discerned from the fact that the ok call-
               back fcn is being called.
cba: A callback function that is called when input element has been
     populated. The "this" variable is set to dataEle (or window
     if dataEle is null).
RETURN: Nothing directly, but the dataEle input element is set to 1
        for OK and 0 for cancel. */
function returnOkFunction(dataEle, cbok, dataSrc){

// resolve dataEle
var obref = returnObjRef(dataEle);

// create a function that: (1) sets dataEle value; (2) hides the
// dialog; (3) calls cba.
var f = function(){

// resolve the value to save and save it
if(!dataSrc){var v = "1";}
else if("value" in dataSrc){var v = dataSrc.value;}
else {var v = "Error";}
if(obref){obref.value = v;}

// if obref is null, set it to window just for the cbok call
if(!obref){obref = window;}

// call the callback and remove the dialog
if(cbok){if(typeof cbok == "function"){cbok.call(obref);}}
dismiss.call(this);
}  // end anonymous inner fcn

// return the anonymous inner fcn
return f;
}  // end fcn returnOkFunction


// inner function to position popup
function quadrantPosition(ev, ele){
/* Sets the left and top properties of an element, based on the 
quandrant that a click event occurred in.
PARAMS:
ev: An onclick event.  The event type is not checked by this fcn.
    If it was not an event that includes mouse coordinates, 
    results will be unpredictable.
ele: The element to position. Must already be absolutely positioned
     and an immediate child of the <body> element.  
RETURN: Nothing. Only the style.top and style.left properties are 
        changed. The element's display and position properties are
        not changed. */

// if ev(ent) is empty, then just center the element on the screen
if(!ev){
ele.style.top = "50%";
ele.style.left = "50%";
ele.style.transform = "translateX(-50%) translateY(-50%)";
return;}

// resolve evt, just in case
var evt = ev || window.event;

// get the event coordinates relative to page
var x = 0;
var y = 0;
if(evt.pageX){
  x = evt.pageX;
  y = evt.pageY;}
else {
  docscr.getDocScroll();
  x = evt.clientX + docscr.left;
  y = evt.clientY + docscr.top;}

// window size and half-way cutoff positions
var ww = window.innerWidth;
var wh = window.innerHeight;
var cox = parseInt(ww / 2);
var coy = parseInt(wh / 2);

// position the element vertically (decide quadrant based on
// window position, but set position based on abs. posn)
if(evt.clientY >= coy){
  ele.style.top = "auto";
  ele.style.bottom = (wh - y) + "px";}
else {
  ele.style.top = y + "px";
  ele.style.bottom = "auto";}

// position the element horizontally
if(evt.clientX >= cox){
  ele.style.left = "auto";
  ele.style.right = (ww - x) + "px";}
else {
  ele.style.left = x + "px";
  ele.style.right = "auto";}
}  // end inner fcn quadrantPosition
}  // end constructor PopupMsg


function IsDate(s){
/* PARAMS: s: A date string.
RETRNS: 0 if s is not a valid date. Otherwise, returns
a JS ts for the date. */
if(s.length == 0){return 0;}
var d = new Date(s);
if(isNaN(d)){return 0;}
if(d.getTime){
var gt = d.getTime();
return gt;}
return 0;
} // end fcn IsDate



/* DRAGALIZER */
/* Has a single makeDragable method that accepts a single argument: 
The element to be made dragable. The element must:
1) Be a direct child of the body element
2) Have position:absolute.
Other configurations will give unpredictable results.
*/

function Dragalizer(){

this.makeDragable = function(ele){

// set up element
ele.style.position = "absolute";
ele.draggable = true;
ele.ondragstart = eleOnmouseDown;

// make sure element is a direct child of body
if(ele.parentNode != document.body){
ele = ele.parentNode.removeChild(ele);
document.body.appendChild(ele);}

// set up the document as drop target
document.documentElement.ondragenter = dragEnter;
document.documentElement.ondragover = dragOver;
document.documentElement.ondrop = eleOnmouseUp;


function eleOnmouseDown(evt){
var xy = new Array();
var mRelDoc = new Array();

// resolve event object
var e = evt || window.event;
if(!e){return;}

// set up data xfer object
if(!e.dataTransfer){return;}
var dt = e.dataTransfer;
dt.effectAllowed = "move";

// mouse x-coordinate relative to document
if(e.pageX){
  xy[0] = parseInt(e.pageX);
  xy[1] = parseInt(e.pageY);}
else {
  var docscr = getDocScroll();
  xy[0] = e.clientX + docscr.left;
  xy[1] = e.clientY + docscr.top;}

// get dialog x & y coordinate relative to doc
xy[2] = getBodyOffsetX(this);
xy[3] = getBodyOffsetY(this)

for(var i = 0; i < 4; i++){
  xy[i] = parseInt(xy[i]);
  if(isNaN(xy[i])){xy[i] = 0;}}  

// calculate mouse coordinates relative to element
mRelDoc[0] = xy[0] - xy[2];
mRelDoc[1] = xy[1] - xy[3];

// store original coordiates in dataTransfer object
var s = mRelDoc.join(",");
dt.setData("text/plain", s);
}  // end inner fcn eleOnmouseDown


function eleOnmouseUp(evt){
// resolve element
var e = evt || window.event;
if(!e){return;}

// prevent default and stop propagation (otherwise FF assumes
// the data is a URL and it tries to change pages).
if(e.preventDefault){e.preventDefault();}
else if("returnValue" in e){e.returnValue = true;}
if(e.stopPropagation){e.stopPropagation();}
else if("cancelBubble" in e){e.cancelBubble = true;}

// get the dataTransfer object and its data: 
// mouse coordinate relative to element
var dt = e.dataTransfer
var s = dt.getData("text/plain");
var xy = s.split(",");

// get the mouse x-coordinates relative to doc
if(e.pageX){
  var mx = e.pageX;
  var my = e.pageY;}
else {
  var docscr = getDocScroll();
  var mx = e.clientX + docscr.left;
  var my = e.clientY + docscr.top;}

// make sure mouse coordinates are integers
mx = parseInt(mx);
if(isNaN(mx)){mx = 0;}
my = parseInt(my);
if(isNaN(my)){my = 0;}

// calculate the new element position
var dx = mx - xy[0];
var dy = my - xy[1];

// update the element position
ele.style.left = dx + "px";
ele.style.top = dy + "px";
}  // end inner fcn eleOnmouseUp


function dragOver(evt){
// resolve event object
var e = evt || window.event;
if(!e){return;}

// prevent default action as a way of telling browser that the 
// target is "interested in" the drag
if(e.preventDefault){e.preventDefault();}
else if("returnValue" in e){e.returnValue = true;}
else {return false;}
}  // end inner fcn dragOver


function dragEnter(evt){
// resolve the event object
var e = evt || window.event;
if(!e){return;}
e.dataTransfer.dropEffect = "move";
}  // end inner fcn dragEnter
}  // end method makeDragable
}  // end constructor Dragalizer


function getBodyOffsetX(ele){
if(typeof ele == "string"){ele = document.getElementById(ele);}
var g = ele;
var x = ele.offsetLeft;
while(g.offsetParent){
x += g.offsetParent.offsetLeft;
g = g.offsetParent;}
return x;
}  // end fcn getBodyOffsetX


function getBodyOffsetY(ele){
if(typeof ele == "string"){ele = document.getElementById(ele);}
var g = ele;
var y = ele.offsetTop;
while(g.offsetParent){
y += g.offsetParent.offsetTop;
g = g.offsetParent;}
return y;
}  // end fcn getBodyOffsetY


function SpeechBalloon(bkgdCo, bdrCo, shadCo, cloClana){
/* Colors must include hash. All are opt except bkgd.
cloClana: Optional class name for the close div.  Can be used for
additional styling (esp. :hover).  Caution: It looks like className
styles cannot override styles set in js.
1. Create a speech balloon object, passing backgroundColor, 
borderColor (optional), shadowColor (optional), and class name for
the Close div (optional).
2. Customize the borderWidth, shadowWidth, and textColor properties 
   if needed. Width properties are expressed as a pure number (in pixes)
3. Call show method, passing text and element to attach it to.
*/

this.borderWidth = "1";
this.shadowWidth = "1";
this.textColor = "#000000";
this.textSizeFactor = 0.9;

// create paragraph for text
var para = document.createElement("p");
para.style.padding = "0";
para.style.margin = "0";
para.style.position = "relative";
para.style.color = this.textColor;

// create main balloon
var divouter = document.createElement("div");
divouter.style.position = "absolute";
divouter.style.maxWidth = "30%";
if(bkgdCo){divouter.style.backgroundColor = bkgdCo;}
if(bdrCo){divouter.style.border = this.borderWidth + "px solid " + bdrCo;}
if(shadCo){divouter.style.boxShadow = "0 0 1px " + 
     this.shadowWidth + "px " + shadCo;}
divouter.style.margin = "0";
divouter.style.padding = "0.25em";
divouter.style.visibility = "hidden";
divouter.style.opacity = "0";
divouter.style.transition = "opacity 1s ease 10ms, clip 2s";

// create triangle
var tria = document.createElement("div");
tria.style.position = "absolute";
tria.style.width = "1em";
tria.style.height = "1em";
tria.style.backgroundColor = bkgdCo;
tria.style.margin = "0";
tria.style.padding = "0";
tria.style.transform = "rotate(45deg)";

// set triangle border and size
tria.style.borderColor = bdrCo;
tria.style.borderWidth = this.shadowWidth + "px";

// create close div
var clo = document.createElement("div");
clo.style.position = "relative";
clo.style.float = "right";
clo.style.cursor = "pointer";
clo.onclick = closeBalloon;
if(cloClana){clo.className = cloClana;}
else {
  clo.style.backgroundColor = "#c0c0c0";
  clo.style.color = "#ffffff";
  clo.style.borderRadius = "5px";
  clo.style.border = "1px solid #bfbfbf";
  clo.style.fontSize = "0.9em";}

// create close text node
var tn = document.createTextNode("Close");

// add the triangle, paragraph, and close div to the outer div
clo.appendChild(tn);
clo = divouter.appendChild(clo);
para = divouter.appendChild(para);
tria = divouter.appendChild(tria);
document.body.appendChild(divouter);

// reset zindex
tria.style.zIndex = "1";
para.style.zIndex = "2";
clo.style.zIndex = "3";


//function to show balloon
this.show = function(ele, txt){

// get setable properties
var bw = this.borderWidth;
var w = this.shadowWidth;
var tc = this.textColor;
var z = this.textSizeFactor;

// reset properties for outerdiv and para
if(bdrCo){
  divouter.style.border = bw + "px solid " + bdrCo;
  tria.style.borderWidth = bw + "px";}
if(shadCo){
  divouter.style.boxShadow = "0 0 1px " + w + "px " + shadCo;}
para.style.color = tc;
divouter.style.fontSize = z + "em";

// resolve ele id if necessary
if(typeof ele == "string"){ele = document.getElementById(ele);}

// set paragraph text
var tn = document.createTextNode(txt);
para.appendChild(tn);

// get window size and half-way cutoff positions
var ww = window.innerWidth;
var wh = window.innerHeight;
var cox = parseInt(ww / 2);
var coy = parseInt(wh / 2);

// get document coordinates (left,top) of element to point to
var x = getBodyOffsetX(ele);
var y = getBodyOffsetY(ele);
var eleh = ele.offsetHeight;
var elew = ele.offsetWidth;

// get window coordinates of element to point to
// get scroll amounts
var br = ele.getBoundingClientRect();
var docscr = getDocScroll();

// get height of balloon + triangle
var bh = divouter.offsetHeight;
var th = tria.offsetHeight;
var ab = 2 * th * th;
var diag = Math.sqrt(ab);
var diaghalf = diag / 2
var ttlheight = bh + diaghalf;

// calculate triangle vertical shift
var tvs = Math.ceil(th / 2);
if(bdrCo){tvs += parseInt(bw, 10);}

/* when positioning an absolutely positioned child of <body>, it
must be set relative to the INITIAL CONTAINING BLOCK; i.e., the 
viewport with top at (0,0).

{            [   x        }            ]

<--   {init viewport}   -->

             <-- [scrolled viewport] -->
                 
                  x                                          Element

                  <- Ro -->                      Initial right value
    
                  <-------- Rf -------->           Final right value

<---------- ww ---------->                              Window Width

                          <---- sL ---->                  scrollLeft

Using above geography: Rf = Ro + sL

Solve for Ro (since that's what's needed to position ele after scroll):
Ro = Rf - sL

Expand Rf since you will often only have the LEFT position:
Ro = (ww - Lf) - sL

Therefore:
Given the Left coordinate that is desired under the current scroll,
determine the right coordinate relative to the initial containing 
block: 

Ro = ww - Lf - sL

If setting Left (or Top for that matter), the document position
will already equal the position relatie to the inital containing
block.
*/

// compare using viewport coordinates; set using coordinates
// relative to initial containing block
if(br.left <= cox){
  divouter.style.left = x + "px";
  divouter.style.right = "auto";
  tria.style.left = "1em";
  tria.style.right = "auto";}
else {
  var r = ww - br.left - elew - docscr.left;
  divouter.style.right = r + "px";
  tria.style.right = "1em";
  tria.style.left = "auto";}

// position traingle and popup vertically (vertical shift happens
// before the rotation)
if(br.top >= 2 * ttlheight){
  tria.style.top = "auto";
  tria.style.bottom = "-" + tvs + "px";
  var t = y - ttlheight;
  if(bdrCo){tria.style.borderStyle = "none solid solid none";}
  if(shadCo){
    var w = this.shadowWidth;
    tria.style.boxShadow = w + "px " + w + "px 1px " + shadCo;
    t -= parseInt(w, 10);}
  divouter.style.top = t + "px";
  divouter.style.bottom = "auto";}
else if(br.top <= coy){
  tria.style.top = "-" + tvs + "px";
  tria.style.bottom = "auto";
  var t = y + eleh + diaghalf;
  if(bdrCo){tria.style.borderStyle = "solid none none solid";}
  if(shadCo){
    var w = this.shadowWidth;
    tria.style.boxShadow = "-" + w + "px -" + w + "px 1px " + shadCo;
    t += parseInt(w, 10);}
  divouter.style.top = t + "px";
  divouter.style.bottom = "auto";}
else {
  tria.style.bottom = "-" + tvs + "px";
  var t = y - ttlheight;
  if(bdrCo){tria.style.borderStyle = "none solid solid none";}
  if(shadCo){
    var w = this.shadowWidth;
    tria.style.boxShadow = w + "px " + w + "px 1px " + shadCo;
    t -= parseInt(w, 10);}
  divouter.style.top = t + "px";
  divouter.style.bottom = "auto";}

// show the speech balloon
divouter.style.visibility = "visible";
divouter.style.opacity = "1";
}  // end inner fcn show


function closeBalloon(){
divouter.style.visibility = "hidden";
divouter.style.opacity = "0";
}  // end fcn closeBalloon
}  // end constructor SpeechBalloon


function getDocumentHW(){
// reference documentElement and body
var DE = document.documentElement;
var DB = document.body;

// get body bounding rectangle
if(DB.getBoundingClientRect){var Brect = DB.getBoundingClientRect();}
else {var Brect = {"height":0, "width":0};}

// get documentElement bounding rectangle
if(DE.getBoundingClientRect){var DErect = DE.getBoundingClientRect();}
else {var DErect = {"height":0, "width":0};}

// get scroll height
if(DB.scrollHeight){var DBsh = DB.scrollHeight;} else {var DBsh = 0;}
if(DE.scrollHeight){var DEsh = DE.scrollHeight;} else {var DEsh = 0;}

// get offset height
if(DB.offsetHeight){var DBoh = DB.offsetHeight;} else {var DBoh = 0;}
if(DE.offsetHeight){var DEoh = DE.offsetHeight;} else {var DEoh = 0;}

// get clientHeight
if(DB.clientHeight){var DBch = DB.clientHeight;} else {var DBch = 0;}
if(DE.clientHeight){var DEch = DE.clientHeight;} else {var DEch = 0;}

// get the max height and max width from all the above
var mh = Math.max(Brect.height, DErect.height, DBsh, DEsh, DBoh, DEoh, DBch, DEch);

// get scroll width
if(DB.scrollWidth){var DBsw = DB.scrollWidth;} else {var DBsw = 0;}
if(DE.scrollWidth){var DEsw = DE.scrollWidth;} else {var DEsw = 0;}

// get offset height
if(DB.offsetWidth){var DBow = DB.offsetWidth;} else {var DBow = 0;}
if(DE.offsetWidth){var DEow = DE.offsetWidth;} else {var DEow = 0;}

// get clientWidth
if(DB.clientWidth){var DBcw = DB.clientWidth;} else {var DBcw = 0;}
if(DE.clientWidth){var DEcw = DE.clientWidth;} else {var DEcw = 0;}

// get the max height and max width from all the above
var mw = Math.max(Brect.width, DErect.width, DBsw, DEsw, DBow, DEow, DBcw, DEcw);

// return object with document height and width
var rv = {"h":mh, "w":mw, "height":mh, "width":mw}
return rv;
}  // end fcn getDocumentHW


function getDocScroll(){
var x = 0;
var y = 0;

// get horizontal scroll
if(window.scrollX){x = window.scrollX;}
else if(window.pageXOffset){x = window.pageXOffset;}
else if(window.scrollLeft){x = window.scrollLeft;}
else if(document.documentElement.scrollLeft){
  x = document.documentElement.scrollLeft;}
else if(document.body.scrollLeft){x = document.body.scrollLeft;}

// get vertical scroll
if(window.scrollY){y = window.scrollY;}
else if(window.pageYOffset){y = window.pageYOffset;}
else if(window.scrollTop){y = window.scrollTop;}
else if(document.documentElement.scrollTop){
  y = document.documentElement.scrollTop;}
else if(document.body.scrollTop){y = document.body.scrollTop;}

// return object
var rv = {"left":x, "l":x, "x":x, "top":y, "t":y, "y":y, 
  "scrollLeft":x, "scrollTop":y};
return rv;
}  // end fcn getDocScroll


function getCookieVal(d){
var c = document.cookie;
if(!c){return "";}
var nvp = null;
var s = null;

// split cookie string into segments at ;
var seg = c.split(";");
var L = seg.length;
for(var i = 0; i < L; i++){

// split each segment into nvp at =
if(seg[i]){
s = seg[i];
nvp = s.split("=");

// trim name and value
nvp[0] = JStrim(nvp[0]);
nvp[1] = JStrim(nvp[1]);

// check for stored uid
if(nvp[0]){
if(nvp[0] == d){
if(nvp[1]){
  s = decodeURIComponent(nvp[1]);
  return s;}
if(typeof nvp[1] == "undefined"){return "";}
if(nvp[1] == null){return "";}
if(nvp[1] == "0"){return "0";}
if(nvp[1] == 0){return "0";}}}}}

// return default
return "";
}  // end fcn getCookieVal


function saveCookieVal(na, va, minToExp){
// validate name. val may be empty or zero.
if(!na){return;}
var nvp = new Array();
var s;

// make sure minToExp is an integer
minToExp = parseInt(minToExp);
if(isNaN(minToExp)){minToExp = 0;}
var secToExp = minToExp * 60;

// ecode and concatenate nvp
na = encodeURIComponent(na);
va = encodeURIComponent(va);
nvp[nvp.length] = na + "=" + va;

// determine GMT string for expiration date
if(!secToExp){gmt = null;}
else {
var D = new Date();
var ts = D.getTime() + (secToExp * 1000);
D.setTime(ts);
var gmt = D.toGMTString();
gmt = encodeURIComponent(gmt);}

// add expiration
/* 
max-age: standards compliant, sec, not supported by IE
expires: deprecated, GMT string, supported by most browsers
*/
if(gmt){
nvp[nvp.length] = "expires=" + gmt + ";max-age=" + secToExp;}

// set cookie (return nvp for t/s)
s = nvp.join(";");
document.cookie = s;
return s;
}  // end fcn saveCookieVal


