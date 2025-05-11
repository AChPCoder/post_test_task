<?php

namespace App\Helper;

class Db
{
    private static Db|null $db = null;

    /**
     * @return Db|null
     */
    public static function getDb(): ?Db
    {
        if (!isset(self::$db)) {
            self::$db = new Db();
        }
        return self::$db;
    }

    public function __construct()
    {
        $type = Config::getConfig()['db']['current'];
        switch ($type) {
            case 'mysql':
                $mysql_config = Config::getConfig()['db']['mysql'];
                $this->connection = new \mysqli(
                    $mysql_config['host'],
                    $mysql_config['username'],
                    $mysql_config['password'],
                    $mysql_config['database'],
                    $mysql_config['port'],
                );
                if ($mysql_config['charset']) {
                    $this->connection->set_charset($mysql_config['charset']);
                }
                break;
            default:
                throw new \Exception('Unsupported db engine');
        }
    }

    public \mysqli|false $connection;

    /** Execute sql query
     * @param string $sql An SQL query
     * @return \mysqli_result|bool For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries, mysqli_query() will return
     * a mysqli_result object. For other successful queries mysqli_query() will return TRUE. Returns FALSE on failure.
     */
    public static function query($sql): \mysqli_result|bool
    {
        return self::getDb()->connection->query($sql);
    }

    /**
     * @param string $sql The query, as a string.
     * <br/> Data inside the query should be properly escaped.
     * @return \mysqli_result|bool false if the first statement failed. To retrieve subsequent errors from other statements you have to call mysqli_next_result first.
     */
    public static function multiQuery($sql): \mysqli_result|bool
    {
        return self::getDb()->connection->multi_query($sql);
    }

    public static function re($str) {
        return self::getDb()->connection->real_escape_string($str);
    }
}