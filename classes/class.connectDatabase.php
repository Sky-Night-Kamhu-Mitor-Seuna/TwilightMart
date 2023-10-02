<?php
/*************************************************************
 * 
 * 連線至DataBase
 * 
*************************************************************/
class DBConnection{
    private $debugmode=false;
    private $connection;
    private $dbname;
    private $host;
    private $port;
    private $user;
    private $passwd;
    private $charset;
    /************************************************
     * ### DBConnection ###
     * @param string $dbname 資料庫
     * @param string $host 位址
     * @param int $port 端口
     * @param string $user 用戶
     * @param string $passwd 密碼
     * @param string $charset 字符集
     ************************************************/
    public function __construct($dbname, $host, $port=3306, $user, $passwd, $charset='utf8mb4'){
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->passwd = $passwd;
        $this->charset = $charset;
        $this->resetDBname($dbname);
    }
    /************************************************
     * ### 修改配置資料庫 ###
     * @param string $dbname 資料庫
     ************************************************/
    public function resetDBname($dbname){
        $this->dbname=$dbname;
        $this->setDBConnection();
    }
    /************************************************
     * ### 設置DB資訊 ###
     * @param string $host 位址
     * @param int $port 端口
     * @param string $user 用戶
     * @param string $passwd 密碼
     * @param string $charset 字符集
     ************************************************/
    private function setDBConnection(){
        $dsn="mysql:dbname=$this->dbname;host=$this->host;port=$this->port;charset=$this->charset";
        try{
            $this->connection=new PDO($dsn, $this->user,$this->passwd);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }catch(Exception $e){
            if($this->debugmode) exit($e);
            else exit('Error: Connect to MySQL was failed.');
        }
        //$link = new PDO($dsn,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_PERSISTENT => false));
    }
    /************************************************
     * ### 抓取單筆資料 ###
     * @param string $SQL SQL語句
     * @param bool $print_r 是否直接印出
     ************************************************/
    public function single($SQL,$print_r=false){
        try{
            $outputValue=$this->connection->query($SQL)->fetch(PDO::FETCH_ASSOC);
            return $print_r ? print_r($outputValue,true) : $outputValue;
        }catch(PDOException $e){
            if($this->debugmode) exit($e);
            else exit('Error: single Catch Data was failed.');
        }
    }
    /************************************************
     * ### 抓取多筆資料 ###
     * @param string $SQL SQL語句
     * @param bool $print_r 是否直接印出
     ************************************************/
    public function each($SQL,$print_r=false){
        try{ 
            $outputValue=$this->connection->query($SQL)->fetchAll(PDO::FETCH_ASSOC);
            return $print_r ? print_r($outputValue,true) : $outputValue;
        }catch(PDOException $e){
            if($this->debugmode) exit($e);
            else exit('Error: each Catch Datas was failed.');
        }
    }
    // 
    /************************************************
     * ### 抓取多筆預填入 ###
     * @param string $SQL SQL語句
     * @param array $DATA 代填入值
     * @param bool $print_r 是否直接印出
     ************************************************/
    public function prepare($SQL,$DATA=array(null),$print_r=false){
        try{
            if(!is_array($DATA)){throw new Exception("Error: wrong data type.");}
            // $SQL = "SELECT * FROM table WHERE col1 = ?,col2 = ?, col3= ?"
            $value=$this->connection->prepare($SQL);
            // $DATA = ['col1DATA','col2DATA','col3DATA'];
            $value->execute($DATA);
            $outputValue=$value->fetchAll(PDO::FETCH_ASSOC);
            return $print_r ? print_r($outputValue,true) : $outputValue;
        }catch(PDOException $e){
            if($this->debugmode) exit($e);
            else exit('Error: prepare Catch Datas was failed.');
        }
    }
    /************************************************
     * ### 設置debug模式 ###
     * @param bool $isdebug 設置debug模式
     ************************************************/
    public function deBugMode($isdebug=true){
        $this->debugmode=$isdebug;
    }
}?>
