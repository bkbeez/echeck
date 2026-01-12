<?php include('../../app/autoload.php'); ?>
<?php
    if( !isset($_SERVER['PHP_AUTH_USER'])||!isset($_SERVER['PHP_AUTH_USER']) ){
        header('HTTP/1.1 401 Authorization Required');
        header('WWW-Authenticate: Basic realm="Access denied"');
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Login Required']);
        exit();
    }else{
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json_payload = file_get_contents('php://input');
            $input = json_decode($json_payload, true);
            if(json_last_error() === JSON_ERROR_NONE){
                if( isset($input['events_id'])&&$input['events_id'] ){
                    $event = DB::one("SELECT events.*
                                    , CONCAT(DATE_FORMAT(events.start_date,'%d/%m/'), (YEAR(events.start_date)+543),' เวลา ',DATE_FORMAT(events.start_date, '%H:%i')) AS start_date_display
                                    , CONCAT(DATE_FORMAT(events.end_date,'%d/%m/'), (YEAR(events.end_date)+543),' เวลา ',DATE_FORMAT(events.end_date, '%H:%i')) AS end_date_display
                                    FROM events
                                    WHERE events.events_id=:events_id
                                    LIMIT 1;"
                                    , array('events_id'=>$input['events_id'])
                    );

                    http_response_code(200);
                    echo json_encode(['status' => 'success', 'info'=>$event]);
                    exit();
                }else{
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Not found events_id']);
                    exit();
                }
            }else{
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON payload']);
                exit();
            }
        }
        http_response_code(405);
        header('Allow: POST');
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
?>