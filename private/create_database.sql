DROP DATABASE mythical_pets;

CREATE DATABASE mythical_pets
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

DROP USER 'database_admin'@'localhost';


CREATE USER 'database_admin'@'localhost'
IDENTIFIED BY 'mythPetsAdmin';

GRANT SELECT, UPDATE, INSERT, DELETE
    ON mythical_pets.*
    TO 'database_admin'@'localhost';

USE mythical_pets;

CREATE TABLE users
(
  userID INTEGER AUTO_INCREMENT PRIMARY KEY,
  passwordHash VARCHAR(60) NOT NULL,
  firstName VARCHAR(50) NOT NULL,
  lastName VARCHAR(50) NOT NULL,
  admin BOOLEAN DEFAULT FALSE,
  deleted BOOLEAN DEFAULT FALSE
) ENGINE = InnoDB;

CREATE TABLE emailAddresses
(
  userID INT PRIMARY KEY,
  email VARCHAR(80) NOT NULL UNIQUE,
  FOREIGN KEY (userID) REFERENCES users(userID) ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE postcodes
(
  postcode VARCHAR(20) NOT NULL,
  country VARCHAR(48) NOT NULL,
  city VARCHAR(50) NOT NULL,
  PRIMARY KEY (postcode, country)
) ENGINE = InnoDB;

CREATE TABLE addresses
(
  userID INT PRIMARY KEY,
  addressLine1 VARCHAR(50) NOT NULL,
  addressLine2 VARCHAR(50),
  postcode VARCHAR(20) NOT NULL,
  INDEX (postcode),
  FOREIGN KEY (userID) REFERENCES users(userID)  ON UPDATE CASCADE,
  FOREIGN KEY (postcode) REFERENCES postcodes(postcode)  ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE mythologies
(
  mythologyID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(50)
) ENGINE = InnoDB;

CREATE TABLE animalClasses
(
  animalClassID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(50)
) ENGINE = InnoDB;

CREATE TABLE sortBy
(
  sortID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(50)
) ENGINE = InnoDB;

CREATE TABLE items
(
  itemID INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  mythology INT NOT NULL,
  animalClass INT NOT NULL,
  description TEXT NOT NULL,
  userID INT NOT NULL,
  startTime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  endTime DATETIME NOT NULL,
  buyNowPrice DECIMAL(10,2) NOT NULL,
  startingPrice DECIMAL(10,2) NOT NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  views INT DEFAULT 0,
  FOREIGN KEY (userID) REFERENCES users(userID)  ON UPDATE CASCADE,
  FOREIGN KEY (mythology) REFERENCES mythologies(mythologyID) ON UPDATE CASCADE,
  FOREIGN KEY (animalClass) REFERENCES animalClasses(animalClassID) ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE bids
(
  bidID INT AUTO_INCREMENT PRIMARY KEY,
  itemID INT NOT NULL,
  bidValue DECIMAL(10,2) NOT NULL,
  timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  userID INT NOT NULL,
  FOREIGN KEY (userID) REFERENCES users(userID)  ON UPDATE CASCADE,
  FOREIGN KEY (itemID) REFERENCES items(itemID)  ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE pictures
(
  pictureID INT AUTO_INCREMENT PRIMARY KEY,
  itemID INT NOT NULL,
  pictureName VARCHAR(100) NOT NULL,
  FOREIGN KEY (itemID) REFERENCES items(itemID)  ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE trackedItems
(
  userID INT NOT NULL,
  itemID INT NOT NULL,
  PRIMARY KEY (userID, itemID),
  FOREIGN KEY (userID) REFERENCES users(userID) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (itemID) REFERENCES items(itemID) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE catCatCorrelations
(
    mythologyIDRow INT NOT NULL,
    animalClassIDRow INT NOT NULL,
    mythologyIDCol INT NOT NULL,
    animalClassIDCol INT NOT NULL,
    correlation FLOAT NOT NULL,
    PRIMARY KEY (mythologyIDRow, animalClassIDRow, mythologyIDCol, animalClassIDCol),
    FOREIGN KEY (mythologyIDRow) REFERENCES mythologies(mythologyID) ON UPDATE CASCADE,
    FOREIGN KEY (animalClassIDRow) REFERENCES animalClasses(animalClassID) ON UPDATE CASCADE,
    FOREIGN KEY (mythologyIDCol) REFERENCES mythologies(mythologyID) ON UPDATE CASCADE,
    FOREIGN KEY (animalClassIDCol) REFERENCES animalClasses(animalClassID) ON UPDATE CASCADE
) ENGINE = InnoDB;

/* INSERT SOME TEST DATA */
INSERT INTO users (passwordHash, firstName, lastName, admin)
    VALUES ("$2y$10$xChffNMhN9h8.NigFT8CIOJ5NduuaV/QeQkNU.4uEHHs6qRDgzJYe", "Andy", "Brinkmeyer", 0);

INSERT INTO users (passwordHash, firstName, lastName, admin)
    VALUES ("$2y$10$bMA.SJdbtEmuyPcWBxgOqOvvJA.nHOT1YFy6M9Bd1WPykrf8MpJUW", "Jack", "Fraser Nash", 0);

INSERT INTO users (passwordHash, firstName, lastName, admin)
    VALUES ("$2y$10$6JXn5eNU/D5fumyxYqYi/.quZwUE4uV8gORbdou.PPolToxZWqLi.", "James", "Haworth Wheatman", 0);

INSERT INTO users (passwordHash, firstName, lastName, admin)
    VALUES ("$2y$10$uP1qQuOuEJ6POfxtPeS6K.3xllGg.rdA2kQPyXtThy.zKOt0l8mVi", "Simon", "Kanani", 0);

INSERT INTO emailAddresses (userID, email) VALUES (1, "Andy@email.com");

INSERT INTO emailAddresses (userID, email) VALUES (2, "Jack@email.com");

INSERT INTO emailAddresses (userID, email) VALUES (3, "James@email.com");

INSERT INTO emailAddresses (userID, email) VALUES (4, "Simon@email.com");

INSERT INTO postcodes (postcode, country, city) VALUES ("SE1 1AB", "United Kingdom", "London");

INSERT INTO postcodes (postcode, country, city) VALUES ("WC1E 6AP", "United Kingdom", "London");


INSERT INTO addresses (userID, addressLine1, addressLine2, postcode)
    VALUES (1, "Some Street", "", "SE1 1AB");

INSERT INTO addresses (userID, addressLine1, addressLine2, postcode)
    VALUES (2, "Long Lane", "", "SE1 1AB");

INSERT INTO addresses (userID, addressLine1, addressLine2, postcode)
    VALUES (3, "Gower Street", "", "WC1E 6AP");

INSERT INTO addresses (userID, addressLine1, addressLine2, postcode)
    VALUES (4, "Torrington Place", "", "WC1E 6AP");

INSERT INTO mythologies (title)
    VALUES ("Egyptian");

INSERT INTO mythologies (title)
    VALUES ("Greek");

INSERT INTO mythologies (title)
    VALUES ("Roman");

INSERT INTO animalClasses (title)
    VALUES ("Land Beasts");

INSERT INTO animalClasses (title)
    VALUES ("Fairies");

INSERT INTO animalClasses (title)
    VALUES ("Reptiles");

INSERT INTO animalClasses (title)
    VALUES ("Dragons");

INSERT INTO animalClasses (title)
    VALUES ("Aquatic");

INSERT INTO animalClasses (title)
    VALUES ("Arachnids");

INSERT INTO animalClasses (title)
    VALUES ("Birds");

INSERT INTO sortBy (title)
    VALUES ("Ending Soon");

INSERT INTO sortBy (title)
    VALUES ("New Auctions");

INSERT INTO sortBy (title)
    VALUES ("Buy Now Low-High");

INSERT INTO sortBy (title)
    VALUES ("Buy Now High-Low");

/* Now insert sample data: 3 results for each class per mythology (54 entries)*/

/* Mythology group 1 & Class group 1:  Egyptian & Land Beasts */
INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Sphinx", 1, 1, "A giant of egyptian culture.",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 4.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Guard Dog", 1, 1, "The protector of the realm, he shall not let you pass!.",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 14.50, 4.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Egyptian Fox", 1, 1, "A small creature the size of a ping pong ball that can be found hiding in pita bushes.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 2.50, 0.8);

/* Mythology group 1 & Class group 2:  Egyptian & Fairies */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Healer", 1, 2, "She helps all who are worthy.",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 9.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Mystic", 1, 2, "Can tell the future, trust their word and it may come true.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 8.80, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Wozoo", 1, 2, "Has the ability to throw fireballs from its hands.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 21.80, 0.5);

/* Mythology group 1 & Class group 3:  Egyptian & Reptiles */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Hekret", 1, 3, "fire breathing snake - fear him or else.",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 19.50, 6.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Apedes", 1, 3, "a crocodile from ancient egypt, a rare creature",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 4.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Masebo", 1, 3, "large burying worm that can become very territorial",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 7.50, 0.5);

/* Mythology group 1 & Class group 4:  Egyptian & Dragons */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Spyro", 1, 4, "Everyone's favourite purple egyptian dragon friend.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 4.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Hydra", 1, 4, "A multi-headed beast, all bow before him.",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 29.0, 12.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Trezo", 1, 4, "Some say he served the gods, others say he is one.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 150.0, 12.0);

/* Mythology group 1 & Class group 5:  Egyptian & Aquatic */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Manateno", 1, 5, "Dweller of the sea, some say cities of fish flock to his back.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 5.50, 0.3);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Serpent", 1, 5, "A huge creature that can call the whole ocean at once.",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 15.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Falafeli", 1, 5, "A small creature the size of a ping pong ball that can be found swimming in humus lakes.",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 4.70, 0.2);

/* Mythology group 1 & Class group 6:  Egyptian & Arachnids */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Breeno", 1, 6, "A spider with potion hands, a common creature back in 147 AD.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 4.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Tarantulio", 1, 6, "A dark spider that loves cheese, especially cheddar.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 4.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Genoy", 1, 6, "The dancing spider, an entertaining yet fearful beast.",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 4.50, 0.5);

/* Mythology group 1 & Class group 7:  Egyptian & Birds */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Griffin", 1, 7, "Man's new best friend, a loyal companion for anyone.",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 27.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Bennu", 1, 7, "Is said to have hypnotic powers over certain people.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 9.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Horus", 1, 7, "Walks on two feet yet can fly using only its ears.",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 7.90, 0.5);

/* Mythology group 2 & Class group 1:  Greek & Land Beasts */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Gyro", 2, 1, "A delicious animal that usually can be found living together with chips in the wild.",
        1, DATE_ADD(NOW(), INTERVAL 5 DAY), 8.50, 1);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Cyclops", 2, 1, "Only one eye but sees the future.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 7.50, 0.4);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Faun", 2, 1, "A human with ram horns and hooves as feet, he runs very quickly.",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 11.50, 0.5);

/* Mythology group 2 & Class group 2:  Greek & Fairies */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Autumnae", 2, 2, "Only out in Autumn, she is the heart of fall",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 41.50, 13.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Crysta", 2, 2, "Is said to grant eternal wealth to those willing to help others.",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 33.50, 10.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Colorini", 2, 2, "Creates rainbows with her bare hands, astonishing!",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 46.50, 14.50);

/* Mythology group 2 & Class group 3:  Greek & Reptiles */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Naturio", 2, 3, "Multi-headed snake sure to help you get revenge on those enemies!",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 14.20, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Medusa", 2, 3, "Turns all who gaze into her eyes to stone.",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 4.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Snarrior", 2, 3, "A snake-warrior from ancient times, he protects your riches from thieves.",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 18.50, 0.5);

/* Mythology group 2 & Class group 4:  Greek & Dragons */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Night Fury", 2, 4, "A dark but friendly dragon - suitable for kids under 12.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 3.50, 0.2);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Horntail", 2, 4, "Make no mistake, the horntail is a fearsome beast capable of burning down a city.",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 700.0, 100.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Drogon", 2, 4, "The biggest, baddest dragon in all of Westeros. Daenerys is selling after she lost her 'mother of dragons' title.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 1000.0, 200.0);

/* Mythology group 2 & Class group 5:  Greek & Aquatic */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Flarian", 2, 5, "A sea horse in the truest form possible",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 44.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Cresta", 2, 5, "A black sea serpent capable of turning water to wine.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 54.50, 12.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Kygan", 2, 5, "A Mermaid that can help you swim underwater without needing to breathe!",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 72.50, 0.5);

/* Mythology group 2 & Class group 6:  Greek & Arachnids */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Huspi", 2, 6, "Half human half spider, be careful!",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 34.50, 4.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Gratchan", 2, 6, "A huge spider with great fangs, ouch.",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 75.0, 12.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Poseno", 2, 6, "Highly territorial, make sure his bed is comfortable.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 800.0, 150.0);

/* Mythology group 2 & Class group 7:  Greek & Birds */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Myrial", 2, 7, "A human head with a bird body that speaks fluent english.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 96.50, 18.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Phoenix", 2, 7, "The mighty phoenix, he shall rise from the ashes.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 650.0, 150.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Zygore", 2, 7, "A bird that flies faster than the speed of sound, try to race it!",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 310.50, 122.5);

/* Mythology group 3 & Class group 1:  Roman & Land Beasts */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Cerebus", 3, 1, "Fire-breathing, three-headed monster that will happily destroy manking given the chance, be cautious!",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 620.00, 95.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Minotaur", 3, 1, "The bull of all bulls, bigger than your house, watch out for his horns!",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 800.00, 200.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Gungan", 3, 1, "Can only say three words, none of which you will probably understand...",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 49.50, 0.5);

/* Mythology group 3 & Class group 2:  Roman & Fairies */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Roman Unicorn", 3, 2, "A horse with magical powers, the stuff of dreams.",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 500.0, 140.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Roman Wizard", 3, 2, "Helped roman soldiers heal and use special powers in ancient times, no wonder they were so strong!",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 88.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Romani", 3, 2, "The sacred fairy of ancient Rome, she is said to have given trees their leaves.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 9.50, 0.9);

/* Mythology group 3 & Class group 3:  Roman & Reptiles */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Basilisk", 3, 3, "The mighty basilisk, a ferocious snake capable of biting off human heads for breakfast.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 754.50, 10.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Snakani", 3, 3, "Pink coloured snake that heals people when it bites them, how interesting!",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 24.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Whimshae", 3, 3, "The white snake: said to freeze those that touch its scales.",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 34.50, 0.95);

/* Mythology group 3 & Class group 4:  Roman & Dragons */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Chimera", 3, 4, "A winged, three-headed lion that breathes fire, this one will scare away anyone.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 944.50, 300.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Orimae", 3, 4, "A horned dragon from ancient Rome, will burn your foes as well as pierce them.",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 750.00, 200.0);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Entrion", 3, 4, "The white dragon: fierce but wise and can help you in any quest.",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 800.00, 250.0);

/* Mythology group 3 & Class group 5:  Roman & Aquatic */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Octro", 3, 5, "A giant octopus from long ago, restored to full health and ready to digest those pirates.",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 200.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Husea", 3, 5, "Humans of the sea depths, buy yours today and help nurture it!",
        4, DATE_ADD(NOW(), INTERVAL 7 DAY), 4.50, 0.59);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Strangi", 3, 5, "Can strangle its prey in miliseconds, these creatures are as big as 4 blue whales.",
        2, DATE_ADD(NOW(), INTERVAL 7 DAY), 500.0, 100.0);

/* Mythology group 3 & Class group 6:  Roman & Arachnids */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Spidiro", 3, 6, "The tiniest, yet most deadly ancient spider... also brings people back to life.",
        1, DATE_ADD(NOW(), INTERVAL 7 DAY), 14.50, 0.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Spihu", 3, 6, "This arachnid has a human head - so go ahead and talk to him.",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 94.50, 10.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Daranoi", 3, 6, "The winged spider: it can fly and catch your house flies!",
        3, DATE_ADD(NOW(), INTERVAL 7 DAY), 3.50, 0.5);

/* Mythology group 3 & Class group 7:  Roman & Birds */

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Ririon", 3, 7, "Said to have come from another galaxy, this bird might just grant you a wish if you treat is nicely.",
        1, NOW(), 300.00, 19.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Utkash", 3, 7, "Brings with it joy and happiness wherever it travels, be sure to wish him well!",
        1, NOW(), 299.50, 12.5);

INSERT INTO items (name, mythology, animalClass, description, userID, endTime, buyNowPrice, startingPrice)
        VALUES ("Padrito", 3, 7, "A descendant of ancient Italy and the gods, you can ride it to far, far away.",
        1, NOW(), 100.50, 0.5);

INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('1', '1', 'Sphinx.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('2', '2', 'Guard.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('3', '3', 'Fox.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('4', '4', 'Healer.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('5', '5', 'Mystic.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('6', '6', 'Wozoo.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('7', '7', 'Hekret.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('8', '8', 'Apedes.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('9', '9', 'Masebo.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('10', '10', 'Spyro.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('11', '11', 'Hydra.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('12', '12', 'Trezo.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('13', '13', 'Manateno.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('14', '14', 'Serpent.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('15', '15', 'Falafeli.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('16', '16', 'Breeno.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('17', '17', 'Tarantulio.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('18', '18', 'Genoy.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('19', '19', 'Griffin.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('20', '20', 'Bennu.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('21', '21', 'Horus.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('22', '22', 'Gyro.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('23', '23', 'Cyclops.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('24', '24', 'Faun.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('25', '25', 'Autumnae.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('26', '26', 'Crysta.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('27', '27', 'Colorini.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('28', '28', 'Naturio.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('29', '29', 'Medusa.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('30', '30', 'Snarrior.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('31', '31', 'Night.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('32', '32', 'Horntail.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('33', '33', 'Drogon.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('34', '34', 'Flarian.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('35', '35', 'Cresta.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('36', '36', 'Kygan.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('37', '37', 'Huspi.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('38', '38', 'Gratchan.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('39', '39', 'Poseno.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('40', '40', 'Myrial.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('41', '41', 'Phoenix.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('42', '42', 'Zygore.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('43', '43', 'Cerebus.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('44', '44', 'Minotaur.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('45', '45', 'Gungan.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('46', '46', 'Unicorn.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('47', '47', 'Wizard.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('48', '48', 'Romani.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('49', '49', 'Basilisk.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('50', '50', 'Snakani.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('51', '51', 'Whimshae.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('52', '52', 'Chimera.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('53', '53', 'Orimae.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('54', '54', 'Entrion.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('55', '55', 'Octro.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('56', '56', 'Husea.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('57', '57', 'Strangi.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('58', '58', 'Spidiro.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('59', '59', 'Spihu.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('60', '60', 'Daranoi.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('61', '61', 'Ririon.jpg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('62', '62', 'Utkash.jpeg');
INSERT INTO pictures (pictureID, itemID, pictureName) VALUES ('63', '63', 'Padrito.jpg');

INSERT INTO bids (itemID, bidValue, userID) VALUES (1, 2, 1);
INSERT INTO bids (itemID, bidValue, userID) VALUES (1, 3.5, 2);
INSERT INTO bids (itemID, bidValue, userID) VALUES (1, 4, 3);

INSERT INTO bids (itemID, bidValue, userID) VALUES (2, 3.5, 4);
INSERT INTO bids (itemID, bidValue, userID) VALUES (2, 4, 3);
INSERT INTO bids (itemID, bidValue, userID) VALUES (2, 5.5, 2);

INSERT INTO bids (itemID, bidValue, userID) VALUES (3, 1.5, 1);
INSERT INTO bids (itemID, bidValue, userID) VALUES (3, 3.5, 3);
INSERT INTO bids (itemID, bidValue, userID) VALUES (3, 10, 4);

INSERT INTO bids (itemID, bidValue, userID) VALUES (63, 50, 2);
INSERT INTO bids (itemID, bidValue, userID) VALUES (62, 65.50, 2);
INSERT INTO bids (itemID, bidValue, userID) VALUES (61, 75.50, 2);

INSERT INTO bids (itemID, bidValue, userID) VALUES (60, 2, 1);
INSERT INTO bids (itemID, bidValue, userID) VALUES (57, 150.75, 1);

INSERT INTO bids (itemID, bidValue, userID) VALUES (10, 1.50, 1);
INSERT INTO bids (itemID, bidValue, userID) VALUES (10, 2.00, 3);
