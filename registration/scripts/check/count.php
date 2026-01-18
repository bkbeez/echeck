<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php');?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    $events_id = ( (isset($_GET['events_id'])&&$_GET['events_id']) ? $_GET['events_id'] : null );
    function getStats($events_id) {
        $total = DB::one("SELECT COUNT(*) as cnt FROM events_lists WHERE status = 1 AND events_id=:events_id", array('events_id'=>$events_id));
        $lists = DB::sql("SELECT firstname, lastname, organization, date_checkin 
                            FROM events_lists 
                            WHERE status = 1 AND events_id=:events_id
                            ORDER BY date_checkin DESC 
                            LIMIT 20", array('events_id'=>$events_id));
        $data_list = [];
        if($lists){
            foreach($lists as $row) {
                $data_list[] = [
                    'name' => $row['firstname'].' '.$row['lastname'],
                    'org'  => $row['organization'],
                    'time' => date('H:i:s', strtotime($row['date_checkin']))
                ];
            }
        }
        return [
            'count' => number_format($total['cnt']),
            'list'  => $data_list
        ];
    }
    while (true) {
        $data = getStats($events_id);
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();
        sleep(2);
    }
?>