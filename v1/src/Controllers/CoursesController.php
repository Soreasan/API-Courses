<?php
//CoursesController
namespace TestingCenter\Controllers;

use TestingCenter\Http;
use TestingCenter\Models\Course;
use TestingCenter\Models\Token;
use TestingCenter\Utilities\Cast;
use TestingCenter\Utilities\DatabaseConnection;

class CoursesController
{

    protected $request = array();
    protected $options = array(Http\Methods::GET, Http\Methods::POST, Http\Methods::PUT, Http\Methods::DELETE, Http\Methods::OPTIONS);

    //This is basically a select statement
    public function get($id)
    {
        $pdo = DatabaseConnection::getInstance();

        //Check if CourseData already exists (courseNumber or courseTitle)
        $sql = $pdo->prepare("SELECT courseData_id FROM CourseData WHERE courseData_id = :courseData_id");
        $data = array("courseNumber" => $input->getCourseNumber(), "courseTitle" => $input->getCourseTitle());
        $sql->execute($data);
        $sqlResults = $sql->fetchAll();

        //Displays requested data
        echo $sqlResults;
        #return new Course($id);
    }

    //update
    public function put($id)
    {
        //Requires same authentication as delete so I copied the code up
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to update Courses.");
        }

        if (!isset($id[0])) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("CoursesID Required");
        } else {

            $json_input = (object) json_decode(file_get_contents('php://input')); //Decode raw payload / json
            $input = Cast::cast("\\TestingCenter\\Models\\Course", $json_input); //Cast to Course Data Object

            if (is_null($input)) {
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("No data to post.");
            }

            $pdo = DatabaseConnection::getInstance();
        }
    }

    //create or update, primarily creation
    public function post()
    {
        //Requires same authentication as delete so I copied the code up
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to create Courses.");
        }

        $json_input = (object) json_decode(file_get_contents('php://input')); //Decode raw payload / json
        $input = Cast::cast("\\TestingCenter\\Models\\Course", $json_input); //Cast to Course Data Object

        if (is_null($input)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("No data to post.");
        }

        $pdo = DatabaseConnection::getInstance();

        //Check if CourseData already exists (courseNumber or courseTitle)
        $sql = $pdo->prepare("SELECT courseData_id FROM CourseData WHERE courseNumber = :courseNumber OR courseTitle = :courseTitle");
        $data = array("courseNumber" => $input->getCourseNumber(), "courseTitle" => $input->getCourseTitle());
        $sql->execute($data);
        $sqlResults = $sql->fetchAll();

        //get $courseData_id -- create new CourseData if needed
        if (empty($sqlResults)) {
            // CourseData doesnt exist. Make one.
            $sql = $pdo->prepare("INSERT INTO CourseData (courseNumber, courseTitle) VALUES (:courseNumber, :courseTitle) ");
            $data = array("courseNumber" => $input->getCourseNumber(), "courseTitle" => $input->getCourseTitle());
            $sql->execute($data);

            $courseData_id = $pdo->lastInsertId();
        } else {
            $courseData_id = (int) $sqlResults[0]["courseData_id"];
        }

        //Create new Courses
        $sql = $pdo->prepare("INSERT INTO Courses (courseCRN, courseYear, courseSemester, courseData_id) VALUES (:courseCRN, :courseYear, :courseSemester, :courseData_id)");
        $data = array("courseCRN" => $input->getCourseCRN(), "courseYear" => $input->getCourseYear(), "courseSemester" => $input->getCourseSemester(), "courseData_id" => $courseData_id);
        $sql->execute($data);
    }

    //delete, deletes stuff....
    public function delete($crn)
    {
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
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
            exit("CourseCRN to Delete: " . $crn[0]); //just a test statment
        }
    }

    public function options()
    {
        header("Allow: " . implode(", ", $this->options));
    }
}
