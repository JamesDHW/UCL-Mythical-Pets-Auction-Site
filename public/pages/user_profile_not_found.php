<?php
require_once '../../private/init.php';
 ?>
<!DOCTYPE html>
<html>
  <?php require TEMPLATE_PATH . "/html_head.php"; ?>
  <body>
    <?php
    require TEMPLATE_PATH . '/nav.php';
    if (!isset($_SESSION['userID'])){
      echo ("Please Log In to View Your Profile!");
    } else {
      echo ("User Profile Not Found!");
    }
    ?>
  </body>
</html>
