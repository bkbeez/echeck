<?php require('mail/class.smtp.php'); ?>
<?php require('mail/class.phpmailer.php'); ?>
<?php
/*
 * Mail Class
 */
class Mail {
    protected $smtp = MAIL_SMTP;
    protected $host = MAIL_HOST;
    protected $port = MAIL_POST;
    protected $debug = 0;
    protected $username = MAIL_USER;
    protected $password = MAIL_PASS;
    protected $from_name = APP_CODE;
    protected $from_reply = MAIL_NOREPLY;
    protected $admin_mail = MAIL_ADMIN;

    /**
     * Get Template
     * @param  template
     * @return boolean
     */
    static function getTemplate($template='default')
    {
        $_SITE_ = 'https://nice.edu.cmu.ac.th';
        $_TITLE_ = APP_CODE;
        $_LOGO_  = '<div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">';
            $_LOGO_ .= '';
            $_LOGO_ .= '<b style="color:#2576bb;font-size:42px;font-family:sans-serif;"><sup><small style="color:#FFF;font-size:22px;">THE </small></sup>N</b>';
            $_LOGO_ .= '<b style="color:#fec655;font-size:42px;font-family:sans-serif;">I</b>';
            $_LOGO_ .= '<b style="color:#36ba8b;font-size:42px;font-family:sans-serif;">C</b>';
            $_LOGO_ .= '<b style="color:#f68878;font-size:42px;font-family:sans-serif;">E</b>';
            $_LOGO_ .= '<b style="color:#2576bb;font-size:42px;font-family:sans-serif;"> EDU CMU </b>';
            $_LOGO_ .= '<b style="color:#2576bb;font-size:42px;font-family:sans-serif;">2</b>';
            $_LOGO_ .= '<b style="color:#fec655;font-size:42px;font-family:sans-serif;">0</b>';
            $_LOGO_ .= '<b style="color:#36ba8b;font-size:42px;font-family:sans-serif;">2</b>';
            $_LOGO_ .= '<b style="color:#f68878;font-size:42px;font-family:sans-serif;">5</b>';
        $_LOGO_ .= '</div>';
        $_LOGO_ .= '<span style="font-size:19px;">'.Util::meeting('name_'.App::lang()).'</span>';
        $_DEAR_  = Lang::get('Dear');
        $_FOOTER_ = ((App::lang()=='en')?APP_FACT_EN:APP_FACT_TH);
        $_NOREPLY_ = ((App::lang()=='en')?'*** Automatic message, <u style="color:red;">Please do not reply to this email.</u> ***':'*** ข้อความอัตโนมัติ <u style="color:red;">โปรดอย่าตอบกลับอีเมล์ฉบับนี้</u> ***');
        return str_replace(array('_SITE_','_TITLE_', '_LOGO_', '_DEAR_', '_FOOTER_', '_NOREPLY_'), array($_SITE_, $_TITLE_, $_LOGO_, $_DEAR_, $_FOOTER_, $_NOREPLY_), file_get_contents(APP_ROOT.'/app/library/mail/template/'.$template.'.html', dirname(__FILE__)));
    }

    /**
     * Send
     * @param  subject, content, $to, $log
     * @return boolean
     */
    static function send($subject, $content, $to=array(), $logmsg=null)
    {
        $self = new Mail();
        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = $self->debug;
        if( $self->debug>0 ){
            $mail->Debugoutput = 'html';
        }
        $mail->Host = $self->host;
        $mail->Port = $self->port;
        $mail->SMTPAuth = true;
        $mail->Username = $self->username;
        $mail->Password = $self->password;
        $mail->SMTPSecure = $self->smtp;
        $mail->SMTPAutoTLS = true;
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false
                                                , 'verify_peer_name' => false
                                                , 'allow_self_signed' => true
                                            )
        );
        $mail->setFrom($self->from_reply, $self->from_name);
        $mail->addReplyTo($self->from_reply, $self->from_name);
        $mail->Subject = $subject;
        if( Helper::isLocal() ){
            if( isset($to['testing']) ){
                if( isset($to['name'])&&$to['name'] ){
                    $mail->addAddress($to['email'], $to['name']);
                }else{
                    $mail->addAddress($to['email']);
                }
            }else{
                $mail->addAddress($self->admin_mail, 'Tester');
            }
        }else{
            if( isset($to['name'])&&$to['name'] ){
                $mail->addAddress($to['email'], $to['name']);
            }else{
                $mail->addAddress($to['email']);
            }
        }
        $mail->AltBody = Lang::get('AutoReplyMsg');
        $mail->MsgHTML($content);
        if( !$mail->Send() ) {
            if( $logmsg ){
                Log::mail( array('subject'=>$subject, 'message'=>$logmsg, 'status'=>400, 'remark'=>$mail->ErrorInfo) );
            }
            return array( 'status'=>'error', 'title'=>Lang::get('Error'), 'text'=>$mail->ErrorInfo );
        } else {
            if( $logmsg ){
                Log::mail( array('subject'=>$subject, 'message'=>$logmsg, 'status'=>200, 'remark'=>'success') );
            }
            return array( 'status'=>'success', 'title'=>Lang::get('Success'), 'text'=>Lang::get('SuccessSend'));
        }
    }

}
?>