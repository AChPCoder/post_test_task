CREATE TABLE IF NOT EXISTS `posts`
(
    `id`     INT(11) AUTO_INCREMENT,
    `userId` INT(11),
    `title`  VARCHAR(255),
    `body`   TEXT,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `comments`
(
    `id`     INT(11) AUTO_INCREMENT,
    `postId` INT(11),
    `name`   VARCHAR(255),
    `email`  VARCHAR(255),
    `body`   TEXT,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM COLLATE utf8_general_ci;