<?php

namespace App\Controller;

use App\Bootstrap\Web;
use App\Helper\Db;
use App\Helper\H;

class IndexController
{
    public function index()
    {
        $data = [];

        $page = Web::$parts[0] ?? 1;

        if ($page == '') {
            $page = 1;
        }

        if ($page < 1) {
            $this->send404();
        }

        $items = [];
        $errors = [];

        $search_term = $_GET['s'] ?? '';
        $search_term = trim($search_term);
        $search_term_letter_count = mb_strlen($search_term);

        $sql = '
            SELECT count(p.id) AS `count`
            FROM posts AS p
            LIMIT 1
        ';

        $queryCount = Db::query($sql);
        $count = intval($queryCount->fetch_assoc()['count'] ?? 0);

        $comment_ids = [];
        $comments = [];

        $is_ok_for_getting_list = $count && $search_term_letter_count > 2;
        $data['is_search_made'] = false;
        if ($is_ok_for_getting_list) {
            $data['is_search_made'] = true;

            // getting items data
            $sql = '
                SELECT p.id, p.*, GROUP_CONCAT(c.id SEPARATOR \' \') AS `comment_ids`
                FROM posts as p
                LEFT JOIN comments as c ON p.id = c.postId
                WHERE c.body LIKE "%'.Db::re($search_term).'%"
                GROUP BY p.id
            ';

            /** @var \mysqli_result $query */
            $query = Db::query($sql);
            if ($query->num_rows) {
                while ($row = $query->fetch_assoc()) {
                    $comment_ids_from_query = $row['comment_ids'] ?? '';
                    $comment_ids_arr = explode(' ', $comment_ids_from_query);
                    $comment_ids = array_merge($comment_ids, $comment_ids_arr);
                    $items[] = $row;
                }

                $comment_ids = array_unique($comment_ids);
                $comment_ids = array_map('intval', $comment_ids);
                $sql = '
                    SELECT c.*
                    FROM comments as c
                    WHERE c.id IN (' . implode(', ', $comment_ids) . ')
                ';
                $query = Db::query($sql);
                if ($query->num_rows) {
                    while ($row = $query->fetch_assoc()) {
                        $postId = $row['postId'];
                        if (!key_exists($postId, $comments)) {
                            $comments[$postId] = [];
                        }
                        $comments[$postId][] = $row;
                    }
                }
            }
        } else {
            if (key_exists('s', $_GET)) {
                $errors['invalid_search_term_count'] = 'Запрос должен быть от 3 символов';
            }
        }

        $data['search_items'] = $items;
        $data['search_items_comments'] = $comments;
        $data['search_term'] = $search_term;
        $data['errors'] = $errors;

        $template_data = [];
        $template_data['content'] = H::commonViewRender(__DIR__ . '/../../resources/views/form.php', $data);

        echo H::commonViewRender(__DIR__ . '/../../resources/views/layout/layout.php', $template_data);
    }

    /**
     * @return never-return
     */
    private function send404()
    {
        http_response_code(404);
        echo 'Not found';
        exit;
    }
}