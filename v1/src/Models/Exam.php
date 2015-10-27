<?php
/**
 * Created by PhpStorm.
 * User: Joshua
 * Date: 10/7/2015
 * Time: 1:37 PM
 */

namespace TestingCenter\Models;


class Exam
{
    protected $exam_name = '';
    protected $num_attempts_allowed = '';
    protected $faculty_id = '';
    protected $time_limit = '';
    protected $calculator = '';
    protected $text_book = '';
    protected $scratch_paper = '';
    protected $e_book = '';
    protected $notes = '';
    protected $open_date = '';
    protected $close_date = '';
    public $exam_id;

    function __construct($exam_id,  $exam_name = "",$num_attempts_allowed = 1, $faculty_id = "", $time_limit = "", $calculator = calculator::None, $text_book = "", $e_book = "", $notes = "")
    {
        $this->exam_id = $exam_id;
        $this->exam_name = $exam_name;
        $this->num_attempts_allowed = $num_attempts_allowed;
        $this->faculty_id = $faculty_id;
        $this->time_limit = $time_limit;
        $this->calculator = $calculator;
        $this->text_book = $text_book;
        $this->e_book = $e_book;
        $this->notes = $notes;
    }

    function set($attr, $value) {
        $this->$attr = $value;
    }
}



class calculator extends \SplEnum
{
    const __default = self::None;

    const None = "null";
    const Graphing = "graphing";
    const Scientific = "scientific";
    const Four_Function = "4_function";
}