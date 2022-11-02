<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Firebase\JWT\JWT;

function vite_assets(): HtmlString
{
    $devServerIsRunning = false;
    if (app()->environment('local')) {
        try {
            Http::get("http://localhost:5173");
            $devServerIsRunning = true;
        } catch (Exception) {
        }
    }
    
    if ($devServerIsRunning) {
        return new HtmlString(<<<HTML
            <script type="module" src="http://localhost:5173/@vite/client"></script>
            <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
        HTML);
    }
    
    $manifest = json_decode(file_get_contents(
        public_path('build/manifest.json')
    ), true);
    
    return new HtmlString(<<<HTML
        <script type="module" src="/build/{$manifest['resources/js/app.js']['file']}"></script>
        <link rel="stylesheet" href="/build/{$manifest['resources/js/app.js']['css'][0]}">
    HTML);
}

function Jwt_Token() {
    $secret_Key  = '68V0zWFrS72GbpPreidkQFLfj4v9m3Ti+DXc8OB0gcM=';
    $date   = new DateTimeImmutable();
    $expire_at     = $date->modify('+6 minutes')->getTimestamp();      // Add 60 seconds
    $domainName = "your.domain.name";
    $username   = "username";                                           // Retrieved from filtered POST data
    $request_data = [
        'iat'  => $date->getTimestamp(),         // Issued at: time when the token was generated
        'iss'  => $domainName,                       // Issuer
        'nbf'  => $date->getTimestamp(),         // Not before
        'exp'  => $expire_at,                           // Expire
        'userName' => $username,                     // User name
    ];

    $JwtToken = JWT::encode(
        $request_data,
        $secret_Key,
        'HS512'
    );
    return $JwtToken;
}