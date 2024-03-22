<?php

class Todo
{
    private $id;
    private $title;
    private $description;

    public function __construct($id, $title, $description)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}

class TodoManager
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function addTodo($title, $description)
    {
        $stmt = $this->pdo->prepare("INSERT INTO todos (title, description) VALUES (?, ?)");
        $stmt->execute([$title, $description]);
    }

    public function getTodos()
    {
        $stmt = $this->pdo->query("SELECT * FROM todos");
        $todos = [];
        while ($row = $stmt->fetch()) {
            $todos[] = new Todo($row['id'], $row['title'], $row['description']);
        }

        return $todos;
    }


    public function editTodo($id, $title, $description)
    {
        $stmt = $this->pdo->prepare("UPDATE todos SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $id]);
    }

    public function deleteTodo($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM todos WHERE id = ?");
        $stmt->execute([$id]);
    }
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=todo;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$todoManager = new TodoManager($pdo, $isEdit);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["addTodo"])) {
        $title = $_POST["title"];
        $description = $_POST["description"];
        $todoManager->addTodo($title, $description);
    } elseif (isset($_POST["editTodo"])) {
        $id = $_POST["id"];
        $title = $_POST["title"];
        $description = $_POST["description"];
        $todoManager->editTodo($id, $title, $description);
    } elseif (isset($_POST["deleteTodo"])) {
        $id = $_POST["id"];
        $todoManager->deleteTodo($id);
    }
}

$todos = $todoManager->getTodos();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo App | Anwar Aji</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="app">
        <p>Created with mindblowing😂| Anwar Ajijuloh</p>
        <div class="title">
            <h1>Todo App</h1>
            <img src="https://i.pinimg.com/564x/ab/47/6b/ab476b9a5f79c98684ce46849910dcbe.jpg" alt="profile-alt">
        </div>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" id="title" name="title" placeholder="Enter Title ..." required>
            <textarea name="description" id="description" cols="30" rows="2" placeholder="Enter Description ..."></textarea>
            <button id="add-todo" type="submit" name="addTodo">Add Todo</button>
        </form>

        <h4>Todo List</h4>
        <?php if (count($todos) > 0) : ?>
            <ul id="todo-list">
                <?php foreach ($todos as $todo) : ?>
                    <li class="todo-item">
                        <strong><?php echo $todo->getTitle(); ?></strong><br>
                        <?php echo $todo->getDescription(); ?><br>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="edit_id" value="<?php echo $todo->getId(); ?>">
                            <input type="submit" name="editTodo" value="Edit">
                            <button type="button" onclick="confirmDelete(<?php echo $todo->getId(); ?>)">Delete</button>
                        </form>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="edit_id" value="<?php echo $todo->getId(); ?>">
                            <label for="editTitle">Title:</label><br>
                            <input type="text" id="editTitle" name="title" value="<?php echo $todo->getTitle(); ?>" required><br>
                            <label for="editDescription">Description:</label><br>
                            <textarea id="editDescription" name="description" required><?php echo $todo->getDescription(); ?></textarea><br>
                            <button type="submit" name="confirmEditTodo">Confirm Edit</button>
                            <button type="button" onclick="cancelEdit()">Cancel</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No todos yet.</p>
        <?php endif; ?>
    </div>
    <script src="main.js"></script>
</body>

</html>