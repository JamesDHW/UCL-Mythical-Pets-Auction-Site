<?php require_once '../../private/init.php'; ?>

<!doctype html>
<html lang="en">
<?php require TEMPLATE_PATH . "/html_head.php"; ?>
<body>
  <?php
  require TEMPLATE_PATH . '/nav.php';
  ?>
  <div class="container" style="background-color: white">
    <div class="row m-2" align="center">
      <h1>Here are your search results for <?php echo $_GET['itemSearch'] ?> </h1>
    </div>
    <div class="row mb-3">
      <?php
      if(!isset($_GET['itemSearch'])){
        require TEMPLATE_PATH . '/404item.php';
      } else {require PUBLIC_PATH . "/forms/search_query.php";}
      ?>
    </div>
  </div>
</body>
 </html>
