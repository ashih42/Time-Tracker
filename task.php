<?php
require 'inc/functions.php';

$pageTitle = "Task | Time Tracker";
$page = "tasks";

$project_id = $title = $date = $time = '';

if (isset($_GET['id'])) {
  $task_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
  list($task_id, $title, $date, $time, $project_id) = get_task($task_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $task_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
  $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);
  $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
  $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
  $time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_NUMBER_INT);
  $date_match = explode('/', $date);

  if (empty($project_id) || empty($title) || empty($date) || empty($time)) {
    $error_message = 'Please fill in the required fields: Project, Title, Date, Time';
  } elseif (
    count($date_match) !== 3
    || strlen($date_match[0]) !== 2
    || strlen($date_match[1]) !== 2
    || strlen($date_match[2]) !== 4
    || !checkdate($date_match[0], $date_match[1], $date_match[2])) {
    $error_message = 'Invalid Date';
  } else {
    if (add_task($project_id, $title, $date, $time, $task_id)) {
      header('Location: task_list.php');
      exit;
    } else {
      $error_message = 'Could not add task';
    }
  }
}

include 'inc/header.php';
?>

<div class="section page">
  <div class="col-container page-container">
    <div class="col col-70-md col-60-lg col-center">
      <h1 class="actions-header"><?= empty($task_id) ? 'Add' : 'Update' ?> Task</h1>
      <?php
      if (isset($error_message))
        echo "<p class='message'>$error_message</p>";
      ?>
      <form class="form-container form-add" method="post" action="task.php">
        <table>
          <tr>
            <th>
              <label for="project_id">Project</label>
            </th>
            <td>
              <select name="project_id" id="project_id">
                <option value="">Select One</option>
                <?php
                foreach (get_project_list() as $item) {
                  echo "<option value='{$item['project_id']}'";
                  if ($project_id == $item['project_id'])
                    echo ' selected';
                  echo ">{$item['title']}</option>";
                }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <th><label for="title">Title<span class="required">*</span></label></th>
            <td><input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" /></td>
          </tr>
          <tr>
            <th><label for="date">Date<span class="required">*</span></label></th>
            <td><input type="text" id="date" name="date" value="<?= htmlspecialchars($date) ?>" placeholder="mm/dd/yyyy" /></td>
          </tr>
          <tr>
            <th><label for="time">Time<span class="required">*</span></label></th>
            <td><input type="text" id="time" name="time" value="<?= htmlspecialchars($time) ?>" /> minutes</td>
          </tr>
        </table>
        <?php
        if (!empty($task_id))
          echo "<input type='hidden' name='id' value='$task_id'>";
        ?>
        <input class="button button--primary button--topic-php" type="submit" value="Submit" />
      </form>
    </div>
  </div>
</div>

<?php include "inc/footer.php"; ?>
