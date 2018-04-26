<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!--top navigation bar-->
<nav class="navbar">
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
        <li class="nav-brand">
            <a href="http://ec2-54-201-22-32.us-west-2.compute.amazonaws.com">SLCC Gardens</a>
        </li>
        <li class="nav-user">
            <a href="#login" id="top-login">Login</a>
        </li>
        <li class="nav-user">
            <a href="#register" id="top-register">Register</a>
        </li>
    </ul>
</nav>
<!--end top navigation bar-->
<!--small screen side menu-->
<div class="side-nav" id="side-menu">
    <a href="#" id="side-close" class="btn-close">&times;</a>
    <a href="#login" id="side-login">Login</a>
    <a href="#register" id="side-register">Register</a>
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
            <div class="error" id="login-error"></div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="modal-footer">
            <ul class="modal-btn-group">
                <li class="modal-btn-item">
                    <input type="submit" class="btn btn-primary" value="Login" id="login-btn">
                </li>
                <li class="modal-btn-item">
                    <button class="btn btn-secondary swap-btn">Sign Up</button>
                </li>
                <li class="modal-btn-item-right">
                    <button class="btn btn-default cancel-btn">Cancel</button>
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
            <div class="error" id="register-error"></div>
            <label for="new-username">Username</label>
            <input type="text" id="new-username" name="new-username" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            <label for="phone">Phone #</label>
            <input type="number" id="phone" name="phone">
            <label for="new-password">Password</label>
            <input type="password" id="new-password" name="new-password" required>
        </div>
        <div class="modal-footer">
            <ul class="modal-btn-group">
                <li class="modal-btn-item">
                    <input type="submit" class="btn btn-primary" value="Register" id="register">
                </li>
                <li class="modal-btn-item">
                    <button class="btn btn-secondary swap-btn">Login</button>
                </li>
                <li class="modal-btn-item-right">
                    <button class="btn btn-default cancel-btn">Cancel</button>
                </li>
            </ul>
        </div>
    </form>
</div>
<!--login modal end-->
<script src="js/landing.js"></script>
</body>
</html>
