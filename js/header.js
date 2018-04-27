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
    // login modal variables
    const loginModal = document.querySelector('#login-modal');
    const loginError = document.querySelector('#cs-login-error');
    const topLoginBtn = document.getElementById('top-login');
    const sideLoginBtn = document.getElementById('side-login');

    // registration variables
    const registerModal = document.querySelector('#register-modal');
    const topRegisterBtn = document.getElementById('top-register');
    const sideRegisterBtn = document.getElementById('side-register');

    // modal close/cancel button variables
    const modalCloseBtns = document.querySelectorAll('.close');// array of close buttons
    const modalCancelBtns = document.querySelectorAll('.cs-cancel-btn');// array of cancel buttons
    const modalSwapBtns = document.querySelectorAll('.cs-swap-btn');// array of swap buttons

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
        this.children[0][0].focus();
    }

    function hide() {
        this.style.display = 'none';
    }

    function showLogin(e) {
        e.preventDefault();
        loginModal.show();
    }

    // close modal function
    function closeLoginModal(e) {
        e.preventDefault();
        loginModal.hide();// hide the modal by changing the display to none
        if (window.location == (window.origin + '/events') && localStorage.user == undefined){
            window.location = window.origin + '/home';
        }
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
        if (window.location == (window.origin + '/events') && localStorage.user == undefined){
            window.location = window.origin + '/home';
        }
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
     * Login modal handler
     *********************************************************************************/
    // navigation login button handlers
    topLoginBtn.addEventListener('click', showLogin);
    sideLoginBtn.addEventListener('click', showLogin);

    // login close/cancel buttons (Login appears first)
    modalCloseBtns[0].addEventListener('click', closeLoginModal);
    modalCancelBtns[0].addEventListener('click', closeLoginModal);

    // login modal swap handler
    modalSwapBtns[0].addEventListener('click', swapToRegister);
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

        if (window.location == (window.origin + '/events') && localStorage.user == undefined){
            window.location = window.origin + '/home';
        }
    });// end outside of content area function
    /***************************************
     * End alternative modal closing handler
     ****************************************/

    /**************************
     * page responsive handlers
     ***************************/
    // side menu open/close handlers (small screens)
    sideMenu.addEventListener('click', openSideMenu);
    sideCloseBtn.addEventListener('click', closeSideMenu);
    /******************************
     * end page responsive handlers
     *******************************/
})();