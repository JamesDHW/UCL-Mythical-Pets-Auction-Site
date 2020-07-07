<?php
  # compute the time difference of now and an sql timestamp
  function timeDiff($sqlTime) {
    $phpTime = new DateTime(date('Y-m-d H:i:s', strtotime($sqlTime)));
    $now = new DateTime(date('Y-m-d H:i:s'));
    $interval = $phpTime->diff($now);
    if (inFuture($sqlTime)) {
      return $interval->format('%D days, %H hours, %I minutes, %S seconds');
    } else {
      return $interval->format('ended %D days, %H hours, %I minutes, %S seconds ago');
    }
  }

  function inFuture($sqlTime) {
    $phpTime = new DateTime(date('Y-m-d H:i:s', strtotime($sqlTime)));
    $now = new DateTime(date('Y-m-d H:i:s'));
    $interval = $phpTime->diff($now);
    if ($interval->format('%R') == '-') {
      return 1;
    } else {
      return 0;
    }
  }
 ?>
