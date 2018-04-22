/******************************************************************************************************************
* Author: Ricky Mendez
*
* This is the start of the proof of concept for the Salt Lake Community College Gardens website extension site.
* The landing page consists of a split main content area, top navigation bar, login and registration modals, and
* is responsive. Everything on the front end uses vanilla javascript, custom css and html.
******************************************************************************************************************/

/*
* The immediately invoked function expression (IIFE) encapsulates the javascript by wrapping the code inside of a
* function. This function is available to the global scope but everything inside is not.
*/
(function () {
    // landing page variables
    const left = document.querySelector('.left');
    const right = document.querySelector('.right');
    const container = document.querySelector('.container');

    //session variables
    let user = null;// used to allow access to user specific features
    const SESSION_URL = window.origin + '/CSIS2470/Final/landing.php';// change the endpoint per project structure
    const AUTH_URL = "php/SLCCGAuthenticate.php";

    // login modal variables
    const loginModal = document.querySelector('#login-modal');
    const username = document.querySelector('#username');
    const password = document.querySelector('#password');
    const loginError = document.querySelector('#login-error');
    const topLoginBtn = document.getElementById('top-login');
    const sideLoginBtn = document.getElementById('side-login');

    // registration variables
    const registerModal = document.querySelector('#register-modal');
    const topRegisterBtn = document.getElementById('top-register');
    const sideRegisterBtn = document.getElementById('side-register');

    // modal close/cancel button variables
    const modalCloseBtns = document.querySelectorAll('.close');// array of close buttons
    const modalCancelBtns = document.querySelectorAll('.cancel-btn');// array of cancel buttons
    const modalSwapBtns = document.querySelectorAll('.swap-btn');// array of swap buttons

    // side menu variables (small screens)
    const sideMenu = document.getElementById('open-side');
    const sideCloseBtn = document.getElementById('side-close');

    // object inheritance
    registerModal.show = show;
    registerModal.hide = hide;
    loginModal.show = show;
    loginModal.hide = hide;
    loginError.show = show;
    loginError.hide = hide;

    /******************
     * helper functions
     *******************/
    function show(){
        this.style.display = 'block';
    }

    function hide() {
        this.style.display = 'none';
    }

    function showLogin(e) {
        e.preventDefault();
        loginModal.show();
        username.focus();
    }

    // close modal function
    function closeLoginModal(e) {
        e.preventDefault();
        loginModal.hide();// hide the modal by changing the display to none
    }// end close function

    function swapToRegister(e) {
        e.preventDefault();
        loginModal.hide();
        registerModal.show();
    }

    function showRegister(e) {
        e.preventDefault();
        registerModal.show();
    }

    function closeRegisterModal(e) {
        e.preventDefault();
        registerModal.hide();// hide the modal by changing the display to none
    }

    function swapToLogin(e) {
        e.preventDefault();
        registerModal.hide();
        loginModal.show();
    }

    // small screen menu handler
    function openSideMenu(e) {
        e.preventDefault();
        document.getElementById('side-menu').style.width = '250px';
    }

    function closeSideMenu(e) {
        e.preventDefault();
        document.getElementById('side-menu').style.width = '0';
    }
    /**********************
     * end helper functions
     ***********************/

    /********************************************************************************
     * The login modal will display on startup when the user first visits the sight
     *********************************************************************************/
    // navigation login button handlers
    topLoginBtn.addEventListener('click', showLogin);
    sideLoginBtn.addEventListener('click', showLogin);

    // login close/cancel buttons (Login appears first)
    modalCloseBtns[0].addEventListener('click', closeLoginModal);
    modalCancelBtns[0].addEventListener('click', closeLoginModal);

    // login modal swap handler
    modalSwapBtns[0].addEventListener('click', swapToRegister);

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
                    // parse the data from the server
                    var res = JSON.parse(this.responseText);

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
            loginError.innerHTML = 'All fields are required!';
            loginError.show();
        }// end validation
    };// end login form submission handler
    /*************************
     * end login modal handler
     **************************/

    /***************************
     * Registration form handler
     ****************************/
    // navigation register button handlers
    topRegisterBtn.addEventListener('click', showRegister);
    sideRegisterBtn.addEventListener('click', showRegister);

    // register close/cancel buttons (register appears second)
    modalCloseBtns[1].addEventListener('click', closeRegisterModal);
    modalCancelBtns[1].addEventListener('click', closeRegisterModal);

    // register modal swap handler
    modalSwapBtns[1].addEventListener('click', swapToLogin);
    /*******************************
     * End registration form handler
     ********************************/

    /************************************************************************
     * Alternative modal close functionality
     * Applies to both the register and login modals
     * When you click outside of the content area when the modal is displayed
     * the modal will close
     * Similarly, the side menu that is active on small screens will close
     *************************************************************************/
    // allow clicking anywhere outside of the modal to hide the modal
    window.addEventListener('click', function (e) {
        const sideMenu = document.querySelector('.side-nav');
        const nav = document.querySelector('.navbar');
        const split = document.querySelectorAll('.split');
        // check where the user has clicked
        if (e.target == loginModal){
            loginModal.hide();// close the modal when outside of the content area
        }

        if (e.target == registerModal){
            registerModal.hide();
        }

        if (sideMenu.style.width == '250px'){
            if (e.target == nav || e.target == split[0] || e.target == split[1]){
                closeSideMenu();
            }
        }
    });// end outside of content area function
    /***************************************
     * End alternative modal closing handler
     ****************************************/

    /**************************
     * page responsive handlers
     ***************************/
    //left mouse enter event
    left.addEventListener('mouseenter', () => {
        container.classList.add('hover-left');
    });// end left mouse enter

    // left mouse leave event
    left.addEventListener('mouseleave', () => {
        container.classList.remove('hover-left');
    });// end left mouse leave

    // right mouse enter event
    right.addEventListener('mouseenter', () => {
        container.classList.add('hover-right');
    });// end right mouse enter

    // right mouse leave event
    right.addEventListener('mouseleave', () => {
        container.classList.remove('hover-right');
    });// end right mouse leave
    // end responsive handlers

    // side menu open/close handlers (small screens)
    sideMenu.addEventListener('click', openSideMenu);
    sideCloseBtn.addEventListener('click', closeSideMenu);
    /******************************
     * end page responsive handlers
     *******************************/
})();
