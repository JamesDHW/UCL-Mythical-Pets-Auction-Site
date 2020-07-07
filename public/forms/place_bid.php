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

    # check if bidValue is set and numeric
    if (!isset($_POST['bidValue'])) {
        header("Location: ../pages/item.php?itemID=$itemID&errorMessage=invalidInput");
        exit();
    }
    if (!is_numeric($_POST['bidValue'])) {
        header("Location: ../pages/item.php?itemID=$itemID&errorMessage=invalidInput");
        exit();
    }

    # get item data
    require PRIVATE_PATH . '/connect_database.php';
    if (!$connection) {
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $query = "SELECT name, startTime, endTime, buyNowPrice, startingPrice, userID, bidValue FROM items i
              LEFT JOIN (SELECT itemID, MAX(bidValue) bidValue FROM bids GROUP BY itemID) b ON i.itemID = b.itemID WHERE i.itemID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $itemID);
    $success = $stmt->execute();
    if (!$success) {
        $connection->close();
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $stmt->bind_result($name, $startTime, $endTime, $buyNowPrice, $startingPrice, $sellerID, $highestBid);
    if (!$stmt->fetch()) {
        $connection->close();
        include TEMPLATE_PATH . '/404item.php';
        exit();
    }
    $stmt->close();

    # check if the user is the seller
    if ($_SESSION['userID'] == $sellerID) {
        $connection->close();
        header("Location: ../pages/item.php?itemID=$itemID&errorMessage=ownItem");
        exit();
    }

    # check if the auction is active
    require FUNCTIONS_PATH . '/time_diff.php';
    if (inFuture($startTime) || !inFuture($endTime)) {
        $connection->close();
        header("Location: ../pages/item.php?itemID=$itemID&errorMessage=notActive");
        exit();
    }

    # check if the bid is high enough
    if (is_null($highestBid)) {
        $minBid = (float)$startingPrice - 0.01;
    } else {
        $minBid = (float)$highestBid;
    }
    if ((float)$_POST['bidValue'] <= $minBid) {
        $connection->close();
        header("Location: ../pages/item.php?itemID=$itemID&errorMessage=bidTooLow");
        exit();
    }
    $bidValue = round((float)$_POST['bidValue'], 2);

    # add new bid entry
    $query = "INSERT INTO bids(itemID, bidValue, userID) VALUES (?,?,?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('sss', $itemID, $bidValue, $_SESSION['userID']);
    $success = $stmt->execute();
    if (!$success) {
        $connection->close();
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $stmt->close();

    # add item to tracked, if user already tracks it MySQL will just reject the query
    $query = "INSERT INTO trackedItems(userID, itemID) VALUES (?,?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('ss', $_SESSION['userID'], $itemID);
    $stmt->execute();
    $stmt->close();

    # redirect to item page
    header("Location: ../pages/item.php?itemID=$itemID");

    # notify users who track the item
    $query = "SELECT email, firstName FROM users u INNER JOIN emailAddresses e ON u.userID = e.userID 
            INNER JOIN (SELECT userID FROM trackedItems WHERE itemID = ?) t ON u.userID = t.userID";
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
        $body = "Hi $trackedFirstName, a new bid was placed on the item $name.";
        $header = "From: $senderName <$senderEmail>\r\n";
        # mail($recipient, $subject, $mail_body, $header);
    }
    $connection->close();