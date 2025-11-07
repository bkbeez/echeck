<?php
/**
 * Google Class
 */
class Google {
    private $GOOGLE_APP_ID = GOOGLE_APP_ID;
    private $GOOGLE_APP_SECRET = GOOGLE_APP_SECRET;
    private $GOOGLE_CALLBACK_URI = null;
    private $GOOGLE_AUTHORIZE_URL = 'https://accounts.google.com/o/oauth2/auth';
    private $GOOGLE_TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
    private $USER_INFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';
    private $SCOPE = null;
    private $ACCESS = 'offline';
    private $PROMPT = 'consent';

    function __construct($app_id = null, $app_secret = null, $call_back = null)
    {
        if($app_id!==null) $this->GOOGLE_APP_ID = $app_id;
        if($app_secret!==null) $this->GOOGLE_APP_SECRET = $app_secret;
        if($call_back!==null) $this->GOOGLE_CALLBACK_URI = $call_back;
    }

    function setAppId($appid)
    {
        $this->GOOGLE_APP_ID = $appid;
    }

    function setAppSecret($appSecret)
    {
        $this->GOOGLE_APP_SECRET = $appSecret;
    }

    function setCallbackUri($uri)
    {
        $this->GOOGLE_CALLBACK_URI = $uri;
    }

    function setScope($scope)
    {
        $this->SCOPE = $scope;
    }

    function initGoogle()
    {
        header("location: $this->GOOGLE_AUTHORIZE_URL?response_type=code&client_id=$this->GOOGLE_APP_ID&redirect_uri=$this->GOOGLE_CALLBACK_URI&scope=$this->SCOPE&access_type=$this->ACCESS&prompt=$this->PROMPT");
        exit();
    }

    function getAccessTokenAuthCode($code)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->GOOGLE_TOKEN_URL);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS,
        "code=$code".
        "&client_id=$this->GOOGLE_APP_ID".
        "&client_secret=$this->GOOGLE_APP_SECRET".
        "&redirect_uri=$this->GOOGLE_CALLBACK_URI".
        "&grant_type=authorization_code");
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    function getAccessTokenClientCred()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->GOOGLE_TOKEN_URL);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERPWD, $this->GOOGLE_APP_ID.":".$this->GOOGLE_APP_SECRET);
        curl_setopt($curl, CURLOPT_POSTFIELDS,
        "grant_type=client_credentials".
        "&scope=$this->SCOPE");
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

}
?>