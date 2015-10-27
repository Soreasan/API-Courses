<?php

namespace TestingCenter\Models;

class Reservation implements \JsonSerializable
{
    protected $id;
    protected $startDate = '';
    protected $endDate = '';
    protected $studentId = '';
    protected $examId = '';
    protected $deleted = false;

    function __construct($id)
    {
        $this->id = $id;
    }

    public function __get($property)
    {
        switch ($property) {
            case "id":
            case "reservation_id":
                return $this->id;
            case "startDate":
            case "start_date":
                return $this->startDate;
            case "endDate":
            case "end_date":
                return $this->endDate;
            case "studentId":
            case "student_id":
                return $this->studentId;
            case "examId":
            case "exam_id":
                return $this->examId;
            case "deleted":
                return $this->deleted;
            case "student":
                return "Student Placeholder";
            case "exam":
                return "Exam Placeholder";
            // TODO: What other cases?  Course?  Exceptions?  Instructor?
        }
    }

    public function __set($property, $value)
    {
        if ($property != "id" && property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }
}