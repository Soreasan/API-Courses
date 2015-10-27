<?php
/**
 * Created by PhpStorm.
 * User: tannergriffin
 * Date: 10/15/2015
 * Time: 8:53 AM
 */
namespace TestingCenter\Controllers;
use TestingCenter\Http;
use TestingCenter\Models\Token;
use TestingCenter\Models\Workstation;
use TestingCenter\Utilities\Cast;
use TestingCenter\Utilities\DatabaseConnection;

/*
 * The Controller performs all of the logic required to populate the attributes of Model it controls.  It will access
 * the database and perform all of the gets/sets/updates for the Model.  It will usually return an object of the
 * model's type.
 */
class WorkstationsController
{
    protected $options = array(Http\Methods::GET, Http\Methods::POST, Http\Methods::PUT, Http\Methods::DELETE, Http\Methods::OPTIONS);
    protected $request = array();

    public function get($id)
    {
        $id = intval(current($id));
        $dbh  = DatabaseConnection::getInstance();
        //$role = Token::getRoleFromToken();
       // if ($role == Token::ROLE_FACULTY || $role == Token::ROLE_AIDE) {
            //return 1 workstation;
            if ($id > 0) {
                $get_info = $dbh->prepare("SELECT * FROM Workstations WHERE Workstation_Id = $id");
                $get_info->execute();
                $temp = $get_info->fetchObject('TestingCenter\Models\Workstation');
                if ($temp == false) {
                    http_response_code(Http\StatusCodes::BAD_REQUEST);
                    exit("Invalid Input");
                }
                return $temp;
            }
            //return all workstations
            else if ($id == 0) {
                $workstations = array();
                $get_info = $dbh->prepare("SELECT * FROM Workstations");
                $get_info->execute();
                $temp = $get_info->fetchObject('TestingCenter\Models\Workstation');
                while ($temp) {
                    array_push($workstations, $temp);
                    $temp = $get_info->fetchObject('TestingCenter\Models\Workstation');
                    //$station = Cast::cast('\TestingCenter\Models\Workstation',$t);
                    //array_push($workstations,$station);
                }

                return $workstations;

            } else {
                http_response_code(Http\StatusCodes::BAD_REQUEST);
                exit("Invalid Input");
            }
      //  }
       /* else{
            http_response_code(Http\StatusCodes::UNAUTHORIZED);
            exit("Students are not allowed to access workstations");
        }*/
    }

    public function post($id, $occupied = false, $operational = true)
    {

        $dbh  = DatabaseConnection::getInstance();
        $id = intval(current($id));


        if(isset($_GET["Occupied"])) {
            $occupied = boolval($_GET["Occupied"]);
        }
        if(isset($_GET["Operational"])) {
            $operational = boolval($_GET["Operational"]);
        }
        if ($id > 0) {

            $post_info = $dbh->prepare("INSERT INTO Workstations (Workstation_id, Occupied, Operational) VALUES (:id,:occupied,:operational)");
            $post_info->execute(array(':id' => $id, ':occupied' => $occupied, ':operational' => $operational));

            $get_info = $dbh->prepare("SELECT * FROM Workstations WHERE Workstation_Id = :id");

            $get_info->execute(array(':id' => $id));

            $temp = $get_info->fetchObject('TestingCenter\Models\Workstation');

            return $temp;

        }else {
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("Invalid Input");
        }


    }

    public function put($id,$occupied = false, $operational = true)
    {
        $dbh  = DatabaseConnection::getInstance();
        $id = intval(current($id));
        if(isset($_GET["Occupied"])) {
            $occupied = boolval($_GET["Occupied"]);
        }
        if(isset($_GET["Operational"])) {
            $operational = $_GET["Operational"];
        }
        //echo $_GET["Operational"];
     //   $role = Token::getRoleFromToken();
       // $get_info = $dbh->prepare("SELECT * FROM Workstations WHERE Workstation_Id = $id");
       // $temp = $get_info->fetchObject('TestingCenter\Models\Workstation');
       // if ($temp){
            $get_info = $dbh->prepare("UPDATE Workstations SET Occupied = :occupied, Operational = :operational WHERE Workstation_id = :id");
            $get_info->execute(array(':occupied' => $occupied, ':operational' => $operational, ':id' => $id));

        //}
    }

    public function delete($id)
    {
        $dbh  = DatabaseConnection::getInstance();
        $id = intval(current($id));
        //$role = Token::getRoleFromToken();
        //if ($role == Token::ROLE_AIDE){
        $get_info = $dbh->prepare("DELETE FROM Workstations WHERE Workstation_Id = $id");
        return $get_info->execute();

        /*
           }
           else{
           http_response_code(Http\StatusCodes::UNAUTHORIZED);
           exit("Only Testing Center Aides can delete workstations");
           }
         */
    }

    public function options()
    {
        header("Allow: " . implode(", ", $this->options));
    }
}