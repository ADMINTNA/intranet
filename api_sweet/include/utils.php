<?php
// File: /api_sweet/includes/utils.php

function get_crm_token() {
    // Reemplaza estos valores con tus credenciales SuiteCRM
    $client_id = 'gpt-client-id';
    $client_secret = 'gpt-client-secret';
    $username = 'Mauricio';
    $password = 'Mausora.1306..';
    $token_url = 'https://sweet.icontel.cl/Api/access_token';

    $postFields = [
        'grant_type' => 'password',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'username' => $username,
        'password' => $password,
        'platform' => 'base'
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['access_token'] ?? false;
}
