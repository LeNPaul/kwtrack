<?php
$user_id = $_SESSION['user_id'];

$sql = "SELECT schedule FROM users WHERE user_id=?";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$schedJSON = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Edit Ad Schedules</h1>

<table class="table table-striped">
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
</table>
