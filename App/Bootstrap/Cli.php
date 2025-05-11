<?php

namespace App\Bootstrap;

use App\Helper\Db;
use App\Migration\M20250509_0900_create_tables;

class Cli
{
    public array $argv;

    public function __construct($argv)
    {
        $this->argv = $argv;
    }

    /** createDbSchemas
     * @return void
     */
    public function process()
    {
        $cli_action = $this->argv[1] ?? '';
        switch ($cli_action) {
            case 'migrate':
                $this->makeMigration();
                break;
            case 'import_fetch':
                $this->makeImport();
                break;
            default:
                throw new \Exception('Need specify first arg as \'migrate\' or \'import_fetch\' to select cli action');
        }
    }

    private function makeMigration()
    {
        $is_up_or_down = $this->argv[2] ?? '';
        switch ($is_up_or_down) {
            case '1':
                $migration = new M20250509_0900_create_tables();
                $migration->Up();
                echo 'Adding tables finished';
                break;
            case '0':
                $migration = new M20250509_0900_create_tables();
                $migration->Down();
                echo 'Removing tables finished';
                break;
            default:
                throw new \Exception('Need specify second arg as 1 ot 0 to select apply or revert migration');
        }
    }

    private function makeImport()
    {
        $sql = '
            TRUNCATE posts;
            TRUNCATE comments;
        ';
        /** @var bool $isOk */
        $isOk = Db::multiQuery($sql);
        if (!$isOk) {
            throw new \Exception('Error while clearing data of posts and comments');
        }
        Db::getDb()->connection->next_result();
        $url = 'https://jsonplaceholder.typicode.com/posts';
        $fn_get_posts = function () use ($url) {
            $curl = curl_init($url);
            $headers = [
                'Accept: application/json'
            ];
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $json_response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            return compact('json_response', 'status');
        };
        $d = $fn_get_posts();
        $json_response = $d['json_response'];

        $post_added = 0;
        $posts_assoc_arr = json_decode($json_response, 1);
        if ($posts_assoc_arr && !empty($posts_assoc_arr) && is_array($posts_assoc_arr)) {
            foreach ($posts_assoc_arr as $post) {
                $userId = $post['userId'];
                $id = $post['id'];
                $title = $post['title'];
                $body = $post['body'];

                $sql = '
                    INSERT INTO posts
                    SET
                        userId = ' . intval($userId) . '
                        , id = ' . intval($id) . '
                        , title = "' . Db::re($title) . '"
                        , body = "' . Db::re($body) . '"
                ';

                $queryIsOk = !!Db::query($sql);
                if ($queryIsOk) {
                    $post_added += 1;
                }
            }
        }

        $url = 'https://jsonplaceholder.typicode.com/comments';
        $fn_get_comments = function () use ($url) {
            $curl = curl_init($url);
            $headers = [
                'Accept: application/json'
            ];
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $json_response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            return compact('json_response', 'status');
        };
        $d = $fn_get_comments();
        $json_response = $d['json_response'];

        $comments_added = 0;
        $comments_assoc_arr = json_decode($json_response, 1);
        if ($comments_assoc_arr && !empty($comments_assoc_arr) && is_array($comments_assoc_arr)) {
            foreach ($comments_assoc_arr as $post) {
                $postId = $post['postId'];
                $id = $post['id'];
                $name = $post['name'];
                $email = $post['email'];
                $body = $post['body'];

                $sql = '
                    INSERT INTO comments
                    SET
                        postId = ' . intval($postId) . '
                        , id = ' . intval($id) . '
                        , name = "' . Db::re($name) . '"
                        , email = "' . Db::re($email) . '"
                        , body = "' . Db::re($body) . '"
                ';

                $queryIsOk = !!Db::query($sql);
                if ($queryIsOk) {
                    $comments_added += 1;
                }
            }
        }

        echo 'Загружено ' . $post_added . ' записей и ' . $comments_added . ' комментариев';
    }
}