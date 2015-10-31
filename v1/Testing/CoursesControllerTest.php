<?php

namespace TestingCenter\Testing;

use TestingCenter\Controllers\CoursesController;
use TestingCenter\Models\Course;
use TestingCenter\Utilities\Testing;
use TestingCenter\Http\Methods;
use TestingCenter\Utilities\Cast;

class CoursesControllerTest extends \PHPUnit_Framework_TestCase // backslash is in global namespace
{
	public function testValidPost()
	{
		echo __FUNCTION__ . PHP_EOL;

		$token = $this->privateGetFacultyToken();

		$body = '{
				  "instructor": "1",
				  "courseCRN": "99999",
				  "courseYear": "2999",
				  "courseSemester": "Fall",
				  "courseNumber": "9999",
				  "courseTitle": "Test Course"
				}';

		$url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter/courses/";

		$output = Testing::callAPIOverHTTP($url, Methods::POST, $body, $token, Testing::JSON);

		$this->assertNotFalse($output); //False on error, otherwise it's the raw results.
	}

	public function testValidPut()
	{
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetFacultyToken();

        $body = '{
				  "instructor": "1",
				  "courseCRN": "99999",
				  "courseYear": "3000",
				  "courseSemester": "Fall",
				  "courseNumber": "9999",
				  "courseTitle": "Test Course"
				}';

        $url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter/courses/";

        $output = Testing::callAPIOverHTTP($url, Methods::PUT, $body, $token, Testing::JSON);

        $this->assertNotFalse($output); //False on error, otherwise it's the raw results.
	}

	public function testValidGetOne()
	{
		echo __FUNCTION__ . PHP_EOL;

        $token = '';
        $body = '';
        $url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter/courses/99999";

        $output = Testing::callAPIOverHTTP($url, Methods::GET, $body, $token, Testing::JSON);

        //Test output by converting to a course object
        //$json_output = (object) json_decode($output);
        //print_r($json_output);
        //$course = Cast::cast("\\TestingCenter\\Models\\Course", $json_output);
        //print_r($course);
        //$this->privateTestModel($course);

		$this->assertEquals(1, count($output)); //Test there is only one returned

        $json_test_string = "[{\"instructor\":\"\",\"courseCRN\":\"99999\",\"courseYear\":\"3000\",\"courseSemester\":\"Fall\",\"courseNumber\":\"9999\",\"courseTitle\":\"Test Course\"}]";
        $this->assertJsonStringEqualsJsonString($json_test_string, $output); //Compare against expected JSON object.
	}

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

    public function testValidDelete()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetFacultyToken();
        $body = '';
        $url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter/courses/99999";

        $output = Testing::callAPIOverHTTP($url, Methods::DELETE, $body, $token, Testing::JSON);

        $this->assertNotFalse($output); //False on error, otherwise it's the raw results.
    }

    public function testInvalidGetOne()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = '';
        $body = '';
        $url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter/courses/ThisIsAnInvalidCourseID";

        $output = Testing::callAPIOverHTTP($url, Methods::GET, $body, $token, Testing::JSON);

        $this->assertEquals("Course CRN not found", $output);
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