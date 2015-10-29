<?php

namespace TestingCenter\Testing;

use TestingCenter\Controllers\CoursesController;
use TestingCenter\Models\Course;
use TestingCenter\Utilities\Testing;
use TestingCenter\Http\Methods;

class CoursesControllerTest extends \PHPUnit_Framework_TestCase // backslash is in global namespace
{

//	/**
//	 * @depends testPost
//	 */
	public function testValidGetAll()
	{
        echo __FUNCTION__ . PHP_EOL;

        $controller = new CoursesController();
		$uri = array();
		$results = $controller->get($uri);

		$this->assertGreaterThan(0, count($results));

		foreach ($results as $model) {
			$this->privateTestModel($model);
		}
	}


	public function testValidPut()
	{
        echo __FUNCTION__ . PHP_EOL;


	}

	public function testValidPost()
	{
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetFacultyToken();

		$body = '{
				  "instructor": "1",
				  "courseCRN": "21108",
				  "courseYear": "2015",
				  "courseSemester": "Fall",
				  "courseNumber": "3620",
				  "courseTitle": "Server-Side Web Development"
				}';

		$url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter/courses/";

		$output = Testing::callAPIOverHTTP($url, Methods::POST, $body, $token, Testing::JSON);

		//print_r($output);

		$this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.

		//$this->assertJsonStringEqualsJsonString(""); //Compare against expected JSON object. You  could also do other tests.
	}

	public function testValidGetOne()
	{
		echo __FUNCTION__ . PHP_EOL;

		$controller = new CoursesController();
		$uri = array(21108);
		$results = $controller->get($uri);

		$this->assertEquals(1, count($results));

		foreach ($results as $model) {
			$this->privateTestModel($model);
		}
	}


    public function testInvalidGetOne()
    {
        echo __FUNCTION__ . PHP_EOL;

		$controller = new CoursesController();
		$uri = array("I want in");
		$results = $controller->get($uri);

		//print_r($results);

		$this->assertEquals("Course CRN not found ", $results);


    }
// need to test invalid credentials
// need to test invailid data type
// the data not on database
    public function testInvalidPut()
    {
        echo __FUNCTION__ . PHP_EOL;


    }
// need to test invalid credentials
// need to test invailid data type
    public function testInvalidPost()
    {
        echo __FUNCTION__ . PHP_EOL;


    }
// need to test invalid credentials
// need to test invailid data type
    public function testInvalidDelete()
    {
        echo __FUNCTION__ . PHP_EOL;


    }

	public function testValidDelete()
	{
		echo __FUNCTION__ . PHP_EOL;
		$token = $this->privateGetFacultyToken();

		$body = '{
				  "instructor": "1",
				  "courseCRN": "21108",
				  "courseYear": "2015",
				  "courseSemester": "Fall",
				  "courseNumber": "3620",
				  "courseTitle": "Server-Side Web Development"
				}';

		$url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter/courses/21108";

		$output = Testing::callAPIOverHTTP($url, Methods::DELETE, $body, $token, Testing::JSON);

		//print_r($output);

		$this->assertNotFalse($output); //False on error, otherwise it's the raw results. You should be able to json_decode to read the response.


	}

	private function privateTestModel(Course $model)
	{
		$this->assertNotEmpty($model->getCourseCRN());
		$this->assertNotEmpty($model->getCourseNumber());
		$this->assertNotEmpty($model->getCourseSemester());
		$this->assertNotEmpty($model->getCourseTitle());
	}

	private function privateGetFacultyToken()
	{
		$token = "";
		$body = "username=genericfac&password=Hello896";
		$url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter/tokens";

		$output = Testing::callAPIOverHTTP($url, Methods::POST, $body, $token, Testing::FORM);

		return $output;
	}

	private function privateGetStudentToken()
	{
		$token = "";
		$body = "username=generic&password=Hello357";
		$url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter/tokens";

		$output = Testing::callAPIOverHTTP($url, Methods::POST, $body, $token, Testing::FORM);

		return $output;
	}
}