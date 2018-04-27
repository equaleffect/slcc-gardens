$(document).ready(function () {
    (function () {
        // assign the stored username to the anchor tags displaying the username
        if (localStorage.user != undefined) {
            $('.cs-username').html(localStorage.username + '&nbsp;<span class="caret hidden-sm"></span>');
        }

        // show the available navigation options based on login status
        userNavToggle();
    }());// end startup function call

    // snackbar function
    function snackbarNotification(msg){
        // store the snackbar element in a local variable
        var sb = document.getElementById('snackbar');

        sb.innerHTML = msg;

        // show the snackbar by adding a css class
        sb.className = "show";

        // set a delay function
        setTimeout(() => {
            // after 3 seconds change remove the css class to hide the snackbar
            sb.className = sb.className.replace('show', '');
            sb.innerHTML = '';
        }, 3000);
    }// end snackbar function

    /**
     * navbar responsive handler
     *
     * show and hide navigation elements based on login and permissions
     */
    function userNavToggle() {
        if ($(this).width() < 569){
            $('.nav-user').hide();
            $('.cs-user-toggle').hide();
            if (localStorage.user != undefined){
                $('.cs-default').hide();
                $('.cs-user').show();
            } else {
                $('.cs-user').hide();
                $('.cs-default').show();
            }
        } else {
            if (!$('#side-menu')[0].width > 0){
                $('#side-menu').width(0);
            }
            $('.nav-user').show();
            if (localStorage.user != undefined){
                $('#top-login').hide();
                $('#top-register').hide();
                $('#nav-user').fadeIn(200);
                $('#nav-logout').fadeIn(200);
            } else {
                $('#top-login').fadeIn(200);
                $('#top-register').fadeIn(200);
                $('#nav-logout').hide();
                $('#nav-user').hide();
            }
        }
    }

    // responsive navigation on window resize
    $(window).resize(function () {
        userNavToggle();
    });

    /**
     * pseudo logout function
     * */
    // logout helper function
    function logout(e) {
        e.preventDefault();
        $.post('common/logout.php', function () {
            localStorage.removeItem('user');
            userNavToggle();
        });
    }// end logout helper function

    // logout button click handlers
    $('#top-logout').click(logout);
    $('#side-logout').click(logout);

    //session variables
    const AUTH_URL = "common/SLCCGAuthenticate.php";
    const REG_URL = "boxes/addUser.php";
    const username = $('#username');
    const password = $('#password');
    const registerModal = $('#register-modal');
    const loginModal = $('#login-modal');
    const loginError = $('#cs-login-error');

    // login form submission handler
    loginModal.onsubmit = e => {
        e.preventDefault();
        // basic form validation currently checks only for filled out fields that are required
        if (username.value != '' && password.value != '') {
            // create the parameters to send to server
            let params = "username=" + encodeURIComponent(username.value)  +
                "&password=" + encodeURIComponent(password.value) +
                "&AJAXAuth=AJAXAuth";
            // make an ajax post request to the server
            const xhr = new XMLHttpRequest();
            // post request start
            xhr.open('POST', AUTH_URL);

            // handle ajax response
            xhr.onload = function () {
                if (xhr.status == 200) {
                    // parse the data from the server. Some AJAX calls
                    // return an error string instead of JSON. The
                    // try-catch controlls for that.
                    var t = this.responseText;
                    try {var res = JSON.parse(t);}
                    catch(e){
                        var res = {};
                        res.failmess = t;}

                    // check data received from the server for a message property and notify the user of the error message
                    if (res.failmess) {
                        // set the text of the message that is displayed to the user
                        loginError.innerHTML = res.failmess;

                        // make the login error element visible
                        loginError.show();
                    } else {
                        // check the visibility of the error element
                        if (!loginError.hidden) {
                            // hide the error element
                            loginError.hide();
                        }

                        // XXX for texting only
                        alert("Message for testing only. Login was successful. Session variables have been set.");

                        userNavToggle();
                        // hide the modal on success
                        loginModal.hide();
                    }// end form submit check
                }// end status code 200:OK check
            };// end ajax response

            // set headers for form submission
            xhr.setRequestHeader('Content-type', "application/x-www-form-urlencoded");

            // send ajax with form data
            xhr.send(params);
        } else {
            // when either login field isn't filled out display the error element
            loginError.html('All fields are required!');
            loginError.show();
        }// end validation
    };// end login form submission handler

    // add new user through AJAX call
    registerModal.onsubmit = registerUser;

    function registerUser(e){
        e.preventDefault();

        // reference input fields
        var unip = document.getElementById("new-username");
        var emip = document.getElementById("email");
        var phip = document.getElementById("phone");
        var pwip = document.getElementById("new-password");
        var regerr = document.getElementById("register-error");

        // hide a previously shown error message
        regerr.style.display = "none";

        // get values from input fields
        var un = unip.value.trim();
        var em = emip.value.trim();
        var ph = phip.value.trim();
        var pw = pwip.value;

        // validate form fields
        var tf = validateRegistration();
        if(!tf){return}

        // URI encode fields
        var a = encodeURIComponent(un);
        var b = encodeURIComponent(em);
        var c = encodeURIComponent(ph);
        var d = encodeURIComponent(pw);

        // make post string
        var poststr = "AJAXAdd=AJAXAdd&uname=" + a +
            "&emaddr=" + b + "&phone=" + c +
            "&psswd=" + d;

        // configure xhr object (I'm ignoring browsers that are too
        // old to support this natively)
        var xhr = new XMLHttpRequest();
        xhr.open("POST", REG_URL);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = processRegistrationReturn;
        xhr.send(poststr);


        // inner fcn to processRegistration return value
        function processRegistrationReturn(){
            // check for http error
            if(this.status != 200){
                alert("There was an error processing this request. Please " +
                    "refresh your browser and try again.");
                return;}

            // get return text and convert to object
            var t = this.responseText;
            // alert("XXX this is reponse text: " + t);
            // some of the authentication functions return an error message
            // as a plain string (instead of JSON). The try-catch covers
            // that situation.
            try {var regObj = JSON.parse(t);}
            catch(e){
                var regObj = {};
                regObj.failmess = t;
            }

            // check for eror
            if(regObj.failmess){
                if(Array.isArray(regObj.failmess)){
                    var s = regObj.failmess.join(" ");
                    regerr.textContent = s;
                } else {regerr.textContent = regObj.failmess;}

                regerr.style.display = "block";
                return;
            }

            // // if you get here, registration was processed w/o error.
            // var rv = confirm("Your registration has been processed.  Click OK to log in.");
            // if(rv){swapToLogin();}
            // else {closeRegisterModal();}
            userNavToggle();
            $('#register-modal').fadeOut();
            $('#login-modal').fadeIn();
        }  // end inner fcn processRegistratioReturn


        function validateRegistration(){
            // make sure required field are present
            if(!un){
                alert("A username is required.");
                unip.focus();
                return false;
            }
            if(!em) {
                alert("An email address is required.");
                emip.focus();
                return false;
            }
            if(!pw){
                alert("A password is required.");
                pwip.focus();
                return false;
            }

            // make sure password is at least 8 chars long
            var L = pw.length;
            if(L < 8){
                alert("A password must be at least 8 characters long.");
                pwip.focus();
                return false;
            }

            // all checks passed
            return true;
        }  // end inner fcn validateRegistration
    }  // end fcn registerUser
});