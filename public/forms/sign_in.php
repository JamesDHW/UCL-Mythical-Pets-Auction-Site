<?php
require_once '../../private/init.php';
session_start();
$email = $_POST['email'];
$password = $_POST['password'];
if(!isset($_SESSION['userID'])){
  #connect to database
  require PRIVATE_PATH . '/connect_database.php';
  #check if connection successful
  if(!$connection){
    require TEMPLATE_PATH . '/404item.php';
  } else{
    #check if email entered by user exists ONCE in database
    $findUserID = "SELECT userID FROM emailAddresses WHERE email = ?";
    $stmt = $connection->prepare($findUserID);
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $stmt->bind_result($userID);
    $fetched = $stmt->fetch();
    $stmt->close();
    if(!is_null($fetched)) {
      #if user found, retrieve passwordHash of user
      $findUserHash = "SELECT firstName, passwordHash, admin FROM users WHERE userID = ?";
      $stmt = $connection->prepare($findUserHash);
      $stmt->bind_param('i', $userID);
      $stmt->execute();
      $stmt->bind_result($firstName, $hash, $admin);
      $fetched = $stmt->fetch();
      $stmt->close();
      #verify correct password input
      if(password_verify($password, $hash)){
        #set Session Variables and redirect to hompage
        $_SESSION['userID'] = $userID;
        $_SESSION['username'] = $firstName;
        $_SESSION['admin'] = $admin;
        header('Location: ../index.php');
      }else{
        $_SESSION['badCredentials'] = True;
      }
    }else{
      $_SESSION['badCredentials'] = True;
    }
  }
  mysqli_close($connection);
  header('Location: ../index.php');
} else{
  #log user out & redirect to landing page (when already logged in at submit)
  header('Location: log_out.php');
}
?>
