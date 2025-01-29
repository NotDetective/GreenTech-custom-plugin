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

function fetch_weather_data() {
    $api_url = "https://weatherbit-v1-mashape.p.rapidapi.com/forecast/3hourly?lat=51.841576&lon=5.875698&units=metric&lang=nl";
    $api_key = "690f4e0e93mshb0ef3fcbfa1a095p199fd8jsn921d799f9c79";

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: weatherbit-v1-mashape.p.rapidapi.com",
            "x-rapidapi-key: $api_key"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "Error fetching weather data: $err";
    }

    return $response;
}

// Function to display weather data in a widget
function display_weather_widget() {
    $weather_data = fetch_weather_data();

    // Decode JSON data
    $data = json_decode( $weather_data, true );

    if ( isset( $data['data'][0] ) ) {
        $forecast = $data['data'][0];
        $description = $forecast['weather']['description'];
        $temp = $forecast['temp'];
        $wind_speed = $forecast['wind_spd'];
        $humidity = $forecast['rh'];

        echo "<div class='weather-widget' style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
        echo "<h3>Weather Forecast</h3>";
        echo "<p><strong>Description:</strong> $description</p>";
        echo "<p><strong>Temperature:</strong> {$temp}°C</p>";
        echo "<p><strong>Humidity:</strong> {$humidity}%</p>";
        echo "<p><strong>Wind Speed:</strong> {$wind_speed} m/s</p>";
        echo "</div>";
    } else {
        echo "<p>Unable to fetch weather data.</p>";
    }
}

// Add the weather widget to WordPress
function register_weather_widget() {
    wp_add_dashboard_widget(
        'weather_widget',       // Widget slug
        'Weather Forecast',     // Widget title
        'display_weather_widget' // Display callback
    );
}
add_action( 'wp_dashboard_setup', 'register_weather_widget' );