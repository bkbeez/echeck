<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php');?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
    set_time_limit(0);
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    header('X-Accel-Buffering: no'); 
    if (function_exists('apache_setenv')) {
        @apache_setenv('no-gzip', 1);
    }
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    while (ob_get_level()) ob_end_flush();
    ob_implicit_flush(1);
    $events_id = ( (isset($_GET['events_id'])&&$_GET['events_id']) ? $_GET['events_id'] : null );
    $last_checksum = null;
    session_write_close();
    while (true) {
        if (connection_aborted()) break;
        $check = DB::one("SELECT COUNT(id) as cnt, MAX(date_checkin) as last_update 
                          FROM events_lists 
                          WHERE status = 1 AND events_id=:events_id", 
                          array('events_id'=>$events_id));
        $current_checksum = ($check['cnt'] ?? 0) . '_' . ($check['last_update'] ?? '');
        if ($current_checksum !== $last_checksum) {
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
            $data = [
                'count' => number_format($check['cnt'] ?? 0),
                'list'  => $data_list
            ];
            echo "data: " . json_encode($data) . "\n\n";
            flush(); 
            $last_checksum = $current_checksum;
        } 
        usleep(500000);
    }
?>