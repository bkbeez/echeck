<?php
/**
 * DB Class
 */
class DB {
    /**
     *  Init
     */
    protected $driver = DB_DRIVER;
    protected $host = DB_HOST;
    protected $dbname = DB_NAME;
    protected $username = DB_USER;
    protected $password = DB_PASS;

    /**
     * Test
     * @param  init
     * @return string
     */
    static function test($init=array()){
        // Init
        $self = new DB();
        $driver = isset($init["driver"]) ? $init["driver"] : $self->driver;
        $host = isset($init["host"]) ? $init["host"] : $self->host;
        $dbname = isset($init["dbname"]) ? $init["dbname"] : $self->dbname;
        $username = isset($init["username"]) ? $init["username"] : $self->username;
        $password = isset($init["password"]) ? $init["password"] : $self->password;
        $connect = null;
        try {
            $connect = new PDO("$driver:host=$host;dbname=$dbname", $username, $password);
            if (!$connect) {
                die("Connection failed: ");
            }else{
                echo $dbname." was connected.";
            }
        } catch(PDOException $e) {
            // Roll back the transaction if something failed
            echo $e->getMessage();
        }
        // Close
        $connect = null;
    }

    /**
     * Create
     * @param  sql, init, parameters
     * @return boolean
     */
    static function create($sql, $parameters=array(), $init=array()){
        // Init
        $self = new DB();
        $driver = isset($init["driver"]) ? $init["driver"] : $self->driver;
        $host = isset($init["host"]) ? $init["host"] : $self->host;
        $dbname = isset($init["dbname"]) ? $init["dbname"] : $self->dbname;
        $username = isset($init["username"]) ? $init["username"] : $self->username;
        $password = isset($init["password"]) ? $init["password"] : $self->password;
        $result = null;
        $connect = null;
        try {
            $connect = new PDO("$driver:host=$host;dbname=$dbname", $username, $password);
            if (!$connect) {
                die("Connection failed: ");
            }
            $connect->exec("set names utf8");
            // Statement
            $statement = $connect->prepare($sql);
            // Execute
            if( count($parameters)>0 ){
                $result = $statement->execute($parameters);
            }else{
                $result = $statement->execute();
            }
        } catch(PDOException $e) {
            if( isset($init["ignore_error"]) ){
                // Not show error
            }else{
                echo $sql . "<br>" . $e->getMessage();
            }
        }
        // Close
        $connect = null;
        // Done
        return $result;
    }

    /**
     * Create Last Insert Id
     * @param  sql, init, parameters
     * @return string
     */
    static function createLastInsertId($sql, $parameters=array(), $init=array()){
        // Init
        $self = new DB();
        $driver = isset($init["driver"]) ? $init["driver"] : $self->driver;
        $host = isset($init["host"]) ? $init["host"] : $self->host;
        $dbname = isset($init["dbname"]) ? $init["dbname"] : $self->dbname;
        $username = isset($init["username"]) ? $init["username"] : $self->username;
        $password = isset($init["password"]) ? $init["password"] : $self->password;
        $result = null;
        $connect = null;
        try {
            $connect = new PDO("$driver:host=$host;dbname=$dbname", $username, $password);
            if (!$connect) {
                die("Connection failed: ");
            }
            $connect->exec("set names utf8");
            // Statement
            $statement = $connect->prepare($sql);
            // Execute
            if( count($parameters)>0 ){
                $statement->execute($parameters);
            }else{
                $result = $statement->execute();
            }
            $result = $connect->lastInsertId();
        } catch(PDOException $e) {
            if( isset($init["ignore_error"]) ){
                // Not show error
            }else{
                echo $sql . "<br>" . $e->getMessage();
            }
        }
        // Close
        $connect = null;
        // Done
        return $result;
    }

    /**
     * Update
     * @param  sql, init, parameters
     * @return boolean
     */
    static function update($sql, $parameters=array(), $init=array()){
        // Init
        $self = new DB();
        $driver = isset($init["driver"]) ? $init["driver"] : $self->driver;
        $host = isset($init["host"]) ? $init["host"] : $self->host;
        $dbname = isset($init["dbname"]) ? $init["dbname"] : $self->dbname;
        $username = isset($init["username"]) ? $init["username"] : $self->username;
        $password = isset($init["password"]) ? $init["password"] : $self->password;
        $result = null;
        $connect = null;
        try {
            $connect = new PDO("$driver:host=$host;dbname=$dbname", $username, $password);
            if (!$connect) {
                die("Connection failed: ");
            }
            $connect->exec("set names utf8");
            // Statement
            $statement = $connect->prepare($sql);
            // Execute
            if( count($parameters)>0 ){
                $statement->execute($parameters);
            }else{
                $result = $statement->execute();
            }
            if($statement->rowCount()>0){
                $result = true;
            }
        } catch(PDOException $e) {
            if( isset($init["ignore_error"]) ){
                // Not show error
            }else{
                echo $sql . "<br>" . $e->getMessage();
            }
        }
        // Close
        $connect = null;
        // Done
        return $result;
    }

    /**
     * Delete
     * @param  sql, init, parameters
     * @return boolean
     */
    static function delete($sql, $parameters=array(), $init=array()){
        // Init
        $self = new DB();
        $driver = isset($init["driver"]) ? $init["driver"] : $self->driver;
        $host = isset($init["host"]) ? $init["host"] : $self->host;
        $dbname = isset($init["dbname"]) ? $init["dbname"] : $self->dbname;
        $username = isset($init["username"]) ? $init["username"] : $self->username;
        $password = isset($init["password"]) ? $init["password"] : $self->password;
        $result = null;
        $connect = null;
        try {
            $connect = new PDO("$driver:host=$host;dbname=$dbname", $username, $password);
            if (!$connect) {
                die("Connection failed: ");
            }
            $connect->exec("set names utf8");
            // Statement
            $statement = $connect->prepare($sql);
            // Execute
            if( count($parameters)>0 ){
                $result = $statement->execute($parameters);
            }else{
                $result = $statement->execute();
            }
        } catch(PDOException $e) {
            if( isset($init["ignore_error"]) ){
                // Not show error
            }else{
                echo $sql . "<br>" . $e->getMessage();
            }
        }
        // Close
        $connect = null;
        // Done
        return $result;
    }

    /**
     * Query
     * @param  sql, init, parameters
     * @return array
     */
    static function query($sql, $parameters=array(), $init=array()){
        // Init
        $self = new DB();
        $driver = isset($init["driver"]) ? $init["driver"] : $self->driver;
        $host = isset($init["host"]) ? $init["host"] : $self->host;
        $dbname = isset($init["dbname"]) ? $init["dbname"] : $self->dbname;
        $username = isset($init["username"]) ? $init["username"] : $self->username;
        $password = isset($init["password"]) ? $init["password"] : $self->password;
        $result = null;
        $connect = null;
        try {
            $connect = new PDO("$driver:host=$host;dbname=$dbname", $username, $password);
            if (!$connect) {
                die("Connection failed: ");
            }
            $connect->exec("set names utf8");
            // Statement
            $statement = $connect->prepare($sql);
            // Execute
            if( count($parameters)>0 ){
                $statement->execute($parameters);
            }else{
                $result = $statement->execute();
            }
            // Fetch
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetchAll();
        } catch(PDOException $e) {
            if( isset($init["ignore_error"]) ){
                // Not show error
            }else{
                echo $sql . "<br>" . $e->getMessage();
            }
        }
        // Close
        $connect = null;
        // Done
        return $result;
    }

    /**
     * One
     * @param  sql, parameters
     * @return array
     */
    static function one($sql, $parameters=array()){
        $result = DB::query($sql, $parameters);
        return ( isset($result[0]) ? $result[0] : null );
    }

    /**
     * Sql
     * @param  sql, parameters
     * @return array
     */
    static function sql($sql, $parameters=array()){
        return DB::query($sql, $parameters);
    }

}
?>