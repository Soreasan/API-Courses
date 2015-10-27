<?php
/**
 * Created by PhpStorm.
 * User: marys_000
 * Date: 10/15/2015
 * Time: 8:24 AM
 */

namespace TestingCenter\Controllers;

use \TestingCenter\Http;
use TestingCenter\Models\Exam;
use TestingCenter\Models\Token;
use TestingCenter\Models\Attempt;
use TestingCenter\Utilities\Cast;
use TestingCenter\Utilities\DatabaseConnection;

class AttemptsController
{


    //protected $request = array();
    protected $options = array(Http\Methods::GET, Http\Methods::POST, Http\Methods::PUT, Http\Methods::DELETE, Http\Methods::OPTIONS);

    public function get($id)
    {
            /*try {
            $dbh = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass);
        }catch(PDOException $e){
            echo "Something has gone wrong: ".$e->getMessage();
            //die(); exit();
        }*/
        $id = intval(current($id));
        $dbh  = DatabaseConnection::getInstance();

        //$workstation = new WorkstationsController();    //call the workstation controller to get the workstation id function
        //$workstation->get(null);

       //Once we have this, what do we do with it?!

       // $exam = new ExamsController();  //call the Exams Controller to get the Exam id function
        //$exam->get(null);



        if ($id == 0)
        {
            //all
            $attempts = array();
            $get_info = $dbh->prepare("SELECT * FROM Attempt");
            $get_info->execute();
            $temp = $get_info->fetchObject('TestingCenter\Models\Attempt');
            while($temp){
                array_push($attempts,$temp);
                $temp = $get_info->fetchObject('TestingCenter\Models\Attempt');
            }
            return $attempts;
        }else if($id > 0){
           /* if(is_numeric($id[0]))
            {
                //bad
                exit();
            }*/
            $get_info = $dbh->prepare("SELECT * FROM Attempt WHERE Attempt_id = $id");
            $get_info->execute();
            $temp = $get_info->fetchObject('TestingCenter\Models\Attempt');
            if ($temp == false) {
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("Invalid Input");
            }
            return $temp;
        }
        //http://icarus.cs.weber.edu/~tg46219/attempt/workstations/%7Bworkstation_id
        //call the exam controller to get the attempts allowed
    }
 
    public function put($id)
    {
        $id = intval(current($id));
        $dbh  = DatabaseConnection::getInstance();
        $endTime = $_SERVER['REQUEST_TIME'];

        $statement = $dbh->prepare("UPDATE Attempt SET dateTimeEnd = :endTime WHERE Attempt_id = $id");
        //$stmt = $dbh->prepare($statement);
        $statement->execute(array(':endTime' => $endTime));


    }

    public function post($studentID, $examID, $workstationID, $reservationID)
    {
        //Database Try/Catch
        //$id = intval(current($id));
        $dbh  = DatabaseConnection::getInstance();
        $input = (object) json_decode(file_get_contents('php://input'));

        $input = Cast::cast("\\TestingCenter\\Models\\Attempt", $input);

        if (is_null($input)) {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("No data to post.");
        }


        $statement = "SELECT count(*) WHERE Exam_id = $examID AND student_id = $studentID GROUP BY Attempt_num";
        $stmt = $dbh->prepare($statement);
        $attemptNum = $stmt->execute();
        $attemptAllowed = 3;    //this is where we would call the exam controller's Attempt column

        if($attemptNum < $attemptAllowed)
        {
            $attemptNum = 0;    //New Attempt is Attempt 0
            $endTime = null;    //New Attempt will not have an end time
            $startTime = $_SERVER['REQUEST_TIME'];  //this gives us 0s???

            /* INSERTING DATA*/
            $statement = $dbh->prepare("INSERT INTO `Attempt` (student_id, Attempt_num, Exam_id, dateTimeStart, dateTimeEnd, Workstation_id, Reservation_id" .
                " VALUES (:student_id, :Attempt_num, :Exam_id, :dateTimeStart, :dateTimeEnd, :Workstation_id, :Reservation_id);");

            $data = array("student_id"=>$studentID, "Attempt_num"=>$attemptNum, "Exam_id"=>$examID, "dateTimeStart"=>$startTime,
                "dateTimeEnd"=>$endTime, "Workstation_id"=>$workstationID, "Reservation_id"=>$reservationID);
            $statement->execute($data);

            $temp = $statement->fetchObject('TestingCenter\Model\Attempt');

            return $temp;

        }else{
            exit("No more attempts allowed!!!!!!!!!");
        }


        //return $input; DETECT THIS!@!!1!
    }

    public function delete($id)
    {
        /**
         * This is a sample of checking the user's permissions before allowing the behavior.
         */
        $role = Token::getRoleFromToken();
        if ($role != Token::ROLE_FACULTY) {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete attempts.");
        }
    }

    public function options()
    {
        header("Allow: " . implode(", ", $this->options));
    }
}