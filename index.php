<?php

ob_start();

require __DIR__ . "/vendor/autoload.php";

if (empty($_SESSION['user_login'])) {
    echo "Guest";

    /**
     * AUTH FACEBOOK
     */
    $facebook = new \League\OAuth2\Client\Provider\Facebook([
        'clientId' => FACEBOOK['app_id'],
        'clientSecret' => FACEBOOK['app_secret'],
        'redirectUri' => FACEBOOK['app_redirect'],
        'graphApiVersion' => FACEBOOK['app_version']
    ]);

    $authUrl = $facebook->getAuthorizationUrl([
        "scope" => ["email"]
    ]);

    $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);

    if($error){
        echo "Você precisa autorizar para continuar";
    }

    $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

    if($code){
        $token = $facebook->getAccessToken("authorization_code", [
            "code" => $code
        ]);

        $_SESSION['user_login'] = $facebook->getResourceOwner($token);
        header("Refresh: 0");
    }

    echo "<a title='FB Login' href='{$authUrl}'>Facebook Login</a>";

} else {
    /** @var $user \League\OAuth2\Client\Provider\Facebook */
    $user = $_SESSION['user_login'];
    echo "<img width='120' src='{$user->getPictureUrl()}' /><h1>Bem vindo {$user->getFirstName()}</h1>";
    var_dump($user);

    echo "<a title='Sair' href='?off=true'>Sair</a>";

    $off = filter_input(INPUT_GET, "off", FILTER_VALIDATE_BOOLEAN);

    if($off){
        unset($_SESSION['user_login']);
        header("Refresh: 0");
    }
}


ob_end_flush();