CREATE TABLE posts
(
    id     int,
    userId int,
    title  varchar(255),
    body   text
) ENGINE = MyISAM COLLATE utf8_general_ci;

CREATE TABLE comments
(
    id     int,
    postId int,
    name   varchar(255),
    email  varchar(255),
    body   text
) ENGINE = MyISAM COLLATE utf8_general_ci;