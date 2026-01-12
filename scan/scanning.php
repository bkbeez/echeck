<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/scan'); ?>
<?php
    if( isset($_POST['decoded_text'])&&$_POST['decoded_text'] ){
        $events_id = null;
        $links = explode('/?', $_POST['decoded_text']);
        if( (isset($links[0])&&$links[0])&&(isset($links[1])&&$links[1]) ){
            $url = $links[0];
            $events = explode('=', $links[1]);
            $events_id = ( (isset($events[1])&&$events[1]) ? $events[1] : null );
            if( $events_id ){
                $htmls ='<form name="OpenForm" action="'.APP_PATH.'/scan/" method="POST" enctype="multipart/form-data" group="form" style="display:none;">';
                    $htmls .='<input type="hidden" name="events_id" value="'.$events_id.'"/>';
                    $htmls .='<input type="submit"/>';
                $htmls .='</form>';
                Status::success( "สแกนเรียบร้อยแล้ว", array('htmls'=>$htmls));
            }
        }
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถสแกนได้") );
?>