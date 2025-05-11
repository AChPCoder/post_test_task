<?php

namespace App\Migration;

use App\Helper\Db;

class M20250509_0900_create_tables
{
    public function Up()
    {
        $sql = '
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
        ';

        /** @var bool $query */
        $query = Db::multiQuery($sql);

        return $query;

    }

    public function Down()
    {
        $sql = '
            DROP TABLE posts;
            DROP TABLE comments;
        ';

        /** @var bool $query */
        $query = Db::multiQuery($sql);

        return $query;
    }
}