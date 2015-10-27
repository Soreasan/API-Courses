<?php
/**
 * Created by PhpStorm.
 * User: Joshua
 * Date: 10/26/2015
 * Time: 1:23 PM
 */

namespace TestingCenter\Testing;


use TestingCenter\Controllers\TokensController;
use TestingCenter\Models\Token;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testPostAsStudent()
    {
        $token = $this->generateToken('generic', 'Hello357');

        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_STUDENT, Token::getRoleFromToken($token));
    }

    public function testPostAsFaculty()
    {
        $token = $this->generateToken('genericfac', 'Hello896');

        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_FACULTY, Token::getRoleFromToken($token));
    }

    public function testPostAsTech()
    {
        $token = $this->generateToken('generictech', 'Hello361');

        $this->assertNotNull($token);
        $this->assertEquals(Token::ROLE_AIDE, Token::getRoleFromToken($token));
    }

    private function generateToken($username, $password)
    {
        $_POST['username'] = $username;
        $_POST['password'] = $password;

        $tokenController = new TokensController();
        return $tokenController->post();
    }
}