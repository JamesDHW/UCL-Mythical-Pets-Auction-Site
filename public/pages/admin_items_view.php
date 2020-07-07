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
    <div class="row m-2 pull-right">
      <button class="btn m-2 btn-success" onclick="location.href='/mythical-pets/public/pages/add_item.php'">Add New Item</button>
    </div>
    <table class="table table-striped">
      <thead>
        <th scope="col">Item ID</th>
        <th scope="col">Item Name</th>
        <th scope="col">Mythology</th>
        <th scope="col">Animal Class</th>
        <th scope="col">Description</th>
        <th scope="col">Start Price</th>
        <th scope="col">Latest Bid</th>
        <th scope="col">Start Time</th>
        <th scope="col">Finish Time</th>
        <th scope="col">Seller Profile</th>
        <th scope="col">Delete Item</th>
      </thead>
      <tbody>
      <?php require PUBLIC_PATH . '/forms/get_all_items.php';?>
      </tbody>
    </table>
  </body>
</html>
