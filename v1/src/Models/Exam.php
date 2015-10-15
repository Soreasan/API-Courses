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
    protected $class = '';
    protected $name = '';
    protected $instructor = '';
    protected $start_date = '';
    protected $end_date = '';
    public $id;

    function __construct($id = null)
    {
        $this->id = $id;
    }
}