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
use TestingCenter\Utilities\DatabaseConnection;

class ExamsController
{

    private $db_connection = null;
    protected $request = array();
    protected $options = array(Http\Methods::GET, Http\Methods::POST, Http\Methods::PUT, Http\Methods::DELETE, Http\Methods::OPTIONS);

    function __construct()
    {
        $this->db_connection = DatabaseConnection::getInstance();
        if ($this->db_connection == null) {
            exit("It died");
        }
    }

    public function get($uri)
    {
        $role = Token::getRoleFromToken();

        switch ($role) {
            case Token::ROLE_FACULTY:
                //faculty_id will be the last token of the uri. So it will go something like ../exams/exam_id/faculty_id/
                $endOfUri = end($uri);
                if ($endOfUri == null) {
                    http_response_code(Http\StatusCodes::BAD_REQUEST);
                    exit("Please enter your faculty ID");
                }
                $sql = "SELECT * FROM Exam e JOIN Exam_Course ec ON e.exam_id = ec.exam_id WHERE e.faculty_id = $endOfUri";
                if ($uri != null) { //this may need to be modified to something like '$uri[0] != 0'
                    $sql .= " AND e.exam_id = " . $uri[0];
                }
                $getQuery = $this->db_connection->query($sql);
                $getQuery->setFetchMode(PDO::FETCH_ASSOC);
                /* (below) I don't think we need to do this while loop. Josh said that we need to just get the exams and let front end do this.
                I only say this because with the Aide and Student roles, we don't have to loop through all the exams. Our
                *SQL statement is limiting what exams we get depending on the faculty_id, so that should be taken care of.

                $exams = [];
                while ($row = $getQuery->fetch) {
                    $exams[0] = $row['exam_name'];
                }*/
                break;
            case Token::ROLE_AIDE:
                $sql = "SELECT * FROM Exam e JOIN Exam_Course ec ON e.exam_id = ec.exam_id WHERE ec.close_date >= CURDATE()";
                $getQuery = $this->db_connection->query($sql);
                $getQuery->setFetchMode(PDO::FETCH_ASSOC);
                break;
            case Token::ROLE_STUDENT:
                $sql = "SELECT * FROM Exam e JOIN Exam_Course ec ON e.exam_id = ec.exam_id WHERE ec.close_date >= CURDATE()";
                $getQuery = $this->db_connection->query($sql);
                $getQuery->setFetchMode(PDO::FETCH_ASSOC);
                break;
            default:
                exit("Error ID-10T: Enter something smart, stupid! (valid role)");
                break;
        }
        return new Exam($uri);
    }

    public function put($uri)
    {
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to create exams.");
        }
        $input = json_decode(file_get_contents('php://input'));

        if (is_null($input)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("No data to post.");
        }
        $exam_object = Cast::cast('TestingCenter\Models\Exam', $input);


        if (!is_string($exam_object->exam_name)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Exam name needs to be a string");
        }
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$exam_object->open_date)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Please match the the format, (YYYY-MM-DD)");
        }
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$exam_object->close_date)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Close date needs to be a date (YYYY-MM-DD)");
        }
        //return proper status code

        $sql = $this->db_connection->prepare("UPDATE Exam SET exam_name=:exam_name, num_attempts_allowed=:num_attempts_allowed, faculty_id=:faculty_id, time_limit=:time_limit, scratch_paper=:scratch_paper, calculator=:calculator, text_book=:text_book, e_book=:e_book, notes=:notes"
        . " WHERE exam_id=:exam_id");

        $data = array("exam_id"=>$exam_object->exam_id, "exam_name"=>$exam_object->exam_name, "num_attempts_allowed"=>$exam_object->num_attempts_allowed, "faculty_id"=>$exam_object->faculty_id, "time_limit"=>$exam_object->time_limit, "scratch_paper"=>$exam_object->scratch_paper, "calculator"=>$exam_object->calculator,"text_book"=>$exam_object->text_book, "e_book"=>$exam_object->e_book, "notes"=>$exam_object->notes);
        $sql->execute($data);

        $second_sql = $this->db_connection->prepare("UPDATE Exam_Course SET open_date=:open_date, close_date=:close_date WHERE exam_id=:exam_id");
        $second_data = array("exam_id"=>$exam_object->exam_id, "open_date"=>$exam_object->open_date, "close_date"=>$exam_object->close_date);
        $second_sql->execute($second_data);

    }

    public function post($uri)
    {
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to update exams.");
        }

        $input = json_decode(file_get_contents('php://input'));

        if (is_null($input)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("No data to post.");
        }
        $exam_object = Cast::cast('TestingCenter\Models\Exam' ,$input);
        if (!is_string($exam_object->exam_name)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Exam name needs to be a string");
        }
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$exam_object->open_date)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Please match the the format, (YYYY-MM-DD)");
        }
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$exam_object->close_date)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Close date needs to be a date (YYYY-MM-DD)");
        }
        //return proper status code

        $sql = $this->db_connection->prepare("INSERT INTO Exam (exam_name, num_attempts_allowed, faculty_id, time_limit, scratch_paper, calculator, text_book, e_book, notes)"
        . "VALUES (:exam_name, :num_attempts_allowed, :faculty_id, :time_limit, :scratch_paper, :calculator, :text_book, :e_book, :notes)");
        $data = array("exam_name"=>$exam_object->exam_name, "num_attempts_allowed"=>$exam_object->num_attempts_allowed, "faculty_id"=>$exam_object->faculty_id, "time_limit"=>$exam_object->time_limit, "scratch_paper"=>$exam_object->scratch_paper, "calculator"=>$exam_object->calculator,"text_book"=>$exam_object->text_book, "e_book"=>$exam_object->e_book, "notes"=>$exam_object->notes);
        $sql->execute($data);

        $second_sql = $this->db_connection->prepare("INSERT INTO Exam_Course (open_date, close_date) VALUES (:open_date, :close_date)");
        $second_data = array("open_date"=>$exam_object->open_date, "close_date"=>$exam_object->close_date);
        $second_sql->execute($second_data);

        http_response_code(Http\StatusCodes::CREATED);

    }

    public function delete($uri)
    {
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete exams.");
        } else {
            if ($uri[0] != null) {
                $endOfUri = end($uri);
                $sql = "DELETE FROM Exam WHERE exam_id = " . $endOfUri;
            } else {
                exit("You need to enter a valid Exam ID to delete");
            }
        }
    }

    public function options()
    {
        header("Allow: ". implode(", ", $this->options));
    }
}