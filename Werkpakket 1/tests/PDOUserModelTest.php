<?php

namespace App\Model;

use PHPUnit\Framework\TestCase;

class PDOUserModelTest extends TestCase
{
    private $connection;

    public function setUp()
    {
        $this->connection = new Connection('sqlite::memory:');
        $this->connection->getPDO()->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );

        $this->connection->getPDO()->exec('CREATE TABLE Users (
          `id` int(11) NOT NULL,
          `username` varchar(20) NOT NULL UNIQUE,
          `role` varchar(25) NOT NULL,
          PRIMARY KEY (id)
        )');

        $users = $this->generateDummyData();
        foreach ($users as $user) {
            $this->connection->getPDO()->exec(sprintf(
                'INSERT INTO Users (id, username, role) VALUES (%d, "%s", "%s")',
                $user['id'],
                $user['username'],
                $user['role']
            ));
        }
    }

    public function tearDown()
    {
        $this->connection = null;
    }

    private function generateDummyData()
    {
        return [
            [
                'id' => 1,
                'username' => 'Jan',
                'role' => 'Administrator'
            ],
            [
                'id' => 2,
                'username' => 'Michiel',
                'role' => 'Moderator'
            ],
            [
                'id' => 3,
                'username' => 'Jeff',
                'role' => 'User'
            ],
            [
                'id' => 4,
                'username' => 'test user',
                'role' => 'user'
            ],
        ];
    }

    public function testGetAllUsers_usersInDatabase_ArrayUsers()
    {
        $userModel = new PDOUserModel($this->connection);

        $actualUsers = $userModel->getAllUsers();
        $expectedUsers = $this->generateDummyData();

        $this->assertEquals('array', gettype($actualUsers));
        $this->assertEquals(count($expectedUsers), count($actualUsers));

        foreach($actualUsers as $actualUser) {
            $this->assertContains($actualUser, $expectedUsers);
        }
    }

    public function testGetUser_userInDatabase_ArrayUser()
    {
        $userModel = new PDOUserModel($this->connection);

        $actualUser = $userModel->getUser(1);
        $expectedUser = $this->generateDummyData()[0];

        $this->assertEquals('array', gettype($actualUser));
        $this->assertEquals($actualUser, $expectedUser);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUser_invalidParameter_InvalidArgumentEception()
    {
        $userModel = new PDOUserModel($this->connection);
        $userModel->getUser(6);
    }

    public function testRemoveUser_validParameter()
    {
        $userModel = new PDOUserModel($this->connection);

        $usersBeforeRemove = $userModel->getAllUsers();
        $userModel->removeUser(1);
        $usersAfterRemove = $userModel->getAllUsers();

        $this->assertEquals(count($usersBeforeRemove) - 1, count($usersAfterRemove));

        // Check if the user was actually removed
        $this->expectException(\InvalidArgumentException::class);
        $userModel->getUser(1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveUser_invalidParameter_InvalidArgumentException()
    {
        $userModel = new PDOUserModel($this->connection);
        $userModel->removeUser(6);
    }

    public function testEditUserRole_validParameter_ArrayUser()
    {
        $userModel = new PDOUserModel($this->connection);

        $userBeforeEdit = $userModel->getUser(1);
        $userModel->editUserRole(1, 'Moderator');
        $userAfterEdit = $userModel->getUser(1);

        $this->assertEquals($userAfterEdit['role'], 'Moderator');
        $this->assertNotEquals($userBeforeEdit['role'], $userAfterEdit['role']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEditUserRole_invalidParameter_InvalidArgumentException()
    {
        $userModel = new PDOUserModel($this->connection);
        $userModel->editUserRole(6, "Moderator");
    }
}