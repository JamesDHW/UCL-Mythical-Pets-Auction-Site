<?php
  session_start();
  $_SESSION = array();
  if (isset($_COOKIE['userID'])) {
    setcookie('userID', '', time()-3600);
    setcookie('username', '', time()-3600);
    setcookie('admin', '', time()-3600);
  }
  session_destroy();
  header('Location: ../index.php');
?>
