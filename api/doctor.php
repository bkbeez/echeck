<?php
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.edu.cmu.ac.th/v1/student/doctor/2568');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'checkin:Check$in1215');
    $result = curl_exec($ch);
    curl_close($ch);  
    echo $result;
?>