<?php
/**
 * Helper Class
 */
class Helper {

    /**
     * Is Local
     * @param void
     * @return boolean
     */
    static function isLocal()
    {
        return in_array($_SERVER["HTTP_HOST"], array('127.0.0.1','localhost','checkin.edu.cmu'));
    }

    /**
     * Valid Time
     * @param date
     * @return string
     */
    static function validTime($value)
    {
        $values = explode(':',$value);
        if( isset($values[0])&&intval($values[0])>23 ){
            return false;
        }else if( isset($values[1])&&intval($values[1])>59 ){
            return false;
        }

        return true;
    }

    /**
     * Date
     * @param date
     * @return string
     */
    static function date($date, $is_be=true)
    {
        if( $date instanceof DateTime ){
            // This is date
        }else{
            $date = new datetime($date);
        }
        $year = intval($date->format("Y"));
        if( $is_be ){
            $year += 543;
        }

        return ($date->format("d")."/".$date->format("m")."/".$year);
    }

    /**
     * Date Save
     * @param date
     * @return string
     */
    static function dateSave($date, $is_be=true)
    {
        $results = null;
        $dates = explode(" ", $date);
        if( isset($dates[0])&&$dates[0] ){
            $date = explode("/", $dates[0]);
            $year = intval($date[2]);
            if( $is_be ){
                $year -= 543;
            }
            $results = $year."-".$date[1]."-".$date[0];
        }
        if( isset($dates[1])&&$dates[1] ){
            $results .= $dates[1];
        }

        return $results;
    }

    /**
     * Date Display
     * @param  date
     * @return string
     */
    static function dateDisplay($date, $lang='th')
    {
        if( $date instanceof DateTime ){
            // This is date
        }else{
            $date = new datetime($date);
        }
        $day = intval($date->format("d"));
        $year = intval($date->format("Y"));
        if( $lang=='en' ){
            $result = $date->format("F").' '.$day.', '.$year;
        }else{
            switch ( $date->format("m") ) {
                case '01': $month = "มกราคม";   break;
                case '02': $month = "กุมภาพันธ์";  break;
                case '03': $month = "มีนาคม";    break;
                case '04': $month = "เมษายน";   break;
                case '05': $month = "พฤษภาคม";  break;
                case '06': $month = "มิถุนายน";   break;
                case '07': $month = "กรกฎาคม";  break;
                case '08': $month = "สิงหาคม";   break;
                case '09': $month = "กันยายน";   break;
                case '10': $month = "ตุลาคม";    break;
                case '11': $month = "พฤศจิกายน"; break;
                case '12': $month = "ธันวาคม";   break;
                default:
                    $month = null;
                    break;
            }
            $result = $day.' '.$month.' '.($year+543);
        }

        return $result;
    }

    /**
     * Date Display Short
     * @param  date
     * @return string
     */
    static function dateDisplayShort($date, $lang='th')
    {
        if( $date instanceof DateTime ){
            // This is date
        }else{
            $date = new datetime($date);
        }
        $day = intval($date->format("d"));
        $year = intval($date->format("Y"));
        if( $lang=='en' ){
            $result = $date->format("M").' '.$day.', '.$year;
        }else{
            switch ( $date->format("m") ) {
                case '01': $month = "ม.ค.";   break;
                case '02': $month = "ก.พ.";  break;
                case '03': $month = "มี.ค.";    break;
                case '04': $month = "เม.ย.";   break;
                case '05': $month = "พ.ค.";  break;
                case '06': $month = "มิ.ย.";   break;
                case '07': $month = "ก.ค.";  break;
                case '08': $month = "ส.ค.";   break;
                case '09': $month = "ก.ย.";   break;
                case '10': $month = "ต.ค.";    break;
                case '11': $month = "พ.ย."; break;
                case '12': $month = "ธ.ค.";   break;
                default:
                    $month = null;
                    break;
            }
            $result = $day." ".$month.' '.($year+543);
        }

        return $result;
    }

    /**
     * Datetime Ago
     * @param  date, showdate
     * @return integer
     */
    static function datetimeAgo($date, $lang='th', $showdate=true, $checkover=array())
    {
        $display = "";
        $recent = Helper::datetimeCalculate($date);
        $dayshow = false;
        if( $recent['year']>0 ){
            $display .= $recent['year'].( ($lang=='th') ? ' ปี ' : ' Y ' );
            if($showdate){
                $dayshow = $showdate;
            }
        }
        if( $recent['month']>0 ){
            $display .= $recent['month'].( ($lang=='th') ? ' ด ' : ' M ' );
            if($showdate){
                $dayshow = $showdate;
            }
        }
        if( $recent['week']>0 ){
            $display .= $recent['week'].( ($lang=='th') ? ' ส. ' : ' W ' );
            if($showdate){
                $dayshow = $showdate;
            }
        }
        if( $recent['day']>0 ){
            $display .= $recent['day'].( ($lang=='th') ? ' วัน ' : ' D ' );
            if($showdate){
                $dayshow = $showdate;
            }
        }
        if( $recent['hour']>0 ){
            $display .= $recent['hour'].( ($lang=='th') ? ' ชม. ' : ' h ' );
        }
        if( $recent['minute']>0 ){
            $display .= $recent['minute'].( ($lang=='th') ? ' น. ' : ' m ' );
        }
        $display = ( ($display!="") ? trim($display) : null );
        if($display&&$dayshow){
            $display = Helper::dateDisplayShort($date, $lang)."&bull;".$display;
        }
        $is_over = false;
        if( count($checkover)>0 ){
            foreach($checkover as $key => $value){
                if( $value>0 ){
                    if( $key=='minute' ){
                        if( $value<=$recent['minute'] ){
                            $is_over = true;
                            break;
                        }else if( $recent['hour']>0||$recent['day']>0||$recent['week']>0||$recent['month']>0||$recent['year']>0 ){
                            $is_over = true;
                            break;
                        }
                    }else if( $key=='hour' ){
                        if( $value<=$recent['hour'] ){
                            $is_over = true;
                            break;
                        }else if( $recent['day']>0||$recent['week']>0||$recent['month']>0||$recent['year']>0 ){
                            $is_over = true;
                            break;
                        }
                    }else if( $key=='day' ){
                        if( $value<=$recent['day'] ){
                            $is_over = true;
                            break;
                        }else if( $recent['week']>0||$recent['month']>0||$recent['year']>0 ){
                            $is_over = true;
                            break;
                        }
                    }else if( $key=='week' ){
                        if( $value<=$recent['week'] ){
                            $is_over = true;
                            break;
                        }else if( $recent['month']>0||$recent['year']>0 ){
                            $is_over = true;
                            break;
                        }
                    }else if( $key=='year'&&$value<=$recent['year'] ){
                        $is_over = true;
                        break;
                    }
                }
            }
        }

        return ( $is_over ? '<mark class=blink style=color:red;>'.$display.'</mark>' : $display );
    }

    /**
     * Datetime Calculate
     * @param  start_date_time, end_date_time
     * @return array
     */
    static function datetimeCalculate($start_date_time, $end_date_time=null)
    {
        $from = new datetime($start_date_time);
        $to = ( ($end_date_time) ? new datetime($end_date_time) : new datetime() );
        $result = array('year'   => 0,
                        'month'  => 0,
                        'week'   => 0,
                        'day'    => 0,
                        'hour'   => 0,
                        'minute' => 0,
                        'second' => 0,
        );
        foreach($result as $set => &$inx) {
            while($from <= $to){ 
                $from->modify('+1 ' . $set);
                if ($from > $to) {
                    $from->modify('-1 ' . $set);
                    break;
                } else {
                    $inx++;
                }
            }
        }

        return $result;
    }

    /**
     * Datetime Display
     * @param  date
     * @return string
     */
    static function datetimeDisplay($date, $lang='th')
    {
        if( $date instanceof DateTime ){
            // This is date
        }else{
            $date = new datetime($date);
        }
        $result = $date->format("d/m");
        if( $lang=='th' ){
            $result .= '/'.intval($date->format("Y"))+543;
        }else{
            $result .= '/'.$date->format("Y");
        }
        $result .= ' '.$date->format(" H:i:s");

        return $result; 
    }

    /**
     * Decimal Save
     * @param decimal
     * @return decimal
     */
    static function decimalSave($decimal)
    {
        return doubleval(str_replace(',', '', $decimal));
    }

    /**
     * Decimal Greater
     * @param decimal
     * @return decimal
     */
    static function decimalGreater($value1, $value2)
    {
        $decimal1s = explode('.', number_format($value1,2,".",""));
        $decimal2s = explode('.', number_format($value2,2,".",""));
        if( intval($decimal1s[0])>intval($decimal2s[0]) ){
            return true;
        }else if( intval($decimal1s[0])==intval($decimal2s[0])&&intval($decimal1s[1])>intval($decimal2s[1]) ){
            return true;
        }

        return false;
    }

    /**
     * Decimal Less
     * @param decimal
     * @return decimal
     */
    static function decimalLess($value1, $value2)
    {
        $decimal1s = explode('.', number_format($value1,2,".",""));
        $decimal2s = explode('.', number_format($value2,2,".",""));
        if( intval($decimal1s[0])<intval($decimal2s[0]) ){
            return true;
        }else if( intval($decimal1s[0])==intval($decimal2s[0])&&intval($decimal1s[1])<intval($decimal2s[1]) ){
            return true;
        }

        return false;
    }

    /**
     * Decimal Equal
     * @param decimal
     * @return decimal
     */
    static function decimalEqual($value1, $value2)
    {
        $decimal1s = explode('.', number_format($value1,2,".",""));
        $decimal2s = explode('.', number_format($value2,2,".",""));
        if( intval($decimal1s[0])==intval($decimal2s[0])&&intval($decimal1s[1])==intval($decimal2s[1]) ){
            return true;
        }

        return false;
    }

    /**
     * Decimal Not Equal
     * @param decimal
     * @return decimal
     */
    static function decimalNotEqual($value1, $value2)
    {
        $decimal1s = explode('.', number_format($value1,2,".",""));
        $decimal2s = explode('.', number_format($value2,2,".",""));
        if( intval($decimal1s[0])!=intval($decimal2s[0]) ){
            return true;
        }else if( intval($decimal1s[0])==intval($decimal2s[0])&&intval($decimal1s[1])!=intval($decimal2s[1]) ){
            return true;
        }

        return false;
    }

    /**
     * Decimal Greater Equal
     * @param decimal
     * @return decimal
     */
    static function decimalGreaterEqual($value1, $value2)
    {
        $decimal1s = explode('.', number_format($value1,2,".",""));
        $decimal2s = explode('.', number_format($value2,2,".",""));
        if( intval($decimal1s[0])>intval($decimal2s[0]) ){
            return true;
        }else if( intval($decimal1s[0])==intval($decimal2s[0])&&intval($decimal1s[1])>intval($decimal2s[1]) ){
            return true;
        }else if( intval($decimal1s[0])==intval($decimal2s[0])&&intval($decimal1s[1])==intval($decimal2s[1]) ){
            return true;
        }

        return false;
    }

    /**
     * Decimal Less Equal
     * @param decimal
     * @return decimal
     */
    static function decimalLessEqual($value1, $value2)
    {
        $decimal1s = explode('.', number_format($value1,2,".",""));
        $decimal2s = explode('.', number_format($value2,2,".",""));
        if( intval($decimal1s[0])<intval($decimal2s[0]) ){
            return true;
        }else if( intval($decimal1s[0])==intval($decimal2s[0])&&intval($decimal1s[1])<intval($decimal2s[1]) ){
            return true;
        }else if( intval($decimal1s[0])==intval($decimal2s[0])&&intval($decimal1s[1])==intval($decimal2s[1]) ){
            return true;
        }

        return false;
    }

    /**
     * money Thai
     * @param number
     * @return string
     */
    static function moneyThai($number, $include_unit=true, $display_zero=true){
        if (!is_numeric($number)) { return null; }
        $baht_numbers = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
        $baht_units = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
        $baht_one_in_tenth = 'เอ็ด';
        $baht_twenty = 'ยี่';
        $baht_intteger = 'ถ้วน';
        $baht_baht = 'บาท';
        $baht_satang = 'สตางค์';
        $baht_point = 'จุด';
        $log = floor(log($number, 10));
        if ($log > 5) {
            $millions = floor($log / 6);
            $million_value = pow(1000000, $millions);
            $normalised_million = floor($number / $million_value);
            $rest = $number - ($normalised_million * $million_value);
            $millions_text = '';
            for ($i = 0; $i < $millions; $i++) {
                $millions_text .= $baht_units[6];
            }
            return Helper::moneyThai($normalised_million, false) . $millions_text . Helper::moneyThai($rest, true, false);
        }
        $number_str = (string)floor($number);
        $text = '';
        $unit = 0;
        if ($display_zero && $number_str == '0') {
            $text = $baht_numbers[0];
        } else for ($i = strlen($number_str) - 1; $i > -1; $i--) {
            $current_number = (int)$number_str[$i];
            $unit_text = '';
            if ($unit == 0 && $i > 0) {
                $previous_number = isset($number_str[$i - 1]) ? (int)$number_str[$i - 1] : 0;
                if ($current_number == 1 && $previous_number > 0) {
                    $unit_text .= $baht_one_in_tenth;
                } else if ($current_number > 0) {
                    $unit_text .= $baht_numbers[$current_number];
                }
            } else if ($unit == 1 && $current_number == 2) {
                $unit_text .= $baht_twenty;
            } else if ($current_number > 0 && ($unit != 1 || $current_number != 1)) {
                $unit_text .= $baht_numbers[$current_number];
            }
            if ($current_number > 0) {
                $unit_text .= $baht_units[$unit];
            }
            $text = $unit_text . $text;
            $unit++;
        }
        if ($include_unit) {
            $text .= $baht_baht;
            $satang = explode('.', number_format($number, 2, '.', ''))[1];
            $text .= $satang == 0
                ? $baht_intteger
                : Helper::moneyThai($satang, false) . $baht_satang;
        } else {
            $exploded = explode('.', $number);
            if (isset($exploded[1])) {
                $text .= $baht_point;
                $decimal = (string)$exploded[1];
                for ($i = 0; $i < strlen($decimal); $i++) {
                    $text .= $baht_numbers[$decimal[$i]];
                }
            }
        }

        return $text;
    }

    /**
     * Number English
     * @param number
     * @return string
     */
    static function numberEnglish($number){
        if (!is_numeric($number)) { return null; }
        $words = array(0=>'', 1=>'one', 2=>'two', 3=>'three', 4=>'four', 5=>'five', 6=>'six', 7=>'seven', 8=>'eight', 9=>'nine'
                    , 10=>'ten', 11=>'eleven', 12=>'twelve', 13=>'thirteen', 14=>'fourteen', 15=>'fifteen', 16=>'sixteen', 17=>'seventeen', 18=>'eighteen', 19=>'nineteen'
                    , 20=>'twenty', 30=>'thirty', 40=>'forty', 50=>'fifty', 60=>'sixty', 70=>'seventy', 80=>'eighty', 90 =>'ninety'
        );
        if ($number < 20) {
            return $words[$number];
        }
        if ($number < 100) {
            return $words[10 * floor($number / 10)].' '.$words[$number % 10];
        }
        if ($number < 1000) {
            return $words[floor($number / 100)].' hundred '.Helper::numberEnglish($number % 100);
        }
        if ($number < 1000000) {
            return Helper::numberEnglish(floor($number / 1000)).' thousand '.Helper::numberEnglish($number % 1000);
        }

        return Helper::numberEnglish(floor($number / 1000000)).' million '.Helper::numberEnglish($number % 1000000);
    }

    /**
     * money English
     * @param number
     * @return string
     */
    static function moneyEnglish($number){
        if (!is_numeric($number)) { return null; }
        $text = '';
        if( $number>0 ){
            $numbers = explode('.', $number);
            if(isset($numbers[0])&&$numbers[0] ){
                $text .= Helper::numberEnglish($numbers[0]).' baht';
            }
            if(isset($numbers[1])&&$numbers[1] ){
                $text .= ' and '.Helper::numberEnglish($numbers[1]).' satang';
            }else{
                $text .= ' only';
            }
        }else{
            $text = 'zero baht only';
        }

        return $text;
    }

    /**
     * Number Save
     * @param decimal
     * @return decimal
     */
    static function numberSave($number)
    {
        return intval(str_replace(',', '', $number));
    }

    /**
     * Number Thai Month
     * @param  number
     * @return string
     */
    static function numberThaiMonth($number, $lang='th')
    {
        $month = null;
        $m = intval($number);
        switch ( $m ) {
            case '01' : $month = ( ($lang=='en') ? "January" : "มกราคม");   break;
            case '02' : $month = ( ($lang=='en') ? "February" : "กุมภาพันธ์");  break;
            case '03' : $month = ( ($lang=='en') ? "March" : "มีนาคม");    break;
            case '04' : $month = ( ($lang=='en') ? "April" : "เมษายน");   break;
            case '05' : $month = ( ($lang=='en') ? "May" : "พฤษภาคม");  break;
            case '06' : $month = ( ($lang=='en') ? "June" : "มิถุนายน");   break;
            case '07' : $month = ( ($lang=='en') ? "July" : "กรกฎาคม");  break;
            case '08' : $month = ( ($lang=='en') ? "August" : "สิงหาคม");   break;
            case '09' : $month = ( ($lang=='en') ? "September" : "กันยายน");   break;
            case '10' : $month = ( ($lang=='en') ? "October" : "ตุลาคม");    break;
            case '11' : $month = ( ($lang=='en') ? "November" : "พฤศจิกายน"); break;
            case '12' : $month = ( ($lang=='en') ? "December" : "ธันวาคม");   break;
            default :
                $month = null;
                break;
        }

        return $month;
    }

    /**
     * String Save
     * @param string
     * @return string
     */
    static function stringSave($string)
    {
        $values = explode(" ", $string);
        $results = "";
        foreach ($values as $value) {
            if($value&&$value!=" "){
                $results .= " ".trim($value);
            }
        }

        return substr($results, 1);
    }

    /**
     * String Sql In
     * @param string
     * @return string
     */
    static function stringSqlIn($string)
    {
        $values = explode(",", $string);
        $results = "";
        foreach ($values as $value) {
            if($value){
                $results .= ",'$value'";
            }
        }
        $results = substr($results, 1);

        return $results;
    }

    /**
     * String Html
     * @param string
     * @return string
     */
    static function stringHtml($string)
    {
        return htmlentities($string);
    }

    /**
     * String Title Short
     * @param  string
     * @return string
     */
    static function stringTitleShort($string)
    {
        return str_replace(array( 'Professor Dr.', 'Professor', 'Associate Professor Dr.', 'Associate Professor', 'Assistant Professor Dr.', 'Assistant Professor', 'Lecturer Dr.', 'Lecturer', 'ผู้ช่วยศาสตราจารย์ ดร.', 'ผู้ช่วยศาสตราจารย์', 'รองศาสตราจารย์ ดร.', 'รองศาสตราจารย์', 'ศาสตราจารย์ ดร.', 'ศาสตราจารย์', 'อาจารย์ ดร.', 'อาจารย์', 'นางสาว' )
                        , array( 'Prof. Dr.', 'Prof.', 'Assoc. Prof. Dr.', 'Assoc. Prof.', 'Asst. Prof. Dr.', 'Asst. Prof.', 'Lect. Dr.', 'Lect.', 'ผศ. ดร.', 'ผศ.', 'รศ. ดร.', 'รศ.', 'ศ. ดร.', 'ศ.', 'อ. ดร.', 'อ.', 'น.ส.' )
                        , $string);
    }

    /**
     * String Title Ignore
     * @param  string
     * @return string
     */
    static function stringTitleIgnore($string)
    {
        return str_replace(array( 'Professor Dr.', 'Professor', 'Associate Professor Dr.', 'Associate Professor', 'Assistant Professor Dr.', 'Assistant Professor', 'Lecturer Dr.', 'Lecturer', 'ผู้ช่วยศาสตราจารย์ ดร.', 'ผู้ช่วยศาสตราจารย์', 'รองศาสตราจารย์ ดร.', 'รองศาสตราจารย์', 'ศาสตราจารย์ ดร.', 'ศาสตราจารย์', 'อาจารย์ ดร.' )
                         , array( 'Prof. Dr.', 'Prof.', 'Assoc. Prof. Dr.', 'Assoc. Prof.', 'Asst. Prof. Dr.', 'Asst. Prof.', 'Lect. Dr.', 'Lect.', 'ผศ. ดร.', 'ผศ.', 'รศ. ดร.', 'รศ.', 'ศ. ดร.', 'ศ.', 'อ. ดร.' )
                         , $string);  
    }

    /**
     * Make Directory
     * @param  string 
     * @return true/false[Boolean]
     */
    static function mkdir($dir, $mod=0775) { 
        if (file_exists($dir)) return true;
        if( mkdir($dir) ){
            chmod($dir, $mod);
            $htaccess = fopen($dir."/.htaccess", "w") or die("Unable to create file!");
            fwrite($htaccess, "<IfModule mod_rewrite.c>\nOptions -Indexes\n</IfModule>");
            fclose($htaccess);
            return true; 
        }
        return false;
    } 

    /**
     * Remove Directory
     * @param  string 
     * @return true/false[Boolean]
     */
    static function rmdir($dir) { 
        if (!file_exists($dir)) return true; 
        if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
        foreach (scandir($dir) as $item) { 
            if ($item == '.' || $item == '..') continue; 
            if (!Helper::rmdir($dir . "/" . $item)) { 
                chmod($dir . "/" . $item, 0775); 
                if (!Helper::rmdir($dir . "/" . $item)) return false; 
            }; 
        } 
        return rmdir($dir); 
    }

    /**
     * Remove File
     * @param  string 
     * @return true/false[Boolean]
     */
    static function rmfile($file) { 
        if (!file_exists($file)) return true; 
        return unlink($file); 
    }

    /**
     * Scan Files
     * @param  path
     * @return files
     */
    static function scanFlies($path, $file_only=false) {
        $files = array();
        if (!file_exists($path)||!is_dir($path)) return null;  
        foreach (scandir($path) as $item) { 
            if( $file_only ){
                if ($item == '.' || $item == '..' || $item == '.htaccess' || $item == '.DS_Store' || $item == 'index.html' || is_dir($path.'/'.$item)) continue; 
            }else{
                if ($item == '.' || $item == '..' || $item == '.htaccess' || $item == '.DS_Store' || $item == 'index.html') continue; 
            }
            array_push($files, $item);
        } 
        return $files; 
    }

    /**
     * Scan File
     * @param  path
     * @return file
     */
    static function scanFlieFirst($path, $file_only=false) { 
        if (!file_exists($path)||!is_dir($path)) return null;  
        foreach (scandir($path) as $item) {
            if( $file_only ){
                if ($item == '.' || $item == '..' || $item == '.htaccess' || $item == '.DS_Store' || $item == 'index.html' || is_dir($path.'/'.$item)) continue; 
            }else{
                if ($item == '.' || $item == '..' || $item == '.htaccess' || $item == '.DS_Store' || $item == 'index.html') continue; 
            }
            return $item;
            break;
        } 
        return null; 
    }

    /**
     * Size File
     * @param  bit, point, base
     * @return htmls
     */
    static function sizeFile($file){
        if( file_exists($file) ){
            return Helper::bitFormat(filesize($file),2);
        }

        return '0 bytes';
    }

    /**
     * bit Format
     * @param  bit, point, base
     * @return htmls
     */
    static function bitFormat($bit, $point=0, $cal_base=1000){
        $units = array( 'bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $bit > 0 ? floor(log($bit, $cal_base)) : 0;
        return (number_format($bit / pow($cal_base, $power), $point, '.', ','). $units[$power]);
    }

    /**
     * Debug
     * @param  datas, exit 
     * @return true/false[Boolean]
     */
    static function debug($datas, $is_exit=false){
        echo '<pre style="border:none!important;padding:0!important;margin:0!important;">';
        print_r($datas);
        echo '</pre>';
        if($is_exit) exit();
    }

}
?>