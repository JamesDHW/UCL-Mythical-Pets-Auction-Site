<?php
    require_once '../../private/init.php';

    # fetch the categories
    require PRIVATE_PATH . '/connect_database.php';
    if (!$connection) {
        require TEMPLATE_PATH . '/serverError.php';
        exit();
    }
    $query = "SELECT title FROM mythologies";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $stmt->bind_result($mythology);
    $mythologies = array();
    while ($stmt->fetch()) {
        array_push($mythologies, $mythology);
    }
    $stmt->close();

    $query = "SELECT title FROM animalClasses";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $stmt->bind_result($animalClass);
    $animalClasses = array();
    while ($stmt->fetch()) {
        array_push($animalClasses, $animalClass);
    }
    $stmt->close();
    $connection->close();

    # load the template
    require TEMPLATE_PATH . '/add_item.php';
