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

    }

    //create or update, primarily creation
    public function post($id)
    {

    }

    //delete, deletes stuff....
    public function delete($crn)
    {
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_STUDENT) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete Courses.");
        } else {

            $pdo = DatabaseConnection::getInstance();

            $statement = $pdo->prepare("DELETE FROM Courses where courseCRN = :courseCRN");
            $data = array("courseCRN" => $crn);
            $statement->execute($data);
        }
    }

    public function options()
    {
        header("Allow: " . implode(", ", $this->options));
    }
}
