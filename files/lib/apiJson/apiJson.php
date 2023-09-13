<?php

// error_reporting(E_ALL | E_WARNING);
// ini_set("display_errors", 1);
//// 
class ApiJson
{

    private $methods, $args, $strcall;

    public function __construct($rawData)
    {
        $dato = explode("/", $_SERVER["REQUEST_URI"]);
        $funcion = $dato[(count($dato) - 1)];
        if (is_null($funcion) || $funcion == '' || strpos(strtolower($funcion), 'php')) {
            $data = json_decode($rawData);
            $funcion = (isset($data->metodo)) ? $data->metodo : '';
            $rawData = (isset($data->data)) ? json_encode($data->data) : '';
        }
        $this->strcall = $funcion;
        $this->args = $rawData;
        $this->methods = array();
        $this->getHeaders();
    }

    public function Register($name)
    {
        $this->methods[$name] = true;
    }

    public function Remove($name)
    {
        $this->methods[$name] = false;
    }

    private function call($name, $args)
    {
        if ($this->methods[$name] == true) {
            $result = call_user_func($name, $args);
            if (!isset($result['codError'])) {
                $result['data'] = $result;
                $result['codError'] = 200;
            }
            $this->printResponse($result['codError']);
            return json_encode($result['data']);
        }
    }

    function start()
    {
        try {
            if (!function_exists($this->strcall))
                throw new Exception("Function '" . $this->strcall . "' does not exist.");
            if (!$this->methods[$this->strcall])
                throw new Exception("Access denied for function '" . $this->strcall . "'.");

            print $this->call($this->strcall, json_decode($this->args));
        } catch (Exception $e) {
            $this->printResponse(404);
            print json_encode(
                array(
                    "codError" => '404',
                    "desError" => 'Metodo no existe'
                )
            );
        }
    }

    private function getHeaders(){
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
        return true;
    }

    private function printResponse($codError)
    {
        switch ($codError) {
            case 200:
                header("HTTP/1.0 200 OK");
                break;
            case 400:
                header("HTTP/1.0 400 Bad Request");
                break;
            case 401:
                header("HTTP/1.0 401 Unauthorized");
                break;
            case 404:
                header("HTTP/1.0 404 Not Found");
                break;
            case 500:
                header("HTTP/1.0 500 Internal server error");
                break;
            default:
                header("HTTP/1.0 200 OK");
                break;
        }
    }

}
