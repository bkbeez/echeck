<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php 
    $total = DB::one("SELECT COUNT(*) as cnt FROM events_lists WHERE status = 1");
    $lists = DB::sql("SELECT firstname, lastname, organization, date_checkin 
                        FROM events_lists 
                        WHERE status = 1 
                        ORDER BY date_checkin DESC 
                        LIMIT 120");

    $data_list = [];
    if($lists){
        foreach($lists as $row) {
            $data_list[] = [
                'name' => $row['firstname'].' '.$row['lastname'],
                'org'  => $row['organization'],
                'time' => date('H:i', strtotime($row['date_checkin']))
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'count'  => number_format($total['cnt']),
        'list'   => $data_list
    ]);
?>