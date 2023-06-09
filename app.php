<?php require('controllers/authController.php'); ?>
<?php refresh() ?>
<?php

// verify the user using token
if(isset($_GET['token'])){
  $token = $_GET['token'];
  verifyUser($token);
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>App - SmarboLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
  <link href="styles/main.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="images/favicon.ico">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <div class="container">
    <nav class="top-nav" id="navBar"></nav>
    <main>
      <!-- If user is not logged in -->
      <?php if(!isset($_SESSION['username'])): ?>
      <div class="logged-out-box main-child">
        <h1 id="child">You are logged out.</h1>
        <p id="child" class="message" style="text-align: center;">It seems you are currently not logged in to an account.<br>Please log in to access the rest of the site.</p>
        <a href="login.php" class="button" id="child">Log In</a>
        <a href="signup.php" class="button" id="child">Sign Up</a>
      </div>
      <?php endif; ?>
      <!--- If user is logged in  --->
      <!-- Error messages -->
      <?php if(count($errors) > 0): ?>
        <div class="error-message">
          <?php foreach($errors as $error): ?>
            <li><?php echo $error; ?></li>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <!-- Success messages -->
      <?php if(count($completeds) > 0): ?>
        <div class="success-message">
          <?php foreach($completeds as $complete): ?>
            <li><?php echo $complete; ?></li>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if(isset($_SESSION['username'])): ?>
        <div class="app-box">
          <div class="app-box-topbar">Hello, <?php echo $_SESSION['username']; ?>.</div>
          <div class="app-box-topbar logout"><a href="app.php?logout=1">Log Out</a></div>
          <div class="app-box-container">
            <div class="item-1 item">
              <h1 class="title">Overview</h1>
              <h3>Balance:</h3>
              <p><?php echo number_format($_SESSION['sb_bal']); ?> SmarboBits (SB)</p>
              <p><?php echo number_format($_SESSION['cs_bal']); ?> CrystalShards (CS)</p><br>
              <h3>Missions Completed: <?php echo $_SESSION['missions_complete'];?>.</h3><br>
              <div class="section-2">
                <h3>Account Info:</h3>
                <p>Username: <?php echo $_SESSION['username']; ?></p>
                <p>Email: <?php echo $_SESSION['email']; ?></p>
                <p>
                  <?php if($_SESSION['verified'] == 0){echo "You are not verified.";}else{echo "You are verified.";} ?>
                </p><br>
                <h3>Other Pages:</h3>
                <p><a href="#settings" id="other-pages-link">Account Settings</a></p>
                <p><a href="#smarboswap" id="other-pages-link">SmarboSwap</a></p>
                <p><a href="#smarbosocial" id="other-pages-link">Smarbo Social</a></p>
              </div>
              </div>
              <div class="item-2 item">
                <h1 class="title">Transfer</h1>
                <form action="app.php" method="post" class="transfer-form">
                  <div>
                    <select name="currency" class="currencies-select input">
                      <option value="smarbobits">SB</option>
                      <option value="crystalshards">CS</option>
                    </select>
                    <input type="number" name="amount" placeholder="Amount" class="amount input" value="">
                    <label for="reciever" style="font-size: large;">←↓→</label>
                    <input type="text" name="reciever" placeholder="Reciever's Username" class="reciever input" value="">
                    <button type="submit" name="transfer-btn" class="transfer-btn input">Send</button>
                  </div>
                </form>
              </div>
            <div class="item-4 item"><h1 class="title">Missions</h1></div>
          </div>
        </div>
      <?php endif; ?>
    </main>
  </div>
  <!-- Navbar Script -->
  <script src="scripts/navbar.js"></script>
  <!-- Script to remove Confirm Resubmission popup on refresh. -->
  <script>
    if ( window.history.replaceState ) {
      window.history.replaceState( null, null, window.location.href );
    }
  </script>
</body>

</html>
