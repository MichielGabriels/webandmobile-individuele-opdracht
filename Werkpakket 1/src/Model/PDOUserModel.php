<?php

namespace App\Model;

class PDOUserModel implements UserModel
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    function getAllUsers()
    {
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT id, username, role FROM Users');
        $statement->execute();

        return $statement->fetchAll($pdo::FETCH_ASSOC);
    }

    function getUser($id) {
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT id, username, role FROM Users WHERE id = :id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);

        $statement->execute();

        $user = $statement->fetch($pdo::FETCH_ASSOC);
        if ($user == 0) {
            throw new \InvalidArgumentException();
        }

        return $user;
    }

    function removeUser($id)
    {
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('DELETE FROM Users WHERE id = :id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);

        $statement->execute();

        if ($statement->rowCount() == 0) {
            throw new \InvalidArgumentException();
        }
    }

    function editUserRole($id, $newRole)
    {
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('
            UPDATE Users
            SET role = :role
            WHERE id = :id
        ');
        $statement->bindParam(':role', $newRole, \PDO::PARAM_STR);
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);

        $statement->execute();

        if ($statement->rowCount() == 0) {
            throw new \InvalidArgumentException();
        }

        return $this->getUser($id);
    }
}