<?php
include 'hadder.php';
include '../function.php';
$conn = new mysqli("stemlearning.in", "steml1og_stemftest", "7V2WDw385ykQ+)N", "steml1og_stemftest") or die('Cannot connect to db');
$date = date('Y-m-d');
$datet = date('Y-m-d H:i:s');

$update = $conn->prepare('update user_detail set user_type = "Executives" where role = "Executive" or role = "admin";');
$update->execute();

$get_user_count = $conn->prepare("SELECT COUNT(*) AS user_count FROM user_detail where role = 'Associates' and teamname is not null;");
$get_user_count->execute();
$get_user_count_res = $get_user_count->get_result();
$get_user_count_row = $get_user_count_res->fetch_assoc();
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<div class="app-main__outer">
    <!-- <div class="text-lg text-center">
        <label>Batch Planning Timer</label>&nbsp;
        <div id="time-elapsed"></div>
    </div> -->
    <div class="app-main__inner">
        <div class="app-page-title text-right"></div><!-- app-page-title close -->
        <div class="container">
            <?php
            $sqlcb = "SELECT * FROM current_batch WHERE 1";

            $resultcb = $conn->query($sqlcb);
            $rowcd = $resultcb->fetch_assoc();
            $batchno = $rowcd['batchno']; //current batch from current batch table
            $batchdate = $rowcd['batchdate']; //current batch date from current batch table

            $earlier = new DateTime(date('Y-m-d', strtotime($batchdate)));
            $later = new DateTime($date);

            // echo $earlier;
            //
            // echo $pos_diff = $earlier->diff($later)->format("%r%a"); //3

            ?>
            <?php
            $dep = $_SESSION['department'];
            $review = mysqli_query($conn, "SELECT * FROM othertask WHERE touser='$username' and cast(startdt as DATE)='$tdate' and cdatet is null and tasktype='Batch Planning'");
            $review = mysqli_fetch_array($review);
            // if(1==1)
            // if($review)
            // {
            // $rid = $review["id"];
            ?>


            <?php
            $todaysDate = date('Y-m-d');
            ?>
            <div class="row text-center">
                <form method="post" class="col-4">

                    <label for="">Production Assign Date: </label>
                    <input type="date" class="form-control" id="fixdate" value="<?php echo $todaysDate; ?>" readonly>

                    <!-- <div class="form-row">
                        <div class="col">
                            <label for="">Shift type: </label>
                            <select class="form-control" name="" id="shift_type">
                                <option value="1">General Shift</option>
                                <option value="2">Multiple Shift</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row form-inline" id="gen_shift_time">
                        <div class="col">
                            <label for="">Shift start time</label>
                            <input type="time" name="" id="gen_shift_start_time">
                            <label for="">Shift end time</label>
                            <input type="time" name="" id="gen_shift_end_time">
                        </div>
                        <button type="button" id="gen_shift_submit" name="button">Create Shift</button>
                    </div>
                    <div id="shift_time_form" style="display:none;">

                        <div class="form-row text-center">
                            <div class="col">

                                <label for="">Shift 1</label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <label for="">Start time</label>
                                <input type="time" name="" id="shift1starttime">
                            </div>
                            <div class="col">
                                <label for="">End time</label>
                                <input type="time" name="" id="shift1endtime">
                            </div>
                        </div>
                        <hr>
                        <div class="form-row text-center">
                            <div class="col">

                                <label for="">Shift 2</label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <label for="">Start time</label>
                                <input type="time" name="" id="shift2starttime">
                            </div>
                            <div class="col">
                                <label for="">End time</label>
                                <input type="time" name="" id="shift2endtime">
                            </div>
                        </div>
                        <hr>
                        <div class="form-row">
                            <div class="col">
                                <label for="">Shift 1 Number of Associates</label>
                                <input type="number">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <label for="">Shift 2 Number of Associates</label>
                                <input type="number">
                            </div>
                        </div>

                        <button type="button" id="multi_shift_submit" name="button">Create Shift</button>
                    </div> -->


                    <!-- <label for=""></label>
                  <input type="text" name="" value=""> -->
                </form>
                <div class="col-4">
                    <h5 id="batch_diff"></h5>
                    <!-- guage chart -->
                    <div id="chart_div" class="d-flex justify-content-center" style="display:none;"></div>

                </div>
                <div class="col-4" style="display:none;">
                    <p>Associate Work Details</p>
                    <div class="associate_detail">

                    </div>
                </div>
            </div>
            <hr>
            <button type="button" id="role_button" class="btn btn-secondary">General Tasks & Machine Assign</button>
            <div class="container border border-secondary shadow-sm p-3 mb-5 bg-white rounded" id="role_card" style="display:none;">
                <div class="row">

                    <div class="col-6">
                        <h5>General Tasks</h5>
                        <label for="">Tasks</label>
                        <select class="form-control" id="gen_task">
                            <option value="Loading/Unloading">Loading/Unloading</option>
                            <option value="Garbage">Garbage</option>
                            <option value="Floor Cleaning">Floor Cleaning</option>
                        </select>

                        <label for="">Associates</label>
                        <select class="form-control" id="gen_task_assoc">
                            <?php
                            // SELECT * FROM `task_id` where model_name='pythagoras' and process_name='Sand belt finishing' and part_name='Corner round after pasting' and batchno='test64646-766'
                            $select_users = $conn->prepare("SELECT * FROM user_detail where role = 'Associates' and teamname is not null;");
                            $select_users->execute();
                            $select_users_res = $select_users->get_result();
                            while ($select_users_row = $select_users_res->fetch_assoc())
                            {
                                // $barcode[] = $select_users_row['barcode'];
                                // $username[] = $select_users_row['fullname'];
                            ?>
                                <option value="<?php echo $select_users_row['user_name']; ?>"><?php echo $select_users_row['fullname']; ?></option>
                            <?php
                            }
                            //selected users to be removed from stack
                            ?>
                        </select>
                        <button type="button" id="gen_task_submit">Assign Task</button>
                    </div>
                    <div class="col-6">
                        <h5>Machine Assign</h5>
                        <label for="">Associates</label>
                        <select class="form-control" id="machin_assoc">
                            <?php
                            // SELECT * FROM `task_id` where model_name='pythagoras' and process_name='Sand belt finishing' and part_name='Corner round after pasting' and batchno='test64646-766'
                            $select_users = $conn->prepare("SELECT * FROM user_detail where role = 'Associates' and teamname is not null;");
                            $select_users->execute();
                            $select_users_res = $select_users->get_result();
                            while ($select_users_row = $select_users_res->fetch_assoc())
                            {
                                // $barcode[] = $select_users_row['barcode'];
                                // $username[] = $select_users_row['fullname'];
                            ?>
                                <option value="<?php echo $select_users_row['user_name']; ?>"><?php echo $select_users_row['fullname']; ?></option>
                            <?php
                            }
                            //selected users to be removed from stack
                            ?>
                        </select>
                        <label for="">Workstation</label>
                        <select class="form-control" id="machine_workstation">
                            <?php
                            // SELECT * FROM `task_id` where model_name='pythagoras' and process_name='Sand belt finishing' and part_name='Corner round after pasting' and batchno='test64646-766'
                            $select_workstation = $conn->prepare("SELECT * FROM workstation where machine = 'yes';");
                            $select_workstation->execute();
                            $select_workstation_res = $select_workstation->get_result();
                            while ($select_workstation_row = $select_workstation_res->fetch_assoc())
                            {
                                // $barcode[] = $select_users_row['barcode'];
                                // $username[] = $select_users_row['fullname'];
                            ?>
                                <option value="<?php echo $select_workstation_row['name']; ?>"><?php echo $select_workstation_row['name'] . ' / ' . $select_workstation_row['username']; ?></option>
                            <?php
                            }
                            //selected users to be removed from stack
                            ?>
                        </select>
                        <button type="button" id="machine_assign">Assign Machine</button>
                    </div>

                </div>

            </div>



            <?php

            $display_general_or_multi_shift = $conn->prepare("SELECT DISTINCT shift_type FROM shift_user_plan where DATE(shiftdate) =? ;");
            $display_general_or_multi_shift->bind_param('s', $todaysDate);
            $display_general_or_multi_shift->execute();
            $display_general_or_multi_shift_res = $display_general_or_multi_shift->get_result();

            // echo $todaysDate;



            ?>



            <hr>
            <div class="container border border-secondary shadow-sm p-3 mb-5 bg-white rounded shift1_card" <?php if ($display_general_or_multi_shift_res->num_rows > 1)
                                                                                                            {
                                                                                                                echo 'style="display:none;"';
                                                                                                            }
                                                                                                            else if ($display_general_or_multi_shift_res->num_rows == 0)
                                                                                                            {
                                                                                                                echo 'style="display:none;"';
                                                                                                            } ?>>

                <h5 class="text-center">General Shift</h5>
                <!-- only shift 1 -->
                <!-- shift 1 -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Process Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Workstation Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Material Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-4" role="tab">Team Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-5" role="tab">Model Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-6" role="tab">User Planner</a>
                    </li>
                </ul><!-- Tab panes -->




                <!-- GENERAL SHIFT -->

                <div class="tab-content">
                    <div class="tab-pane active" id="tabs-1" role="tabpanel">
                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Process Batch Plan</center>
                                </header>
                                <hr>
                                <label for="">Model</label>
                                <select class="form-control" id="processes" name="processes">
                                    <option selected disabled>Select Model</option>
                                    <?php
                                    //assigned task to be removed from stack
                                    $select_process = $conn->prepare("SELECT distinct model_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_process->bind_param('ss', $batchno, $todaysDate);
                                    $select_process->execute();
                                    $select_process_res = $select_process->get_result();
                                    while ($select_process_row = $select_process_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_process_row['model_name']; ?>"><?php echo $select_process_row['model_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>

                                <!-- GENERAL SHIFT -->
                                <label for="">Process</label>
                                <select class="form-control" id="model_processes">
                                    <option selected disabled>Select Process</option>
                                </select>

                                <!-- GENERAL SHIFT -->
                                <label for="">Parts</label>
                                <select class="form-control" id="process_parts">
                                    <option selected disabled>Select Process</option>
                                </select>
                                <div id="unit_time_details">

                                </div>
                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="process_start_time">
                                <label for="">Number of Associates</label>
                                <input type="number" class="form-control" id="process_users">

                                <!-- GENERAL SHIFT -->
                                <label for="">Associates</label>
                                <select class="form-control" id="username" name="username[]" multiple>

                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="process_assign">Assign</button>
                            </div>

                        </div>
                        <div id="process_gen_card" class="shift_assign_unassign">

                        </div>

                        <!-- GENERAL -->
                    </div>
                    <div class="tab-pane" id="tabs-2" role="tabpanel">
                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Select Workstation</center>
                                </header>
                                <hr>
                                <label for="">Workstation</label>
                                <select class="form-control" id="workstation">
                                    <?php
                                    // SELECT * FROM `task_id` where model_name='pythagoras' and process_name='Sand belt finishing' and part_name='Corner round after pasting' and batchno='test64646-766'
                                    $select_workstation = $conn->prepare("SELECT * FROM workstation where machine = 'yes';");
                                    $select_workstation->execute();
                                    $select_workstation_res = $select_workstation->get_result();
                                    while ($select_workstation_row = $select_workstation_res->fetch_assoc())
                                    {
                                        // $barcode[] = $select_users_row['barcode'];
                                        // $username[] = $select_users_row['fullname'];
                                    ?>
                                        <option value="<?php echo $select_workstation_row['name']; ?>"><?php echo $select_workstation_row['name'] . ' / ' . $select_workstation_row['username']; ?></option>
                                    <?php
                                    }
                                    //selected users to be removed from stack
                                    ?>
                                </select>

                                <div id="work_unit_time">

                                </div>


                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Assign Process</center>
                                </header>
                                <hr>
                                <label for="">Process</label>
                                <select class="form-control" id="workprocesses">
                                    <option selected disabled>Select Process</option>
                                    <?php
                                    //assigned task to be removed from stack
                                    $select_workprocess = $conn->prepare("SELECT distinct model_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_workprocess->bind_param('ss', $batchno, $todaysDate);
                                    $select_workprocess->execute();
                                    $select_workprocess_res = $select_workprocess->get_result();
                                    while ($select_workprocess_row = $select_workprocess_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_workprocess_row['model_name']; ?>"><?php echo $select_workprocess_row['model_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <!-- GENERAL SHIFT -->
                                <label for="">Part Name</label>
                                <select class="form-control" id="work_parts">
                                </select>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="workstation_start_time">
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="workstation_assign">Assign</button>
                            </div>
                        </div>

                        <div id="workstation_gen_card" class="shift_assign_unassign">

                        </div>

                    </div>
                    <div class="tab-pane" id="tabs-3" role="tabpanel">

                        <!-- material wise planner -->
                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Material Batch Plan</center>
                                </header>
                                <hr>
                                <!-- GENERAL SHIFT -->
                                <label for="">Material: </label>
                                <select class="form-control" id="material">
                                    <option selected disabled>Select Material</option>
                                    <?php
                                    //assigned task to be removed from stack

                                    //material->part name == assign to user
                                    $select_process = $conn->prepare("SELECT distinct material_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_process->bind_param('ss', $batchno, $todaysDate);
                                    $select_process->execute();
                                    $select_process_res = $select_process->get_result();
                                    while ($select_process_row = $select_process_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                        if (!empty($select_process_row['material_name']))
                                        {
                                    ?>
                                            <option value="<?php echo $select_process_row['material_name']; ?>"><?php echo $select_process_row['material_name']; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>

                                <label for="">Models: </label>
                                <select class="form-control" id="material_model">

                                </select>

                                <label for="">Process: </label>
                                <select class="form-control" id="material_process">

                                </select>

                                <div id="material_unit_time">

                                </div>


                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="material_start_time">
                                <label for="">Number of Associates</label>
                                <input type="number" class="form-control" id="material_user_count">
                                <label for="">Associates</label>
                                <select class="form-control" multiple id="material_username">

                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="material_assign">Assign</button>
                            </div>
                        </div>

                        <div id="material_gen_card" class="shift_assign_unassign">

                        </div>

                    </div>
                    <div class="tab-pane" id="tabs-4" role="tabpanel">
                        <!-- team wise planning -->

                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Team Batch Plan</center>
                                </header>
                                <hr>
                                <label for="">Team: </label>
                                <select class="form-control" id="teams">
                                    <option selected disabled>Select Team</option>
                                    <?php
                                    //assigned task to be removed from stack

                                    //material->part name == assign to user
                                    $select_team = $conn->prepare("SELECT distinct teamname FROM user_detail where teamname is not null");
                                    $select_team->bind_param('s', $batchno);
                                    $select_team->execute();
                                    $select_team_res = $select_team->get_result();
                                    while ($select_team_row = $select_team_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_team_row['teamname']; ?>"><?php echo $select_team_row['teamname']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <!-- GENERAL SHIFT -->
                                <label for="">Model: </label>
                                <select class="form-control" id="team_model">
                                </select>

                                <label for="">Process:</label>
                                <select class="form-control" id="team_process">
                                </select>

                                <div id="team_unit_time">

                                </div>


                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" id="team_start_time">
                                <label for="">No of Associates</label>
                                <input type="number" id="team_count">
                                <label for="">Associates</label>
                                <select class="form-control" multiple id="team_username">
                                </select>

                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="team_assign">Assign</button>
                            </div>
                        </div>

                        <div id="team_gen_card" class="shift_assign_unassign">

                        </div>

                    </div>

                    <!-- model planning -->
                    <div class="tab-pane" id="tabs-5" role="tabpanel">

                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Model Batch Plan</center>
                                </header>
                                <hr>
                                <!-- GENERAL SHIFT -->
                                <label for="">Model: </label>
                                <select class="form-control" id="models">
                                    <option selected disabled>Select Model</option>
                                    <?php
                                    //assigned task to be removed from stack

                                    //material->part name == assign to user
                                    $select_process = $conn->prepare("SELECT distinct model_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_process->bind_param('ss', $batchno, $todaysDate);
                                    $select_process->execute();
                                    $select_process_res = $select_process->get_result();
                                    while ($select_process_row = $select_process_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_process_row['model_name']; ?>"><?php echo $select_process_row['model_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>

                                <label for="">Process: </label>
                                <select class="form-control" id="model_process">

                                </select>

                                <div id="model_unit_time">

                                </div>
                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start time</label>
                                <input type="time" id="model_start_time">
                                <label for="">No of Associates</label>
                                <input type="number" id="model_user_count">
                                <label for="">Associates</label>
                                <select class="form-control" multiple id="model_username">
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="model_assign">Assign</button>
                            </div>
                        </div>


                        <div id="model_gen_card" class="shift_assign_unassign">

                        </div>
                    </div>







                    <div class="tab-pane" id="tabs-6" role="tabpanel">
                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Associate Wise Tab</center>
                                </header>
                                <hr>
                                <label for="">Associates Having Less Than 8 Hours Work</label>
                                <select class="form-control" id="user" name="user">

                                </select>

                                <!-- GENERAL SHIFT -->
                                <label for="">Select User Models</label>
                                <select class="form-control" id="user_models">
                                    <option selected disabled>Select Models</option>
                                </select>

                                <!-- GENERAL SHIFT -->
                                <label for="">Select User Process</label>
                                <select class="form-control" id="user_process">
                                    <option selected disabled>Select Process</option>
                                </select>

                                <div id="unit_time_details">

                                </div>


                                <!-- GENERAL SHIFT -->
                                <label for="">Select User Parts</label>
                                <select class="form-control" id="model_processes">
                                    <option selected disabled>Select Parts</option>
                                </select>



                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Displaying Time</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="process_start_time">

                                <label for="">Unit Time</label>
                                <div id="user_unit_time"></div>



                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="process_assign">Assign</button>
                            </div>

                        </div>
                        <div id="process_gen_card" class="shift_assign_unassign">

                        </div>

                        <!-- GENERAL -->
                    </div>










                </div>
            </div>
            <hr>
            <div class="container border border-secondary shadow-sm p-3 mb-5 bg-white rounded shift_cards" <?php if ($display_general_or_multi_shift_res->num_rows == 1)
                                                                                                            {
                                                                                                                echo 'style="display:none;"';
                                                                                                            }
                                                                                                            else if ($display_general_or_multi_shift_res->num_rows == 0)
                                                                                                            {
                                                                                                                echo 'style="display:none;"';
                                                                                                            }  ?>>

                <h5 class="text-center">First Shift</h5>


                <!-- shift 1 -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tabs-12" role="tab">Process Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-22" role="tab">Workstation Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-32" role="tab">Material Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-42" role="tab">Team Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-52" role="tab">Model Planner</a>
                    </li>
                </ul><!-- Tab panes -->






                <div class="tab-content">
                    <div class="tab-pane active" id="tabs-12" role="tabpanel">

                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Process Batch Plan</center>
                                </header>
                                <hr>
                                <label for="">Model</label>
                                <select class="form-control" id="processes1" name="processes">
                                    <option selected disabled>Select Model</option>
                                    <?php
                                    //assigned task to be removed from stack
                                    $select_process = $conn->prepare("SELECT distinct model_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_process->bind_param('ss', $batchno, $todaysDate);
                                    $select_process->execute();
                                    $select_process_res = $select_process->get_result();
                                    while ($select_process_row = $select_process_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_process_row['model_name']; ?>"><?php echo $select_process_row['model_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>

                                <label for="">Process</label>
                                <select class="form-control" id="model_processes1">
                                    <option selected disabled>Select Process</option>
                                </select>
                                <!-- SHIFT 1 -->
                                <label for="">Parts</label>
                                <select class="form-control" id="process_parts1">
                                    <option selected disabled>Select Process</option>
                                </select>
                                <div id="unit_time_details1">

                                </div>
                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="process_start_time1">
                                <label for="">Number of Associates</label>
                                <input type="number" class="form-control" id="process_users1">
                                <!-- SHIFT 1 -->
                                <label for="">Associates</label>
                                <select class="form-control" id="username1" name="username[]" multiple>

                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="process_assign1">Assign</button>
                            </div>

                        </div>

                        <div id="process_shift1_card" class="shift1_assign_unassign">

                        </div>

                    </div>
                    <div class="tab-pane" id="tabs-22" role="tabpanel">
                        <!-- SHIFT 1 -->
                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Select Workstation</center>
                                </header>
                                <hr>
                                <label for="">Workstation</label>
                                <select class="form-control" id="workstation1">
                                    <?php
                                    // SELECT * FROM `task_id` where model_name='pythagoras' and process_name='Sand belt finishing' and part_name='Corner round after pasting' and batchno='test64646-766'
                                    $select_workstation = $conn->prepare("SELECT * FROM workstation where machine = 'yes';");
                                    $select_workstation->execute();
                                    $select_workstation_res = $select_workstation->get_result();
                                    while ($select_workstation_row = $select_workstation_res->fetch_assoc())
                                    {
                                        // $barcode[] = $select_users_row['barcode'];
                                        // $username[] = $select_users_row['fullname'];
                                    ?>
                                        <option value="<?php echo $select_workstation_row['name']; ?>"><?php echo $select_workstation_row['name'] . ' / ' . $select_workstation_row['username']; ?></option>
                                    <?php
                                    }
                                    //selected users to be removed from stack
                                    ?>
                                </select>

                                <div id="work_unit_time1">

                                </div>


                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Assign Process</center>
                                </header>
                                <hr>
                                <label for="">Process</label>
                                <select class="form-control" id="workprocesses1">
                                    <option selected disabled>Select Process</option>
                                    <?php
                                    //assigned task to be removed from stack
                                    $select_workprocess = $conn->prepare("SELECT distinct model_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_workprocess->bind_param('ss', $batchno, $todaysDate);
                                    $select_workprocess->execute();
                                    $select_workprocess_res = $select_workprocess->get_result();
                                    while ($select_workprocess_row = $select_workprocess_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_workprocess_row['model_name']; ?>"><?php echo $select_workprocess_row['model_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <!-- SHIFT 1 -->
                                <label for="">Part Name</label>
                                <select class="form-control" id="work_parts1">
                                </select>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="workstation_start_time1">
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="workstation_assign1">Assign</button>
                            </div>
                        </div>

                        <div id="workstation_shift1_card" class="shift1_assign_unassign">

                        </div>

                    </div>
                    <div class="tab-pane" id="tabs-32" role="tabpanel">

                        <!-- material wise planner -->
                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Material Batch Plan</center>
                                </header>
                                <hr>
                                <!-- SHIFT 1 -->
                                <label for="">Material: </label>
                                <select class="form-control" id="material1">
                                    <option selected disabled>Select Material</option>
                                    <?php
                                    //assigned task to be removed from stack

                                    //material->part name == assign to user
                                    $select_process = $conn->prepare("SELECT distinct material_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_process->bind_param('ss', $batchno, $todaysDate);
                                    $select_process->execute();
                                    $select_process_res = $select_process->get_result();
                                    while ($select_process_row = $select_process_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                        if (!empty($select_process_row['material_name']))
                                        {
                                    ?>
                                            <option value="<?php echo $select_process_row['material_name']; ?>"><?php echo $select_process_row['material_name']; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>

                                <label for="">Models: </label>
                                <select class="form-control" id="material_model1">

                                </select>

                                <label for="">Process: </label>
                                <select class="form-control" id="material_process1">

                                </select>

                                <div id="material_unit_time1">

                                </div>


                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="material_start_time1">
                                <label for="">Number of Associates</label>
                                <input type="number" class="form-control" id="material_user_count1">
                                <label for="">Associates</label>
                                <select class="form-control" multiple id="material_username1">

                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="material_assign1">Assign</button>
                            </div>
                        </div>

                        <div id="material_shift1_card" class="shift1_assign_unassign">

                        </div>

                    </div>
                    <div class="tab-pane" id="tabs-42" role="tabpanel">
                        <!-- team wise planning -->

                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Team Batch Plan</center>
                                </header>
                                <hr>
                                <label for="">Team: </label>
                                <select class="form-control" id="teams1">
                                    <option selected disabled>Select Team</option>
                                    <?php
                                    //assigned task to be removed from stack

                                    //material->part name == assign to user
                                    $select_team = $conn->prepare("SELECT distinct teamname FROM user_detail where teamname is not null");
                                    $select_team->bind_param('s', $batchno);
                                    $select_team->execute();
                                    $select_team_res = $select_team->get_result();
                                    while ($select_team_row = $select_team_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_team_row['teamname']; ?>"><?php echo $select_team_row['teamname']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <!-- SHIFT 1 -->
                                <label for="">Model: </label>
                                <select class="form-control" id="team_model1">
                                </select>

                                <label for="">Process:</label>
                                <select class="form-control" id="team_process1">
                                </select>

                                <div id="team_unit_time1">

                                </div>


                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" id="team_start_time1">
                                <label for="">No of Associates</label>
                                <input type="number" id="team_count1">
                                <label for="">Associates</label>
                                <select class="form-control" multiple id="team_username1">
                                </select>

                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="team_assign1">Assign</button>
                            </div>
                        </div>

                        <div id="team_shift1_card" class="shift1_assign_unassign">

                        </div>

                    </div>

                    <!-- model planning -->
                    <div class="tab-pane" id="tabs-52" role="tabpanel">

                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Model Batch Plan</center>
                                </header>
                                <hr>
                                <!-- SHIFT 1 -->
                                <label for="">Model: </label>
                                <select class="form-control" id="models1">
                                    <option selected disabled>Select Model</option>
                                    <?php
                                    //assigned task to be removed from stack

                                    //material->part name == assign to user
                                    $select_process = $conn->prepare("SELECT distinct model_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_process->bind_param('ss', $batchno, $todaysDate);
                                    $select_process->execute();
                                    $select_process_res = $select_process->get_result();
                                    while ($select_process_row = $select_process_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_process_row['model_name']; ?>"><?php echo $select_process_row['model_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>

                                <label for="">Process: </label>
                                <select class="form-control" id="model_process1">

                                </select>

                                <div id="model_unit_time1">

                                </div>
                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start time</label>
                                <input type="time" id="model_start_time1">
                                <label for="">No of Associates</label>
                                <input type="number" id="model_user_count1">
                                <label for="">Associates</label>
                                <select class="form-control" multiple id="model_username1">
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="model_assign1">Assign</button>
                            </div>
                        </div>


                        <div id="model_shift1_card" class="shift1_assign_unassign">

                        </div>

                    </div>
                </div>
            </div>
            <hr>
            <div class="container border border-secondary shadow-sm p-3 mb-5 bg-white rounded shift_cards" <?php if ($display_general_or_multi_shift_res->num_rows == 1)
                                                                                                            {
                                                                                                                echo 'style="display:none;"';
                                                                                                            }
                                                                                                            else if ($display_general_or_multi_shift_res->num_rows == 0)
                                                                                                            {
                                                                                                                echo 'style="display:none;"';
                                                                                                            }
                                                                                                            ?>>

                <h5 class="text-center">Second Shift</h5>

                <!-- shift 2 -->

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tabs-13" role="tab">Process Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-23" role="tab">Workstation Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-33" role="tab">Material Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-43" role="tab">Team Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tabs-53" role="tab">Model Planner</a>
                    </li>
                </ul><!-- Tab panes -->






                <div class="tab-content">
                    <div class="tab-pane active" id="tabs-13" role="tabpanel">
                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Process Batch Plan</center>
                                </header>
                                <hr>
                                <label for="">Model</label>
                                <select class="form-control" id="processes2" name="processes">
                                    <option selected disabled>Select Model</option>
                                    <?php
                                    //assigned task to be removed from stack
                                    $select_process = $conn->prepare("SELECT distinct model_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_process->bind_param('ss', $batchno, $todaysDate);
                                    $select_process->execute();
                                    $select_process_res = $select_process->get_result();
                                    while ($select_process_row = $select_process_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_process_row['model_name']; ?>"><?php echo $select_process_row['model_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>

                                <label for="">Process</label>
                                <select class="form-control" id="model_processes2">
                                    <option selected disabled>Select Process</option>
                                </select>

                                <!-- SHIFT 1 -->

                                <label for="">Parts</label>
                                <select class="form-control" id="process_parts2">
                                    <option selected disabled>Select Process</option>
                                </select>
                                <div id="unit_time_details2">

                                </div>
                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="process_start_time2">
                                <label for="">Number of Associates</label>
                                <input type="number" class="form-control" id="process_users2">

                                <!-- SHIFT 1 -->

                                <label for="">Associates</label>
                                <select class="form-control" id="username2" name="username[]" multiple>

                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="process_assign2">Assign</button>
                            </div>

                        </div>

                        <div id="process_shift2_card" class="shift2_assign_unassign">

                        </div>

                    </div>
                    <!-- SHIFT 2 -->
                    <div class="tab-pane" id="tabs-23" role="tabpanel">

                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Select Workstation</center>
                                </header>
                                <hr>
                                <label for="">Workstation</label>
                                <select class="form-control" id="workstation2">
                                    <?php
                                    // SELECT * FROM `task_id` where model_name='pythagoras' and process_name='Sand belt finishing' and part_name='Corner round after pasting' and batchno='test64646-766'
                                    $select_workstation = $conn->prepare("SELECT * FROM workstation where machine = 'yes';");
                                    $select_workstation->execute();
                                    $select_workstation_res = $select_workstation->get_result();
                                    while ($select_workstation_row = $select_workstation_res->fetch_assoc())
                                    {
                                        // $barcode[] = $select_users_row['barcode'];
                                        // $username[] = $select_users_row['fullname'];
                                    ?>
                                        <option value="<?php echo $select_workstation_row['name']; ?>"><?php echo $select_workstation_row['name'] . ' / ' . $select_workstation_row['username']; ?></option>
                                    <?php
                                    }
                                    //selected users to be removed from stack
                                    ?>
                                </select>

                                <div id="work_unit_time2">

                                </div>


                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Assign Process</center>
                                </header>
                                <hr>
                                <label for="">Process</label>
                                <select class="form-control" id="workprocesses2">
                                    <option selected disabled>Select Process</option>
                                    <?php
                                    //assigned task to be removed from stack
                                    $select_workprocess = $conn->prepare("SELECT distinct model_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_workprocess->bind_param('ss', $batchno, $todaysDate);
                                    $select_workprocess->execute();
                                    $select_workprocess_res = $select_workprocess->get_result();
                                    while ($select_workprocess_row = $select_workprocess_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_workprocess_row['model_name']; ?>"><?php echo $select_workprocess_row['model_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <!-- SHIFT 2 -->
                                <label for="">Part Name</label>
                                <select class="form-control" id="work_parts2">
                                </select>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="workstation_start_time2">
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="workstation_assign2">Assign</button>
                            </div>
                        </div>

                        <div id="workstation_shift2_card" class="shift2_assign_unassign">

                        </div>

                    </div>
                    <div class="tab-pane" id="tabs-33" role="tabpanel">

                        <!-- material wise planner -->
                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Material Batch Plan</center>
                                </header>
                                <hr>
                                <!-- SHIFT 2 -->
                                <label for="">Material: </label>
                                <select class="form-control" id="material2">
                                    <option selected disabled>Select Material</option>
                                    <?php
                                    //assigned task to be removed from stack

                                    //material->part name == assign to user
                                    $select_process = $conn->prepare("SELECT distinct material_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_process->bind_param('ss', $batchno, $todaysDate);
                                    $select_process->execute();
                                    $select_process_res = $select_process->get_result();
                                    while ($select_process_row = $select_process_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                        if (!empty($select_process_row['material_name']))
                                        {
                                    ?>
                                            <option value="<?php echo $select_process_row['material_name']; ?>"><?php echo $select_process_row['material_name']; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>

                                <label for="">Models: </label>
                                <select class="form-control" id="material_model2">

                                </select>

                                <label for="">Process: </label>
                                <select class="form-control" id="material_process2">

                                </select>

                                <div id="material_unit_time2">

                                </div>


                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" class="form-control" id="material_start_time2">
                                <label for="">Number of Associates</label>
                                <input type="number" class="form-control" id="material_user_count2">
                                <label for="">Associates</label>
                                <select class="form-control" multiple id="material_username2">

                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="material_assign2">Assign</button>
                            </div>
                        </div>

                        <div id="material_shift2_card" class="shift2_assign_unassign">

                        </div>

                    </div>
                    <div class="tab-pane" id="tabs-43" role="tabpanel">
                        <!-- team wise planning -->

                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Team Batch Plan</center>
                                </header>
                                <hr>
                                <label for="">Team: </label>
                                <select class="form-control" id="teams2">
                                    <option selected disabled>Select Team</option>
                                    <?php
                                    //assigned task to be removed from stack

                                    //material->part name == assign to user
                                    $select_team = $conn->prepare("SELECT distinct teamname FROM user_detail where teamname is not null");
                                    $select_team->bind_param('s', $batchno);
                                    $select_team->execute();
                                    $select_team_res = $select_team->get_result();
                                    while ($select_team_row = $select_team_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_team_row['teamname']; ?>"><?php echo $select_team_row['teamname']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <!-- SHIFT 2 -->
                                <label for="">Model: </label>
                                <select class="form-control" id="team_model2">
                                </select>

                                <label for="">Process:</label>
                                <select class="form-control" id="team_process2">
                                </select>

                                <div id="team_unit_time2">

                                </div>


                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start Time</label>
                                <input type="time" id="team_start_time2">
                                <label for="">No of Associates</label>
                                <input type="number" id="team_count2">
                                <label for="">Associates</label>
                                <select class="form-control" multiple id="team_username2">
                                </select>

                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="team_assign2">Assign</button>
                            </div>
                        </div>

                        <div id="team_shift2_card" class="shift2_assign_unassign">

                        </div>

                    </div>

                    <!-- model planning -->
                    <div class="tab-pane" id="tabs-53" role="tabpanel">

                        <div class="row">
                            <div class="col-sm col-md-6 col-lg-6 card card-primary card-outline">
                                <header>
                                    <center>Model Batch Plan</center>
                                </header>
                                <hr>
                                <!-- SHIFT 2 -->
                                <label for="">Model: </label>
                                <select class="form-control" id="models2">
                                    <option selected disabled>Select Model</option>
                                    <?php
                                    //assigned task to be removed from stack

                                    //material->part name == assign to user
                                    $select_process = $conn->prepare("SELECT distinct model_name from task_id where batchno=? and date(plandtfm)=?;");
                                    $select_process->bind_param('ss', $batchno, $todaysDate);
                                    $select_process->execute();
                                    $select_process_res = $select_process->get_result();
                                    while ($select_process_row = $select_process_res->fetch_assoc())
                                    {
                                        // $process[] = $select_process_row['process_name'];
                                    ?>
                                        <option value="<?php echo $select_process_row['model_name']; ?>"><?php echo $select_process_row['model_name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>

                                <label for="">Process: </label>
                                <select class="form-control" id="model_process2">

                                </select>

                                <div id="model_unit_time2">

                                </div>
                            </div>
                            <div class="col-sm col-md-6 col-lg-6 card card-danger card-outline">
                                <header>
                                    <center>Select Associates</center>
                                </header>
                                <hr>
                                <label for="">Start time</label>
                                <input type="time" id="model_start_time2">
                                <label for="">No of Associates</label>
                                <input type="number" id="model_user_count2">
                                <label for="">Associates</label>
                                <select class="form-control" multiple id="model_username2">
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="model_assign2">Assign</button>
                            </div>
                        </div>


                        <div id="model_shift2_card" class="shift2_assign_unassign">

                        </div>

                    </div>
                </div>

                <br><br>


            </div>

        </div>

        <br>


        <div id="modellogs"></div>



        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script>
            var startDateTime = new Date(2024, 0, 29, 16, 46, 0, 0); // YYYY (M-1) D H m s ms (start time and date from DB)
            var startStamp = startDateTime.getTime();

            var newDate = new Date();
            var newStamp = newDate.getTime();

            var timer; // for storing the interval (to stop or pause later if needed)

            function updateClock() {
                newDate = new Date();
                newStamp = newDate.getTime();
                var diff = Math.round((newStamp - startStamp) / 1000);

                var d = Math.floor(diff / (24 * 60 * 60)); /* though I hope she won't be working for consecutive days :) */
                diff = diff - (d * 24 * 60 * 60);
                var h = Math.floor(diff / (60 * 60));
                diff = diff - (h * 60 * 60);
                var m = Math.floor(diff / (60));
                diff = diff - (m * 60);
                var s = diff;

                document.getElementById("time-elapsed").innerHTML = d + " days   " + h + " hours: " + m + " minutes: " + s + " seconds spent in planning";
            }

            timer = setInterval(updateClock, 1000);
        </script>

        <script type='text/javascript'>
            $(document).ready(function() {
                // Event handler for selecting options in selectBox1
                $('#shift1username').on('change', function() {
                    var selectedValues = $(this).val();
                    // Remove selected options from selectBox2
                    $('#shift2username option').filter(function() {
                        return $.inArray(this.value, selectedValues) !== -1;
                    }).remove();
                });
            });

            $('#role_button').on('click', function() {
                $('#role_card').slideToggle();
            });


            $('#fixdate').on('change', function b() {
                document.getElementById("fixdate").readOnly = true;;

                var fixdate = $('#fixdate').val();
                var data = "fixdate=" + fixdate + "&type=batch_diff";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        // console.log(res);
                        $('#batch_diff').text(res);
                    }
                });
            });

            // ----------shift card display start----------
            $('#gen_shift_submit').on('click', function() {
                $('.shift1_card').show();
            });

            $('#multi_shift_submit').on('click', function() {
                $('#shift_users').show();
            });

            $('#multi_submit').on('click', function() {
                $('.shift_cards').show();
            });
            // ----------shift card display end----------

            // --------shift user selection count start---------
            var user_count = <?php echo $get_user_count_row['user_count']; ?>;
            console.log(user_count);

            $('#shift1username').on('change', function() {
                var shift1user = $('#shift1username option:selected').length + " associates selected";
                // console.log(shift1user);
                $('#shift1display').html(shift1user);
            });
            $('#shift2username').on('change', function() {
                var shift2user = $('#shift2username option:selected').length + " associates selected";
                // console.log(shift2user);
                $('#shift2display').html(shift2user);
            });
            // --------shift user selection count end---------

            // -----------executive role change start-----------
            $('#role_submit').on('click', function() {
                var exec_role = $('#exec_role').val();
                var temp_dept = $('#temp_dept').val();
                var fixdate = $('#fixdate').val();

                var data = "exec_role=" + exec_role + "&temp_dept=" + temp_dept + "&fixdate=" + fixdate + "&type=exec_role";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                    }
                });

                // console.log(exec_role);
                // console.log(temp_dept);
            });
            // -----------executive role change end-----------

            // --------------Team change start-------------
            $('#change_team_submit').on('click', function() {
                var team_assoc = $('#team_assoc').val();
                var new_team_name = $('#new_team_name').val();
                var fixdate = $('#fixdate').val();

                var data = "team_assoc=" + team_assoc + "&new_team_name=" + new_team_name + "&fixdate=" + fixdate + "&type=team_change";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                    }
                });

                console.log(team_assoc);
                console.log(new_team_name);
            });
            // --------------Team change end-------------

            // -----------General Task assign start-----------
            $('#gen_task_submit').on('click', function() {
                var gen_task = $('#gen_task').val();
                var gen_task_assoc = $('#gen_task_assoc').val();
                var fixdate = $('#fixdate').val();

                var data = "gen_task=" + gen_task + "&gen_task_assoc=" + gen_task_assoc + "&fixdate=" + fixdate + "&type=gen_task";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                    }
                });

                console.log(gen_task);
                console.log(gen_task_assoc);
            });
            // -----------General Task assign end-----------

            // ---------machine assign start--------


            $('#machine_assign').on('click', function() {
                var machin_assoc = $('#machin_assoc').val();
                var machine_workstation = $('#machine_workstation').val();

                var data = "machin_assoc=" + machin_assoc + "&machine_workstation=" + machine_workstation + "&type=machine_assign";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                    }
                });

                console.log(machin_assoc);
                console.log(machine_workstation);
            });
            // ---------machine assign end--------

            // -------shift timings toggle start--------
            // $('#shift_type').on('change', function() {
            //     var shift_type = this.value;
            //     // console.log(shift_type);
            //     if (shift_type == 2) {
            //         $('#shift_time_form').show();
            //         $('#gen_shift_time').hide();
            //     } else {
            //         $('#shift_time_form').hide();
            //         $('#gen_shift_time').show();

            //     }
            // });
            // ---------shift timings toggle end----------

            // ---------gen shift create---------
            $('#gen_shift_submit').on('click', function() {
                var fixdate = $('#fixdate').val();
                var gen_shift_start_time = $('#gen_shift_start_time').val();
                var gen_shift_end_time = $('#gen_shift_end_time').val();

                var data = "gen_shift_start_time=" + gen_shift_start_time + "&gen_shift_end_time=" + gen_shift_end_time + "&fixdate=" + fixdate + "&type=gen_shift_create";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                    }
                });

                // console.log(fixdate);
                // console.log(gen_shift_start_time);
                // console.log(gen_shift_end_time);
            });
            // ---------gen shift create---------


            // ----------multi shift create start----------


            $('#multi_submit').on('click', function() {
                var fixdate = $('#fixdate').val();
                var shift1starttime = $('#shift1starttime').val();
                var shift1endtime = $('#shift1endtime').val();
                var shift1_user_arr = $('#shift1username option:selected').toArray().map(item => item.value);
                var shift1_user_arr = JSON.stringify(shift1_user_arr);

                var shift2starttime = $('#shift2starttime').val();
                var shift2endtime = $('#shift2endtime').val();
                var shift2_user_arr = $('#shift2username option:selected').toArray().map(item => item.value);
                var shift2_user_arr = JSON.stringify(shift2_user_arr);



                var data = "shift1starttime=" + shift1starttime + "&shift1endtime=" + shift1endtime + "&shift1_user_arr=" + shift1_user_arr + "&shift2starttime=" + shift2starttime + "&shift2endtime=" + shift2endtime + "&shift2_user_arr=" + shift2_user_arr + "&fixdate=" + fixdate + "&type=multi_shift_create";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                    }
                });

                // console.log(shift1starttime);
                // console.log(shift1endtime);
                // console.log(shift1_user_arr);
                // console.log(shift2starttime);
                // console.log(shift2endtime);
                // console.log(shift2_user_arr);
            });
            // ----------multi shift create end----------

            // GENERAL SHIFT

            // --------- processwise - general shift---------
            $('#processes').on('change', function() {
                var model_name = $('#processes').val();
                var fixdate = $('#fixdate').val();
                var data = "model_name=" + model_name + "&fixdate=" + fixdate + "&type=model_fetch";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        // var obj = jQuery.parseJSON(res);
                        // var process_arr = obj.process_name.join('');
                        // var parts_arr = obj.part_name.join('');
                        // var username = obj.username.join('');
                        $('#model_processes').html(res);
                        // $('#process_parts').html(parts_arr);
                        // $('#username').html(username);
                        // console.log(process_arr);
                        // console.log(parts_arr);

                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=process";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#process_gen_card').html(res1);
                            }
                        });
                    }
                });
                //   console.log(proc_arr.process_name);
                // console.log(proc_arr.part_name);

                $('#process_parts').empty();
                $('#unit_time_details').empty();
                $('#process_users').val('');
                $('#username').empty();
                $('#process_start_time').val('00:00');
            });

            // GENERAL SHIFT

            $('#model_processes').on('change', function() {
                var fixdate = $('#fixdate').val();
                var model_name = $('#processes').val();
                var processes = $('#model_processes').val();

                var data = "model_name=" + model_name + "&processes=" + processes + "&fixdate=" + fixdate + "&type=get_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#process_parts').html(res);
                    }
                });
            });

            // GENERAL SHIFT

            $('#process_parts').on('change', function() {
                var parts = $('#process_parts').val();

                var data = "parts=" + parts + "&type=unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#unit_time_details').html(res);
                    }
                });
            });

            // GENERAL SHIFT

            $('#process_users').on('change', function() {
                var model_name = $('#processes').val();
                var process_start_time = $('#process_start_time').val();
                var data = "model_name=" + model_name + "&process_start_time=" + process_start_time + "&type=process_users";
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#username').html(res);
                    }
                });
            });
            // --------- processwise - general shift---------

            // ---------Process wise start---------
            $('#process_assign').on('click', function() {
                var process_user_arr = $('#username option:selected').toArray().map(item => item.value);
                var proc_user_arr = JSON.stringify(process_user_arr);
                // console.log(json_select_arr);
                // console.log($('#username option:selected').length);
                var processes = $('#process_parts').val();
                var process_start_time = $('#process_start_time').val();



                var fixdate = $('#fixdate').val();
                var data = "process_start_time=" + process_start_time + "&processes=" + processes + "&username=" + proc_user_arr + "&fixdate=" + fixdate + "&type=process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#processes').val('').trigger('change');
                        $('#username').val('').trigger('change');
                    }
                });
            });
            // ---------Process wise end---------

            // --------- processwise selection data - shift 1 - start---------

            $('#processes1').on('change', function() {
                var model_name = $('#processes1').val();
                var fixdate = $('#fixdate').val();
                var data = "model_name=" + model_name + "&fixdate=" + fixdate + "&type=model_fetch";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        // var obj = jQuery.parseJSON(res);
                        // var process_arr = obj.process_name.join('');
                        // var parts_arr = obj.part_name.join('');
                        // var username = obj.username.join('');
                        $('#model_processes1').html(res);
                        // $('#process_parts').html(parts_arr);
                        // $('#username').html(username);
                        // console.log(process_arr);
                        // console.log(parts_arr);

                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=process&shift=shift1";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#process_shift1_card').html(res1);
                            }
                        });
                    }
                });
                //   console.log(proc_arr.process_name);
                // console.log(proc_arr.part_name);

                $('#process_parts1').empty();
                $('#unit_time_details1').empty();
                $('#process_users1').val('');
                $('#username1').empty();
                $('#process_start_time1').val('00:00');
            });

            // SHIFT 1

            $('#model_processes1').on('change', function() {
                var fixdate = $('#fixdate').val();
                var model_name = $('#processes1').val();
                var processes = $('#model_processes1').val();

                var data = "model_name=" + model_name + "&processes=" + processes + "&fixdate=" + fixdate + "&type=get_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#process_parts1').html(res);
                    }
                });
            });

            // SHIFT 1

            $('#process_parts1').on('change', function() {
                var parts = $('#process_parts1').val();

                var data = "parts=" + parts + "&type=unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#unit_time_details1').html(res);
                    }
                });
            });

            $('#process_users1').on('change', function() {
                var model_name = $('#processes1').val();
                var process_start_time = $('#process_start_time1').val();
                var data = "model_name=" + model_name + "&process_start_time=" + process_start_time + "&type=process_users";
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#username1').html(res);
                    }
                });
            });
            // --------- processwise selection data - shift 1 - end---------

            // ---------Process wise shift1 start---------
            $('#process_assign1').on('click', function() {
                var process_user_arr = $('#username1 option:selected').toArray().map(item => item.value);
                var proc_user_arr = JSON.stringify(process_user_arr);
                // console.log(json_select_arr);
                // console.log($('#username option:selected').length);
                var processes = $('#process_parts1').val();
                var process_start_time = $('#process_start_time1').val();



                var fixdate = $('#fixdate').val();
                var data = "process_start_time=" + process_start_time + "&processes=" + processes + "&username=" + proc_user_arr + "&fixdate=" + fixdate + "&type=process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#processes1').val('').trigger('change');
                        $('#username1').val('').trigger('change');
                    }
                });
            });
            // ---------Process wise shift1 end---------

            // --------- processwise selection data - shift 2 - start---------

            $('#processes2').on('change', function() {
                var model_name = $('#processes2').val();
                var fixdate = $('#fixdate').val();
                var data = "model_name=" + model_name + "&fixdate=" + fixdate + "&type=model_fetch";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        // var obj = jQuery.parseJSON(res);
                        // var process_arr = obj.process_name.join('');
                        // var parts_arr = obj.part_name.join('');
                        // var username = obj.username.join('');
                        $('#model_processes2').html(res);
                        // $('#process_parts').html(parts_arr);
                        // $('#username').html(username);
                        // console.log(process_arr);
                        // console.log(parts_arr);

                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=process&shift=shift2";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#process_shift2_card').html(res1);
                            }
                        });
                    }
                });
                //   console.log(proc_arr.process_name);
                // console.log(proc_arr.part_name);

                $('#process_parts2').empty();
                $('#unit_time_details2').empty();
                $('#process_users2').val('');
                $('#username2').empty();
                $('#process_start_time2').val('00:00');
            });

            // SHIFT 2

            $('#model_processes2').on('change', function() {
                var fixdate = $('#fixdate').val();
                var model_name = $('#processes2').val();
                var processes = $('#model_processes2').val();

                var data = "model_name=" + model_name + "&processes=" + processes + "&fixdate=" + fixdate + "&type=get_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#process_parts2').html(res);
                    }
                });
            });

            // SHIFT 2

            $('#process_parts2').on('change', function() {
                var parts = $('#process_parts2').val();

                var data = "parts=" + parts + "&type=unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#unit_time_details2').html(res);
                    }
                });
            });

            $('#process_users2').on('change', function() {
                var model_name = $('#processes2').val();
                var process_start_time = $('#process_start_time2').val();
                var data = "model_name=" + model_name + "&process_start_time=" + process_start_time + "&type=process_users";
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#username2').html(res);
                    }
                });
            });
            // --------- processwise selection data - shift 2 - end---------

            // ---------Process wise shift2 start---------
            $('#process_assign2').on('click', function() {
                var process_user_arr = $('#username2 option:selected').toArray().map(item => item.value);
                var proc_user_arr = JSON.stringify(process_user_arr);
                // console.log(json_select_arr);
                // console.log($('#username option:selected').length);
                var processes = $('#process_parts2').val();
                var process_start_time = $('#process_start_time2').val();



                var fixdate = $('#fixdate').val();
                var data = "process_start_time=" + process_start_time + "&processes=" + processes + "&username=" + proc_user_arr + "&fixdate=" + fixdate + "&type=process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#processes2').val('').trigger('change');
                        $('#username2').val('').trigger('change');
                    }
                });
            });
            // ---------Process wise shift2 end---------

            // ---------workstation selection data -general-start----------
            $('#workprocesses').on('change', function() {
                var workprocesses = $('#workprocesses').val();
                var fixdate = $('#fixdate').val();

                var data = "workprocesses=" + workprocesses + "&fixdate=" + fixdate + "&type=work_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#work_parts').html(res);

                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=workstation";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#workstation_gen_card').html(res1);
                            }
                        });
                    }
                });
            });
            // GENERAL SHIFT
            $('#work_parts').on('change', function() {
                var work_parts = $('#work_parts').val();

                var data = "work_parts=" + work_parts + "&type=work_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#work_unit_time').html(res);
                    }
                });
            });
            // ---------workstation selection data -general-end----------

            // -----------workstation wise start-----------
            $('#workstation_assign').on('click', function() {
                // console.log(this.value);

                var workstation = $('#workstation').val();
                var workprocesses = $('#work_parts').val();
                var fixdate = $('#fixdate').val();
                var workstation_start_time = $('#workstation_start_time').val();

                var data = "workstation_start_time=" + workstation_start_time + "&workstation=" + workstation + "&workprocesses=" + workprocesses + "&fixdate=" + fixdate + "&type=workstation";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#workstation').val('').trigger('change');
                        $('#workprocesses').val('').trigger('change');
                        $('#work_parts').val('').trigger('change');
                        $('#workstation_start_time').val('00:00');
                        $('#work_unit_time').empty();
                    }
                });
            });
            // -----------workstation wise start-----------

            // ---------workstation selection data -shift 1-start----------
            $('#workprocesses1').on('change', function() {
                var workprocesses = $('#workprocesses1').val();
                var fixdate = $('#fixdate').val();

                var data = "workprocesses=" + workprocesses + "&fixdate=" + fixdate + "&type=work_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#work_parts1').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=workstation&shift=shift1";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#workstation_shift1_card').html(res1);

                            }
                        });
                    }
                });
            });
            // SHIFT 1
            $('#work_parts1').on('change', function() {
                var work_parts = $('#work_parts1').val();

                var data = "work_parts=" + work_parts + "&type=work_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#work_unit_time1').html(res);
                    }
                });
            });
            // ---------workstation selection data -shift 1-end----------

            // -----------workstation wise shift 1 start-----------
            $('#workstation_assign1').on('click', function() {
                // console.log(this.value);

                var workstation = $('#workstation1').val();
                var workprocesses = $('#work_parts1').val();
                var fixdate = $('#fixdate').val();
                var workstation_start_time = $('#workstation_start_time1').val();

                var data = "workstation_start_time=" + workstation_start_time + "&workstation=" + workstation + "&workprocesses=" + workprocesses + "&fixdate=" + fixdate + "&type=workstation";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#workstation1').val('').trigger('change');
                        $('#workprocesses1').val('').trigger('change');
                        $('#work_parts1').val('').trigger('change');
                        $('#workstation_start_time1').val('00:00');
                        $('#work_unit_time1').empty();
                    }
                });
            });
            // -----------workstation wise shift 1 end-----------

            // ---------workstation selection data -shift 2-start----------
            $('#workprocesses2').on('change', function() {
                var workprocesses = $('#workprocesses2').val();
                var fixdate = $('#fixdate').val();

                var data = "workprocesses=" + workprocesses + "&fixdate=" + fixdate + "&type=work_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#work_parts2').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=workstation&shift=shift2";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#workstation_shift2_card').html(res1);

                            }
                        });
                    }
                });
            });
            // SHIFT 2
            $('#work_parts2').on('change', function() {
                var work_parts = $('#work_parts2').val();

                var data = "work_parts=" + work_parts + "&type=work_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#work_unit_time2').html(res);
                    }
                });
            });
            // ---------workstation selection data -shift 2-end----------

            // -----------workstation wise shift 2 start-----------
            $('#workstation_assign2').on('click', function() {
                // console.log(this.value);

                var workstation = $('#workstation2').val();
                var workprocesses = $('#work_parts2').val();
                var fixdate = $('#fixdate').val();
                var workstation_start_time = $('#workstation_start_time2').val();

                var data = "workstation_start_time=" + workstation_start_time + "&workstation=" + workstation + "&workprocesses=" + workprocesses + "&fixdate=" + fixdate + "&type=workstation";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#workstation2').val('').trigger('change');
                        $('#workprocesses2').val('').trigger('change');
                        $('#work_parts2').val('').trigger('change');
                        $('#workstation_start_time2').val('00:00');
                        $('#work_unit_time2').empty();
                    }
                });
            });
            // -----------workstation wise shift 2 end-----------

            // GENERAL SHIFT
            // ------------material wise data fetch -general-start-----------
            $('#material').on('change', function() {
                var material = $('#material').val();
                var fixdate = $('#fixdate').val();

                var data = "material=" + material + "&fixdate=" + fixdate + "&type=material_models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_model').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=material";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#material_gen_card').html(res1);
                            }
                        });
                    }
                });
                $('#material_process').empty();
                $('#material_username').empty();
                $('#material_start_time').val('00:00');
                $('#material_unit_time').empty();
                $('#material_user_count').val('');
            });
            // GENERAL SHIFT
            $('#material_model').on('change', function() {
                var fixdate = $('#fixdate').val();
                var material_model = $('#material_model').val();

                var data = "material_model=" + material_model + "&fixdate=" + fixdate + "&type=material_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_process').html(res);
                    }
                });
            });

            $('#material_user_count').on('change', function() {
                var material_model = $('#material_model').val();
                var material_start_time = $('#material_start_time').val();

                var data = "material_model=" + material_model + "&material_start_time=" + material_start_time + "&type=material_users";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_username').html(res);
                    }
                });
            });

            $('#material_process').on('change', function() {
                var material_process = $('#material_process').val();

                var data = "material_process=" + material_process + "&type=material_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_unit_time').html(res);
                    }
                });
            });
            // ------------material wise data fetch -general-end-----------

            // ---------material wise start---------
            $('#material_assign').on('click', function() {

                var material = $('#material').val();
                var material_model = $('#material_process').val();
                var material_username = $('#material_username option:selected').toArray().map(item => item.value);
                var material_username_arr = JSON.stringify(material_username);
                var fixdate = $('#fixdate').val();
                var material_start_time = $('#material_start_time').val();

                var data = "material_start_time=" + material_start_time + "&material=" + material + "&material_model=" + material_model + "&material_username_arr=" + material_username_arr + "&fixdate=" + fixdate + "&type=material";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_model').empty();
                        $('#material_process').empty();
                        $('#material_username').empty();
                        $('#material_unit_time').empty();
                        $('#material_start_time').val('00:00');
                    }
                });

                // console.log(material);
                // console.log(material_model);
                // console.log(material_username_arr);

            });
            // ---------material wise end---------

            // SHIFT 1
            // ------------material wise data fetch -shift 1-start-----------
            $('#material1').on('change', function() {
                var material = $('#material1').val();
                var fixdate = $('#fixdate').val();

                var data = "material=" + material + "&fixdate=" + fixdate + "&type=material_models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_model1').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=material&shift=shift1";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#material_shift1_card').html(res1);

                            }
                        });
                    }
                });
                $('#material_process1').empty();
                $('#material_username1').empty();
                $('#material_start_time1').val('00:00');
                $('#material_unit_time1').empty();
                $('#material_user_count1').val('');
            });
            // SHIFT 1
            $('#material_model1').on('change', function() {
                var fixdate = $('#fixdate').val();
                var material_model = $('#material_model1').val();

                var data = "material_model=" + material_model + "&fixdate=" + fixdate + "&type=material_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_process1').html(res);
                    }
                });
            });

            $('#material_user_count1').on('change', function() {
                var material_model = $('#material_model1').val();
                var material_start_time = $('#material_start_time1').val();

                var data = "material_model=" + material_model + "&material_start_time=" + material_start_time + "&type=material_users";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_username1').html(res);
                    }
                });
            });

            $('#material_process1').on('change', function() {
                var material_process = $('#material_process1').val();

                var data = "material_process=" + material_process + "&type=material_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_unit_time1').html(res);
                    }
                });
            });
            // ------------material wise data fetch -shift 1-end-----------

            // ---------material wise shift 1 start---------
            $('#material_assign1').on('click', function() {

                var material = $('#material1').val();
                var material_model = $('#material_process1').val();
                var material_username = $('#material_username1 option:selected').toArray().map(item => item.value);
                var material_username_arr = JSON.stringify(material_username);
                var fixdate = $('#fixdate').val();
                var material_start_time = $('#material_start_time1').val();

                var data = "material_start_time=" + material_start_time + "&material=" + material + "&material_model=" + material_model + "&material_username_arr=" + material_username_arr + "&fixdate=" + fixdate + "&type=material";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_model1').empty();
                        $('#material_process1').empty();
                        $('#material_username1').empty();
                        $('#material_unit_time1').empty();
                        $('#material_start_time1').val('00:00');
                    }
                });

                // console.log(material);
                // console.log(material_model);
                // console.log(material_username_arr);

            });
            // ---------material wise shift 1 end---------

            // SHIFT 2
            // ------------material wise data fetch -shift 2-start-----------
            $('#material2').on('change', function() {
                var material = $('#material2').val();
                var fixdate = $('#fixdate').val();

                var data = "material=" + material + "&fixdate=" + fixdate + "&type=material_models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_model2').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=material&shift=shift2";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#material_shift2_card').html(res1);

                            }
                        });
                    }
                });
                $('#material_process2').empty();
                $('#material_username2').empty();
                $('#material_start_time2').val('00:00');
                $('#material_unit_time2').empty();
                $('#material_user_count2').val('');
            });
            // SHIFT 2
            $('#material_model2').on('change', function() {
                var fixdate = $('#fixdate').val();
                var material_model = $('#material_model2').val();

                var data = "material_model=" + material_model + "&fixdate=" + fixdate + "&type=material_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_process2').html(res);
                    }
                });
            });

            $('#material_user_count2').on('change', function() {
                var material_model = $('#material_model2').val();
                var material_start_time = $('#material_start_time2').val();

                var data = "material_model=" + material_model + "&material_start_time=" + material_start_time + "&type=material_users";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_username2').html(res);
                    }
                });
            });

            $('#material_process2').on('change', function() {
                var material_process = $('#material_process2').val();

                var data = "material_process=" + material_process + "&type=material_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_unit_time2').html(res);
                    }
                });
            });
            // ------------material wise data fetch -shift 2-end-----------

            // ---------material wise shift 2 start---------
            $('#material_assign2').on('click', function() {

                var material = $('#material2').val();
                var material_model = $('#material_process2').val();
                var material_username = $('#material_username2 option:selected').toArray().map(item => item.value);
                var material_username_arr = JSON.stringify(material_username);
                var fixdate = $('#fixdate').val();
                var material_start_time = $('#material_start_time2').val();

                var data = "material_start_time=" + material_start_time + "&material=" + material + "&material_model=" + material_model + "&material_username_arr=" + material_username_arr + "&fixdate=" + fixdate + "&type=material";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_model2').empty();
                        $('#material_process2').empty();
                        $('#material_username2').empty();
                        $('#material_unit_time2').empty();
                        $('#material_start_time2').val('00:00');
                    }
                });

                // console.log(material);
                // console.log(material_model);
                // console.log(material_username_arr);

            });
            // ---------material wise shift 2 end---------

            // ---------team selection data - general- start---------
            $('#teams').on('change', function() {

                var team = $('#teams').val();
                var fixdate = $('#fixdate').val();

                $('#team_model').empty();
                $('#team_process').empty();
                $('#team_unit_time').empty();
                $('#team_count').val('');
                $('#team_start_time').val('00:00');
                $('#team_username').empty();

                var data = "team=" + team + "&fixdate=" + fixdate + "&type=team_model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_model').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=team";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#team_gen_card').html(res1);
                            }
                        });
                    }
                });
            });
            // GENERAL SHIFT
            $('#team_model').on('change', function() {
                var fixdate = $('#fixdate').val();
                var team_model = $('#team_model').val();

                var data = "team_model=" + team_model + "&fixdate=" + fixdate + "&type=team_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_process').html(res);
                    }
                });
            });

            $('#team_process').on('change', function() {
                var team_process = $('#team_process').val();

                var data = "team_process=" + team_process + "&type=team_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_unit_time').html(res);
                    }
                });
            });

            $('#team_count').on('change', function() {
                var team = $('#teams').val();

                var data = "team=" + team + "&type=team_username";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_username').html(res);
                    }
                });
            });

            // ---------team selection data - general- end---------

            // -----------team wise start------------
            $('#team_assign').on('click', function() {
                var teams = $('#teams').val();
                var team_model = $('#team_model').val();
                var team_process = $('#team_process').val();
                var team_username = $('#team_username option:selected').toArray().map(item => item.value);
                var team_username_arr = JSON.stringify(team_username);
                var fixdate = $('#fixdate').val();
                var team_start_time = $('#team_start_time').val();

                var data = "team_process=" + team_process + "&team_start_time=" + team_start_time + "&teams=" + teams + "&team_model=" + team_model + "&team_username_arr=" + team_username_arr + "&fixdate=" + fixdate + "&type=team";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        // $('#teams').empty();
                        $('#team_model').empty();
                        $('#team_process').empty();
                        $('#team_unit_time').empty();
                        $('#team_count').empty();
                        $('#team_start_time').val('00:00');
                        $('#team_username').empty();

                    }
                });

                // console.log(teams);
                // console.log(team_model);
                // console.log(team_username_arr);

            });
            // -----------team wise end------------

            // ---------team selection data - shift 1- start---------
            $('#teams1').on('change', function() {
                var team = $('#teams1').val();
                var fixdate = $('#fixdate').val();

                $('#team_model1').empty();
                $('#team_process1').empty();
                $('#team_unit_time1').empty();
                $('#team_count1').val('');
                $('#team_start_time1').val('00:00');
                $('#team_username1').empty();

                var data = "team=" + team + "&fixdate=" + fixdate + "&type=team_model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_model1').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=team&shift=shift1";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#team_shift1_card').html(res1);

                            }
                        });
                    }
                });
            });
            // SHIFT 1
            $('#team_model1').on('change', function() {
                var fixdate = $('#fixdate').val();
                var team_model = $('#team_model1').val();

                var data = "team_model=" + team_model + "&fixdate=" + fixdate + "&type=team_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_process1').html(res);
                    }
                });
            });

            $('#team_process1').on('change', function() {
                var team_process = $('#team_process1').val();

                var data = "team_process=" + team_process + "&type=team_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_unit_time1').html(res);
                    }
                });
            });

            $('#team_count1').on('change', function() {
                var team = $('#teams1').val();

                var data = "team=" + team + "&type=team_username";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_username1').html(res);
                    }
                });
            });

            // ---------team selection data - shift 1- end---------

            // -----------team wise shift 1 start------------
            $('#team_assign1').on('click', function() {
                var teams = $('#teams1').val();
                var team_model = $('#team_model1').val();
                var team_process = $('#team_process1').val();
                var team_username = $('#team_username1 option:selected').toArray().map(item => item.value);
                var team_username_arr = JSON.stringify(team_username);
                var fixdate = $('#fixdate').val();
                var team_start_time = $('#team_start_time1').val();

                var data = "team_process=" + team_process + "&team_start_time=" + team_start_time + "&teams=" + teams + "&team_model=" + team_model + "&team_username_arr=" + team_username_arr + "&fixdate=" + fixdate + "&type=team";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        // $('#teams1').empty();
                        $('#team_model1').empty();
                        $('#team_process1').empty();
                        $('#team_unit_time1').empty();
                        $('#team_count1').empty();
                        $('#team_start_time1').val('00:00');
                        $('#team_username1').empty();

                    }
                });

                // console.log(teams);
                // console.log(team_model);
                // console.log(team_username_arr);

            });
            // -----------team wise shift 1 end------------

            // ---------team selection data - shift 2- start---------
            $('#teams2').on('change', function() {
                var team = $('#teams2').val();
                var fixdate = $('#fixdate').val();

                $('#team_model2').empty();
                $('#team_process2').empty();
                $('#team_unit_time2').empty();
                $('#team_count2').val('');
                $('#team_start_time2').val('00:00');
                $('#team_username2').empty();

                var data = "team=" + team + "&fixdate=" + fixdate + "&type=team_model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_model2').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=team&shift=shift2";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#team_shift2_card').html(res1);

                            }
                        });
                    }
                });
            });
            // SHIFT 2
            $('#team_model2').on('change', function() {
                var fixdate = $('#fixdate').val();
                var team_model = $('#team_model2').val();

                var data = "team_model=" + team_model + "&fixdate=" + fixdate + "&type=team_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_process2').html(res);
                    }
                });
            });

            $('#team_process2').on('change', function() {
                var team_process = $('#team_process2').val();

                var data = "team_process=" + team_process + "&type=team_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_unit_time2').html(res);
                    }
                });
            });

            $('#team_count2').on('change', function() {
                var team = $('#teams2').val();

                var data = "team=" + team + "&type=team_username";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_username2').html(res);
                    }
                });
            });

            // ---------team selection data - shift 2- end---------

            // -----------team wise shift 2 start------------
            $('#team_assign2').on('click', function() {
                var teams = $('#teams2').val();
                var team_model = $('#team_model2').val();
                var team_process = $('#team_process2').val();
                var team_username = $('#team_username2 option:selected').toArray().map(item => item.value);
                var team_username_arr = JSON.stringify(team_username);
                var fixdate = $('#fixdate').val();
                var team_start_time = $('#team_start_time2').val();

                var data = "team_process=" + team_process + "&team_start_time=" + team_start_time + "&teams=" + teams + "&team_model=" + team_model + "&team_username_arr=" + team_username_arr + "&fixdate=" + fixdate + "&type=team";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        // $('#teams2').empty();
                        $('#team_model2').empty();
                        $('#team_process2').empty();
                        $('#team_unit_time2').empty();
                        $('#team_count2').empty();
                        $('#team_start_time2').val('00:00');
                        $('#team_username2').empty();

                    }
                });

                // console.log(teams);
                // console.log(team_model);
                // console.log(team_username_arr);

            });
            // -----------team wise shift 2 end------------

            // ----------model wise selection data start----------
            $('#models').on('change', function() {
                var models = $('#models').val();
                var fixdate = $('#fixdate').val();

                $('#model_process').empty();

                $('#model_unit_time').empty();
                $('#model_user_count').empty();
                $('#model_start_time').val('00:00');
                $('#model_username').empty();

                var data = "models=" + models + "&fixdate=" + fixdate + "&type=models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_process').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=model";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#model_gen_card').html(res1);
                            }
                        });
                    }
                });
            });

            $('#model_process').on('change', function() {
                var model_process = $('#model_process').val();

                var data = "model_process=" + model_process + "&type=model_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_username').html(res);
                    }
                });
            });

            $('#model_start_time').on('change', function() {
                var model_process = $('#model_process').val();

                var data = "model_process=" + model_process + "&type=model_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_unit_time').html(res);
                    }
                });
            });
            // model_start_time
            // model_user_count
            // model_process
            // model_username
            // model_unit_time
            // model_assign
            // ----------model wise selection data start----------

            // ---------model wise start---------
            $('#model_assign').on('click', function() {
                var model = $('#model').val();
                var model_process = $('#model_process').val();
                var model_username = $('#model_username option:selected').toArray().map(item => item.value);
                var model_username_arr = JSON.stringify(model_username);
                var fixdate = $('#fixdate').val();
                var model_start_time = $('#model_start_time').val();

                var data = "model_start_time=" + model_start_time + "&model=" + model + "&model_process=" + model_process + "&model_username_arr=" + model_username_arr + "&fixdate=" + fixdate + "&type=model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#models').val('').trigger('change');
                        $('#model_process').empty();

                        $('#model_unit_time').empty();
                        $('#model_user_count').empty();
                        $('#model_start_time').val('00:00');
                        $('#model_username').empty();

                    }
                });

                // console.log(model);
                // console.log(model_process);
                // console.log(model_username_arr);

            });
            // ---------model wise end---------

            // ----------model wise selection data - shift 1-  start----------
            $('#models1').on('change', function() {
                var models = $('#models1').val();
                var fixdate = $('#fixdate').val();

                $('#model_process1').empty();

                $('#model_unit_time1').empty();
                $('#model_user_count1').empty();
                $('#model_start_time1').val('00:00');
                $('#model_username1').empty();

                var data = "models=" + models + "&fixdate=" + fixdate + "&type=models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_process1').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=model&shift=shift1";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#model_shift1_card').html(res1);

                            }
                        });
                    }
                });
            });

            $('#model_process1').on('change', function() {
                var model_process = $('#model_process1').val();

                var data = "model_process=" + model_process + "&type=model_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_username1').html(res);
                    }
                });
            });
            // model_process
            // model_username
            // model_unit_time
            // model_assign
            // ----------model wise selection data - shift 1-  end----------

            // ---------model wise shift 1 start---------
            $('#model_assign1').on('click', function() {
                var model = $('#model1').val();
                var model_process = $('#model_process1').val();
                var model_username = $('#model_username1 option:selected').toArray().map(item => item.value);
                var model_username_arr = JSON.stringify(model_username);
                var fixdate = $('#fixdate').val();
                var model_start_time = $('#model_start_time1').val();

                var data = "model_start_time=" + model_start_time + "&model=" + model + "&model_process=" + model_process + "&model_username_arr=" + model_username_arr + "&fixdate=" + fixdate + "&type=model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#models1').val('').trigger('change');
                        $('#model_process1').empty();

                        $('#model_unit_time1').empty();
                        $('#model_user_count1').empty();
                        $('#model_start_time1').val('00:00');
                        $('#model_username1').empty();

                    }
                });

                // console.log(model);
                // console.log(model_process);
                // console.log(model_username_arr);

            });
            // ---------model wise shift 1 end---------

            // ----------model wise selection data - shift 2-  start----------
            $('#models2').on('change', function() {
                var models = $('#models2').val();
                var fixdate = $('#fixdate').val();

                $('#model_process2').empty();

                $('#model_unit_time2').empty();
                $('#model_user_count2').empty();
                $('#model_start_time2').val('00:00');
                $('#model_username2').empty();

                var data = "models=" + models + "&fixdate=" + fixdate + "&type=models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_process2').html(res);
                        var fixdate = $('#fixdate').val();
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards&card=model&shift=shift2";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "task_assign_today_pm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#model_shift2_card').html(res1);

                            }
                        });
                    }
                });
            });

            $('#model_process2').on('change', function() {
                var model_process = $('#model_process2').val();

                var data = "model_process=" + model_process + "&type=model_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_username2').html(res);
                    }
                });
            });
            // model_process
            // model_username
            // model_unit_time
            // model_assign
            // ----------model wise selection data - shift 2-  end----------

            // ---------model wise shift 2 start---------
            $('#model_assign2').on('click', function() {
                var model = $('#model2').val();
                var model_process = $('#model_process2').val();
                var model_username = $('#model_username2 option:selected').toArray().map(item => item.value);
                var model_username_arr = JSON.stringify(model_username);
                var fixdate = $('#fixdate').val();
                var model_start_time = $('#model_start_time2').val();

                var data = "model_start_time=" + model_start_time + "&model=" + model + "&model_process=" + model_process + "&model_username_arr=" + model_username_arr + "&fixdate=" + fixdate + "&type=model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#models2').val('').trigger('change');
                        $('#model_process2').empty();

                        $('#model_unit_time2').empty();
                        $('#model_user_count2').empty();
                        $('#model_start_time2').val('00:00');
                        $('#model_username2').empty();

                    }
                });

                // console.log(model);
                // console.log(model_process);
                // console.log(model_username_arr);

            });
            // ---------model wise shift 2 end---------



            // ----------------------user wise general start----------------------


            // GENERAL
            // select associates

            // in the below code, in "data: data", left side data is ajax syntax & right side data is the value in the data variable
            $(document).ready(function() {
                var fixdate = $('#fixdate').val();

                var data = "fixdate=" + fixdate + "&type=user_fetch";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#user').html(res);
                    }
                });
            });






            // GENERAL SHIFT
            $('#user_models').on('change', function() {
                var fixdate = $('#fixdate').val();
                var user_models = $('#user_models').val();

                var data = "user_models=" + user_models + "&fixdate=" + fixdate + "&type=user_model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "task_assign_today_pm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_process').html(res);
                    }
                });
            });


            // ----------------------user wise general end----------------------
        </script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {

                // $('#processes').select2({ width: '100%' });
                // $('#username').select2({ width: '100%' });
                // $('#workstation').select2({ width: '100%' });
                // $('#workprocesses').select2({ width: '100%' });
                // $('#material').select2({ width: '100%' });
                // $('#material_model').select2({ width: '100%' });
                // $('#material_username').select2({ width: '100%' });
                // $('#teams').select2({ width: '100%' });
                // $('#team_model').select2({ width: '100%' });
                // $('#team_username').select2({ width: '100%' });
                // $('#model').select2({ width: '100%' });
                // $('#model_process').select2({ width: '100%' });
                // $('#model_username').select2({ width: '100%' });
                //
                // $('#processes1').select2({ width: '100%' });
                // $('#username1').select2({ width: '100%' });
                // $('#workstation1').select2({ width: '100%' });
                // $('#workprocesses1').select2({ width: '100%' });
                // $('#material1').select2({ width: '100%' });
                // $('#material_model1').select2({ width: '100%' });
                // $('#material_username1').select2({ width: '100%' });
                // $('#teams1').select2({ width: '100%' });
                // $('#team_model1').select2({ width: '100%' });
                // $('#team_username1').select2({ width: '100%' });
                // $('#model1').select2({ width: '100%' });
                // $('#model_process1').select2({ width: '100%' });
                // $('#model_username1').select2({ width: '100%' });
                //
                // $('#processes2').select2({ width: '100%' });
                // $('#username2').select2({ width: '100%' });
                // $('#workstation2').select2({ width: '100%' });
                // $('#workprocesses2').select2({ width: '100%' });
                // $('#material2').select2({ width: '100%' });
                // $('#material_model2').select2({ width: '100%' });
                // $('#material_username2').select2({ width: '100%' });
                // $('#teams2').select2({ width: '100%' });
                // $('#team_model2').select2({ width: '100%' });
                // $('#team_username2').select2({ width: '100%' });
                // $('#model2').select2({ width: '100%' });
                // $('#model_process2').select2({ width: '100%' });
                // $('#model_username2').select2({ width: '100%' });
                //
                // $('#shift1username').select2({ width: '100%' });
                // $('#shift2username').select2({ width: '100%' });
                // $('#shift_type').select2({ width: '100%' });
                // $('#shift_timing').select2({ width: '100%' });
            });
        </script>

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {
                'packages': ['gauge']
            });
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {

                var data = google.visualization.arrayToDataTable([
                    ['Label', 'Value'],
                    ['Workstation', 0],
                    ['Associates', 0]
                ]);

                var options = {
                    redFrom: 0,
                    redTo: 2,
                    yellowFrom: 2,
                    yellowTo: 6,
                    greenFrom: 6,
                    greenTo: 8,
                    majorTicks: 2,
                    max: 8
                };

                var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

                chart.draw(data, options);

                function updateUserChart(value) {
                    data.setValue(1, 1, value);
                    chart.draw(data, options);
                }

                function updateWorkChart(value) {
                    data.setValue(0, 1, value);
                    chart.draw(data, options);
                }

                setInterval(function() {
                    var fixdate = $('#fixdate').val();
                    var data = "fixdate=" + fixdate + "&type=associate_gauge";
                    console.log(data);
                    $.ajax({
                        type: "POST",
                        url: "task_planning_scripts.php",
                        data: data,
                        success: function(res) {
                            console.log(res);
                            var numericValue = parseInt(res);
                            updateUserChart(numericValue);
                        }
                    });

                }, 3000);
                setInterval(function() {
                    var fixdate = $('#fixdate').val();
                    var data = "fixdate=" + fixdate + "&type=work_gauge";
                    console.log(data);
                    $.ajax({
                        type: "POST",
                        url: "task_planning_scripts.php",
                        data: data,
                        success: function(res) {
                            console.log(res);
                            var numericValue = parseInt(res);
                            updateWorkChart(numericValue);
                        }
                    });
                }, 3000);
            }
        </script>


        <!-- batch plan counter -->


    </div><!-- row close -->
</div><!-- container close -->
</div><!-- app-main__inner close -->
</div><!-- app-main__outer close -->

<?php include 'footer.php'; ?>