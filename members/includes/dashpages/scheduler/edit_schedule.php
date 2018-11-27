<?php

echo '<pre>';
var_dump($_POST);
echo '</pre>';

/**
 * Renders the table where the user can select
 *
 * @param Array $input Input array to determine how the table will render.
 *                       If 0 in index, checkbox will be unfilled. If 1, checkbox is checked.
 *
 * @author Ya boi Fernand
 * @return Array
 */

function createScheduleTable($input) {
  $rows = '';

  for ($i = 0; $i < count($input); $i++) {
    $time = formatTime($i);
    $row_temp = '<tr><td><b>' . $time . '</b></td>';

    for ($j = 0; $j < count($input[$i]); $j++) {
      if ($input[$i][$j] == 0) {
        $cb_id     = renderCheckboxID($j, $i);
        $row_temp .= '
        <td>
          <div class="pretty p-icon p-round p-pulse">
            <input id="' . $cb_id . '" type="checkbox" />
            <div class="state p-success">
              <i class="icon mdi mdi-check"></i>
              <label></label>
            </div>
          </div>
        </td>';
      } else {
        $cb_id     = renderCheckboxID($j, $i);
        $row_temp .= '
        <td>
          <div class="pretty p-icon p-round p-pulse">
            <input id="' . $cb_id . '" type="checkbox" value="0" checked />
            <div class="state p-success">
              <i class="icon mdi mdi-check"></i>
              <label></label>
            </div>
          </div>
        </td>';
      }
    }
    $row_temp .= '</tr>';
    $rows     .= $row_temp;
  }

  $tableBase = '
  <table class="table table-light table-bordered table-hover table-striped">
    <thead>
      <tr>
        <th style="width:12.5%;">Time</th>
        <th style="width:12.5%;">Sun</th>
        <th style="width:12.5%;">Mon</th>
        <th style="width:12.5%;">Tue</th>
        <th style="width:12.5%;">Wed</th>
        <th style="width:12.5%;">Thu</th>
        <th style="width:12.5%;">Fri</th>
        <th style="width:12.5%;">Sat</th>
      </tr>
    </thead>

    <tbody>';

  $tableEnd = '</tbody></table>';
  return $tableBase . $rows . $tableEnd;
}

/**
 * Outputs string with formatted time (HH:MM)
 *
 * @param integer $input Any integer from 0-23 to represent HH
 *
 * @author Ya boi Fernand
 * @return String
 */

function formatTime($input) {
  if ($input < 10) {
    return '0' . strval($input) . ':00';
  } else {
    return strval($input) . ':00';
  }
}

/**
 * Outputs string with formatted id unique to each checkbox
 *
 * @param integer $dayInt  Any integer from 0-6 to represent the day of the week
 * @param integer $timeInt Any integer from 0-23 to represent the hour of the day
 *
 * @author Ya boi Fernand
 * @return String
 *
 *  0 = Sunday, 1 = Monday, ..., 6 = Saturday
 */

function renderCheckboxID($dayInt, $timeInt) {
  $day;
  if ($dayInt === 0) $day = '0,';
  if ($dayInt === 1) $day = '1,';
  if ($dayInt === 2) $day = '2,';
  if ($dayInt === 3) $day = '3,';
  if ($dayInt === 4) $day = '4,';
  if ($dayInt === 5) $day = '5,';
  if ($dayInt === 6) $day = '6,';

  return $day . strval($timeInt);
 }



$user_id = $_SESSION['user_id'];

$sql = "SELECT schedule FROM campaigns WHERE user_id=?";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$schedJSON = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Only use the scheduling from the first campaign grabbed as a reference
$schedJSON = $schedJSON[0];

if ($schedJSON == 0) {
  $schedJSON = array_fill(0, 24, array_fill(0, 7, 0));
} else {
  $schedJSON = json_decode($schedJSON);
}
?>

<h1>Edit Ad Schedules</h1>

<?= createScheduleTable($schedJSON) ?>

<button type="submit" id="ad_schedule" class="btn btn-lg btn-primary">Save New Schedule</button>

<script src="includes/dashpages/scheduler/assets/js/edit_schedule.js"></script>