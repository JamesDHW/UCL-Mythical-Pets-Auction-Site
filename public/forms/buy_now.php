<?php
    require_once '../../private/init.php';
    session_start();

    # check if itemID is set
    if (!isset($_POST['itemID'])) {
        include TEMPLATE_PATH . '/404item.php';
        exit();
    }
    $itemID = $_POST['itemID'];

    # check if user is logged in
    if (!isset($_SESSION['userID'])) {
        header("Location: ../pages/item.php?itemID=$itemID&errorMessage=notLoggedIn");
        exit();
    }

    # get item data
    require PRIVATE_PATH . '/connect_database.php';
    if (!$connection) {
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $query = "SELECT name, startTime, endTime, buyNowPrice, userID, bidValue FROM items i
                    LEFT JOIN (SELECT itemID, MAX(bidValue) bidValue FROM bids GROUP BY itemID) b ON i.itemID = b.itemID WHERE i.itemID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $itemID);
    $success = $stmt->execute();
    if (!$success) {
        $connection->close();
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $stmt->bind_result($name, $startTime, $endTime, $buyNowPrice, $sellerID, $highestBid);
    if (!$stmt->fetch()) {
        $connection->close();
        include TEMPLATE_PATH . '/404item.php';
        exit();
    }
    $stmt->close();

    # check if the auction is active
    require FUNCTIONS_PATH . '/time_diff.php';
    if (inFuture($startTime) || !inFuture($endTime)) {
        $connection->close();
        header("Location: ../pages/item.php?itemID=$itemID&errorMessage=notActive");
        exit();
    }

    # check if user is allowed to buy
    if ($_SESSION['userID'] == $sellerID) {
        $connection->close();
        header("Location: ../pages/item.php?itemID=$itemID&errorMessage=ownItem");
        exit();
    }

    # check if buy now is still available
    if (!is_null($highestBid)) {
        if ((float)$buyNowPrice <= (float)$highestBid) {
            $connection->close();
            header("Location: ../pages/item.php?itemID=$itemID&errorMessage=buyNowNotAvailable");
            exit();
        }
    }

    # insert new bid and update item end time
    $connection->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $query = "INSERT INTO bids(itemID, bidValue, userID) VALUES (?,?,?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('sss', $itemID, $buyNowPrice, $_SESSION['userID']);
    $success = $stmt->execute();
    if (!$success) {
        $connection->rollback();
        $connection->close();
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $stmt->close();
    $query = "UPDATE items SET endTime = CURRENT_TIMESTAMP WHERE itemID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $itemID);
    $success = $stmt->execute();
    if (!$success) {
        $connection->rollback();
        $connection->close();
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $stmt->close();
    $connection->commit();

    # redirect the user
    header("Location: ../pages/item.php?itemID=$itemID");

    # notify users who track the item
    $query = "SELECT email, firstName FROM users u INNER JOIN emailAddresses e ON u.userID = e.userID INNER JOIN (SELECT userID FROM trackedItems WHERE itemID = ?) t ON u.userID = t.userID";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $itemID);
    if (!$stmt->execute()) {
        $connection->close();
        exit();
    }
    $stmt->bind_result($trackedMail, $trackedFirstName);
    $senderName = 'Mythical Pets';
    $senderEmail = 'no-reply@mythical-pets.com';
    while ($stmt->fetch()) {
        $recipient = $trackedMail;
        $subject = "Update on the item: $name";
        $body = "The item $name was sold.";
        $header = "From: $senderName <$senderEmail>\r\n";
        # mail($recipient, $subject, $mail_body, $header);
    }
    $connection->close();