<?php
//CoursesController
namespace TestingCenter\Controllers;

use TestingCenter\Http;
use TestingCenter\Models\Course;
use TestingCenter\Models\Token;
use TestingCenter\Utilities\DatabaseConnection;

class CoursesController
{

    protected $request = array();
    protected $options = array(Http\Methods::GET, Http\Methods::POST, Http\Methods::PUT, Http\Methods::DELETE, Http\Methods::OPTIONS);

    //This is basically a select statement
    public function get($id)
    {
        return new Course($id);
    }

    //update
    public function put($id)
    {
        //Requires same authentication as delete so I copied the code up
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_STUDENT) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to update Courses.");
        } else {

            $input = (object) json_decode(file_get_contents('php://input'));

            $input = Cast::cast("\\TestingCenter\\Models\\Course", $input);

            if (is_null($input)) {
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("No data to post.");
            }

            $pdo = DatabaseConnection::getInstance();

            // database update
            //$statement = $pdo->prepare("DELETE FROM Courses where courseCRN = :courseCRN");
            //$data = array("courseCRN" => $crn);
            //$statement->execute($data);
            //returns http OK
        }
    }

    //create or update, primarily creation
    public function post($id)
    {
        //Requires same authentication as delete so I copied the code up
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_STUDENT) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to create Courses.");
        } else {
            $input = (object) json_decode(file_get_contents('php://input'));

            $input = Cast::cast("\\TestingCenter\\Models\\Course", $input);

            if (is_null($input)) {
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("No data to post.");
            }

            $pdo = DatabaseConnection::getInstance();

            //database insert
            //$statement = $pdo->prepare("DELETE FROM Courses where courseCRN = :courseCRN");
            //$data = array("courseCRN" => $crn);
            //$statement->execute($data);
            //returns http OK
        }
    }

    //delete, deletes stuff....
    public function delete($crn)
    {
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_STUDENT) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete Courses.");
        }

        if (!isset($crn[0])) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("CourseCRN Required");
        } else {
            $pdo = DatabaseConnection::getInstance();

            $statement = $pdo->prepare("DELETE FROM Courses where courseCRN = :courseCRN");
            $data = array("courseCRN" => $crn[0]);
            $statement->execute($data);
        }
    }

    public function options()
    {
        header("Allow: " . implode(", ", $this->options));
    }
}
