<tr>
  <th scope="row"><?php echo $accountID?></th>
  <td><?php echo $accountFirstName?></td>
  <td><?php echo $accountLastName?></td>
  <td><?php echo $accountEmail?></td>
  <td><?php if(!is_null($accountAddress1)){echo $accountAddress1;} ?></td>
  <td><?php if(!is_null($accountPostcode)){echo $accountPostcode;} ?></td>
  <td>
    <button type="button" class="btn btn-success" onclick="window.location.href='<?php echo "/mythical-pets/public/pages/user_profile.php?profileID=".$accountID ?>'">Seller Profile</button>
  </td>
  <td>
    <form action= <?php echo "/mythical-pets/public/forms/make_user_admin.php" ?> method="post">
      <input type="hidden" name="profileID" value= <?php echo $accountID ?> >
      <input type="hidden" name="admin" value= <?php echo $accountAdmin ?> >
      <button class="btn btn-outline-success m-1">Admin: <?php echo $accountAdmin ?></button>
    </form>
  </td>
  <td>
    <form action= <?php echo "/mythical-pets/public/forms/remove_user_account.php" ?> method="post">
      <input type="hidden" name="profileID" value= <?php echo $accountID ?> >
      <input type="hidden" name="removed" value= <?php echo $accountRemoved ?> >
      <button class="btn btn-outline-danger m-1">Removed: <?php echo $accountRemoved ?></button>
    </form>
  </td>
</tr>
