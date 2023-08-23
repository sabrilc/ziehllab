<?php

namespace tests\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;
    
    private $user;
    protected function _before()
    {
        $this->user = User::findByUsername('admin');
    }
    
    public function testFindUserByUsername()
    {
        expect_that( User::findByUsername('admin'));
        expect_not( User::findByUsername('not-admin'));
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser()
    {
        expect_that($this->user->validatePassword('12345'));
        expect_not( $this->user->validatePassword('not-12345'));
    
    }

}
