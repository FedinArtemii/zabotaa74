<?php
if (!defined('ABSPATH')) exit;
function zb_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_editor_style('assets/site.css');
    register_nav_menus(['primary' => 'Основная навигация']);
}
add_action('after_setup_theme', 'zb_setup');
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('zb-style', get_theme_file_uri('assets/site.css'), [], '1.0.0');
    wp_enqueue_script('zb-script', get_theme_file_uri('assets/site.js'), [], '1.0.0', ['strategy'=>'defer','in_footer'=>true]);
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');
});
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
add_filter('wp_robots', function($robots) { $robots['noindex']=true; $robots['nofollow']=true; return $robots; });
function zb_navigation($footer=false) {
    $items=['o-pansionate'=>'О пансионате','usloviya-prozhivaniya'=>'Условия проживания','fotogalereya'=>'Фотогалерея','stati'=>'Статьи','otzyvy'=>'Отзывы','kontakty'=>'Контакты'];
    foreach ($items as $slug=>$title) {
        $active=is_page($slug)?' aria-current="page"':'';
        echo '<a href="'.esc_url(home_url('/'.$slug.'/')).'"'.$active.'>'.esc_html($title).'</a>';
    }
}
function zb_arrow() { return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="1.4"/></svg>'; }
function zb_logo() { return '<svg viewBox="0 0 48 48" fill="none" aria-hidden="true"><path d="M24 40C24 28 8 32 8 19C8 8 24 10 24 22C24 10 40 8 40 19C40 32 24 28 24 40Z" stroke="currentColor" stroke-width="1.4"/><path d="M24 39V22" stroke="currentColor" stroke-width="1.4"/></svg>'; }
