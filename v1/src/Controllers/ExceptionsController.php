<?php

/**
 * Created by PhpStorm.
 * User: iamcaptaincode
 * Date: 10/6/2015
 * Time: 9:10 AM
 */
namespace TestingCenter\Controllers;

use \TestingCenter\Http;
use TestingCenter\Models\Exception;
use TestingCenter\Models\Token;
use TestingCenter\Utilities\Cast;
use TestingCenter\Utilities\DatabaseConnection;

class ExceptionsController
{

    protected $request = array();
    protected $options = array(Http\Methods::GET, Http\Methods::POST, Http\Methods::PUT, Http\Methods::DELETE, Http\Methods::OPTIONS);


    public function get($exception_id)
    {
        $input = new Exception();
        $input = (object)json_decode(file_get_contents('php://input'));
        $input = Cast::cast("\\TestingCenter\\Models\\Exception", $input);

//        if (is_null($input)) {
//            http_response_code(Http\StatusCodes::BAD_REQUEST);
//            exit("No data to get.");
//        }
//
        $pdo = DatabaseConnection::getInstance();
//
//        $role = Token::getRoleFromToken();
//
//        //student see exceptions applied to them
//        if($role == Token::ROLE_STUDENT){
//            $statement = $pdo->prepare("SELECT * FROM Exceptions WHERE student_id = :student_id");
//            $data = array("student_id"=>$input->student_id);
//        }
//        //test aides should see exceptions tied to the student they are checking in
//        elseif(Token::ROLE_AIDE){
//            $statement = $pdo->prepare("SELECT * FROM Exceptions WHERE student_id = :student_id");
//            $data = array("student_id"=>$input->student_id);
//        }
//        //faculty should be able to see all exceptions
//        else{
//            $statement = $pdo->prepare("SELECT * FROM Exceptions WHERE exam_id = :exam_id");
//            $data = array("exam_id"=>$input->exam_id);
//        }
//        $statement->execute($data);
        $statement = $pdo->prepare("SELECT * FROM Exceptions");
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;

    }

   public function put($exception_id)
    {
        // This works with the Exception ID as part of a json.
        // I did not see how to use it as a parameter.
        
        $role = Token::getRoleFromToken();
        if ($role == Token::ROLE_FACULTY) {

            $input = new Exception();
            $input = (object)json_decode(file_get_contents('php://input'));
            $input = Cast::cast("\\TestingCenter\\Models\\Exception", $input);

            $pdo = DatabaseConnection::getInstance();
            $statement = $pdo->prepare("UPDATE Exceptions " .
                " SET begin_date = :begin_date, end_date = :end_date, time_extension = :time_extension, calculator = :calculator, notes = :notes, scratch_paper = :scratch_paper, text_book = :text_book, num_attempts = :num_attempts, other = :other" .
                " WHERE exception_id = :exception_id" .
                " AND student_id = :student_id" .
                " AND exam_id = :exam_id");

            $data = array("exception_id"=>$input->exception_id, "student_id"=>$input->student_id, "exam_id"=>$input->exam_id, "begin_date"=>$input->begin_date, "end_date" =>$input->end_date, "time_extension"=>$input->time_extension, "calculator"=>$input->calculator, "notes"=>$input->notes, "scratch_paper"=>$input->scratch_paper, "text_book"=>$input->text_book, "num_attempts"=>$input->num_attempts, "other"=>$input->other);
            $statement->execute($data);
        }else {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to update exams.");

        }
    }

    public function post($exception_id = null)
    {
        $role = Token::getRoleFromToken();
        if ($role == Token::ROLE_FACULTY) {

            $input = new Exception();
            $input = (object)json_decode(file_get_contents('php://input'));

            $input = Cast::cast("\\TestingCenter\\Models\\Exception", $input);

            if (is_null($input)) {
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("No data to post.");
            }

            if(is_null($input->student_id) || is_null($input->exam_id)){
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("Student ID and Exam ID cannot be Null lol");
            }

            $pdo = DatabaseConnection::getInstance();

            $statement = $pdo->prepare("INSERT INTO Exceptions (student_id, exam_id, begin_date, end_date, time_extension, calculator, notes, scratch_paper, text_book, num_attempts, other) " .
                " VALUES (:student_id, :exam_id, :begin_date, :end_date, :time_extension, :calculator, :notes, :scratch_paper, :text_book, :num_attempts, :other)");

            $data = array("student_id"=>$input->student_id, "exam_id"=>$input->exam_id, "begin_date"=>$input->begin_date, "end_date" =>$input->end_date, "time_extension"=>$input->time_extension, "calculator"=>$input->calculator, "notes"=>$input->notes, "scratch_paper"=>$input->scratch_paper, "text_book"=>$input->text_book, "num_attempts"=>$input->num_attempts, "other"=>$input->other);

            $statement->execute($data);

            http_response_code(Http\StatusCodes::CREATED);
            return $input;
            //once they inserted the data, do a get to return the data they just inserted. pdo has a last inserted id
        }
        else{
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to add exceptions.");
        }
    }

    public function delete($exception_id)
    {
        /**
         * This is a sample of checking the user's permissions before allowing the behavior.
         */
        $input = (object)json_decode(file_get_contents('php://input'));

        $input = Cast::cast("\\TestingCenter\\Models\\Exception", $input);

        if (is_null($input)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("No data to post.");
        }

        if(is_null($input->student_id) || is_null($input->exam_id)){
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Student ID and Exam ID cannot be Null lol");
        }

        $pdo = DatabaseConnection::getInstance();

        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete exceptions.");
        }

        // I could be wrong, so please fix if I am
        $statement = $pdo->prepare("DELETE FROM Exceptions WHERE student_id = :student_id AND exam_id = :exam_id");
        $data = array("student_id"=>$input->student_id, "exam_id"=>$input->exam_id);
        $statement->execute($data);

    }


    public function options()
    {
        header("Allow: " . implode(", ", $this->options));
    }


}
