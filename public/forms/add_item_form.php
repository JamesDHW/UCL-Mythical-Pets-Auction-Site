<?php
  require_once '../../private/init.php';

  function echoStatus($errorCode, $errorMessage) {
      header('Content-Type: application/json');
      echo json_encode(array(
          "status" => $errorCode,
          "errorMessage" => $errorMessage
      ));
  };

  function rollback($connection) {
      $connection->rollback();
      $connection->close();
      echoStatus('-1', 'An internal error occurred. Please try again later.');
  }

  session_start();

  # check if the user is logged in
  if (!isset($_SESSION['userID'])) {
      echoStatus('-2', 'You are not logged in.');
      exit();
  }

  # check if all required fields are set
  if (array_search('', $_POST) == true) {
  echoStatus('0', 'All fields are required.');
  exit();
  };

  # check if numeric values are actually numeric
  if (!is_numeric($_POST['buyNowPrice']) || !is_numeric($_POST['startingPrice'])
      || !is_numeric($_POST['startTime']) || !is_numeric($_POST['endTime'])) {
      echoStatus('0', 'Please only enter numbers in numeric fields.');
  }

  # check if start time
  if ((int)$_POST['startTime'] >= (int)$_POST['endTime']) {
      echoStatus('0', 'The start time lies after the end time.');
      exit();
  }

  # check if the start time lies in the past
  date_default_timezone_set('UTC');
  if ((int)$_POST['startTime'] < (time() - 300)) {
      echoStatus('0', 'The start time lies in the past.');
      exit();
  }

  $startTime = date('Y-m-d H:i:s', (int)$_POST['startTime']);
  $endTime = date('Y-m-d H:i:s', (int)$_POST['endTime']);

  # insert the item data
  require PRIVATE_PATH . '/connect_database.php';
  if (!$connection) {
      echoStatus('-1', 'An internal error occurred. Please try again later.');
      exit();
  }
  $connection->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

  # get the category IDs
  $query = "SELECT mythologyID FROM mythologies WHERE title = ?";
  $stmt = $connection->prepare($query);
  $stmt->bind_param('s', $_POST['mythology']);
  $success = $stmt->execute();
  if (!$success) {
      rollback($connection);
      exit();
  };
  $stmt->bind_result($mythologyID);
  if (!$stmt->fetch()) {
      rollback($connection);
      exit();
  }
  $stmt->close();

  $query = "SELECT animalClassID FROM animalClasses WHERE title = ?";
  $stmt = $connection->prepare($query);
  $stmt->bind_param('s', $_POST['animalClass']);
  $success = $stmt->execute();
  if (!$success) {
      rollback($connection);
      exit();
  };
  $stmt->bind_result($animalClassID);
  if (!$stmt->fetch()) {
      rollback($connection);
      exit();
  }
  $stmt->close();

  # add the new item
  $query = "INSERT INTO items(name, mythology, animalClass, description, userID, startTime, endTime, buyNowPrice, startingPrice)
          VALUES (?,?,?,?,?,?,?,?,?)";
  $stmt = $connection->prepare($query);
  $stmt->bind_param('sssssssss', $_POST['title'], $mythologyID, $animalClassID,
      $_POST['description'], $_SESSION['userID'], $startTime, $endTime, $_POST['buyNowPrice'], $_POST['startingPrice']);
  $success = $stmt->execute();
  if (!$success) {
      rollback($connection);
      exit();
  };
  $stmt->close();

  # save the pictures and add them to the database
  $itemID = $connection->insert_id;
  $dir = "../images/$itemID";
  try {
      $direError = mkdir($dir);
  } catch (Exception $e) {
      rollback($connection);
      exit();
  }
  if (!$direError) {
      $connection->rollback();
      $connection->close();
      echoStatus('-1', 'An internal error occurred. Please try again later.');
      exit();
  }
  foreach ($_FILES['pictures']['error'] as $key => $fileError) {
      if ($fileError == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES['pictures']['tmp_name'][$key];
          # add a random string to deal with pictures with the same name
          $name = uniqid() . basename($_FILES['pictures']['name'][$key]);
          $query = "INSERT INTO pictures(itemID, pictureName) VALUES (?,?)";
          $stmt = $connection->prepare($query);
          $stmt->bind_param('ss', $itemID, $name);
          $success = $stmt->execute();
          $stmt->close();
          if ($success) {
              if (!move_uploaded_file($tmp_name, "$dir/$name")) {
                  # if the file could not be moved, rollback the transaction and delete the pictures
                  $connection->rollback();
                  $connection->close();
                  # recursive deletion method from https://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
                  $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                  $files = new RecursiveIteratorIterator($it,
                      RecursiveIteratorIterator::CHILD_FIRST);
                  foreach($files as $file) {
                      if ($file->isDir()){
                          rmdir($file->getRealPath());
                      } else {
                          unlink($file->getRealPath());
                      }
                  }
                  rmdir($dir);
              }
          }
      }
  };

  $success = $connection->commit();
  $connection->close();

  # redirect to item page on success or send error message
  if ($success) {
      header('Content-Type: application/json');
      echo json_encode(array(
          'status' => '1',
          'itemID' => $itemID
      ));
      exit();
  } else {
      echoStatus('-1', 'An internal error occurred. Please try again alter.');
      exit();
  }
