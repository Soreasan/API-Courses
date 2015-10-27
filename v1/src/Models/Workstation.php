<?php
/**
 * Created by PhpStorm.
 * User: jared
 * Date: 10/15/2015
 * Time: 9:13 AM
 */
namespace TestingCenter\Models;
use TestingCenter\Utilities\DatabaseConnection;
use TestingCenter\Http;

/*
 * The model just creates the object and holds the variables. We are using public variables for this project,
 * so the model will feel more like a struct than an actual object.  The controller does all of the logic and
 * manipulation of the model's attributes.
 */
class Workstation
{
    public $Workstation_id = '';
    public $Occupied = '';
    public $Operational = '';

    function __construct($id = null)
    {
        $this->id = $id;
        $this->Occupied = false;
        $this->Operational = true;
    }

/*
    public function available($workstation_id = -1)
    {
        if ($workstation_id > 0){
            $dbh  = DatabaseConnection::getInstance();
            $get_info = $dbh->prepare("SELECT operational FROM Workstations WHERE Workstation_id = $workstation_id");
            $get_info->execute();
            $workstation = $get_info->fetch();
            return station;
        }

        else if($workstation_id == -1){
            /*just for testing*/
    /*
            $this->workstation = array(123=>true, 555=>true, 444=>false);
            return $this->workstation;
        }
        else{
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("No data to post.");
        }
    }

    public function get_current_attempt($workstation_id = -1){
        if ($workstation_id > 0){
            return 1;
    }
        else if($workstation_id == -1) {
            $this->workstation = array(123=>1, 555=>1, 444=>2);
            return $this->workstation;
        }
        else{
            http_response_code(Http\StatusCodes::BAD_REQUEST);
            exit("No data to post.");
        }
    }*/
}