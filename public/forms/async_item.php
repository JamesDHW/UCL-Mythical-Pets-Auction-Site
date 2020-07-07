<?php
    require_once '../../private/init.php';
    session_start();

    if (!isset($_POST['itemID'])) {
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => '-1',
            'message' => 'No item provided.'
        ));
        exit();
    }

    require PRIVATE_PATH . '/connect_database.php';
    if (!$connection) {
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }

    $query = "SELECT i. startTime, i.endTime, i.startingPrice, b.bidValue, b.userID, i.buyNowPrice FROM items i 
        LEFT JOIN (SELECT itemID, userID, bidValue FROM bids WHERE itemID = ? ORDER BY bidValue DESC LIMIT 1) b
        ON i.itemID = b.itemID WHERE i.itemID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('ss', $_POST['itemID'], $_POST['itemID']);
    if (!$stmt->execute()) {
        $connection->close();
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => '-1',
            'message' => 'An error occurred, please try again later.'
        ));
        exit();
    }
    $stmt->bind_result($startTime, $endTime, $startingPrice, $bidValue, $bidUserID, $buyNowPrice);
    if (!$stmt->fetch()) {
        $connection->close();
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => '-1',
            'message' => 'No item found.'
        ));
        exit();
    }
    $stmt->close();
    $connection->close();

    # convert times to readable times diffs
    require_once FUNCTIONS_PATH . '/time_diff.php';
    $toStart = timeDiff($startTime);
    $toEnd = timeDiff($endTime);

    # compute if the auction has not started, is active or has ended.
    $hasStarted = !inFuture($startTime);
    $hasEnded = !inFuture($endTime);
    if (!$hasStarted) {
        $auctionStatusText = 'Starts in:';
        $auctionStatus = '0';
        $timeLeft = $toStart;
    } elseif ($hasEnded) {
        $auctionStatusText = '';
        $auctionStatus = '0';
        $timeLeft = $toEnd;
    } else {
        $auctionStatusText = 'Ends in:';
        $auctionStatus = '1';
        $timeLeft = $toEnd;
    }


    # get if user has highest bid or if starting price
    if (is_null($bidValue)) {
        $bidValue = $startingPrice;
        $bidType = 'Starting Price';
    } else {
        $bidType = '';
    }
    if (isset($_SESSION['userID']) && !is_null($bidUserID)) {
        if ($_SESSION['userID'] == $bidUserID) {
            $bidType = 'Your bid is winning.';
        }
    }

    # check if buy now is available
    if ((float)$buyNowPrice <= (float)$bidValue) {
        $buyNow = '0';
    } else {
        $buyNow = '1';
    }

    # return the data
    header('Content-Type: application/json');
    echo json_encode(array(
        'status' => '1',
        'itemData' => array(
            'auctionStatus' => $auctionStatus,
            'auctionStatusText' => $auctionStatusText,
            'timeLeft' => $timeLeft,
            'bidValue' => $bidValue,
            'bidType' => $bidType,
            'buyNow' => $buyNow
        )
    ));