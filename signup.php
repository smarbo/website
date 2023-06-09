<?php include('controllers/authController.php'); ?>

<!DOCTYPE html>
<html>

<head>
  <title>Sign Up - SmarboLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
  <link href="styles/main.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="images/favicon.ico">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <div class="container">
    <!-- Top Navigation Bar -->
      <nav class="top-nav" id="navBar"></nav>
    <main>
      <!-- Errors -->
      <?php if(count($errors) > 0): ?>
        <div class="error-message">
          <?php foreach($errors as $error): ?>
            <li><?php echo $error; ?></li>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <!-- Login Form -->
      <div class="form-box">
        <img src="images/account-icon.png" class="account-icon">
        <h1 class="form-box-title">Sign Up</h1>
        <form action="signup.php" method="post">
          <div class="form-item">
            <input type="email" name="email" placeholder="Email Address" class="form-item-input" autocomplete="off" minlength="3" value="<?php echo $email; ?>">
          </div>
          <div class="form-item">
            <input type="text" name="username" placeholder="Username" class="form-item-input" autocomplete="off" minlength="3" maxlength="20" value="<?php echo $username; ?>">
          </div>
          <div class="form-item">
            <input type="password" name="password" placeholder="Password" class="form-item-input" autocomplete="off" minlength="7">
          </div>
          <div class="form-item">
            <input type="password" name="passwordConf" placeholder="Confirm Password" class="form-item-input" autocomplete="off">
          </div>
          <div class="form-item">
            <button type="submit" name="signup-btn" class="form-item-submit">SIGN UP</button>
          </div>
        </form>
        <h4 class="link-to-other-page"><a href="login.php">Already have an account?</a></h4>
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
