<?php

/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 * Date: 10/6/2015
 * Time: 9:10 AM
 */
namespace TestingCenter\Controllers;

use \TestingCenter\Http;
use TestingCenter\Models\Exam;
use TestingCenter\Models\Token;
use TestingCenter\Utilities\Cast;
use TestingCenter\Utilities\DatabaseConnection;

class ExamsController
{
    protected $request = array();
    protected $options = array(Http\Methods::GET, Http\Methods::POST, Http\Methods::PUT, Http\Methods::DELETE, Http\Methods::OPTIONS);

    public function get($id)
    {
        return new Exam($id);
    }

    public function put($id)
    {

    }

    public function post($id = null)
    {
        $input = (object) json_decode(file_get_contents('php://input'));

        $input = Cast::cast("\\TestingCenter\\Models\\Exam", $input);

        if (is_null($input)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("No data to post.");
        }

        $pdo = DatabaseConnection::getInstance();

        return $input;
    }

    public function delete($id)
    {
        /**
         * This is a sample of checking the user's pemissions before allowing the behavior.
         */
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete exams.");
        }
    }

    public function options()
    {
        header("Allow: " . implode(", ", $this->options));
    }
}