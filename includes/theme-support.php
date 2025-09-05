<?php


function bothwick_theme_support()
{
    add_theme_support('custom-logo');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('title-tag');
    register_nav_menu('header_menu', 'Header Menu');
    register_nav_menu('footer_menu_link', 'Footer Menu Link');
}


add_action('after_setup_theme', 'bothwick_theme_support');