<?php
/**
 * Status Class
 */
class Status {

    /**
     * success
     * @param  text, values 
     * @return array
     */
    static function success($text, $values=array()){
        $status = array();
        $status['status'] = 'success';
        $status['title'] = Lang::get('Success');
        $status['text'] = $text;
        if( count($values)>0 ){
            foreach($values as $key => $value){
                $status[$key] = $value;
            }
        }

        echo json_encode($status);
        exit();
    }

    /**
     * warning
     * @param  text, values 
     * @return array
     */
    static function warning($text, $values=array()){
        $status = array();
        $status['status'] = 'warning';
        $status['title'] = Lang::get('Warning');
        $status['text'] = $text;
        if( count($values)>0 ){
            foreach($values as $key => $value){
                $status[$key] = $value;
            }
        }

        echo json_encode($status);
        exit();
    }

    /**
     * error
     * @param  text, values
     * @return array
     */
    static function error($text, $values=array()){
        $status = array();
        $status['status'] = 'error';
        $status['title'] = Lang::get('Error');
        $status['text'] = $text;
        if( count($values)>0 ){
            foreach($values as $key => $value){
                $status[$key] = $value;
            }
        }

        echo json_encode($status);
        exit();
    }

}
?>