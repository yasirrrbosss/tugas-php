<?php
// index.php - Simple To-Do List
require_once 'config.php';

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Add new task
    if (isset($_POST['add_task'])) {
        $task = trim($_POST['task']);
        if (!empty($task)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO tasks (task) VALUES (?)");
                $stmt->execute([$task]);
                $message = "Task added successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error adding task: " . $e->getMessage();
                $messageType = "error";
            }
        } else {
            $message = "Please enter a task!";
            $messageType = "error";
        }
    }

    // Toggle task completion
    if (isset($_POST['toggle_task'])) {
        $id = (int)$_POST['task_id'];
        $completed = (int)$_POST['completed'];

        try {
            $stmt = $pdo->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
            $stmt->execute([$completed, $id]);
            $message = $completed ? "Task marked as completed!" : "Task marked as pending!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error updating task: " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Delete task
    if (isset($_POST['delete_task'])) {
        $id = (int)$_POST['task_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Task deleted successfully!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error deleting task: " . $e->getMessage();
            $messageType = "error";
        }
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . ($message ? "?msg=" . urlencode($message) . "&type=" . $messageType : ""));
    exit;
}

// Get message from URL if redirected
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $messageType = $_GET['type'] ?? 'success';
}

// Fetch all tasks
try {
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY completed ASC, created_at DESC");
    $tasks = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error fetching tasks: " . $e->getMessage();
    $messageType = "error";
    $tasks = [];
}

// Calculate statistics
$totalTasks = count($tasks);
$completedTasks = count(array_filter($tasks, function($task) { return $task['completed']; }));
$pendingTasks = $totalTasks - $completedTasks;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Simple To-Do List</h1>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats">
            <div class="stats-item">Total: <span id="total-tasks"><?php echo $totalTasks; ?></span></div>
            <div class="stats-item">Completed: <span id="completed-tasks"><?php echo $completedTasks; ?></span></div>
            <div class="stats-item">Pending: <span id="pending-tasks"><?php echo $pendingTasks; ?></span></div>
        </div>

        <!-- Add Task Form -->
        <div class="add-form">
            <form method="POST" id="addForm">
                <div class="form-group">
                    <label for="task">Add New Task:</label>
                    <input type="text" id="task" name="task" placeholder="Enter your task here..." required maxlength="255">
                </div>
                <button type="submit" name="add_task" class="btn">Add Task</button>
            </form>
        </div>

        <!-- Task List -->
        <div class="task-list">
            <?php if (empty($tasks)): ?>
                <div class="empty-state">
                    No tasks yet. Add your first task above!
                </div>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
                        <div class="task-content">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <input type="hidden" name="completed" value="<?php echo $task['completed'] ? 0 : 1; ?>">
                                <input type="checkbox" 
                                       class="task-checkbox" 
                                       <?php echo $task['completed'] ? 'checked' : ''; ?>
                                       onchange="this.form.submit();"
                                       name="toggle_task">
                            </form>

                            <div>
                                <div class="task-text <?php echo $task['completed'] ? 'completed' : ''; ?>">
                                    <?php echo htmlspecialchars($task['task']); ?>
                                </div>
                                <div class="task-date">
                                    Added: <?php echo date('M j, Y g:i A', strtotime($task['created_at'])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="task-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" name="delete_task" class="btn btn-small btn-delete">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #000000;">
            <p style="font-size: 14px; color: #666666;">
                Simple To-Do List &copy; <?php echo date('Y'); ?> | 
                <strong><?php echo $totalTasks; ?></strong> total tasks
            </p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>