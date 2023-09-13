<?php
/*********
Clase para registro de logs
Autor: Pato Cuvi
Fecha: 2019/02/13
**********/
class registerLog
{
    private $level, $pathLog;
    const ERROR = 400;
    const NOTICE = 300;
    const WARNING = 200;
    const DEBUG = 100;
    const PATHERROR = "/var/log/process/genericLog.log";
    function __construct($level = 100, $pathLog = self::PATHERROR)
    {
        $this->level = $level;
        $this->pathLog = $pathLog;
    }

    public function setLog($level, $pathLog){
        $this->level = $level;
        $this->pathLog = $pathLog;
        return true;
    }

    public function debug($description, $parameter){
        $description = "[debug] [$description]";
        $this->printLog(self::DEBUG, $description, $parameter);
        return true;
    }
    public function warning($description, $parameter){
        $description = "[warning] [$description]";
        $this->printLog(self::WARNING, $description, $parameter);        
        return true;
    }
    public function notice($description, $parameter){
        $description = "[notice] [$description]";
        $this->printLog(self::NOTICE, $description, $parameter);        
        return true;
    }
    public function error($description, $parameter){
        $php = $_SERVER["PHP_SELF"];
        $description = "[error] [$description] [$php]";
        $this->printLog(self::ERROR, $description, $parameter);
        return true;
    }
    private function printLog($level, $description, $parameter){
        if ($level >= $this->level) {
            $date = date("Y-m-d H:i:s");
			$message = json_encode($parameter);
			if($level == self::NOTICE && strlen($message) > 1024){
				$message = substr($message,0,512)."...";
			}				
            $message = "$date $description [".$message."]\n"; 
            error_log($message, 3, $this->pathLog);    
        }
        return true;
    }
}