<div class="col-sm-3 m-4 border border-primary">
  <div class="text-center">
    <img src='<?php echo $base_url . "/images/".$itemID."/".$picName?>' class="img-fluid mh-50 my-2" style="min-height: 150px; max-height: 150px" alt="Responsive image">
  </div>
  <hr class="my-1">
  <div class="row m-1">
    <h2 class="my-1"><?php echo $itemName ?></h2>
  </div>
  <div class="row ml-1">
    <p class="m-1"><?php echo $itemClass ?> | <?php echo $itemMythology ?> </p>
  </div>
  <hr class="my-1">
  <div class="ml-2" style="height: 100px;">
    <p style="height: 100px; overflow: scroll;"><?php echo $itemDesc ?></p>
  </div>
  <hr class="my-1">
  <?php
  if (is_null($bidValue)): ?>
  <div class="row ml-1">
    <h5 class="m-1">Start Price: £<?php echo $itemStart ?></h5>
  </div>
<?php endif; ?>
<?php
if (!is_null($bidValue)): ?>
  <div class="row ml-1">
    <h5 class="m-1">Current Bid: £<?php echo $bidValue ?></h5>
  </div>
<?php endif; ?>
  <div class="row ml-1">
    <p class="m-1">Buy Now: £<?php echo $itemBuyNow ?></p>
  </div>
  <hr class="my-1">
  <div class="row m-2 justify-content-center">
    <button type="button" class="btn btn-success" onclick="window.location.href='<?php echo "/mythical-pets/public/pages/user_profile.php?profileID=".$profileID ?>'">Seller Profile</button>
  </div>
  <div class="row m-2 justify-content-center">
    <button type="button" class="btn btn-primary" onclick= "window.location.href='<?php echo "/mythical-pets/public/pages/item.php?itemID=".$itemID ?>'">View Item Details</button>
  </div>
  <?php if(isset($_SESSION['admin'])){
    if($_SESSION['admin'] == "1"){
      ?>
      <div class="row m-2 justify-content-center">
        <form action= <?php echo "/mythical-pets/public/forms/remove_item.php" ?> method="post">
          <input type="hidden" name="itemID" value= <?php echo $itemID ?> >
          <button class="btn btn-danger">Remove this Item</button>
        </form>
      </div>
      <?php
    }
  }
  ?>
</div>
