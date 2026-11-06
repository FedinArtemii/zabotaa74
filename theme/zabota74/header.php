<?php if (!defined('ABSPATH')) exit; ?>
<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#351a1c"><link rel="icon" href="<?php echo esc_url(get_theme_file_uri('assets/favicon.svg')); ?>" type="image/svg+xml"><meta name="description" content="Пансионат «Забота» для пожилых людей в Копейске рядом с Челябинском. Временное и постоянное проживание, индивидуальный уход и домашняя атмосфера."><?php wp_head(); ?></head>
<body <?php body_class(is_front_page() ? 'home-page' : 'inner-page'); ?>><?php wp_body_open(); ?>
<a class="skip-link" href="#main">Перейти к содержанию</a>
<header class="site-header"><a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Забота — главная"><?php echo zb_logo(); ?><span class="brand-name">забота<span class="brand-caption">ПАНСИОНАТ ДЛЯ ПОЖИЛЫХ</span></span></a>
<nav id="primary-nav" class="primary-nav" aria-label="Основная навигация"><?php zb_navigation(); ?></nav>
<a class="header-phone" href="tel:+79507274120">+7 950 727 41 20 <span>Позвонить нам ↗</span></a>
<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav"><span></span><span></span><span class="sr-only">Открыть меню</span></button></header>
