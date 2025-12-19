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

    private $baseURL;
    private $username;
    private $password;

    public function setCredentials($baseURL, $username, $password){
        $this->baseURL = $baseURL;
        $this->username = $username;
        $this->password = $password;
    }

    public function request($endpoint){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseURL . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        $result = curl_exec($ch);
        curl_close($ch);  
        return $result;

        curl_close($ch);

        if($error) {
            return['error' => $error];
        }
        return json_decode($response , true);
    }

    /**
     * ดึงรายชื่อนักศึกษาตามระดับการศึกษา
     */

    public function getStudent($year, $level) {
        switch ($level) {
            case 'bachelor':
                $endpoint = str_replace("{year}",$year,getenv("API_STUDENT_BACHELOR_ENDPOINT"));
                break;
            case 'master':
                $endpoint = str_replace("{year}",$year,getenv("API_STUDENT_MASTER_ENDPOINT"));
                break;
            case 'doctor':
                $endpoint = str_replace("{year}",$year,getenv("API_STUDENT_DOCTOR_ENDPOINT"));
                break;
            default:
                $endpoint = str_replace("{year}",$year,getenv("API_STUDENT_BACHELOR_ID"));
        }
        return $this->request($endpoint);
    }
}

?>