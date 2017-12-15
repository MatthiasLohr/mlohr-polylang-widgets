<?php

/*
Plugin Name: MLohr's Basic Plugins with extended Polylang Support
Plugin URI: https://mlohr.com/
Description: This plugin adds some more Polylang support to basic widgets like Tag Cloud, Categories and Recent Posts
Version: 0.1.0
Author: Matthias Lohr <matthias@lohr.me>
Author URI: https://mlohr.com/
License: GPLv3
*/

require_once('categories.php');
require_once('recent-posts.php');
require_once('tag-cloud.php');

function mlohr_polylang_init_widgets() {
    register_widget('Mlohr_Polylang_Widget_Categories');
    register_widget('Mlohr_Polylang_Widget_Recent_Posts');
    register_widget('Mlohr_Polylang_Widget_Tag_Cloud');
}

add_action('widgets_init', 'mlohr_polylang_init_widgets', 1);
