<tr>
  <th scope="row"><?php echo $itemID?></th>
  <td><?php echo $itemName?></td>
  <td><?php echo $itemMythology?></td>
  <td><?php echo $itemClass?></td>
  <td><?php echo $itemDesc?></td>
  <td><?php echo $itemStartPrice?></td>
  <td><?php echo $itemBid?></td>
  <td><?php echo $itemStartTime?></td>
  <td><?php echo $itemEndTime?></td>
  <?php if(!strpos($_SERVER['REQUEST_URI'],"my_items.php")){?>
    <td>
      <button type="button" class="btn btn-success"
      onclick="window.location.href='<?php echo "/mythical-pets/public/pages/user_profile.php?profileID=".$profileID ?>'">Seller Profile</button>
    </td>
  <?php }?>
  <td>
    <form action= <?php echo "/mythical-pets/public/forms/remove_item.php" ?> method="post">
      <input type="hidden" name="itemID" value= <?php echo $itemID ?> >
      <button class="btn btn-danger m-1">Delete Item</button>
    </form>
  </td>
</tr>
