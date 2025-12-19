<?php
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, '');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'username:pasword');
    $result = curl_exec($ch);
    curl_close($ch);  
    echo $result;
?>