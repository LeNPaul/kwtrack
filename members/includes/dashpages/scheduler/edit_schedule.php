<?php
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
            <input id="' . $cb_id . '" type="checkbox" value="0" />
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
  <table class="table table-bordered table-hover table-striped">
    <thead>
      <tr>
        <th>Time</th>
        <th>Sun</th>
        <th>Mon</th>
        <th>Tue</th>
        <th>Wed</th>
        <th>Thu</th>
        <th>Fri</th>
        <th>Sat</th>
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
 */

function renderCheckboxID($dayInt, $timeInt) {
  $day;
  if ($dayInt === 0) $day = 'sun';
  if ($dayInt === 1) $day = 'mon';
  if ($dayInt === 2) $day = 'tue';
  if ($dayInt === 3) $day = 'wed';
  if ($dayInt === 4) $day = 'thu';
  if ($dayInt === 5) $day = 'fri';
  if ($dayInt === 6) $day = 'sat';

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

<!-- <table class="table table-striped">
  <thead>
    <tr>
      <th>Time</th>
      <th>Sunday</th>
      <th>Monday</th>
      <th>Tuesday</th>
      <th>Wednesday</th>
      <th>Thursday</th>
      <th>Friday</th>
      <th>Satday</th>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td>00:00</td>
      <td>
        <div class="pretty p-icon p-round p-pulse">
          <input id="sun0000" type="checkbox" value="0" />
          <div class="state p-success">
            <i class="icon mdi mdi-check"></i>
            <label></label>
          </div>
        </div>
      </td>
      <td><input id="mon0000" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="tue0000" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="wed0000" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="thu0000" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="fri0000" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="sat0000" class="form-check-input" type="checkbox" value="0"></td>
    </tr>

    <tr>
      <td>01:00</td>
      <td><input id="sun0100" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="mon0100" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="tue0100" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="wed0100" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="thu0100" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="fri0100" class="form-check-input" type="checkbox" value="0"></td>
      <td><input id="sat0100" class="form-check-input" type="checkbox" value="0"></td>
    </tr>
  </tbody>
</table> -->

<?= createScheduleTable($schedJSON) ?>
