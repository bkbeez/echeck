<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    $result = array();
    $sql = "SELECT (SELECT COALESCE(COUNT(member_id),0) FROM meeting_participant WHERE meeting_id=:meeting_id AND status_id=0) AS checks
                    , (SELECT COALESCE(COUNT(member_id),0) FROM meeting_participant WHERE meeting_id=:meeting_id AND meeting_participant.fullpaper_status='W') AS articles
                    , (SELECT COALESCE(COUNT(member_id),0) FROM meeting_participant WHERE meeting_id=:meeting_id AND meeting_participant.publish_status='W') AS fullpapers";
    $check = DB::one($sql, array('meeting_id'=>User::meeting()));
    $result['checks'] = ((isset($check['checks'])&&$check['checks'])?intval($check['checks']):0);
    $result['articles'] = ((isset($check['articles'])&&$check['articles'])?intval($check['articles']):0);
    $result['fullpapers'] = ((isset($check['fullpapers'])&&$check['fullpapers'])?intval($check['fullpapers']):0);
    echo 'data: '.json_encode($result)."\n\n";
    flush();
?>