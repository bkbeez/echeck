<?php
/**
 * Googleinfo Class
 */
class Googleinfo
{

    function __construct(){}

    function getHttp($url, $access_token)
    {
        $httpHeader = array("Authorization: Bearer ".$access_token);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    function getBasicinfo($access_token)
    {
        return $this->getHttp("https://www.googleapis.com/oauth2/v3/userinfo", $access_token);
    }

}
?>