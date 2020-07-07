<?php
    require_once '../init.php';

    # get all items that ended the day before
    require PRIVATE_PATH . '/connect_database.php';
    if (!$connection) {
        exit();
    }

    $query = "SELECT u.firstName, e.email, iu.name FROM users u INNER JOIN emailAddresses e ON u.userID = e.userID INNER JOIN 
        (SELECT DISTINCT un.userID, ii.name FROM ((SELECT t.userID, t.itemID FROM trackedItems t) UNION 
        (SELECT i.userID, i.itemID FROM items i)) un INNER JOIN 
        (SELECT userID, itemID, name FROM items WHERE 
        DATE(endTime) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)) ii ON un.itemID = ii.itemID) iu ON u.userID = iu.userID";
    $stmt = $connection->prepare($query);
    if (!$stmt->execute()) {
        exit();
    }
    $stmt->bind_result($firstName, $email);
    $senderName = 'Mythical Pets';
    $senderEmail = 'no-reply@mythical-pets.com';

    # compose the mail
    while ($stmt->fetch()) {
        $recipient = $email;
        $subject = "Update on the item: $name";
        $body = "Hi $firstName, the auction for $name ended yesterday. Check your profile to see the outcome.";
        $header = "From: $senderName <$senderEmail>\r\n";
        # mail($recipient, $subject, $mail_body, $header);
    }
    $connection->close();
