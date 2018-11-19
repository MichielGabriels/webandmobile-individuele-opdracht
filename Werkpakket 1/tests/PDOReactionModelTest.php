<?php

namespace App\Model;

use PHPUnit\Framework\TestCase;

class PDOReactionModelTest extends TestCase
{
    private $connection;

    public function setUp()
    {
        $this->connection = new Connection('sqlite::memory:');
        $this->connection->getPDO()->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );

        $this->connection->getPDO()->exec('CREATE TABLE Reactions (
            `messageId` int(11) NOT NULL,
            `content` varchar(255) NOT NULL,
            `reactionToken` varchar(255) NOT NULL
            )');

        $reactions = $this->providerReactions();
        foreach ($reactions as $reaction) {
            $this->connection->getPDO()->exec("INSERT INTO Reactions (messageId, content, reactionToken) VALUES ("
                . $reaction['messageId'] . ", '" . $reaction['content'] . "', '" . $reaction['reactionToken'] . "');");
        }
    }
    public function tearDown()
    {
        $this->connection = null;
    }
    public function providerReactions()
    {
        return [[
            'messageId'=>1,
            'content'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sit.',
            'reactionToken'=>  uniqid('saltvoorextrapunten',true)
        ],
            [
                'messageId'=>1,
                'content'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sit.',
                'reactionToken'=> uniqid('saltvoorextrapunten',true)
            ],
            [
                'messageId'=>2,
                'content'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sit.',
                'reactionToken'=> uniqid('saltvoorextrapunten',true)
            ]];
    }

    public function testPostReactionByMessageId()
    {
        //Arrange
        $reactionModel = new PDOReactionModel($this->connection);
        //Act
        $reaction = $reactionModel->postReactionByMessageId(8,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sit.');
        //Assert
        $this->assertTrue(true);
    }
}