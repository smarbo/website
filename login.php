<!DOCTYPE html>
<html>

<head>
  <title>Log In - SmarboLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
  <link href="styles/main.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="images/favicon.ico">
</head>

<body>
  <div class="container">
    <!-- Top Navigation Bar -->
      <nav class="top-nav" id="navBar"></nav>
    <main>
      <!-- Login Form -->
      <div class="form-box">
        <img src="images/account-icon.png" class="account-icon">
        <h1 class="form-box-title">Member Login</h1>
        <form action="" method="post">
          <div class="form-item">
            <input type="text" name="username" placeholder="Username" class="form-item-input" autocomplete="off">
          </div>
          <div class="form-item">
            <input type="password" name="password" placeholder="Password" class="form-item-input" autocomplete="off">
          </div>
          <div class="form-item">
            <button type="submit" class="form-item-submit">LOGIN</button>
          </div>
        </form>
        <h4 class="link-to-other-page form-link"><a href="signup.php">Don't have an account?</a></h4>
        <h4 class="forgot-password form-link"><a href="#forgot-pwd">Forgot password?</a></h4>
      </div>
    </main>
  </div>

  <!-- Script to remove "Confirm Form Resubmission" popup on refresh. -->
  <script>
    if ( window.history.replaceState ) {
      window.history.replaceState( null, null, window.location.href );
    }
  </script>
  <!-- Navbar Script -->
  <script src="scripts/navbar.js"></script>
</body>

</html>