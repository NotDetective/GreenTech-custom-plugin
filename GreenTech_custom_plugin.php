<?php
/*
Plugin Name: GreenTech Custom Plugin
Description: A brief description of the plugin.
Version: 1.0.0
Author: Micha Elmans, Bo Leenders
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function random_quotes_get_quote() {

    $api_url = 'https://zenquotes.io/api/random';

    $response = wp_remote_get( $api_url );

    if ( is_wp_error( $response ) ) {
        return 'Error: Could not retrieve quote.';
    }

    $body = wp_remote_retrieve_body( $response );

    $data = json_decode( $body );

    if ( ! is_array( $data ) || empty( $data ) ) {
        return 'Error: Could not retrieve quote.';
    }

    return '"' . $data[0]->q . '" - ' . $data[0]->a;
}

function random_quotes_admin_widget() {
    echo '<p>' . esc_html( random_quotes_get_quote() ) . '</p>';
}

function random_quotes_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'random_quotes_widget',       // Widget slug
        'Random Quote',         // Widget title
        'random_quotes_admin_widget'  // Display callback
    );
}
add_action( 'wp_dashboard_setup', 'random_quotes_add_dashboard_widget' );