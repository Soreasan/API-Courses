<?php
//Course Test
namespace TestingCenter\Testing;

use TestingCenter\Controllers\CoursesController;
use TestingCenter\Models\Course;

class CoursesControllerTest extends \PHPUnit_Framework_TestCase // backslash is in global namespace
{

//	/**
//	 * @depends testPost
//	 */
	public function testGet()
	{
		$controller = new CoursesController();
		$uri = array();
		$results = $controller->get($uri);

		$numModels = count($results);
		$this->assertGreaterThan(0, $numModels);

		foreach ($results as $model) {
			$this->privateTestModel($model);
		}
	}

	private function privateTestModel(Course $model)
	{
		$this->assertNotEmpty($model->getCourseCRN());
		$this->assertNotEmpty($model->getCourseNumber());
		$this->assertNotEmpty($model->getCourseSemester());
		$this->assertNotEmpty($model->getCourseTitle());
	}
}
