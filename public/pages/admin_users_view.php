<?php
  require_once '../../private/init.php';
  session_start();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php
  require TEMPLATE_PATH . '/html_head.php';
  if(!isset($_SESSION['admin'])){
    header("Location: /mythical-pets/public/index.php");
  } elseif($_SESSION['admin'] == "0"){
    header("Location: /mythical-pets/public/pages/my_items.php");
  }
  ?>
  <body>
    <?php
    require TEMPLATE_PATH . '/nav.php';
    ?>
    <table class="table table-striped">
      <thead>
        <th scope="col">Account ID</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Email</th>
        <th scope="col">Street</th>
        <th scope="col">Postcode</th>
        <th scope="col">View Profile</th>
        <th scope="col">Privileges</th>
        <th scope="col">Account Status</th>
      </thead>
      <tbody>
      <?php require PUBLIC_PATH . '/forms/get_all_users.php' ?>
      </tbody>
    </table>
  </body>
</html>
