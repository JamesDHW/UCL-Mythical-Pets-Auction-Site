<?php
  require_once '../../private/init.php';
  session_start();
  if($_SESSION['editprofpicfail'] == "False") {
    unset($_SESSION['editprofpicfail']); ?>
    <script type="text/javascript">
      location.reload(true);
    </script>
  <?php } ?>
<!Doctype html>
<html>
  <?php
  require TEMPLATE_PATH . '/html_head.php';
  if(!isset($_GET['profileID'])){
    require TEMPLATE_PATH . '/user_profile_not_found.php';
  } else {
    $profileID = $_GET['profileID'];
    require PRIVATE_PATH . '/connect_database.php';
    if(!$connection){
      require TEMPLATE_PATH . '/404item.php';
    } else{
      $query = "SELECT u.firstName, u.lastName, u.deleted, e.email, a.addressLine1, a.addressLine2, p.postcode, p.country, p.city
                FROM users u
                LEFT JOIN emailAddresses e ON u.userID = e.userID
                LEFT JOIN addresses a ON a.userID = e.userID
                LEFT JOIN postcodes p ON p.postcode = a.postcode
                WHERE u.userID = ?";
      $stmt = $connection->prepare($query);
      $stmt->bind_param('s', $profileID);
      $stmt->execute();
      $stmt->bind_result($fname, $lname, $userDeleted, $userEmail, $userAddL1, $userAddL2, $userPostcode, $userCountry, $userCity);
      $stmt->fetch();
      $stmt->close();
      mysqli_close($connection);
      if(empty($fname) || $userDeleted==1){
        header("Location: user_profile_not_found.php");
      }
    }
    ?>
    <body align="center">
      <?php
        require TEMPLATE_PATH . '/nav.php';
      ?>
      <div class="container">
        <div class="row m-3" align="center">
          <div class="col"><h2 class="title"><?php echo $fname . " " . $lname?></h2></div>
        </div>
        <div class="row m-3">
          <img src = <?php if (@getimagesize($base_url . "/images/profile/" . $_GET['profileID'] . "/profile.jpg")) { echo $base_url . "/images/profile/" . $_GET["profileID"] . "/profile.jpg"; } else {echo $base_url . "/images/profile/default.jpg"; } ?> class="mx-auto" style="min-height: 225px; max-height: 225px"> </img>
        </div>
        <?php if($profileID == $_SESSION['userID']){?>
          <div class="row">
            <form action="../forms/edit_profile_pic.php" method="POST" class="mx-auto" id="editprofpicform" enctype="multipart/form-data">
              <input type="file" name="imgupload" id="imgupload" class="form-control-file" accept=".jpg" style="display:none" value=""/>
              <div id="editprofile" class="btn btn-outline-primary"><?php if(@getimagesize($base_url . "/images/profile/" . $_GET['profileID'] . "/profile.jpg")) {echo "Edit Pic";} else {echo "Add Pic";} ?></div>
              <p id="filePath"></p>
              <button class="btn btn-outline-success" type="submit" id="imageupload" hidden="true">Upload</button>
            </form>
          </div>
        <?php } ?>
        <hr>
      </div>
      <div class="container text-center">
        <div class="row" align="center">
          <p class="mx-auto"><?php echo $userEmail ?></p>
        </div>
        <div class="row">
          <p class="mx-auto"><?php echo $userAddL1 ?></p>
        </div>
          <?php if(!empty($userAddL2)) {
            ?>
            <div class="row">
              <p class="mx-auto"><?php echo $userAddL2 ?></p>
            </div>
          <?php } ?>
          <div class="row">
            <p class="mx-auto"><?php echo $userCity ?></p>
          </div>
          <div class="row">
            <p class="mx-auto"><?php echo $userPostcode ?></p>
          </div>
          <div class="row">
            <p class="mx-auto"><?php echo $userCountry ?></p>
          </div>
        </div>
    <?php
  }
    ?>
  </body>
</html>
<script type="text/javascript">
  window.onload = function(){
    $('#editprofile').click(function(){
      $('#imgupload').click();
    });
    $('#imgupload').change(function() {
      $('#editprofpicform').submit();
    });
  }
</script>
