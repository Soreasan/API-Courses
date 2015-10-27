<?php


namespace TestingCenter\Models;

class Exception
{
    public $exception_id = '';
    public $student_id = '';
    public $exam_id = '';
    public $begin_date = '';
    public $end_date = '';
    public $time_extension = '';
    public $calculator = '';
    public $notes = '';
    public $scratch_paper = '';
    public $text_book = '';
    public $num_attempts = '';
    public $other = '';

    //start designing controller
    //start with root portion of the controller
    //if exception has a post or get, implement methods for each.
    //

    function __construct($id = null)
    {
        $this->exception_id = $id;
    }
}