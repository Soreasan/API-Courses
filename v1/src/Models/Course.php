<?php
//Course Model
namespace TestingCenter\Models;

class Course
{

    public $instructor = '';
    public $courseCRN = '';
    public $courseYear = '';
    public $courseSemester = '';
    public $courseNumber = '';
    public $courseTitle = '';

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
