<?php namespace App\Model;

class PDOMessageModel implements MessageModel
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findMessage($content, $category)
    {
        // NO parameters provided
        if ($content == null && $category == null) {
            throw new \InvalidArgumentException();
        }

        // Content parameter provided
        if ($content !== null && $category == null) {
            if (trim($content) == '') {
                throw new \InvalidArgumentException();
            }

            $pdo = $this->connection->getPDO();

            $statement = $pdo->prepare('SELECT * FROM Messages WHERE content LIKE ?');

            $statement->execute([$this->generateContentQuery($content)]);

            return $statement->fetchAll();
        }

        // Category parameter provided
        if ($content == null && $category !== null) {
            if (trim($category) == '') {
                throw new \InvalidArgumentException();
            }

            $pdo = $this->connection->getPDO();

            $statement = $pdo->prepare('SELECT * FROM Messages WHERE category LIKE ?');

            $statement->execute([$this->generateContentQuery($category)]);

            return $statement->fetchAll();
        }

        // Content AND parameter provider
        if ($content !== null && $category !== null) {
            if (trim($content) == '' || trim($category) == '') {
                throw new \InvalidArgumentException();
            }

            $pdo = $this->connection->getPDO();

            $statement = $pdo->prepare('SELECT * FROM Messages WHERE content LIKE ? and category LIKE ?');

            $contentParameter = $this->generateContentQuery($content);
            $categoryParameter = $this->generateCategoryQuery($category);

            $statement->bindParam(1, $contentParameter);
            $statement->bindParam(2, $categoryParameter);
            $statement->execute();

            return $statement->fetchAll();
        }
    }

    public function getAllMessages()
    {
        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT * FROM Messages');
        $statement->execute();
        
        return $statement->fetchAll();
    }

    public function getMessage($id)
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException();
        }

        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('SELECT * FROM Messages WHERE id = :id');
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $statement->bindColumn(1, $id, \PDO::PARAM_INT);
        $statement->bindColumn(2, $content, \PDO::PARAM_STR);
        $statement->bindColumn(3, $category, \PDO::PARAM_STR);
        $statement->bindColumn(4, $upvotes, \PDO::PARAM_INT);
        $statement->bindColumn(5, $downvotes, \PDO::PARAM_INT);

        $message = null;
        if ($statement->fetch(\PDO::FETCH_BOUND)) {
            $message = ['id' => $id, 'content' => $content, 'category' => $category,
                'upvotes' => $upvotes, 'downvotes' => $downvotes];
        }
        return $message;
    }

    public function addUpvote($id)
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException();
        }

        $upvotes = $this->getMessage($id)['upvotes'] + 1;

        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('UPDATE Messages SET upvotes=:upvotes WHERE id=:id');
        $statement->bindParam(':upvotes', $upvotes, \PDO::PARAM_INT);
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $this->getMessage($id);
    }

    public function addDownvote($id)
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException();
        }

        $downvotes = $this->getMessage($id)['downvotes'] + 1;

        $pdo = $this->connection->getPDO();

        $statement = $pdo->prepare('UPDATE Messages SET downvotes=:downvotes WHERE id=:id');
        $statement->bindParam(':downvotes', $downvotes, \PDO::PARAM_INT);
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $this->getMessage($id);
    }

    private function generateContentQuery($content)
    {
        if (strpos($content, ' ') == false) {
            return '%' . $content . '%';
        }

        $contentArray = explode(' ', $content);

        $contentParameter = '';

        for ($i = 0; $i < count($contentArray); $i++) {
            $contentParameter .= '%' . $contentArray[$i];
        }

        $contentParameter .= '%';

        return $contentParameter;
    }

    private function generateCategoryQuery($category)
    {
        if (strpos($category, ' ') == false) {
            return '%' . $category . '%';
        }

        $categoryArray = explode(' ', $category);

        $categoryParameter = '';

        for ($i = 0; $i < count($categoryArray); $i++) {
            $categoryParameter .= '%' . $categoryArray[$i];
        }

        $categoryParameter .= '%';

        return $categoryParameter;
    }
}
