<?php
    require_once '../../private/init.php';
    session_start();

    # check if itemID is set
    if (!isset($_GET['itemID'])) {
        include TEMPLATE_PATH . '/404item.php';
        exit();
    }

    # check if user logged in
    if (isset($_SESSION['userID'])) {
        $loggedIn = true;
    } else {
        $loggedIn = false;
    }

    # get errorMessage if set
    if (isset($_GET['errorMessage'])) {
        if ($_GET['errorMessage'] == 'notLoggedIn') {
            $errorMessage = 'You are not logged in.';
        } elseif ($_GET['errorMessage'] == 'invalidInput') {
            $errorMessage = 'Please enter a valid value';
        } elseif ($_GET['errorMessage'] == 'ownItem') {
            $errorMessage = 'You can not bid on your own item.';
        } elseif ($_GET['errorMessage'] == 'notActive') {
            $errorMessage = 'This auction is not active.';
        } elseif ($_GET['errorMessage'] == 'bidTooLow') {
            $errorMessage = 'Your bis is too low.';
        } elseif ($_GET['errorMessage'] == 'buyNowNotAvailable') {
            $errorMessage = 'Buy Now is no longer available for this item.';
        } else {
            $errorMessage = 'An error occurred. Please try again later.';
        }
    }

    # fetch item data
    require PRIVATE_PATH . '/connect_database.php';
    if (!$connection) {
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }

    $query = "SELECT i.name, i.mythology, i.animalClass, i.description, i.userID, i.startTime, i.endTime, i.buyNowPrice, i.startingPrice, b.bidValue, b.userID FROM items i
                LEFT JOIN (SELECT itemID, bidValue, userID FROM bids WHERE itemID = ? ORDER BY bidValue DESC LIMIT 1) b ON i.itemID = b.itemID WHERE i.itemID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('ss', $_GET['itemID'], $_GET['itemID']);
    $success = $stmt->execute();
    if (!$success) {
        $connection->close();
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $stmt->bind_result($name, $mythologyID, $animalClassID, $description, $sellerID, $startTime, $endTime, $buyNowPrice, $startingPrice, $highestBid, $bidderID);
    if (!$stmt->fetch()) {
        $connection->close();
        include TEMPLATE_PATH . '/404item.php';
        exit();
    }
    $stmt->close();

    # get categories
    $query = "SELECT m.title, a.title FROM mythologies m INNER JOIN animalClasses a ON a.animalClassID = ? WHERE m.mythologyID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('ss', $animalClassID, $mythologyID);
    $success = $stmt->execute();
    if (!$success) {
        $connection->close();
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $stmt->bind_result($mythology, $animalClass);
    if (!$stmt->fetch()) {
        $animalClass = 'undefined';
        $mythology = 'undefined';
    }
    $stmt->close();

    #fetch bid data
    $query = "SELECT b.bidvalue, b.timestamp, u.firstName FROM bids b LEFT JOIN users u USING (userID) WHERE b.itemID = ? ORDER BY b.bidvalue DESC LIMIT 5";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $_GET['itemID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();


    # check if not started yet, already started or already ended
    require FUNCTIONS_PATH . '/time_diff.php';
    $hasStarted = !inFuture($startTime);
    $hasEnded = !inFuture($endTime);

    # convert times to readable times diffs
    $toStart = timeDiff($startTime);
    $toEnd = timeDiff($endTime);

    # check which price to use for current price
    if (is_null($highestBid)) {
        $currentPrice = $startingPrice;
        $isStartingPrice = true;
        $userBidWinning = false;
    } else {
        $currentPrice = $highestBid;
        $isStartingPrice = false;
        $userBidWinning = false;
        if ($loggedIn) {
            if ($_SESSION['userID'] == $bidderID) {
                $userBidWinning = true;
            }
        }
    }

    # check item of logged in user
    if ($loggedIn) {
        if ($sellerID == $_SESSION['userID']) {
            $isSeller = true;
        } else {
            $isSeller = false;

            # get tracking data
            $query = "SELECT itemID from trackedItems WHERE userID = ? AND itemID = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param('ss', $_SESSION['userID'], $_GET['itemID']);
            $success = $stmt->execute();
            if (!$success) {
                $connection->close();
                include TEMPLATE_PATH . '/serverError.php';
                exit();
            }
            $stmt->bind_result($followedItem);
            if ($stmt->fetch()) {
                $followingItem = true;
            } else {
                $followedItem = false;
            }
            $stmt->close();
        }
    } else {
        $isSeller = false;
        $followedItem = false;
    }

    # fetch the images
    $query = "SELECT pictureName FROM pictures WHERE itemID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $_GET['itemID']);
    $success = $stmt->execute();
    if (!$success) {
        $connection->close();
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $stmt->bind_result($pictureName);
    $pictures = array();
    while ($stmt->fetch()) {
        array_push($pictures, $pictureName);
    }
    if (count($pictures) <= 0) {
        $hasPictures = false;
    } else {
        $hasPictures = true;
    }
    $stmt->close();

    # generate recommendations from categories other than the current one
    $query = "SELECT i.itemID, i.name, c.correlation FROM items i INNER JOIN (SELECT mythologyIDCol, animalClassIDCol,
        correlation FROM catCatCorrelations WHERE mythologyIDRow = $mythologyID AND animalClassIDRow = $animalClassID
        AND (mythologyIDCol != $mythologyID OR animalClassIDCol != $animalClassID) LIMIT 100) c
        ON i.mythology = c.mythologyIDCol AND i.animalClass = c.animalClassIDCol WHERE i.endTime > CURRENT_TIMESTAMP AND i.startTime <= CURRENT_TIMESTAMP ORDER BY c.correlation DESC LIMIT 5";
    $stmt = $connection->prepare($query);
    if (!$stmt->execute()) {
        $connection->close();
        include TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $stmt->bind_result($itemID, $itemName, $correlation);
    $recommendations = array();
    while($stmt->fetch()) {
        $itemArray = array(
            'itemID' => $itemID,
            'itemName' => $itemName
        );
        array_push($recommendations, $itemArray);
    }
    $connection->close();



    # render template
    require TEMPLATE_PATH . '/nav.php';
    require TEMPLATE_PATH . '/single_item.php';
