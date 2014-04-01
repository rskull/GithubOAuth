<?php

// load Class
require_once 'GithubOAuth.php';

$client_id = '__YOUR_CLIENT_ID__';
$client_secret = '__YOUR_CLIENT_ID_SECRET__';

$OAuth = new GithubOAuth($client_id ,$client_secret);

// Callback
if (!empty($_GET['code'])) {

    $access_token = $OAuth->getAccessToken($_GET['code']);
    $result = $OAuth->api('user', 'GET', $access_token);
    $user_info = json_decode($result);

    if (!empty($user_info->login)) {

        // OK
        echo $user_info->login; // rskull
        echo $user_info->name; // R.SkuLL
        echo $user_info->id; // 123456

    } else {
        // Error
    }

} else {

    // Authorize URL
    $url = $OAuth->getAuthURL();

    // test
    echo '<a href="' . $url . '">Github</a>';

}

