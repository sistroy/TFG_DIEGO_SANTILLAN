<?php
/*****************
 * API: API para la prueba de sobrecarga de un POD
 * AUTOR: Diego Santillán D.
 * FECHA: 2023-09-07
 * ***************/ 

include('autoload.php');
$HTTP_RAW_POST_DATA = file_get_contents("php://input");
$HTTP_RAW_POST_DATA = (json_decode($HTTP_RAW_POST_DATA)) ? $HTTP_RAW_POST_DATA : '';
$HTTP_RAW_POST_DATA = (empty($HTTP_RAW_POST_DATA)) ? json_encode(array_merge($_REQUEST, $_FILES)) : $HTTP_RAW_POST_DATA;
$server = new ApiJson($HTTP_RAW_POST_DATA);
$server->Register("testPod");
$server->Register("test");
$server->start();

function testPod($arg) {   
	$startTime = microtime(TRUE);
    $_log = new registerLog(LOGLEVEL,LOGPATH);
    $respValidate = validateArg($arg);  
    if($respValidate){
		$arrLog = array("input"=>json_encode($arg),"output"=>$resp);
		$_log->notice(__METHOD__,$arrLog);
        return $respValidate;
    }

    $token = $arg->token;
    $queryValue = $arg->queryValue;

    $arrResp = array(
        "error" => true,
        "response" => "Error: Ha ocurrido un error comuniquese con su administrador..."
    );

    if($token == "x84fd7d55ef14f7a98e7c78a0b5ccc68b"){
        $arrResp["error"] = false;
        $arrResp["response"] = "Consulta número: $queryValue realizada exitosamente. ";
        $arrResp["hostname"] = gethostname();
        $arrResp["responseTime"] = date("Y-m-d H:i:s");

        sleep(rand(1,30));
        
        $response = array("codError" => 200, "data" => $arrResp);
    }else{
        $response = array("codError" => 401, "data" => "Error en autentificacion");
    }

	$timeProcess = microtime(TRUE) - $startTime;

	$arrLog = array("time"=>$timeProcess, "input"=>json_encode($arg),"output"=>$response);
    $_log->notice(__METHOD__,$arrLog);
    
    return $response;
}

function validateArg($arg){
    $resp = false;
    if (!is_object($arg)) {
        $resp = array("codError" => 400, "data" => "Error en parametros");
    }
    if (is_null($arg)) {
        $resp = array("codError" => 400, "data" => "Error parametros vacios");
    }
    return $resp;
}

function test($arg) {    
    return array("codError" => 200, "data" => $arg);
}