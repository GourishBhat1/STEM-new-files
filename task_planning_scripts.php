<?php
$conn = new mysqli("stemlearning.in", "steml1og_stemftest", "7V2WDw385ykQ+)N", "steml1og_stemftest") or die('Cannot connect to db');

//get day number wrt target date and batch creation date
if (isset($_POST['type']) && $_POST['type'] == "batch_diff")
{
  $fixdate = $_POST['fixdate'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();

  //fixdate and batchdate diff
  $earlier = new DateTime(date('Y-m-d', strtotime($get_current_batch_row['batchdate'])));
  $later = new DateTime($fixdate);

  $pos_diff = $earlier->diff($later)->format("%r%a"); //3
  echo "Batch Plan For: Day " . $pos_diff;
}



























// [[ old script for general shift create - this is to be replaced with new code similar to multi shift ]]



//gen shift create
if (isset($_POST['type']) && $_POST['type'] == "gen_shift_create")
{
  $fixdate = $_POST['fixdate'];
  $gen_shift_start_time = $_POST['gen_shift_start_time'];
  $gen_shift_end_time = $_POST['gen_shift_end_time'];

  // echo $fixdate."\n";
  // echo $gen_shift_start_time."\n";
  // echo $gen_shift_end_time."\n";

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();

  //insert shift in new table with start and end time with shift date
  $select_users = $conn->prepare("SELECT * FROM user_detail where role = 'Associates' and teamname is not null;");
  $select_users->execute();
  $select_users_res = $select_users->get_result();
  while ($select_users_row = $select_users_res->fetch_assoc())
  {
    // echo $get_current_batch_row['batchno']."\n";
    // echo $fixdate."\n";
    // echo $gen_shift_start_time."\n";
    // echo $gen_shift_end_time."\n";
    // echo $select_users_row['user_name']."\n\n";

    $insert_shift = $conn->prepare('INSERT INTO shift_user_plan (batchname, shiftdate, shift_type,  start_time,  end_time,  username) VALUES (?,?, "general", ?,?,?)');
    $insert_shift->bind_param('sssss', $get_current_batch_row['batchno'], $fixdate, $gen_shift_start_time, $gen_shift_end_time, $select_users_row['user_name']);
    $insert_shift->execute();
    echo $conn->error;
  }
  echo 'General shift created';
}





















// [[ new script for general shift create ]]



//gen shift create
if (isset($_POST['type']) && $_POST['type'] == "gen_shift_create_new")
{
  $fixdate = $_POST['fixdate'];
  $gen_shift_start_time = $_POST['gen_shift_start_time'];
  $gen_shift_end_time = $_POST['gen_shift_end_time'];

  // new variable of array
  $gen_shift_user_arr = json_decode($_POST["gen_shift_user_arr"]);

  // echo $fixdate."\n";
  // echo $gen_shift_start_time."\n";
  // echo $gen_shift_end_time."\n";

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();

  //insert shift in new table with start and end time with shift date
  $select_users = $conn->prepare("SELECT * FROM user_detail where role = 'Associates' and teamname is not null;");
  $select_users->execute();
  $select_users_res = $select_users->get_result();

  for ($i = 0; $i < count($gen_shift_user_arr); $i++)
  {


    $insert_shift = $conn->prepare('INSERT INTO shift_user_plan (batchname, shiftdate, shift_type,  start_time,  end_time,  username) VALUES (?,?, "general", ?,?,?)');
    $insert_shift->bind_param('sssss', $get_current_batch_row['batchno'], $fixdate, $gen_shift_start_time, $gen_shift_end_time, $gen_shift_user_arr[$i]);
    $insert_shift->execute();
    echo $conn->error;
  }
  echo 'New General shift created';
}





















// [[  script for multi shift create - refer to this to create new script for general shift  ]]


//multi shift create
if (isset($_POST['type']) && $_POST['type'] == "multi_shift_create")
{
  $shift1starttime = $_POST['shift1starttime'];
  $shift1endtime = $_POST['shift1endtime'];

  $shift2starttime = $_POST['shift2starttime'];
  $shift2endtime = $_POST['shift2endtime'];

  $fixdate = $_POST['fixdate'];

  $shift1_user_arr = json_decode($_POST["shift1_user_arr"]);
  $shift2_user_arr = json_decode($_POST["shift2_user_arr"]);

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();

  for ($i = 0; $i < count($shift1_user_arr); $i++)
  {
    // shift 1 user insert
    $insert_shift = $conn->prepare('INSERT INTO shift_user_plan (batchname, shiftdate, shift_type,  start_time,  end_time,  username) VALUES (?,?, "shift1", ?,?,?)');
    $insert_shift->bind_param('sssss', $get_current_batch_row['batchno'], $fixdate, $shift1starttime, $shift1endtime, $shift1_user_arr[$i]);
    $insert_shift->execute();
  }

  for ($j = 0; $j < count($shift2_user_arr); $j++)
  {
    // shift 2 user insert
    $insert_shift = $conn->prepare('INSERT INTO shift_user_plan (batchname, shiftdate, shift_type,  start_time,  end_time,  username) VALUES (?,?, "shift2", ?,?,?)');
    $insert_shift->bind_param('sssss', $get_current_batch_row['batchno'], $fixdate, $shift2starttime, $shift2endtime, $shift2_user_arr[$j]);
    $insert_shift->execute();
  }

  // echo $shift1starttime."\n";
  // echo $shift1endtime."\n";
  // echo $shift2starttime."\n";
  // echo $shift2endtime."\n";
  // echo $fixdate."\n";
  // print_r($shift1_user_arr);
  // print_r($shift2_user_arr);
}
























//executive role change
if (isset($_POST['type']) && $_POST['type'] == "exec_role")
{
  $exec_role = $_POST['exec_role'];
  $temp_dept = $_POST['temp_dept'];
  $fixdate = $_POST['fixdate'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];

  $get_shift_detail = $conn->prepare("select distinct start_time, end_time from shift_user_plan where shiftdate = date(?)");
  $get_shift_detail->bind_param('s', $fixdate);
  $get_shift_detail->execute();
  $get_shift_detail_res = $get_shift_detail->get_result();
  if ($get_shift_detail_res->num_rows > 1)
  {
    while ($get_shift_detail_row = $get_shift_detail_res->fetch_assoc())
    {
      // echo $get_current_batch_row['batchno'];
      // echo $fixdate;
      // echo $get_shift_detail_row['start_time'];
      // echo $get_shift_detail_row['end_time'];
      // echo $exec_role;
      // echo $temp_dept;
      $exec_role_change = $conn->prepare('INSERT INTO shift_user_plan (batchname, shiftdate, shift_type,  start_time,  end_time,  username, temp_dept) VALUES (?,?, "multi",?,?,?,?)');
      $exec_role_change->bind_param('ssssss', $get_current_batch_row['batchno'], $fixdate, $get_shift_detail_row['start_time'], $get_shift_detail_row['end_time'], $exec_role, $temp_dept);
      $exec_role_change->execute();
    }
  }
  else
  {
    while ($get_shift_detail_row = $get_shift_detail_res->fetch_assoc())
    {
      // echo $get_current_batch_row['batchno'];
      // echo $fixdate;
      // echo $get_shift_detail_row['start_time'];
      // echo $get_shift_detail_row['end_time'];
      // echo $exec_role;
      // echo $temp_dept;

      $exec_role_change = $conn->prepare('INSERT INTO shift_user_plan (batchname, shiftdate, shift_type,  start_time,  end_time,  username, temp_dept) VALUES (?,?, "general",?,?,?,?)');
      $exec_role_change->bind_param('ssssss', $get_current_batch_row['batchno'], $fixdate, $get_shift_detail_row['start_time'], $get_shift_detail_row['end_time'], $exec_role, $temp_dept);
      $exec_role_change->execute();
    }
  }
}

//team change
if (isset($_POST['type']) && $_POST['type'] == "team_change")
{
  $team_assoc = $_POST['team_assoc'];
  $new_team_name = $_POST['new_team_name'];
  // $fixdate = $_POST['fixdate'];

  // echo $team_assoc;
  // echo $new_team_name;

  $update_team = $conn->prepare("UPDATE user_detail SET teamname = ? WHERE user_name = ?");
  $update_team->bind_param('ss', $new_team_name, $team_assoc);
  $update_team->execute();
  // echo $conn->error;
}

//general task
if (isset($_POST['type']) && $_POST['type'] == "gen_task")
{
  $gen_task = $_POST['gen_task'];
  $gen_task_assoc = $_POST['gen_task_assoc'];
  $fixdate = $_POST['fixdate'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];

  $insert_gen_task = $conn->prepare('INSERT INTO shift_user_plan (batchname, shiftdate, username, common_task_1) VALUES (?,?,?,?)');
  $insert_gen_task->bind_param('ssss', $get_current_batch_row['batchno'], $fixdate, $gen_task_assoc, $gen_task);
  $insert_gen_task->execute();
}

//machine assign
if (isset($_POST['type']) && $_POST['type'] == "machine_assign")
{
  $machin_assoc = $_POST['machin_assoc'];
  $machine_workstation_encoded = $_POST['machine_workstation'];
  $machine_workstation = urldecode($machine_workstation_encoded);

  $update_machine = $conn->prepare('UPDATE workstation SET username = ? WHERE name = ?');
  $update_machine->bind_param('ss', $machin_assoc, $machine_workstation);
  $update_machine->execute();
  $conn->error;
}

//machine assign refresh
if (isset($_POST['type']) && $_POST['type'] == "machine_assign_refresh")
{
  $select_workstation = $conn->prepare("SELECT * FROM workstation where machine = 'yes';");
  $select_workstation->execute();
  $select_workstation_res = $select_workstation->get_result();
  while ($select_workstation_row = $select_workstation_res->fetch_assoc())
  {
    // $barcode[] = $select_users_row['barcode'];
    // $username[] = $select_users_row['fullname'];


    echo '<option value="' .  $select_workstation_row['name'] . '">' . $select_workstation_row['name'] . ' / ' . $select_workstation_row['username'] . '</option>';
  }
}

// ---------------------------------task inserting scripts---------------------------------
//processwise update
if (isset($_POST['type']) && $_POST['type'] == "process")
{
  // echo $_POST['processes'];
  // print_r(json_decode($_POST['username']));

  $processes = $_POST['processes'];
  $fixdate = $_POST['fixdate'];
  $username_arr = json_decode($_POST['username']);
  $process_start_time = $_POST['process_start_time'];

  $get_tasks = $conn->prepare("SELECT taskid FROM task_id WHERE stageid = ?");
  $get_tasks->bind_param('s', $processes);
  $get_tasks->execute();
  $get_tasks_res = $get_tasks->get_result();

  $task_split_count = ($get_tasks_res->num_rows / count($username_arr));
  // echo $get_tasks_res->num_rows.'\n';
  // echo count($username_arr).'\n';
  // echo $task_split_count;

  $no_usr = count($username_arr);

  $get_time_est = $conn->prepare("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( unit_time )/? ) ) AS estimate_time from task_id where stageid = ?");
  $get_time_est->bind_param('is', $no_usr, $processes);
  $get_time_est->execute();
  $get_time_est_res = $get_time_est->get_result();
  $get_time_est_row = $get_time_est_res->fetch_assoc();

  $time = $process_start_time;
  $time2 = $get_time_est_row['estimate_time'];

  $secs = strtotime($time2) - strtotime("00:00:00");
  $end_time = date("H:i:s", strtotime($time) + $secs);

  for ($j = 0; $j < count($username_arr); $j++)
  {
    // echo $username_arr[$j];
    // echo $fixdate;
    // echo "\n";

    // echo "SELECT taskid FROM task_id WHERE stageid = ? and tdatefm is null limit ".$task_split_count.";";
    $assign_task_id = $conn->prepare("SELECT taskid FROM task_id WHERE stageid = ? and tdatefm is null limit " . ceil($task_split_count) . ";");
    $assign_task_id->bind_param('s', $processes);
    $assign_task_id->execute();
    $assign_task_id_res = $assign_task_id->get_result();
    while ($assign_task_id_row = $assign_task_id_res->fetch_assoc())
    {
      echo $assign_task_id_row['taskid'] . "\n";
      $update_task_wise = $conn->prepare('UPDATE task_id SET user = ?, tdatefm = ?, start_time = ?, est_complete_time = ?, end_time = ? WHERE taskid = ?');
      $update_task_wise->bind_param('ssssss', $username_arr[$j], $fixdate, $process_start_time, $get_time_est_row['estimate_time'], $end_time, $assign_task_id_row['taskid']);
      $update_task_wise->execute();
    }
  }
  echo 'Process task assign loop complete';
}

//workstation wise
if (isset($_POST['type']) && $_POST['type'] == 'workstation')
{
  $workstation = $_POST['workstation'];
  $workprocesses = $_POST['workprocesses'];
  $fixdate = $_POST['fixdate'];
  $workstation_start_time = $_POST['workstation_start_time'];

  $get_user = $conn->prepare('SELECT * FROM workstation WHERE name = ?');
  $get_user->bind_param('s', $workstation);
  $get_user->execute();
  $get_user_res = $get_user->get_result();
  $get_user_row = $get_user_res->fetch_assoc();

  $get_time_est = $conn->prepare("SELECT distinct unit_time from task_id where sheettask_id = ?");
  $get_time_est->bind_param('s', $workprocesses);
  $get_time_est->execute();
  $get_time_est_res = $get_time_est->get_result();
  $get_time_est_row = $get_time_est_res->fetch_assoc();

  $time = $workstation_start_time;
  $time2 = $get_time_est_row['unit_time'];

  $secs = strtotime($time2) - strtotime("00:00:00");
  $end_time = date("H:i:s", strtotime($time) + $secs);

  $update_workstation_task = $conn->prepare('UPDATE task_id SET workstation = ?,user = ?,tdatefm = ?, start_time = ?, est_complete_time = ?, end_time = ? WHERE sheettask_id = ?');
  $update_workstation_task->bind_param('sssssss', $workstation, $get_user_row['username'], $fixdate, $workstation_start_time, $get_time_est_row['unit_time'], $end_time, $workprocesses);
  $update_workstation_task->execute();
}

//material wise
if (isset($_POST['type']) && $_POST['type'] == 'material')
{
  $material = $_POST['material'];
  $material_model = $_POST['material_model'];
  $material_username_arr = json_decode($_POST['material_username_arr']);
  $fixdate = $_POST['fixdate'];
  $material_start_time = $_POST['material_start_time'];

  // echo $material;
  // echo $material_model;
  // print_r($material_username_arr);
  // echo $fixdate;

  $get_tasks = $conn->prepare("SELECT taskid FROM task_id WHERE stageid = ?");
  $get_tasks->bind_param('s', $material_model);
  $get_tasks->execute();
  $get_tasks_res = $get_tasks->get_result();

  $task_split_count = ($get_tasks_res->num_rows / count($material_username_arr));
  // echo $get_tasks_res->num_rows.'\n';
  // echo count($material_username_arr).'\n';
  // echo $task_split_count;

  $no_usr = count($material_username_arr);

  $get_time_est = $conn->prepare("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( unit_time )/? ) ) AS estimate_time from task_id where stageid = ?");
  $get_time_est->bind_param('is', $no_usr, $material_model);
  $get_time_est->execute();
  $get_time_est_res = $get_time_est->get_result();
  $get_time_est_row = $get_time_est_res->fetch_assoc();

  $time = $material_start_time;
  $time2 = $get_time_est_row['estimate_time'];

  $secs = strtotime($time2) - strtotime("00:00:00");
  $end_time = date("H:i:s", strtotime($time) + $secs);

  for ($j = 0; $j < count($material_username_arr); $j++)
  {
    // echo $material_username_arr[$j];
    // echo $fixdate;
    // echo "\n";

    // echo "SELECT taskid FROM task_id WHERE stageid = ? and tdatefm is null limit ".$task_split_count.";";
    $assign_task_id = $conn->prepare("SELECT taskid FROM task_id WHERE stageid = ? and tdatefm is null limit " . ceil($task_split_count) . ";");
    $assign_task_id->bind_param('s', $material_model);
    $assign_task_id->execute();
    $assign_task_id_res = $assign_task_id->get_result();
    while ($assign_task_id_row = $assign_task_id_res->fetch_assoc())
    {
      echo $assign_task_id_row['taskid'] . "\n";
      $update_task_wise = $conn->prepare('UPDATE task_id SET user = ?, tdatefm = ?, start_time = ?, est_complete_time = ?, end_time = ? WHERE taskid = ?');
      $update_task_wise->bind_param('ssssss', $material_username_arr[$j], $fixdate, $material_start_time, $get_time_est_row['estimate_time'], $end_time, $assign_task_id_row['taskid']);
      $update_task_wise->execute();
    }
  }
  echo 'material task assign loop complete';
}

//team wise
if (isset($_POST['type']) && $_POST['type'] == 'team')
{
  $teams = $_POST['teams'];
  $team_model = $_POST['team_model'];
  $team_process = $_POST['team_process'];
  $team_username_arr = json_decode($_POST['team_username_arr']);
  $fixdate = $_POST['fixdate'];
  $team_start_time = $_POST['team_start_time'];

  echo $teams . "\n";
  echo $team_model . "\n";
  echo $team_process . "\n";
  print_r($team_username_arr);
  echo $fixdate . "\n";
  echo $team_start_time . "\n";

  $get_tasks = $conn->prepare("SELECT taskid FROM task_id WHERE stageid = ?");
  $get_tasks->bind_param('s', $team_process);
  $get_tasks->execute();
  $get_tasks_res = $get_tasks->get_result();

  $task_split_count = ($get_tasks_res->num_rows / count($team_username_arr));
  echo $get_tasks_res->num_rows . '\n';
  echo count($team_username_arr) . '\n';
  echo $task_split_count;

  $no_usr = count($team_username_arr);

  $get_time_est = $conn->prepare("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( unit_time )/? ) ) AS estimate_time from task_id where stageid = ?");
  $get_time_est->bind_param('is', $no_usr, $team_process);
  $get_time_est->execute();
  $get_time_est_res = $get_time_est->get_result();
  $get_time_est_row = $get_time_est_res->fetch_assoc();

  $time = $team_start_time;
  $time2 = $get_time_est_row['estimate_time'];

  $secs = strtotime($time2) - strtotime("00:00:00");
  $end_time = date("H:i:s", strtotime($time) + $secs);

  for ($j = 0; $j < count($team_username_arr); $j++)
  {
    // echo $team_username_arr[$j];
    // echo $fixdate;
    // echo "\n";

    // echo "SELECT taskid FROM task_id WHERE stageid = ? and tdatefm is null limit ".$task_split_count.";";
    $assign_task_id = $conn->prepare("SELECT taskid FROM task_id WHERE stageid = ? and tdatefm is null limit " . ceil($task_split_count) . ";");
    $assign_task_id->bind_param('s', $team_process);
    $assign_task_id->execute();
    $assign_task_id_res = $assign_task_id->get_result();
    while ($assign_task_id_row = $assign_task_id_res->fetch_assoc())
    {
      echo $assign_task_id_row['taskid'] . "\n";
      echo $team_username_arr[$j] . "\n";
      echo $fixdate . "\n";
      echo $team_start_time . "\n";
      echo $get_time_est_row['estimate_time'] . "\n";
      echo $end_time . "\n";
      echo $assign_task_id_row['taskid'] . "\n";
      $update_task_wise = $conn->prepare('UPDATE task_id SET user = ?, tdatefm = ?, start_time = ?, est_complete_time = ?, end_time = ? WHERE taskid = ?');
      $update_task_wise->bind_param('ssssss', $team_username_arr[$j], $fixdate, $team_start_time, $get_time_est_row['estimate_time'], $end_time, $assign_task_id_row['taskid']);
      $update_task_wise->execute();
      $conn->error;
    }
  }
  echo 'team task assign loop complete';
}

//model wise
if (isset($_POST['type']) && $_POST['type'] == 'model')
{
  $model = $_POST['model'];
  $model_process = $_POST['model_process'];
  $model_username_arr = json_decode($_POST['model_username_arr']);
  $fixdate = $_POST['fixdate'];
  $model_start_time = $_POST['model_start_time'];

  // echo $model;
  // echo $model_process;
  // print_r($model_username_arr);
  // echo $fixdate;

  $get_tasks = $conn->prepare("SELECT taskid FROM task_id WHERE stageid = ?");
  $get_tasks->bind_param('s', $model_process);
  $get_tasks->execute();
  $get_tasks_res = $get_tasks->get_result();

  $task_split_count = ($get_tasks_res->num_rows / count($model_username_arr));
  // echo $get_tasks_res->num_rows.'\n';
  // echo count($model_username_arr).'\n';
  // echo $task_split_count;

  $no_usr = count($model_username_arr);

  $get_time_est = $conn->prepare("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( unit_time )/? ) ) AS estimate_time from task_id where stageid = ?");
  $get_time_est->bind_param('is', $no_usr, $model_process);
  $get_time_est->execute();
  $get_time_est_res = $get_time_est->get_result();
  $get_time_est_row = $get_time_est_res->fetch_assoc();

  $time = $model_start_time;
  $time2 = $get_time_est_row['estimate_time'];

  $secs = strtotime($time2) - strtotime("00:00:00");
  $end_time = date("H:i:s", strtotime($time) + $secs);

  for ($j = 0; $j < count($model_username_arr); $j++)
  {
    // echo $model_username_arr[$j];
    // echo $fixdate;
    // echo "\n";

    // echo "SELECT taskid FROM task_id WHERE stageid = ? and tdatefm is null limit ".$task_split_count.";";
    $assign_task_id = $conn->prepare("SELECT taskid FROM task_id WHERE stageid = ? and tdatefm is null limit " . ceil($task_split_count) . ";");
    $assign_task_id->bind_param('s', $model_process);
    $assign_task_id->execute();
    $assign_task_id_res = $assign_task_id->get_result();
    while ($assign_task_id_row = $assign_task_id_res->fetch_assoc())
    {
      // echo $assign_task_id_row['taskid']."\n";
      $update_task_wise = $conn->prepare('UPDATE task_id SET user = ?, tdatefm = ?, start_time = ?, est_complete_time = ?, end_time = ? WHERE taskid = ?');
      $update_task_wise->bind_param('ssssss', $model_username_arr[$j], $fixdate, $model_start_time, $get_time_est_row['estimate_time'], $end_time, $assign_task_id_row['taskid']);
      $update_task_wise->execute();
    }
  }
  echo 'model task assign loop complete';
}

// -------------------select fetch scripts---------------------

// -------process wise scripts - gen shift - start ------
if (isset($_POST['type']) && $_POST['type'] == 'model_fetch')
{
  $model_name = $_POST['model_name'];
  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];

  $get_process = $conn->prepare("SELECT distinct process_name from task_id where batchno=? and model_name = ? and tdatefm is null");
  $get_process->bind_param('ss', $batch_no, $model_name);
  $get_process->execute();
  $get_process_res = $get_process->get_result();
  echo '<option value="" selected disabled>Select Process</option>';
  while ($get_process_row = $get_process_res->fetch_assoc())
  {
    echo '<option value="' . $get_process_row['process_name'] . '">' . $get_process_row['process_name'] . '</option>';
    // $part_name[] = '<option value="'.$get_process_row['stageid'].'">'.$get_process_row['part_name'].'</option>';
  }
  //

  // $data = array();
  // $data['process_name'] = $process_name;
  // $data['part_name'] = $part_name;
  // $data['username'] = $user_arr;
  //
  // echo json_encode($data);


  //get no of task, unit time, estimted time for complete
}

if (isset($_POST['type']) && $_POST['type'] == 'get_parts')
{
  $model_name = $_POST['model_name'];
  $processes = $_POST['processes'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];

  $get_parts = $conn->prepare("SELECT distinct stageid, part_name from task_id where batchno=? and model_name = ? and process_name = ? and tdatefm is null");
  $get_parts->bind_param('sss', $batch_no, $model_name, $processes);
  $get_parts->execute();
  $get_parts_res = $get_parts->get_result();
  echo '<option value="" selected disabled>Select Part Name</option>';
  while ($get_parts_row = $get_parts_res->fetch_assoc())
  {
    echo '<option value="' . $get_parts_row['stageid'] . '">' . $get_parts_row['part_name'] . '</option>';
  }
}

if (isset($_POST['type']) && $_POST['type'] == 'unit_time')
{
  $stageid = $_POST['parts'];

  $get_task_count = $conn->prepare("SELECT count(taskid) as task_count, SEC_TO_TIME( SUM( TIME_TO_SEC( unit_time ) ) ) AS estimate_time from task_id where stageid = ?");
  $get_task_count->bind_param('s', $stageid);
  $get_task_count->execute();
  $get_task_count_res = $get_task_count->get_result();
  $get_task_count_row = $get_task_count_res->fetch_assoc();

  $get_unit_time = $conn->prepare('SELECT DISTINCT unit_time from task_id WHERE stageid = ?');
  $get_unit_time->bind_param('s', $stageid);
  $get_unit_time->execute();
  $get_unit_time_res = $get_unit_time->get_result();
  $get_unit_time_row = $get_unit_time_res->fetch_assoc();

  echo "<strong>Number of Tasks:" . $get_task_count_row['task_count'] . "</strong><br>";
  echo "<strong>Unit Time:" . $get_unit_time_row['unit_time'] . "</strong><br>";
  echo "<strong>Estimate time to completion :" . $get_task_count_row['estimate_time'] . "</strong><br>";
}

if (isset($_POST['type']) && $_POST['type'] == 'process_users')
{
  $model_name = $_POST['model_name'];
  $process_start_time = $_POST['process_start_time'];

  $select_team = $conn->prepare("SELECT * FROM model where model_name = ?;");
  $select_team->bind_param('s', $model_name);
  $select_team->execute();
  $select_team_res = $select_team->get_result();
  $select_team_row = $select_team_res->fetch_assoc();

  $get_users = $conn->prepare('SELECT * FROM user_detail where teamname = ?');
  $get_users->bind_param('s', $select_team_row['teamname']);
  $get_users->execute();
  $get_users_res = $get_users->get_result();
  //add only users assigned for shift
  echo '<option value="" selected disabled>Select Associate</option>';
  while ($get_users_row = $get_users_res->fetch_assoc())
  {
    // $get_time = $conn->prepare('SELECT DISTINCT start_time, est_complete_time from task_id where user = ?');
    // $get_time->bind_param('s', $get_users_row['user_name']);
    // $get_time->execute();
    // $get_time_res = $get_time->get_result;
    // $get_time_row = $get_time->fetch_assoc();
    //better this login; users occupied for that time should not display
    echo '<option value="' . $get_users_row['user_name'] . '">' . $get_users_row['fullname'] . '</option>';
  }
}
// -------process wise scripts - gen shift - end ------

// ------------workstation wise scripts - gen shift - start-------------
if (isset($_POST['type']) && $_POST['type'] == 'work_parts')
{
  $workprocesses = $_POST['workprocesses'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];


  // SELECT DISTINCT sheettask_id FROM `task_id` where comb_grpid='test-roh-A32-laser_Combination 1' and process_name='laser cutting';


  $get_parts = $conn->prepare("SELECT distinct sheettask_id from task_id where batchno=? and comb_grpid = ? and tdatefm is null and process_name='laser cutting'");
  $get_parts->bind_param('ss', $batch_no, $workprocesses);
  $get_parts->execute();
  $get_parts_res = $get_parts->get_result();
  echo '<option value="" selected disabled>Select Part Name</option>';
  while ($get_parts_row = $get_parts_res->fetch_assoc())
  {
    echo '<option value="' . $get_parts_row['sheettask_id'] . '">' . $get_parts_row['sheettask_id'] . '</option>';
  }
}

if (isset($_POST['type']) && $_POST['type'] == 'work_unit_time')
{
  $sheetid = $_POST['work_parts'];

  $get_task_count = $conn->prepare("SELECT count(taskid) as task_count from task_id where sheettask_id = ?");
  $get_task_count->bind_param('s', $sheetid);
  $get_task_count->execute();
  $get_task_count_res = $get_task_count->get_result();
  $get_task_count_row = $get_task_count_res->fetch_assoc();

  // $get_unit_time = $conn->prepare('SELECT DISTINCT unit_time from task_id WHERE sheettask_id = ?');
  $get_unit_time = $conn->prepare('SELECT DISTINCT unit_time from task_id WHERE sheettask_id = ? and unit_time!=""'); //added unit time is not blank to get unit time of only main task; subtask unit is blank
  $get_unit_time->bind_param('s', $sheetid);
  $get_unit_time->execute();
  $get_unit_time_res = $get_unit_time->get_result();
  $get_unit_time_row = $get_unit_time_res->fetch_assoc();

  echo "<strong>Number of Tasks:" . $get_task_count_row['task_count'] . "</strong><br>";
  echo "<strong>Unit Time:" . $get_unit_time_row['unit_time'] . "</strong><br>";
  echo "<strong>Estimate time to completion :" . $get_unit_time_row['unit_time'] . "</strong><br>";
}
// ------------workstation wise scripts - gen shift - end-------------

// -------------material wise scripts - gen shift - start-------------
if (isset($_POST['type']) && $_POST['type'] == 'material_models')
{
  $material_name = $_POST['material'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];

  $material = $conn->prepare('SELECT DISTINCT model_name from task_id where batchno = ? and material_name = ? and tdatefm is null');
  $material->bind_param('ss', $batch_no, $material_name);
  $material->execute();
  $material_res = $material->get_result();
  echo '<option value="" selected disabled>Select Model</option>';
  while ($material_row = $material_res->fetch_assoc())
  {
    echo '<option value="' . $material_row['model_name'] . '">' . $material_row['model_name'] . '</option>';
  }
}

if (isset($_POST['type']) && $_POST['type'] == 'material_process')
{
  $material_model = $_POST['material_model'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];

  $fetch_process = $conn->prepare('SELECT DISTINCT stageid, process_name from task_id where batchno=? and model_name = ? and tdatefm is null');
  $fetch_process->bind_param('ss', $batch_no, $material_model);
  $fetch_process->execute();
  $fetch_process_res = $fetch_process->get_result();
  echo '<option value="" selected disabled>Select Process</option>';
  while ($fetch_process_row = $fetch_process_res->fetch_assoc())
  {
    echo '<option value="' . $fetch_process_row['stageid'] . '">' . $fetch_process_row['process_name'] . '</option>';
  }
}

if (isset($_POST['type']) && $_POST['type'] == 'material_users')
{
  $model_name = $_POST['material_model'];
  $process_start_time = $_POST['material_start_time'];

  $select_team = $conn->prepare("SELECT * FROM model where model_name = ?;");
  $select_team->bind_param('s', $model_name);
  $select_team->execute();
  $select_team_res = $select_team->get_result();
  $select_team_row = $select_team_res->fetch_assoc();

  $get_users = $conn->prepare('SELECT * FROM user_detail where teamname = ?');
  $get_users->bind_param('s', $select_team_row['teamname']);
  $get_users->execute();
  $get_users_res = $get_users->get_result();
  //add only users assigned for shift
  echo '<option value="" selected disabled>Select Associate</option>';
  while ($get_users_row = $get_users_res->fetch_assoc())
  {
    // $get_time = $conn->prepare('SELECT DISTINCT start_time, est_complete_time from task_id where user = ?');
    // $get_time->bind_param('s', $get_users_row['user_name']);
    // $get_time->execute();
    // $get_time_res = $get_time->get_result;
    // $get_time_row = $get_time->fetch_assoc();
    //better this login; users occupied for that time should not display
    echo '<option value="' . $get_users_row['user_name'] . '">' . $get_users_row['fullname'] . '</option>';
  }
}

if (isset($_POST['type']) && $_POST['type'] == 'material_unit_time')
{
  $stageid = $_POST['material_process'];

  $get_task_count = $conn->prepare("SELECT count(taskid) as task_count, SEC_TO_TIME( SUM( TIME_TO_SEC( unit_time ) ) ) AS estimate_time from task_id where stageid = ?");
  $get_task_count->bind_param('s', $stageid);
  $get_task_count->execute();
  $get_task_count_res = $get_task_count->get_result();
  $get_task_count_row = $get_task_count_res->fetch_assoc();

  $get_unit_time = $conn->prepare('SELECT DISTINCT unit_time from task_id WHERE stageid = ?');
  $get_unit_time->bind_param('s', $stageid);
  $get_unit_time->execute();
  $get_unit_time_res = $get_unit_time->get_result();
  $get_unit_time_row = $get_unit_time_res->fetch_assoc();

  echo "<strong>Number of Tasks:" . $get_task_count_row['task_count'] . "</strong><br>";
  echo "<strong>Unit Time:" . $get_unit_time_row['unit_time'] . "</strong><br>";
  echo "<strong>Estimate time to completion :" . $get_task_count_row['estimate_time'] . "</strong><br>";
}
// -------------material wise scripts - gen shift - end-------------
// --------------team wise scripts - general - start--------------
if (isset($_POST['type']) && $_POST['type'] == 'team_model')
{
  $team = $_POST['team'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];

  $get_team_members = $conn->prepare('SELECT distinct task_id.model_name from model left join task_id on task_id.model_name = model.model_name WHERE teamname = ? and batchno = ?');
  $get_team_members->bind_param('ss', $team, $batch_no);
  $get_team_members->execute();
  $get_team_members_res = $get_team_members->get_result();
  echo '<option value="" selected disabled>Select Model</option>';
  while ($get_team_members_row = $get_team_members_res->fetch_assoc())
  {
    echo '<option value="' . $get_team_members_row['model_name'] . '">' . $get_team_members_row['model_name'] . '</option>';
  }
}

if (isset($_POST['type']) && $_POST['type'] == 'team_process')
{
  $team_model = $_POST['team_model'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];

  $team_process = $conn->prepare('SELECT DISTINCT stageid, process_name from task_id where batchno = ? and model_name = ? and tdatefm is null');
  $team_process->bind_param('ss', $batch_no, $team_model);
  $team_process->execute();
  $team_process_res = $team_process->get_result();
  echo '<option value="" selected disabled>Select Process</option>';
  while ($team_process_row = $team_process_res->fetch_assoc())
  {
    echo '<option value="' . $team_process_row['stageid'] . '">' . $team_process_row['process_name'] . '</option>';
  }
}

if (isset($_POST['type']) && $_POST['type'] == 'team_unit_time')
{
  $stageid = $_POST['team_process'];

  $get_task_count = $conn->prepare("SELECT count(taskid) as task_count, SEC_TO_TIME( SUM( TIME_TO_SEC( unit_time ) ) ) AS estimate_time from task_id where stageid = ?");
  $get_task_count->bind_param('s', $stageid);
  $get_task_count->execute();
  $get_task_count_res = $get_task_count->get_result();
  $get_task_count_row = $get_task_count_res->fetch_assoc();

  $get_unit_time = $conn->prepare('SELECT DISTINCT unit_time from task_id WHERE stageid = ?');
  $get_unit_time->bind_param('s', $stageid);
  $get_unit_time->execute();
  $get_unit_time_res = $get_unit_time->get_result();
  $get_unit_time_row = $get_unit_time_res->fetch_assoc();

  echo "<strong>Number of Tasks:" . $get_task_count_row['task_count'] . "</strong><br>";
  echo "<strong>Unit Time:" . $get_unit_time_row['unit_time'] . "</strong><br>";
  echo "<strong>Estimate time to completion :" . $get_task_count_row['estimate_time'] . "</strong><br>";
}

if (isset($_POST['type']) && $_POST['type'] == 'team_username')
{
  $team = $_POST['team'];

  $get_users = $conn->prepare('SELECT * FROM user_detail where teamname = ?');
  $get_users->bind_param('s', $team);
  $get_users->execute();
  $get_users_res = $get_users->get_result();
  //add only users assigned for shift
  echo '<option value="" selected disabled>Select Associate</option>';
  while ($get_users_row = $get_users_res->fetch_assoc())
  {
    // $get_time = $conn->prepare('SELECT DISTINCT start_time, est_complete_time from task_id where user = ?');
    // $get_time->bind_param('s', $get_users_row['user_name']);
    // $get_time->execute();
    // $get_time_res = $get_time->get_result;
    // $get_time_row = $get_time->fetch_assoc();
    //better this login; users occupied for that time should not display
    echo '<option value="' . $get_users_row['user_name'] . '">' . $get_users_row['fullname'] . '</option>';
  }
}

// --------------team wise scripts - general - end--------------
// ---------model wise scripts - general shift - start----------
if (isset($_POST['type']) && $_POST['type'] == 'models')
{
  $model_name = $_POST['models'];

  $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
  $get_current_batch->execute();
  $get_current_batch_res = $get_current_batch->get_result();
  $get_current_batch_row = $get_current_batch_res->fetch_assoc();
  $batch_no = $get_current_batch_row['batchno'];

  $get_process = $conn->prepare("SELECT distinct stageid, process_name from task_id where batchno=? and model_name = ? and tdatefm is null");
  $get_process->bind_param('ss', $batch_no, $model_name);
  $get_process->execute();
  $get_process_res = $get_process->get_result();
  echo '<option value="" selected disabled>Select Process</option>';
  while ($get_process_row = $get_process_res->fetch_assoc())
  {
    echo '<option value="' . $get_process_row['stageid'] . '">' . $get_process_row['process_name'] . '</option>';
    // $part_name[] = '<option value="'.$get_process_row['stageid'].'">'.$get_process_row['part_name'].'</option>';
  }
}

if (isset($_POST['type']) && $_POST['type'] == 'model_process')
{
  $model_name = $_POST['model_process'];

  $select_team = $conn->prepare("SELECT * FROM model where model_name = ?;");
  $select_team->bind_param('s', $model_name);
  $select_team->execute();
  $select_team_res = $select_team->get_result();
  $select_team_row = $select_team_res->fetch_assoc();

  $get_users = $conn->prepare('SELECT * FROM user_detail where teamname = ?');
  $get_users->bind_param('s', $select_team_row['teamname']);
  $get_users->execute();
  $get_users_res = $get_users->get_result();
  //add only users assigned for shift
  echo '<option value="" selected disabled>Select Associate</option>';
  while ($get_users_row = $get_users_res->fetch_assoc())
  {
    // $get_time = $conn->prepare('SELECT DISTINCT start_time, est_complete_time from task_id where user = ?');
    // $get_time->bind_param('s', $get_users_row['user_name']);
    // $get_time->execute();
    // $get_time_res = $get_time->get_result;
    // $get_time_row = $get_time->fetch_assoc();
    //better this login; users occupied for that time should not display
    echo '<option value="' . $get_users_row['user_name'] . '">' . $get_users_row['fullname'] . '</option>';
  }
}

if (isset($_POST['type']) && $_POST['type'] == 'model_unit_time')
{
  $stageid = $_POST['model_process'];

  $get_task_count = $conn->prepare("SELECT count(taskid) as task_count, SEC_TO_TIME( SUM( TIME_TO_SEC( unit_time ) ) ) AS estimate_time from task_id where stageid = ?");
  $get_task_count->bind_param('s', $stageid);
  $get_task_count->execute();
  $get_task_count_res = $get_task_count->get_result();
  $get_task_count_row = $get_task_count_res->fetch_assoc();

  $get_unit_time = $conn->prepare('SELECT DISTINCT unit_time from task_id WHERE stageid = ?');
  $get_unit_time->bind_param('s', $stageid);
  $get_unit_time->execute();
  $get_unit_time_res = $get_unit_time->get_result();
  $get_unit_time_row = $get_unit_time_res->fetch_assoc();

  echo "<strong>Number of Tasks:" . $get_task_count_row['task_count'] . "</strong><br>";
  echo "<strong>Unit Time:" . $get_unit_time_row['unit_time'] . "</strong><br>";
  echo "<strong>Estimate time to completion :" . $get_task_count_row['estimate_time'] . "</strong><br>";
}
// ---------model wise scripts - general shift - end----------

// -------------process shift card - general - start-------------
if (isset($_POST['type']) && $_POST['type'] == 'shift_cards')
{
  //get target date->get shift (gen or multi)->generate cards with
  $target_date = $_POST['fixdate'];



  // print_r($get_shift_row);

  $count = 1;

  //init variable for concat
  $output = '';
  $process = '';
  $workstation = '';
  $material_name = '';
  $user = '';
  $model_name = '';

  if (isset($_POST['shift']))
  {
    $get_shift = $conn->prepare("SELECT distinct date(shiftdate) as shiftdate, start_time, end_time from shift_user_plan where shiftdate = date(?) and shift_type = ?;");
    $get_shift->bind_param('ss', $target_date, $_POST['shift']);
    $get_shift->execute();
    $get_shift_res = $get_shift->get_result();
    $get_shift_row = $get_shift_res->fetch_assoc();

    $temp_s_time = $get_shift_row['shiftdate'] . " " . $get_shift_row['start_time'];
    $temp_e_time = $get_shift_row['shiftdate'] . " " . $get_shift_row['end_time'];

    // echo $temp_s_time."\n";
    // echo $temp_e_time."\n";

    $startTime = strtotime($temp_s_time);
    $endTime = strtotime($temp_e_time);
    $intervel = "120";

    $time = $startTime;

?><h5 class="text-center">Assigned Associates</h5>
    <div class="card-deck">
      <?php
      while ($time < $endTime)
      {
        $s_time = date('H:i', $time);
        $time = strtotime('+' . $intervel . ' minutes', $time);
        $e_time =  date('H:i', $time);

        $tdate = date("Y-m-d", strtotime($get_shift_row['shiftdate']));

        $get_assigned = $conn->prepare('SELECT DISTINCT
          t.stageid,
          t.process_name,
          t.workstation,
          t.material_name,
          t.user,
          t.model_name,
          t.start_time,
          t.end_time,
          t.tdatefm,
          s.shift_type
      FROM
          task_id t
      JOIN
          shift_user_plan s ON t.user = s.username AND DATE(t.tdatefm) = s.shiftdate
      WHERE
          (DATE(t.tdatefm) = ?) AND
          (
              (? <= t.end_time AND ? >= t.start_time) OR
              (? <= t.start_time AND ? >= t.end_time) OR
              (? >= t.start_time AND ? <= t.end_time)
          );');
        $get_assigned->bind_param('sssssss', $tdate, $s_time, $e_time, $s_time, $e_time, $s_time, $e_time);
        $get_assigned->execute();
        $get_assigned_res = $get_assigned->get_result();

        $gen_assign_header = '<div class="card">
        <div class="card-body">
          <h5 class="card-title font-weight-bold">Shift Time: ' . $s_time . " - " . $e_time . '</h5>';

        //init variable for concat
        $process = '';
        $workstation = '';
        $material_name = '';
        $user = '';
        $model_name = '';

        $process .= $gen_assign_header;
        $workstation .= $gen_assign_header;
        $material_name .= $gen_assign_header;
        $user .= $gen_assign_header;
        $model_name .= $gen_assign_header;

        while ($get_assigned_row = $get_assigned_res->fetch_assoc())
        {
          if (!empty($get_assigned_row['process_name']))
          {
            $process .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['process_name'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
          }
          if (!empty($get_assigned_row['workstation']))
          {
            $workstation .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['workstation'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
          }
          if (!empty($get_assigned_row['material_name']))
          {
            $material_name .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['material_name'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
          }
          if (!empty($get_assigned_row['user']))
          {
            $user .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['user'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
          }
          if (!empty($get_assigned_row['model_name']))
          {
            $model_name .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['model_name'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
          }
        }
        $gen_assign_footer = '</div></div>';

        $process .= $gen_assign_footer;
        $workstation .= $gen_assign_footer;
        $material_name .= $gen_assign_footer;
        $user .= $gen_assign_footer;
        $model_name .= $gen_assign_footer;

        if ($_POST['card'] == 'process')
        {
          echo $process;
        }
        if ($_POST['card'] == 'workstation')
        {
          echo $workstation;
        }
        if ($_POST['card'] == 'material')
        {
          echo $material_name;
        }
        if ($_POST['card'] == 'team')
        {
          echo $user;
        }
        if ($_POST['card'] == 'model')
        {
          echo $model_name;
        }
      }


      $startTime = strtotime($temp_s_time);
      $endTime = strtotime($temp_e_time);
      $intervel = "120";

      $time = $startTime;
      ?>
    </div>
    <h5 class="text-center">Unassigned Associates</h5>
    <div class="card-deck">
      <?php
      while ($time < $endTime)
      {
        $s_time = date('H:i', $time);
        $time = strtotime('+' . $intervel . ' minutes', $time);
        $e_time =  date('H:i', $time);

      ?><div class="card">
          <div class="card-body">
            <h5 class="card-title font-weight-bold">Shift Time: <?php echo $s_time . " - " . $e_time; ?></h5>
          </div>
        </div><?php

            }
              ?>
    </div><?php
        }
        else
        {
          $get_shift = $conn->prepare("SELECT distinct date(shiftdate) as shiftdate, start_time, end_time from shift_user_plan where shiftdate = date(?);");
          $get_shift->bind_param('s', $target_date);
          $get_shift->execute();
          $get_shift_res = $get_shift->get_result();
          $get_shift_row = $get_shift_res->fetch_assoc();

          print_r($get_shift_row);
          $temp_s_time = $get_shift_row['shiftdate'] . " " . $get_shift_row['start_time'];
          $temp_e_time = $get_shift_row['shiftdate'] . " " . $get_shift_row['end_time'];

          // echo $temp_s_time."\n";
          // echo $temp_e_time."\n";

          $startTime = strtotime($temp_s_time);
          $endTime = strtotime($temp_e_time);
          $intervel = "120";

          $time = $startTime;

          ?><h5 class="text-center">Assigned Associates</h5>
    <div class="card-deck">
      <?php
          while ($time < $endTime)
          {
            $s_time = date('H:i', $time);
            $time = strtotime('+' . $intervel . ' minutes', $time);
            $e_time =  date('H:i', $time);

            $tdate = date("Y-m-d", strtotime($get_shift_row['shiftdate']));

            $get_assigned = $conn->prepare('SELECT DISTINCT
          t.stageid,
          t.process_name,
          t.workstation,
          t.material_name,
          t.user,
          t.model_name,
          t.start_time,
          t.end_time,
          t.tdatefm,
          s.shift_type
      FROM
          task_id t
      JOIN
          shift_user_plan s ON t.user = s.username AND DATE(t.tdatefm) = s.shiftdate
      WHERE
          (DATE(t.tdatefm) = ?) AND
          (
              (? <= t.end_time AND ? >= t.start_time) OR
              (? <= t.start_time AND ? >= t.end_time) OR
              (? >= t.start_time AND ? <= t.end_time)
          );');
            $get_assigned->bind_param('sssssss', $tdate, $s_time, $e_time, $s_time, $e_time, $s_time, $e_time);
            $get_assigned->execute();
            $get_assigned_res = $get_assigned->get_result();

            $gen_assign_header = '<div class="card">
        <div class="card-body">
          <h5 class="card-title font-weight-bold">Shift Time: ' . $s_time . " - " . $e_time . '</h5>';

            //init variable for concat
            $process = '';
            $workstation = '';
            $material_name = '';
            $user = '';
            $model_name = '';

            $process .= $gen_assign_header;
            $workstation .= $gen_assign_header;
            $material_name .= $gen_assign_header;
            $user .= $gen_assign_header;
            $model_name .= $gen_assign_header;

            while ($get_assigned_row = $get_assigned_res->fetch_assoc())
            {
              if (!empty($get_assigned_row['process_name']))
              {
                $process .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['process_name'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
              }
              if (!empty($get_assigned_row['workstation']))
              {
                $workstation .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['workstation'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
              }
              if (!empty($get_assigned_row['material_name']))
              {
                $material_name .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['material_name'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
              }
              if (!empty($get_assigned_row['user']))
              {
                $user .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['user'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
              }
              if (!empty($get_assigned_row['model_name']))
              {
                $model_name .= '<span class="badge badge-success text-wrap">' . $get_assigned_row['model_name'] . ': ' . $get_assigned_row['start_time'] . '-' . $get_assigned_row['end_time'] . '</span>';
              }
            }
            $gen_assign_footer = '</div></div>';

            $process .= $gen_assign_footer;
            $workstation .= $gen_assign_footer;
            $material_name .= $gen_assign_footer;
            $user .= $gen_assign_footer;
            $model_name .= $gen_assign_footer;

            if ($_POST['card'] == 'process')
            {
              echo $process;
            }
            if ($_POST['card'] == 'workstation')
            {
              echo $workstation;
            }
            if ($_POST['card'] == 'material')
            {
              echo $material_name;
            }
            if ($_POST['card'] == 'team')
            {
              echo $user;
            }
            if ($_POST['card'] == 'model')
            {
              echo $model_name;
            }
          }


          $startTime = strtotime($temp_s_time);
          $endTime = strtotime($temp_e_time);
          $intervel = "120";

          $time = $startTime;
      ?>
    </div>
    <h5 class="text-center">Unassigned Associates</h5>
    <div class="card-deck">
      <?php
          while ($time < $endTime)
          {
            $s_time = date('H:i', $time);
            $time = strtotime('+' . $intervel . ' minutes', $time);
            $e_time =  date('H:i', $time);

      ?><div class="card">
          <div class="card-body">
            <h5 class="card-title font-weight-bold">Shift Time: <?php echo $s_time . " - " . $e_time; ?></h5>
          </div>
        </div><?php

            }
              ?>
    </div><?php


        }
      }
      // -------------process shift card - general - end-------------
      // ----------associate guage data start-----------
      if (isset($_POST['type']) && $_POST['type'] == 'associate_gauge')
      {
        //get all Associates
        //store sum of est_complete_time for each associate; sum array and then divide by 8

        $fixdate = $_POST['fixdate'];

        $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
        $get_current_batch->execute();
        $get_current_batch_res = $get_current_batch->get_result();
        $get_current_batch_row = $get_current_batch_res->fetch_assoc();

        $select_users = $conn->prepare("SELECT * FROM user_detail where role = 'Associates' and teamname is not null;");
        $select_users->execute();
        $select_users_res = $select_users->get_result();
        while ($select_users_row = $select_users_res->fetch_assoc())
        {
          $get_sum_time = $conn->prepare('SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(est_complete_time))) AS est_complete_time from task_id WHERE user = ? and batchno = ? and est_complete_time is not null and date(tdatefm) = ?');
          $get_sum_time->bind_param('sss', $select_users_row['user_name'], $get_current_batch_row['batchno'], $fixdate);
          $get_sum_time->execute();
          $get_sum_time_res = $get_sum_time->get_result();
          $get_sum_time_row = $get_sum_time_res->fetch_assoc();

          if (!empty($get_sum_time_row['est_complete_time']))
          {
            $times[] = $get_sum_time_row['est_complete_time'];
          }
        }

        // $times = ['02:30:00', '03:15:00', '01:45:00', '02:00:00'];
        // print_r($times);
        // Calculate the total seconds
        $totalSeconds = 0;
        foreach ($times as $time)
        {
          list($hours, $minutes, $seconds) = explode(':', $time);
          $totalSeconds += $hours * 3600 + $minutes * 60 + $seconds;
        }

        // Calculate the total hours
        $totalHours = $totalSeconds / 3600;

        // Divide by 8 hours to get the integer value
        $integerValue = (int)($totalHours / 8);

        // Output the result
        // echo "Total Hours: $totalHours\n";
        // echo "Integer Value (Total Hours / 8): $integerValue\n";
        echo $integerValue;
      }
      // ----------associate guage data end-----------
      // ---------workstation guage chart start----------
      if (isset($_POST['type']) && $_POST['type'] == 'work_gauge')
      {
        $fixdate = $_POST['fixdate'];

        $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
        $get_current_batch->execute();
        $get_current_batch_res = $get_current_batch->get_result();
        $get_current_batch_row = $get_current_batch_res->fetch_assoc();

        $select_workstation = $conn->prepare("SELECT * FROM workstation where machine = 'yes';");
        $select_workstation->execute();
        $select_workstation_res = $select_workstation->get_result();
        while ($select_workstation_row = $select_workstation_res->fetch_assoc())
        {
          $get_sum_time = $conn->prepare('SELECT SEC_TO_TIME(AVG(TIME_TO_SEC(est_complete_time))) AS est_complete_time from task_id WHERE workstation = ? and batchno = ? and est_complete_time is not null and date(tdatefm) = ?');
          $get_sum_time->bind_param('sss', $select_workstation_row['name'], $get_current_batch_row['batchno'], $fixdate);
          $get_sum_time->execute();
          $get_sum_time_res = $get_sum_time->get_result();
          $get_sum_time_row = $get_sum_time_res->fetch_assoc();

          if (!empty($get_sum_time_row['est_complete_time']))
          {
            $times[] = $get_sum_time_row['est_complete_time'];
          }
        }


        // $times = ['02:30:00', '03:15:00', '01:45:00', '02:00:00'];
        // print_r($times);
        // Calculate the total seconds
        $totalSeconds = 0;
        foreach ($times as $time)
        {
          list($hours, $minutes, $seconds) = explode(':', $time);
          $totalSeconds += $hours * 3600 + $minutes * 60 + $seconds;
        }

        // Calculate the total hours
        $totalHours = $totalSeconds / 3600;

        // Divide by 8 hours to get the integer value
        $integerValue = (int)($totalHours / 8);

        // Output the result
        // echo "Total Hours: $totalHours\n";
        // echo "Integer Value (Total Hours / 8): $integerValue\n";
        echo $integerValue;
      }
      // ---------workstation guage chart end----------
          ?>