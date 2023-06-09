<?php

session_start();

require 'config/db.php';
require_once 'emailController.php';

$errors = array();
$completeds = array();
$username = "";
$email = "";

function refresh(){
    // globalize the conn variable
    global $conn;

    // check if user is logged in
    if(isset($_SESSION['username'])){
        $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $_SESSION['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        // relogin user (update values)
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['verified'] = $user['verified'];
        $_SESSION['sb_bal'] = $user['sb_balance'];
        $_SESSION['cs_bal'] = $user['cs_balance'];
        $_SESSION['missions_complete'] = $user['missions_complete'];
    }
}

//if user click sign up
if(isset($_POST['signup-btn'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordConf = $_POST['passwordConf'];

    // validation
    if(empty($username)) {
        $errors['username'] = "Username required.";
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = "Invalid email address.";
    }
    if(empty($email)) {
        $errors['email'] = "Email required.";
    }
    if(empty($password)) {
        $errors['password'] = "Password required.";
    }
    if($password !== $passwordConf) {
        $errors['password'] = "The passwords do not match.";
    }

    $emailQuery = "SELECT * FROM users WHERE email=? OR username=? LIMIT 1";
    $stmt = $conn->prepare($emailQuery);
    $stmt->bind_param('ss', $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $userCount = $result->num_rows;
    $stmt->close();

    if($userCount > 0) {
        $errors['email'] = "Email or username already registered.";
    }

    // signup process
    if(count($errors) === 0) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(50));
        $verified = false;
        $smarboBitsBalance = 1000;
        $crystalShardsBalance = 100;
        $missionsComplete = 0;

        $sql = "INSERT INTO users (username, email, verified, token, password, sb_balance, cs_balance, missions_complete) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdssiii', $username, $email, $verified, $token, $password, $smarboBitsBalance, $crystalShardsBalance, $missionsComplete);
        $stmt->execute();

        // login user
        $user_id = $conn->insert_id;
        $_SESSION['id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['verified'] = $verified;
        $_SESSION['sb_bal'] = $smarboBitsBalance;
        $_SESSION['cs_bal'] = $crystalShardsBalance;
        $_SESSION['missions_complete'] = $missionsComplete;

        sendVerificationEmail($email, $token);
        
        // set flash message
        $completeds['login-success'] = "You are now logged in.";
        header('location: app.php');
        exit();
    }

}

// if user clicks login button
if(isset($_POST['login-btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // validation
    if(empty($username)) {
        $errors['username'] = "Username required.";
    }
    if(empty($password)) {
        $errors['password'] = "Password required.";
    }

    if(count($errors) === 0){
        // search the db for a user with the username or email
        $sql = "SELECT * FROM users WHERE email=? OR username=? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if(password_verify($password, $user['password'])){
            // login success
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['verified'] = $user['verified'];
            $_SESSION['sb_bal'] = $user['sb_balance'];
            $_SESSION['cs_bal'] = $user['cs_balance'];
            $_SESSION['missions_complete'] = $user['missions_complete'];
            $completeds['login-success'] = "You are now logged in.";
            // redirect to app.php
            header('location: app.php');
            exit();

        } else{
            $errors['login_fail'] = "Incorrect details.";
        }
    }

    
}


// logout user
if(isset($_GET['logout'])){
    session_destroy();
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['verified']);
    unset($_SESSION['sb_bal']);
    unset($_SESSION['cs_bal']);
    unset($_SESSION['missions_complete']);
    header('location: app.php');
    exit();
}

// transfer currency to other user
if(isset($_POST['transfer-btn'])){
    $currency = $_POST['currency'];
    $amount = $_POST['amount'];
    $reciever = $_POST['reciever'];

    if($amount < 0){
        $errors['value_error'] = "You cannot transfer that amount.";
    }

    if($currency === "smarbobits"){
        // use smarbobits
        if($amount > $_SESSION['sb_bal']){
            $errors['balance_error'] = "Not enough SB balance.";
        }

        // check if user exists.
        $userQuery = "SELECT * FROM users WHERE username=? LIMIT 1";
        $stmt = $conn->prepare($userQuery);
        $stmt->bind_param('s', $reciever);
        $stmt->execute();
        $result = $stmt->get_result();
        $userCount = $result->num_rows;

        if($userCount === 0) {
            $errors['user_nonexistant'] = "The recipient does not exist.";
        } else{
            $recieverUser = $result->fetch_assoc();
        }

        $stmt->close();

        if($_SESSION['username'] == $reciever){
            $errors['cannot_send_to_self'] = "You cannot transfer to yourself.";
        }

        if(count($errors) === 0){
            // no errors, can continue.
            $newBalance = $_SESSION['sb_bal'] - $amount;
            $sql = "UPDATE users SET sb_balance=? WHERE users.username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $newBalance, $_SESSION['username']);
            $stmt->execute();
            $stmt->close();
            // find recipients balance
            $recieverBalance = $recieverUser['sb_balance'];
            $recieverNewBalance = $recieverBalance + $amount;
            // set recievers balance to new one
            $sql = "UPDATE users SET sb_balance=? WHERE users.username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $recieverNewBalance, $reciever);
            $stmt->execute();
            $stmt->close();

            // update user's info
            $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $_SESSION['sb_bal'] = $user['sb_balance'];
            $_SESSION['cs_bal'] = $user['cs_balance'];
            $completeds['transfer-success'] = "Transfer of ".number_format($amount)."SB successful.";
        }
    }
    if($currency === "crystalshards"){
        // use crystalshards
        if($amount > $_SESSION['cs_bal']){
            $errors['balance_error'] = "Not enough CS balance.";
        }

        // check if user exists.
        $userQuery = "SELECT * FROM users WHERE username=? LIMIT 1";
        $stmt = $conn->prepare($userQuery);
        $stmt->bind_param('s', $reciever);
        $stmt->execute();
        $result = $stmt->get_result();
        $recieverUser = $result->fetch_assoc();
        $userCount = $result->num_rows;
        $stmt->close();

        if($_SESSION['username'] == $reciever){
            $errors['cannot_send_to_self'] = "You cannot transfer to yourself.";
        }

        if($userCount === 0) {
            $errors['user_nonexistant'] = "The recipient does not exist.";
        }

        if(count($errors) === 0){
            // no errors, can continue.
            $newBalance = $_SESSION['cs_bal'] - $amount;
            $sql = "UPDATE users SET cs_balance=? WHERE users.username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $newBalance, $_SESSION['username']);
            $stmt->execute();
            $stmt->close();
            // find recipients balance and get recipient user
            $recieverBalance = $recieverUser['cs_balance'];
            $recieverNewBalance = $recieverBalance + $amount;
            // set recievers balance to new one
            $sql = "UPDATE users SET cs_balance=? WHERE users.username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('is', $recieverNewBalance, $reciever);
            $stmt->execute();
            $stmt->close();
            // update user's info
            $sql = "SELECT * FROM users WHERE username=? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $_SESSION['sb_bal'] = $user['sb_balance'];
            $_SESSION['cs_bal'] = $user['cs_balance'];
            $completeds['transfer-success'] = "Transfer of ".number_format($amount)."CS successful.";

        }
    }
}

// verify the user by token
function verifyUser($token)
{
   global $conn;
   global $completeds;
   global $errors;
   $sql = "SELECT * FROM users WHERE token='$token' LIMIT 1";
   $result = mysqli_query($conn, $sql);

   if(mysqli_num_rows($result) > 0){
    $user = mysqli_fetch_assoc($result);
    $update_query = "UPDATE users SET verified=1 WHERE token='$token'";

    if(mysqli_query($conn, $update_query)){
        // log user in
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['verified'] = 1;
        $_SESSION['sb_bal'] = $user['sb_balance'];
        $_SESSION['cs_bal'] = $user['cs_balance'];
        $_SESSION['missions_complete'] = $user['missions_complete'];
        // set flash message
        $completeds['verified'] = "You are now verified!";
        header('location: app.php');
        exit();
    }
   }
   else {
    $errors['usernotfound'] = "User not found.";
   }
}