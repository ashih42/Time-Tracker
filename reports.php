<?php
require 'inc/functions.php';

$page = "reports";
$pageTitle = "Reports | Time Tracker";

$filter = 'all';

if (!empty($_GET['filter'])) {
  $filter = explode(':', filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_STRING));
}

include 'inc/header.php';
?>

<div class="col-container page-container">
  <div class="col col-70-md col-60-lg col-center">
    <div class="col-container">
      <h1 class='actions-header'>
      <?php
      echo 'Report on ';
      if (!is_array($filter)) {
        echo 'All Tasks by Project';
      } else {
        echo ucwords($filter[0]) . ' : ';
        switch ($filter[0]) {
          case 'project':
            $project = get_project($filter[1]);
            echo $project['title'];
            break;
          case 'category':
            echo $filter[1];
            break;
          case 'date';
            echo $filter[1] . ' - ' . $filter[2];
            break;
        }
      }
      ?>
      </h1>
      <form class="form-container form-report" action="reports.php" method="get">
        <label for="filter">Filter:</label>
        <select id="filter" name="filter">
          <option value="">Select One</option>
          <optgroup label="Project">
            <?php
            foreach (get_project_list() as $item)
              echo "<option value='project:{$item['project_id']}'>{$item['title']}</option>";
            ?>
          </optgroup>
          <optgroup label="Category">
            <?php
            foreach (['Billable', 'Charity', 'Personal'] as $item)
              echo "<option value='category:$item'>$item</option>";
            ?>
          </optgroup>
          <optgroup label="Date">
            <option value="date:<?= date('m/d/Y', strtotime('-2 Sunday')) . ':' . date('m/d/Y', strtotime('-1 Saturday')) ?>">Last Week</option>
            <option value="date:<?= date('m/d/Y', strtotime('-1 Sunday')) . ':' . date('m/d/Y') ?>">This Week</option>
            <option value="date:<?= date('m/d/Y', strtotime('first day of last month')) . ':' . date('m/d/Y', strtotime('last day of last month')) ?>">Last Month</option>
            <option value="date:<?= date('m/d/Y', strtotime('first day of this month')) . ':' . date('m/d/Y') ?>">This Month</option>
          </optgroup>
        </select>
        <input class="button" type="submit" value="Run">
      </form>
    </div>
    <div class="section page">
      <div class="wrapper">
        <table>
          <?php
          $total = 0;
          $project_id = 0;
          $project_total = 0;
          $tasks = get_task_list($filter);
          foreach ($tasks as $item) {
            if ($project_id !== $item['project_id']) {
              $project_id = $item['project_id'];
              echo
                "<thread>
                  <tr>
                  <th>{$item['project']}</th>
                  <th>Date</th>
                  <th>Time</th>
                  </tr>
                </thread>";
            }
            $project_total += $item['time'];
            $total += $item['time'];
            echo
              "<tr>
                <td>{$item['title']}</td>
                <td>{$item['date']}</td>
                <td>{$item['time']}</td>
              </tr>";
            if (next($tasks)['project_id'] != $item['project_id']) {
              echo
                "<tr>
                  <th class='project-total-label' colspan='2'>Project Total</th>
                  <th class='project-total-number'>$project_total</th>
                </tr>";
              $project_total = 0;
            }
          }
          ?>
          <tr>
            <th class='grand-total-label' colspan='2'>Grand Total</th>
            <th class='grand-total-number'><?= $total ?></th>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include "inc/footer.php"; ?>
