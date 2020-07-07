<?php
    require_once '../../private/init.php';
    session_start();

    if (!isset($_SESSION['userID'])) {
        header("Location: ../pages/item.php?itemID=0&errorCode=notLoggedIn");
        exit();
    };
    if (!isset($_GET['itemID'])) {
        header("Location: ../pages/item.php?itemID=0&errorCode=itemNotFound");
        exit();
    };

    $userID = $_SESSION['userID'];
    $itemID = $_GET['itemID'];

    require PRIVATE_PATH . '/connect_database.php';
    $query = "DELETE FROM trackedItems WHERE userID = ? AND itemID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('ss', $userID, $itemID);
    $success = $stmt->execute();
    $connection->close();
    if (!$success) {
        header("Location: ../pages/item.php?itemID=$itemID&errorCode=trackNotPossible");
        exit();
    }
    header("Location: ../pages/item.php?itemID=$itemID");
