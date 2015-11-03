<?php

namespace TestingCenter\Testing;

use TestingCenter\Controllers\CoursesController;
use TestingCenter\Models\Course;
use TestingCenter\Utilities\Testing;
use TestingCenter\Http\Methods;

class CoursesControllerTest extends \PHPUnit_Framework_TestCase // backslash is in global namespace
{
	private $base_api_url = "http://icarus.cs.weber.edu/~ap23106/cs3620/Assignments/TestingCenter";

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

		$url = $this->base_api_url."/courses/";

		$output = Testing::callAPIOverHTTP($url, Methods::POST, $body, $token, Testing::JSON);

		$this->assertNotFalse($output); //False on error, otherwise it's the raw results.
	}

    /**
     * @depends testValidPost
     */
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

        $url = $this->base_api_url."/courses/";

        $output = Testing::callAPIOverHTTP($url, Methods::PUT, $body, $token, Testing::JSON);

        $this->assertNotFalse($output); //False on error, otherwise it's the raw results.
	}

    /**
     * @depends testValidPut
     */
	public function testValidGetOne()
	{
		echo __FUNCTION__ . PHP_EOL;

        $token = '';
        $body = '';
        $url = $this->base_api_url."/courses/99999";

        $output = Testing::callAPIOverHTTP($url, Methods::GET, $body, $token, Testing::JSON);

		$this->assertEquals(1, count($output)); //Test there is only one returned

        $json_test_string = '[{"instructor":"","courseCRN":"99999","courseYear":"3000","courseSemester":"Fall","courseNumber":"9999","courseTitle":"Test Course"}]';
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

    public function testInvalidGetOne()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = '';
        $body = '';
        $url = $this->base_api_url."/courses/ThisIsAnInvalidCourseID";

        $output = Testing::callAPIOverHTTP($url, Methods::GET, $body, $token, Testing::JSON);

        $this->assertEquals("CourseCRN not found", $output);
    }

    public function testInvalidPutInvalidCredentials()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetStudentToken(); //get students token to test credential failure

        $body = '{
				  "instructor": "1",
				  "courseCRN": "99999",
				  "courseYear": "3000",
				  "courseSemester": "Fall",
				  "courseNumber": "9999",
				  "courseTitle": "Test Course"
				}';

        $url = $this->base_api_url."/courses/";

        $output = Testing::callAPIOverHTTP($url, Methods::PUT, $body, $token, Testing::JSON);

        $this->assertEquals("Non-Faculty members, are not allowed to update Courses.", $output);
    }

    public function testInvalidPutInvalidCourse()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetFacultyToken();

        $body = '{
				  "instructor": "1",
				  "courseCRN": "ThisIsAnInvalidCourseID",
				  "courseYear": "3000",
				  "courseSemester": "Fall",
				  "courseNumber": "9999",
				  "courseTitle": "Test Course"
				}';

        $url = $this->base_api_url."/courses/";

        $output = Testing::callAPIOverHTTP($url, Methods::PUT, $body, $token, Testing::JSON);

        $this->assertEquals("CourseCRN Not Found", $output);
    }

    public function testInvalidPostInvalidCredentials()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetStudentToken(); //get students token to test credential failure

        $body = '{
				  "instructor": "1",
				  "courseCRN": "99999",
				  "courseYear": "3000",
				  "courseSemester": "Fall",
				  "courseNumber": "9999",
				  "courseTitle": "Test Course"
				}';

        $url = $this->base_api_url."/courses/";

        $output = Testing::callAPIOverHTTP($url, Methods::POST, $body, $token, Testing::JSON);

        $this->assertEquals("Non-Faculty members, are not allowed to create Courses.", $output);
    }

    public function testInvalidPostMissingData()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetFacultyToken();

        $body = '{}';

        $url = $this->base_api_url."/courses/";

        $output = Testing::callAPIOverHTTP($url, Methods::POST, $body, $token, Testing::JSON);

        $this->assertEquals("CourseCRN Required", $output);
    }

    /**
     * @depends testValidPut
     */
    public function testValidDelete()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetFacultyToken();
        $body = '';
        $url = $this->base_api_url."/courses/99999";

        $output = Testing::callAPIOverHTTP($url, Methods::DELETE, $body, $token, Testing::JSON);

        $this->assertNotFalse($output); //False on error, otherwise it's the raw results.
    }

    /**
     * @depends testValidDelete
     */
    public function testInvalidDeleteInvalidCRN()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetFacultyToken();
        $body = '';
        $url = $this->base_api_url."/courses/99999";

        $output = Testing::callAPIOverHTTP($url, Methods::DELETE, $body, $token, Testing::JSON);

        $this->assertEquals("CourseCRN not found", $output);
    }

    public function testInvalidDeleteMissingCRN()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetFacultyToken();
        $body = '';
        $url = $this->base_api_url."/courses/";

        $output = Testing::callAPIOverHTTP($url, Methods::DELETE, $body, $token, Testing::JSON);

        $this->assertEquals("CourseCRN Required", $output);
    }

    public function testInvalidDeleteInvalidCredentials()
    {
        echo __FUNCTION__ . PHP_EOL;

        $token = $this->privateGetStudentToken(); //get students token to test credential failure
        $body = '';
        $url = $this->base_api_url."/courses/99999";

        $output = Testing::callAPIOverHTTP($url, Methods::DELETE, $body, $token, Testing::JSON);

        $this->assertEquals("Non-Faculty members, are not allowed to delete Courses.", $output);
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
		$url = $this->base_api_url."/tokens";

		$output = Testing::callAPIOverHTTP($url, Methods::POST, $body, $token, Testing::FORM);

		return $output;
	}

	private function privateGetStudentToken()
	{
		$token = "";
		$body = "username=generic&password=Hello357";
		$url = $this->base_api_url."/tokens";

		$output = Testing::callAPIOverHTTP($url, Methods::POST, $body, $token, Testing::FORM);

		return $output;
	}
}