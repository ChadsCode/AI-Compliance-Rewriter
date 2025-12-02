<?php
// core/openai.php — wraps CURL + your API key
require_once __DIR__ . '/../config/config.php';

/**
 * Make an API call to OpenAI
 * 
 * @param string $endpoint    The API endpoint (e.g., "/v1/chat/completions")
 * @param array $body         The request body as an array
 * @param string $method      HTTP method (default: POST)
 * @return array              The JSON response decoded as an array
 * @throws Exception          If the API request fails
 */
function openai_api(string $endpoint, array $body = [], string $method = 'POST'): array {
    $url = 'https://api.openai.com' . $endpoint;
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Connection optimizations for speed
    curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
    curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    
    // Handle different HTTP methods
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    } elseif ($method === 'GET') {
        // For GET requests, append parameters to URL if needed
        if (!empty($body)) {
            $url .= '?' . http_build_query($body);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    } else {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
    }

    $resp = curl_exec($ch);
    if ($err = curl_error($ch)) {
        curl_close($ch);
        throw new Exception("cURL error: $err");
    }
    
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code < 200 || $code >= 300) {
        throw new Exception("OpenAI API returned HTTP $code: $resp");
    }
    
    $decoded = json_decode($resp, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Failed to decode API response: " . json_last_error_msg());
    }
    
    return $decoded;
}