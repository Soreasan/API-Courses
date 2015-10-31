<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 10/13/2015
 * Time: 8:02 AM
 */

namespace TestingCenter\Models;

class Student
{
    protected $wNumber = ''; // Unique ID
    protected $firstName = '';
    protected $lastName = '';


    function __construct($wNumber, $firstName, $lastName)
    {
        $this->wNumber = $wNumber;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

}