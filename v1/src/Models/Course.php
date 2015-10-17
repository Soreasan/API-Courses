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

//    function __construct($id)
//    {
//        $this->id = $id;
//    }

    function getCourseNumber()
    {
        return $this->courseNumber;
    }

    function getCourseTitle()
    {
        return $this->courseTitle;
    }

    function getCourseCRN()
    {
        return $this->courseCRN;
    }

    function getCourseYear()
    {
        return $this->courseYear;
    }

    function getCourseSemester()
    {
        return $this->courseSemester;
    }
}
