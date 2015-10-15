<?php
require __DIR__ . '/vendor/autoload.php';
/*const VERSION = "1.0";
$api  = new \Slim\Slim();
$api->get('/', function() use($api){$api->response->setStatus(200); echo "Testing Center API V".VERSION;});
$api->run();
echo $api->request->getPath();*/

$api = new API();
$api->processRequest($_GET['request']);

class API
{
    protected $endpoint = '';
    protected $verb = '';

    public function processRequest($request)
    {
        $request = rtrim($request, '/');
        $uri = explode('/', $request);
        $this->endpoint = array_shift($uri);
        $this->verb = $_SERVER['REQUEST_METHOD'];

        $controllerName = "\\TestingCenter\\Controllers\\";
        $controllerName .= ucwords($this->endpoint);
        $controllerName .= "Controller";

        if (class_exists($controllerName)) {
            $controller = new $controllerName;
            if (method_exists($controller, $this->verb))
                echo json_encode($controller->{$this->verb}($uri));
            else {
                http_response_code(\TestingCenter\Http\StatusCodes::METHOD_NOT_ALLOWED);
            }
        } else {
            http_response_code(\TestingCenter\Http\StatusCodes::NOT_FOUND);
        }

    }
}
