<?php
//Course Model
namespace TestingCenter\Models;

class Course
{

    protected $instructor = '';
    protected $courseCRN = '';
    protected $courseYear = '';
    protected $courseSemester = '';
    protected $courseNumber = '';
    protected $courseTitle = '';
    public $id;

    function __construct($id)
    {
        $this->id = $id;
    }
}
