<?php
/**
 *  Githbu OAuth Class
 *
 *  @version 1.0 2013/05/10
 *  @author R.SkuLL
 *  @license The MIT License
 *
 *  @copyright 2013 Geekz Web Development
 */
class GithubOAuth
{
    // Client ID
    private $client_id;

    // Client Secret
    private $client_secret;

    // Set timeout default
    public $timeout = 30;

    // Set connect timeout
    public $connect_timeout = 30;

    // Authrize URL
    const AUTHORIZE_URL = 'https://github.com/login/oauth/authorize?';

    // Access Token URL
    const ACCESS_TOKEN_URL = 'https://github.com/login/oauth/access_token?';

    // API URL
    const API_URL = 'https://api.github.com/';

    /**
     * コンストラクタ
     */
    public function __construct($client_id, $client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    /**
     * API Request
     * @param string url リクエストURL
     * @param string method メソッド
     * @param mixed param パラメータ
     * @return mixed レスポンス
     */
    public function api($api, $method = 'GET', $param)
    {
        $url = self::API_URL.$api;
        return $this->http($url, $method, $param);
    }

    /**
     * 認証用リクエストURLを生成
     * @return string リクエストURL
     */
    public function getAuthURL()
    {
        $query = http_build_query(array(
            'client_id' => $this->client_id
        ));
        return self::AUTHORIZE_URL.$query;
    }

    /**
     * アクセストークンを取得
     * @param string code 認証後に受け取ったコード
     * @return string アクセストークン
     */
    public function getAccessToken($code)
    {
        $result = null;
        if (!empty($code)) {
            $result = $this->http(self::ACCESS_TOKEN_URL, 'POST', array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $code
            ));
        }
        return $result;
    }

    /**
     * 認証で使うユーザーエージェントを取得
     * @return string UserAgent
     */
    private function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * HTTP Request
     * @param string url リクエストURL
     * @param string method メソッド
     * @param mixed param パラメータ
     * @return mixed レスポンス
     */
    private function http($url, $method = 'GET', $param = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($curl, CURLOPT_HEADER, false);

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                if (!empty($param)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
                }
                break;
            case 'GET':
                if (is_array($param)) $param = http_build_query($param);
                $url .= "?{$param}";
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

}

