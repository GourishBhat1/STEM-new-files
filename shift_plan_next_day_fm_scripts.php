<?php
$conn = new mysqli("stemlearning.in", "steml1og_stemftest", "7V2WDw385ykQ+)N", "steml1og_stemftest") or die('Cannot connect to db');






// ********************************************************************
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


    // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // Deleting old entries of general shift before inserting new ones
    $delete_shift = $conn->prepare("DELETE FROM shift_user_plan WHERE DATE(shiftdate)=?");
    $delete_shift->bind_param('s', $fixdate);
    if ($delete_shift->execute())
    {
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
        echo 'General shift updated';

        $update_plandate_for_fm = $conn->prepare("UPDATE task_id SET plandtfm = ? WHERE tdatefm = ?");
        $update_plandate_for_fm->bind_param('ss', $fixdate, $fixdate);
        $update_plandate_for_fm->execute();
    }
}

// ***************************************************************************************************************
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


    // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // Deleting old entries of general shift before inserting new ones
    $delete_shift = $conn->prepare("DELETE FROM shift_user_plan WHERE DATE(shiftdate)=?");
    $delete_shift->bind_param('s', $fixdate);
    if ($delete_shift->execute())
    {

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

        $update_plandate_for_fm = $conn->prepare("UPDATE task_id SET plandtfm = ? WHERE tdatefm = ?");
        $update_plandate_for_fm->bind_param('ss', $fixdate, $fixdate);
        $update_plandate_for_fm->execute();
    }



    // echo $shift1starttime."\n";
    // echo $shift1endtime."\n";
    // echo $shift2starttime."\n";
    // echo $shift2endtime."\n";
    // echo $fixdate."\n";
    // print_r($shift1_user_arr);
    // print_r($shift2_user_arr);
}

// **************************************************************************
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

// |||||||||||||||||||||||||||||||||||||||\\\\\
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

// |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
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

// |||||||||||||||||||||||||||||||||||||||||||||||||||||||||

//machine assign
if (isset($_POST['type']) && $_POST['type'] == "machine_assign")
{
    $machin_assoc = $_POST['machin_assoc'];
    $machine_workstation = $_POST['machine_workstation'];

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
