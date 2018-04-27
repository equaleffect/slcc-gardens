<!--top navigation bar-->
<nav class="cs-navbar">
    <span class="open-slide">
        <a href="#" id="open-side">
            <svg width="30" height="30">
                <path d="M0,5 30,5" stroke="#fff" stroke-width="5"/>
                <path d="M0,14 30,14" stroke="#fff" stroke-width="5"/>
                <path d="M0,23 30,23" stroke="#fff" stroke-width="5"/>
            </svg>
        </a>
    </span>
    <ul class="top-nav">
        <li class="cs-nav-brand">
            <a href="../index.php">SLCC Gardens</a>
        </li>
        <li class="nav-user">
            <a href="#login" id="top-login">Login</a>
        </li>
        <li class="nav-user">
            <a href="#register" id="top-register">Register</a>
        </li>
        <li class="cs-user-toggle user-enabled" id="nav-user">
            <a class="cs-top-dropdown cs-username" id="top-user"></a>
            <ul class="cs-top-dropdown">
                <li><a href="userEdits.php">Profile</a></li>
                <li><a href="userBulletins.php">My Bulletins</a></li>
            </ul>
        </li>
        <li class="nav-user user-enabled" id="nav-logout">
            <a href="#logout" id="top-logout">Logout</a>
        </li>
        <li class="nav-user" id="nav-add-bulletins">
            <a href="../classifieds/create_bulletin.php" id="top-logout">Post Bulletin</a>
        </li>
    </ul>
</nav>
<!--end top navigation bar-->
<!--small screen side menu-->
<div class="side-nav" id="side-menu">
    <a href="#" id="side-close" class="cs-btn-close">&times;</a>
    <a href="#login" class="cs-default" id="side-login">Login</a>
    <a href="#register" class="cs-default" id="side-register">Register</a>
    <span class="cs-user cs-username" id="side-user"></span>
    <a href="userEdits.php" class="cs-user">Profile</a>
    <a href="userBulletins.php" class="cs-user">My Bulletins</a>
    <a href="#logout" class="cs-user" id="side-logout">Logout</a>
</div>
<!--end side menu-->
<!--login modal start-->
<div id="login-modal" class="modal">
    <form class="modal-content" id="login-form">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2>Login</h2>
        </div>
        <div class="modal-body">
            <div class="cs-error" id="cs-login-error"></div>
            <label for="username">Username</label>
            <input type="text" id="username" name="uname">
            <label for="password">Password</label>
            <input type="password" id="password" name="psswd">
        </div>
        <div class="modal-footer">
            <ul class="modal-btn-group">
                <li class="modal-btn-item">
                    <input type="submit" class="cs-btn cs-btn-primary" value="Login" id="login-btn">
                </li>
                <li class="modal-btn-item">
                    <button class="cs-btn cs-btn-secondary cs-swap-btn">Sign Up</button>
                </li>
                <li class="modal-btn-item-right">
                    <button class="cs-btn cs-btn-default cs-cancel-btn">Cancel</button>
                </li>
            </ul>
        </div>
    </form>
</div>
<!--login modal end-->
<!--registration modal start-->
<div id="register-modal" class="modal">
    <form class="modal-content" id="register-form">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2>Register</h2>
        </div>
        <div class="modal-body">
            <div class="cs-error" id="register-error"></div>
            <label for="new-username">Username</label>
            <input type="text" id="new-username" name="new-username" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            <label for="phone">Phone #</label>
            <input type="text" id="phone" name="phone" placeholder="ex: 1234567890|(123)456-7890|123-456-7890">
            <label for="new-password">Password</label>
            <input type="password" id="new-password" name="new-password" required>
        </div>
        <div class="modal-footer">
            <ul class="modal-btn-group">
                <li class="modal-btn-item">
                    <input type="submit" class="cs-btn cs-btn-primary" value="Register" id="register">
                </li>
                <li class="modal-btn-item">
                    <button class="cs-btn cs-btn-secondary cs-swap-btn">Login</button>
                </li>
                <li class="modal-btn-item-right">
                    <button class="cs-btn cs-btn-default cs-cancel-btn">Cancel</button>
                </li>
            </ul>
        </div>
    </form>
</div>
<!--login modal end-->
<!--toast/snackbar notification-->
<div id="snackbar"></div>
<!--main content end-->