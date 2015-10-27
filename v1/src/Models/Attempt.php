<?php
/**
 * Created by PhpStorm.
 * User: marys_000
 * Date: 10/15/2015
 * Time: 8:40 AM
 */

namespace TestingCenter\Models;



class Attempt
{
    public $attemptID = "";
    public $attemptNum = "";
    public $attemptAllowed = "";
    public $workstationID = "";
    public $startTime = "";
    public $endTime = "";
    public $reservationID ="";
    public $studentID ="";
    public $examID ="";
    //expected end?
   function __construct($studentID,$reservationID,$examID,$workstationID)
    {
        $this->studentID = $studentID;
        $this->reservationID = $reservationID;
        $this->examID = $examID;
        $this->workstationID = $workstationID;
        //$this->attemptAllowed = $this->dbh->query("SELECT Attempt FROM Exam WHERE exam_id = " . $this->examID . ";"); // do we need to fetch?
        //We need to call the exams controller and call the function that created the attempts allowed

        //$this->attemptID = $this->dbh->query("SELECT Attempt_id FROM Attempt WHERE;");    //We need to create this first, how are we going to determine it?
        //$this->dbh->query("SELECT DATETIME FROM DUAL;"); //need to figure out if it is their first attempt and then set attempt num to 0, else query
       // $this->attemptNum = $this->dbh->prepare("SELECT Attempt_num FROM Attempt WHERE Attempt_id = " . $this->attemptID . ";");
        $this->endTime = null;
    }
        //Update to insert new attemptNum into the db
        /*$statement = "UPDATE Attempt SET Attempt_num =".
                $this->attemptNum .
            "WHERE Attempt_id = ".
                $this->attemptID;
        $stmt = $dbh->prepare($statement);
        $stmt->execute();*/




}

