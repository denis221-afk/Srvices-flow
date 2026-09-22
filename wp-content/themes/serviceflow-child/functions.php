<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function serviceflow_child_enqueue_styles() {
    wp_enqueue_style(
        'serviceflow-child',
        get_stylesheet_uri(),
        array(),
        '0.1.0'
    );
}

add_action( 'wp_enqueue_scripts', 'serviceflow_child_enqueue_styles' );