<?php
require 'inc/functions.php';

$pageTitle = "Project | Time Tracker";
$page = "projects";

$title = $category = '';

if (isset($_GET['id'])) {
  $project_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
  list($project_id, $title, $category) = get_project($project_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $project_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
  $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
  $category = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING));

  if (empty($title) || empty($category)) {
    $error_message = 'Please fill in the required fields: Title, Category';
  } else {
    if (add_project($title, $category, $project_id)) {
      header('Location: project_list.php');
      exit;
    } else {
      $error_message = 'Could not add project';
    }
  }
}

include 'inc/header.php';
?>

<div class="section page">
  <div class="col-container page-container">
    <div class="col col-70-md col-60-lg col-center">
      <h1 class="actions-header"><?= empty($project_id) ? 'Add' : 'Update' ?> Project</h1>
      <?php
      if (isset($error_message))
        echo "<p class='message'>$error_message</p>";
      ?>
      <form class="form-container form-add" method="post" action="project.php">
        <table>
          <tr>
            <th><label for="title">Title<span class="required">*</span></label></th>
            <td><input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" /></td>
          </tr>
          <tr>
            <th><label for="category">Category<span class="required">*</span></label></th>
            <td><select id="category" name="category">
              <option value="">Select One</option>
              <?php
              foreach (['Billable', 'Charity', 'Personal'] as $item) {
                echo "<option value='$item'";
                if ($item === $category)
                  echo ' selected';
                echo ">$item</option>";
              }
              ?>
            </select></td>
          </tr>
        </table>
        <?php
        if (!empty($project_id))
          echo "<input type='hidden' name='id' value='$project_id'>"
        ?>
        <input class="button button--primary button--topic-php" type="submit" value="Submit" />
      </form>
    </div>
  </div>
</div>

<?php include "inc/footer.php"; ?>
