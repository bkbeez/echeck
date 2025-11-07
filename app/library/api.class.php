<?php
/**
 * Api Class
 */
class Api {
    private $function = null;
    private $method = null;
    private $action = null;

    function __construct($request){
        if( isset($request['url'])&&$request['url'] ){
            $urls = explode('/', substr($request['url'],1));
            if( isset($urls[0])&&$urls[0] ){
                foreach($urls as $key => $value){
                    if( $key==0 ){
                        $this->function = $value;
                    }else if( $key==1 ){
                        $this->method = $value;
                    }else if( $key==2 ){
                        $this->action = $value;
                    }else{
                        break;
                    }
                }
                if( count(array_splice($urls,3))>0 ){
                    http_response_code(400);
                    echo json_encode(array('error'=>array('code'=>"400",'message'=>"Bad Request")));
                    exit();
                }
            }else{
                http_response_code(400);
                echo json_encode(array('error'=>array('code'=>"400",'message'=>"Bad Request")));
                exit();
            }
        }else{
            http_response_code(400);
            echo json_encode(array('error'=>array('code'=>"400",'message'=>"Bad Request")));
            exit();
        }
    }

    /**
     * Get Function
     * @param  void
     * @return string
     */
    public function getFunction(){
        return $this->function;
    }

    /**
     * Get Method
     * @param  void
     * @return string
     */
    public function getMethod(){
        return $this->method;
    }

    /**
     * Get Action
     * @param  void
     * @return string
     */
    public function getAction(){
        return $this->action;
    }

    /**
     * Error
     */
    public function error(){
        http_response_code(400);
        echo json_encode(array('error' => array('code'=>"400", 'message'=>"Bad Request") ));
        exit();
    }

}
?>