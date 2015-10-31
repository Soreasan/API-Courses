<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 10/13/2015
 * Time: 8:58 AM
 */

namespace TestingCenter\Controllers;
use \TestingCenter\Http;
use TestingCenter\Models\Token;

use TestingCenter\Utilities\DatabaseConnection;

class StudentController
{
    private $students = null;
    private $role = null;
    protected $options = array(Http\Methods::GET, Http\Methods::POST, Http\Methods::PUT, Http\Methods::DELETE, Http\Methods::OPTIONS);

    // Returns a student by wNumber - Complete
    public function get($wNumber) //select
    {
        $this->role = Token::getRoleFromToken();
        if ($this->role != Token::ROLE_FACULTY || $this->role != Token::ROLE_AIDE)
        {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty or Aide's members, are not allowed to get students.");
        } else {
            $pdo = DatabaseConnection::getInstance();
            $sql = $pdo->prepare("SELECT s.firstName, s.lastName FROM Student s WHERE s.wNumber = :wNumber");
            $data = array("wNumber" => $wNumber);
            $sql->execute($data);
            $fName = $sql->fetchColumn(0);
            $lName = $sql->fetchColumn(1);
            return (new Student($wNumber, $fName, $lName));
            //$fName = firstName = DatabaseConnection::getInstance()->query("SELECT firstName FROM `Student` WHERE wNumber = :wNumber");
            //$lName = firstName = DatabaseConnection::getInstance()->query("SELECT lastName FROM `Student` WHERE wNumber = :$wNumber");
        }
    }

    // Update a Student - Complete
    public function put($wNumber, $fName, $lName)
    {
        $this->role = Token::getRoleFromToken();
        if ($this->role != Token::ROLE_FACULTY || $this->role != Token::ROLE_AIDE)
        {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty or Aide's members, are not allowed to get students.");
        } else {
            $pdo = DatabaseConnection::getInstance();
            $sql = $pdo->prepare("SELECT s.firstName, s.lastName FROM Student s WHERE s.wNumber = :wNumber");
            $sql = $pdo->prepare("UPDATE Student SET firstName = :firstName, lastName = :lastName WHERE wNumber = :wNumber");
            $data = array("wNumber" => $wNumber, "firstName" => $fName, "lastName" => $lName);
            $sql->execute($data);
            return (new Student($wNumber, $fName, $lName));
            //$fName = firstName = DatabaseConnection::getInstance()->query("SELECT firstName FROM `Student` WHERE wNumber = :wNumber");
            //$lName = firstName = DatabaseConnection::getInstance()->query("SELECT lastName FROM `Student` WHERE wNumber = :$wNumber");
        }
    }

    // Create / Insert a student - Complete
    public function post($wNumber, $fName, $lName)
    {
        //Requires same authentication as delete so I copied the code up
        $this->role = Token::getRoleFromToken();
        if ($this->role != Token::ROLE_FACULTY)
        {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to get students.");
        } else {
            $pdo = DatabaseConnection::getInstance();

            $sql = $pdo->prepare("INSERT INTO Student (wNumber, firstName, lastName) VALUES (:wNumber, :firstName, :lastName)");
            $data = array("wNumber" => $wNumber, "firstName" => $fName, "lastName" => $lName);
            $sql->execute($data);
        }
    }

    // Deletes a student by wNumber - Complete
    public function delete($wNumber) //delete
    {
        $this->role = Token::getRoleFromToken();
        if ($this->role != Token::ROLE_FACULTY)
        {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete exams.");
        } else {
            $pdo = DatabaseConnection::getInstance();
            $statement = $pdo->prepare("DELETE FROM Student where wNumber = :wNumber");
            $data = array("wNumber" => $wNumber);
            $statement->execute($data);
        }
    }

    // Describes which methods are allowed - GET, POST, DELETE, OPTIONS
    public function options()
    {
        header("Allow: " . implode(", ", $this->options));
    }

    // Gets a list of all students - Not finished
    /*private function get()
    {$this->role = Token::getRoleFromToken();
        if ($this->role != Token::ROLE_FACULTY)
        {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete exams.");
        } else {
            // Don't Exit - Return a Student model instead
            //$fName = firstName = DatabaseConnection::getInstance()->query("SELECT firstName FROM `Student` WHERE wNumber = " . $wNumber);

            if (!isset($this->students)) {
                $this->students = array();
            }

            array_push($this->students,$studentList);

        }
        if (!isset($this->students)) {
            $this->students = array();
        }
        $db =  DatabaseConnection::getInstance();
        $statement = $db->query("SELECT * FROM Student;");


        return $studentList;
    }*/

    // Get Reservation List (Pass in W# return list of reservations)
    /*public function GetReservationsForStudent($wNumber)
    {
        $this->role = Token::getRoleFromToken();
        if ($this->role != Token::ROLE_FACULTY)
        {
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Non-Faculty members, are not allowed to delete exams.");
        } else {
            $reservations = DatabaseConnection::getInstance()->query("SELECT * FROM `Reservation` WHERE wNumber = " . $wNumber);
            // $reservations is a PDOStatement object which contains a readonly string
            $reservationList = null;

            if (!isset($this->reservationList)) {
                $this->students = array();
            }

            foreach ($dbh->query($query) as $row)
            {
                echo "$row[0] $row[1] $row[2] <br>";
            }

            return $reservationList;

            // Example Code
            // Don't Exit - Return a Student model instead
            //$fName = firstName = DatabaseConnection::getInstance()->query("SELECT firstName FROM `Student` WHERE wNumber = " . $wNumber);
            //return (new Student($wNumber, $fName, $lName));
            //exit(json_encode($statement = DatabaseConnection::getInstance()->query("SELECT * FROM `Student` WHERE wNumber = 'w0000000'")));
        }
    }*/

    // Get Exams List?

}