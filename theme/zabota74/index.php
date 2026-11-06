<?php
if (!defined('ABSPATH')) exit;
get_header();
echo '<main id="main">';
if (have_posts()) {
    while(have_posts()) { the_post();
        if (!is_front_page()) {
            echo '<div class="page-heading wrap"><div class="breadcrumbs"><a href="'.esc_url(home_url('/')).'">Главная</a><span>/</span>';
            if(is_singular('post')) echo '<a href="'.esc_url(home_url('/stati/')).'">Статьи</a><span>/</span>';
            echo '<span>'.esc_html(get_the_title()).'</span></div><p class="eyebrow">ПАНСИОНАТ «ЗАБОТА» · КОПЕЙСК</p><h1>'.esc_html(get_the_title()).'</h1></div>';
        }
        echo '<div class="page-content'.(is_singular('post')?' article-content wrap':'').'">';
        the_content();
        echo '</div>';
    }
} else {
    echo '<section class="not-found wrap"><p class="eyebrow">404</p><h1>Здесь пока тихо.</h1><p>Такой страницы нет. Давайте вернёмся домой.</p><a class="text-link" href="'.esc_url(home_url('/')).'">На главную →</a></section>';
}
echo '</main>';
get_footer();
