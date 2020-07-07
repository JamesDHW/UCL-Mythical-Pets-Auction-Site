<?php
require_once '../../private/init.php';
session_start();
$filedestination = "../images/profile/" . $_SESSION['userID'] . "/profile.jpg";
if (!file_exists("../images/profile/" . $_SESSION['userID'] . "/")) {
    mkdir("../images/profile/" . $_SESSION['userID'] . "/", 0777, true);
}
if (move_uploaded_file($_FILES['imgupload']['tmp_name'], $filedestination)) {
  $_SESSION['editprofpicfail'] = "False";
  header('Location: ../pages/user_profile.php?profileID='.$_SESSION['userID']);
} else {
  $_SESSION['editprofpicfail'] = "True";
  header('Location: ../pages/user_profile.php?profileID='.$_SESSION['userID']);
}
?>
