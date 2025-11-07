<?php
/**
 * Lang Class
 */
class Lang {
    /**
     *  get
     *  @param  key,lang 
     *  @return true/false[Boolean]
     */
    static function get($key, $lang=null)
    {
        $language = APP_ROOT."/app/language/i18nlang.php";
        if( file_exists($language) ){
            $languages = include $language;
            if( $lang==null ){
                $lang = App::lang();
            }
            if( isset($languages[$key][$lang]) )
                return $languages[$key][$lang];
        }

        return $key;
    }
}
?>