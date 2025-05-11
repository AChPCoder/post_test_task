<?php

use App\Helper\H;

/** @var string $search_term */
/** @var array $search_items */
/** @var array $search_items_comments */
/** @var array $errors */
?>
<div class="search-container">

    <form action="<?= H::getBaseUrl() ?>" method="get" class="search-form">
        <label class="d-flex flex-column">
            <span>Ключевая фраза</span>
            <input type="text" name="s" placeholder="Введите сюда для поиска от 3 символов" <?=
            !empty($search_term) ? 'value="' . H::e($search_term) . '"' : '' ?> autocomplete="off"/>
        </label>
        <button type="submit">Найти</button>
    </form>

    <div class="search-items-list">
        <?php if ($errors) { ?>
            <div class="search-items-list--errors">
                <?php foreach ($errors as $error) { ?>
                    <div><?= $error ?></div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($is_search_made ?? false) { ?>
            <?php
            $is_ok = isset($search_items) && is_array($search_items) && count($search_items);
            if ($is_ok) { ?>

                <?php foreach ($search_items as $item) { ?>

                    <div class="search-items-list--item">

                        <div class="search-items-list--item-title">
                            <div>Заголовок записи:</div>
                            <div><?= H::e($item['title']) ?></div>
                        </div>

                        <div class="search-items-list--item-body">
                            <div>Комментарии с целевой строкой:</div>

                            <?php $is_set_for_current_post = is_array($search_items_comments)
                                && key_exists($item['id'], $search_items_comments); ?>
                            <?php if ($is_set_for_current_post) { ?>
                                <?php foreach ($search_items_comments[$item['id']] as $comment) { ?>
                                    <div class="search-items-list--item-comment">
                                        <div>
                                            <div>Комментарий с ИД:</div>
                                            <div><?= $comment['id'] ?></div>
                                        </div>
                                        <div>
                                            <div>Текст комментария:</div>
                                            <div><?= $comment['body'] ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>
                <div class="search-items-list--not-found">Для данной поисковой строки не найдены записи</div>
            <?php } ?>

        <?php } ?>
    </div>
</div>

