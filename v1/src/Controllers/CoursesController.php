<?php
//CoursesController
namespace TestingCenter\Controllers;

use TestingCenter\Http;
use TestingCenter\Models\Token;
use TestingCenter\Utilities\Cast;
use TestingCenter\Utilities\DatabaseConnection;

class CoursesController
{

    protected $request = array();
    protected $options = array(Http\Methods::GET, Http\Methods::POST, Http\Methods::PUT, Http\Methods::DELETE, Http\Methods::OPTIONS);

    public function get($crn) //select
    {
        $pdo = DatabaseConnection::getInstance();

        if (isset($crn[0])) {
            $sql = $pdo->prepare("SELECT c.courseCRN, c.courseYear, c.courseSemester, cd.courseNumber, cd.courseTitle FROM Courses c JOIN CourseData cd ON c.courseData_id = cd.courseData_id WHERE c.courseCRN = :courseCRN");
            $data = array("courseCRN" => $crn[0]);
            $sql->execute($data);
            $sqlResults = $sql->fetchAll(\PDO::FETCH_CLASS, 'TestingCenter\Models\Course');
            if (count($sqlResults) == 0) {
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("CourseCRN not found");
            }
            return $sqlResults;
        } else {
            $sql = $pdo->prepare("SELECT c.courseCRN, c.courseYear, c.courseSemester, cd.courseNumber, cd.courseTitle FROM Courses c JOIN CourseData cd ON c.courseData_id = cd.courseData_id");
            $sql->execute();
            $sqlResults = $sql->fetchAll(\PDO::FETCH_CLASS, 'TestingCenter\Models\Course');
            if (count($sqlResults) == 0) {
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("No Courses found");
            }
            return $sqlResults;
        }
    }

    public function put() //update
    {
        //Requires same authentication as delete so I copied the code up
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to update Courses.");
        }

        //Decode raw payload / json & Cast to Course Data Object
        $json_input = (object) json_decode(file_get_contents('php://input'));
        $input = Cast::cast("\\TestingCenter\\Models\\Course", $json_input);

        $this->validateInput($input);

        $pdo = DatabaseConnection::getInstance();

        $courseData_id = $this->getCourseData_Id($pdo, $input);

        $this->updateCourse($pdo, $input, $courseData_id);
    }

    public function post() //create/insert
    {
        //Requires same authentication as delete so I copied the code up
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to create Courses.");
        }

        //Decode raw payload / json & Cast to Course Data Object
        $json_input = (object) json_decode(file_get_contents('php://input'));
        $input = Cast::cast("\\TestingCenter\\Models\\Course", $json_input);

        $this->validateInput($input);

        $pdo = DatabaseConnection::getInstance();

        $courseData_id = $this->getCourseData_Id($pdo, $input);

        $this->createNewCourse($pdo, $input, $courseData_id);
    }

    public function delete($crn) //delete
    {
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete Courses.");
        }

        if (isset($crn[0])) {
            $pdo = DatabaseConnection::getInstance();

            $doesCourseExist = $this->checkIfCourseExists($pdo, $crn[0]);

            if ($doesCourseExist) {
                $statement = $pdo->prepare("DELETE FROM Courses where courseCRN = :courseCRN");
                $data = array("courseCRN" => $crn[0]);
                $statement->execute($data);
            } else {
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("CourseCRN not found");
            }
        } else {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("CourseCRN Required");
        }
    }

    public function options()
    {
        header("Allow: " . implode(", ", $this->options));
    }

    private function validateInput($input)
    {
        if (empty($input->getCourseCRN())) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("CourseCRN Required");
        }
        if (empty($input->getCourseNumber())) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Course Number Required");
        }
        if (empty($input->getCourseYear())) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Course Year Required");
        }
        if (empty($input->getCourseSemester())) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Course Semester Required");
        }
    }

    private function getCourseData_Id($pdo, $input)
    {
        //Check if CourseData already exists (courseNumber or courseTitle)
        $sql = $pdo->prepare("SELECT courseData_id FROM CourseData WHERE courseNumber = :courseNumber OR courseTitle = :courseTitle");
        $data = array("courseNumber" => $input->getCourseNumber(), "courseTitle" => $input->getCourseTitle());
        $sql->execute($data);
        $sqlResults = $sql->fetchAll(\PDO::FETCH_ASSOC);

        //get $courseData_id -- create new CourseData if needed
        if (empty($sqlResults)) {
            // CourseData doesnt exist. Make one.
            $sql = $pdo->prepare("INSERT INTO CourseData (courseNumber, courseTitle) VALUES (:courseNumber, :courseTitle) ");
            $data = array("courseNumber" => $input->getCourseNumber(), "courseTitle" => $input->getCourseTitle());
            $sql->execute($data);
            return $pdo->lastInsertId();
        } else {
            return (int) $sqlResults[0]["courseData_id"];
        }
    }

    private function createNewCourse($pdo, $input, $courseData_id)
    {
        $doesCourseExist = $this->checkIfCourseExists($pdo, $input->getCourseCRN());

        if (!$doesCourseExist) {
            // CourseCRN doesnt exist. Make one.
            $sql = $pdo->prepare("INSERT INTO Courses (courseCRN, courseYear, courseSemester, courseData_id) VALUES (:courseCRN, :courseYear, :courseSemester, :courseData_id)");
            $data = array("courseCRN" => $input->getCourseCRN(), "courseYear" => $input->getCourseYear(), "courseSemester" => $input->getCourseSemester(), "courseData_id" => $courseData_id);
            $sql->execute($data);
        } else {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Duplicate CourseCRN");
        }
    }

    private function updateCourse($pdo, $input, $courseData_id)
    {
        $doesCourseExist = $this->checkIfCourseExists($pdo, $input->getCourseCRN());

        if ($doesCourseExist) {
            // CourseCRN doesnt exist. Make one.
            $sql = $pdo->prepare("UPDATE Courses SET courseYear = :courseYear, courseSemester = :courseSemester, courseData_id = :courseData_id WHERE courseCRN = :courseCRN");
            $data = array("courseCRN" => $input->getCourseCRN(), "courseYear" => $input->getCourseYear(), "courseSemester" => $input->getCourseSemester(), "courseData_id" => $courseData_id);
            $sql->execute($data);
        } else {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("CourseCRN Not Found");
        }
    }

    private function checkIfCourseExists($pdo, $crn)
    {
        $sql = $pdo->prepare("SELECT courseCRN FROM Courses WHERE courseCRN = :courseCRN");
        $data = array("courseCRN" => $crn);
        $sql->execute($data);
        $sqlResults = $sql->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($sqlResults)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
