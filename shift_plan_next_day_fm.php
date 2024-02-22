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
            // Set the default timezone to use. Available since PHP 5.1
            // date_default_timezone_set('UTC'); 
            // Change 'UTC' to your desired timezone

            // Code for getting tomorrow's date. If today is saturday then get day after tomorrow's date

            // Check if today is Saturday (date('w') returns '6')
            if (date('w') == 6)
            {
                // If today is Saturday, get the date of the day after tomorrow
                $desiredDate = date('Y-m-d', strtotime('+2 days'));
            }
            else
            {
                // Otherwise, just get tomorrow's date
                $desiredDate = date('Y-m-d', strtotime('tomorrow'));
            }


            // $desiredDate = date('Y-m-d');
            ?>


            <div class="row text-center">
                <form method="post" class="col-6">

                    <label for="">Shift Plan Date: </label>
                    <input type="date" class="form-control" id="fixdate" value="<?php echo $desiredDate; ?>" readonly>

                    <div class="form-row">
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

                            <br>
                            <br>

                            <label for="">Enter Number of Team Members</label>
                            <!-- \\ -->
                            <input id="gen_no_of_associates" type="number" value="0" min="0" step="1">


                            <br>
                            <br>

                            <!-- [[ new button for general shift submit ]] -->
                            <button type="button" id="gen_shift_submit_new" name="button">Update Shift</button>
                        </div>
                        <!-- [[ old button]] -->
                        <!-- <button type="button" id="gen_shift_submit" name="button">Update Shift</button> -->
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
                                <label for="">Shift 1 Number of Team Members</label>
                                <!-- \\ -->
                                <input id="s1noofassoc" type="number" value="0" min="0" step="1">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <label for="">Shift 2 Number of Team Members</label>
                                <!-- \\ -->
                                <input id="s2noofassoc" type="number" value="0" min="0" step="1">
                            </div>
                        </div>
                        <!-- [[ update shift]] -->
                        <button type="button" id="multi_shift_submit" name="button">Update Shift</button>
                    </div>


                    <!-- <label for=""></label>
                  <input type="text" name="" value=""> -->
                </form>


                <!-- <div class="col-4">
                    <h5 id="batch_diff"></h5>
                    <div id="chart_div" class="d-flex justify-content-center" style="display:none;"></div>
                </div> -->


                <?php

                // $fixdate = $_POST['fixdate'];

                $get_current_batch = $conn->prepare('SELECT * FROM current_batch WHERE 1');
                $get_current_batch->execute();
                $get_current_batch_res = $get_current_batch->get_result();
                $get_current_batch_row = $get_current_batch_res->fetch_assoc();

                //fixdate and batchdate diff
                $earlier = new DateTime(date('Y-m-d', strtotime($get_current_batch_row['batchdate'])));
                $later = new DateTime($desiredDate);

                $pos_diff = $earlier->diff($later)->format("%r%a"); //3
                // echo "Batch Plan For: Day " . $pos_diff;

                ?>



                <?php
                // code for current details
                $display_next_day_shift_details = $conn->prepare("SELECT DISTINCT shift_type, start_time, end_time FROM shift_user_plan where DATE(shiftdate) =? ;");
                $display_next_day_shift_details->bind_param('s', $desiredDate);
                $display_next_day_shift_details->execute();
                $display_next_day_shift_details_res = $display_next_day_shift_details->get_result();


                // echo $todaysDate;



                ?>



                <div class="col-6">
                    <h5>Batch Plan For: <?php echo "Batch Plan For: Day " . $pos_diff; ?></h5>
                    <div class="card bg-success text-dark" style="width: 100%;">
                        <div class="card-body">
                            <h5 class="card-title">Current Details</h5>

                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Shift Date: </strong><?php echo $desiredDate; ?></li>

                            <?php


                            while ($display_next_day_shift_details_row = $display_next_day_shift_details_res->fetch_assoc())
                            {

                                if ($display_next_day_shift_details_row['shift_type'] == 'general')
                                {

                                    echo ' <li class="list-group-item"><strong>Target Planned Shift: </strong>General Shift</li>
                                            <li class="list-group-item"><strong>Start Time: </strong>' . $display_next_day_shift_details_row['start_time'] . '</li>
                                            <li class="list-group-item"><strong>End Time: </strong>' . $display_next_day_shift_details_row['end_time'] . '</li>';
                                }

                                if ($display_next_day_shift_details_row['shift_type'] == 'shift1')
                                {

                                    echo ' <li class="list-group-item"><strong>Target Planned Shift: </strong>Shift 1</li>
                                            <li class="list-group-item"><strong>Start Time: </strong>' . $display_next_day_shift_details_row['start_time'] . '</li>
                                            <li class="list-group-item"><strong>End Time: </strong>' . $display_next_day_shift_details_row['end_time'] . '</li>';
                                }

                                if ($display_next_day_shift_details_row['shift_type'] == 'shift2')
                                {

                                    echo ' <li class="list-group-item"><strong>Target Planned Shift: </strong>Shift 2</li>
                                            <li class="list-group-item"><strong>Start Time: </strong>' . $display_next_day_shift_details_row['start_time'] . '</li>
                                            <li class="list-group-item"><strong>End Time: </strong>' . $display_next_day_shift_details_row['end_time'] . '</li>';
                                }
                            }




                            ?>

                        </ul>

                    </div>
                </div>



                <div class="col-4" style="display:none;">
                    <p>Associate Work Details</p>
                    <div class="associate_detail">

                    </div>
                </div>
            </div>
            <hr>
            <button type="button" id="role_button" class="btn btn-secondary">Team Change & Tasks Menu</button>
            <div class="container border border-secondary shadow-sm p-3 mb-5 bg-white rounded" id="role_card" style="display:none;">
                <div class="row">
                    <div class="col-3">
                        <h5>Role Change</h5>
                        <label for="">Executives</label>
                        <select class="form-control" id="exec_role">
                            <?php
                            $get_exec = $conn->prepare("select * from user_detail where role = 'Executive' or leader = '1'");
                            $get_exec->execute();
                            $get_exec_res = $get_exec->get_result();
                            while ($get_exec_row = $get_exec_res->fetch_assoc())
                            {
                            ?><option value="<?php echo $get_exec_row['user_name']; ?>"><?php echo $get_exec_row['fullname'] . ' - ' . $get_exec_row['department']; ?></option><?php
                                                                                                                                                                            }
                                                                                                                                                                                ?>

                        </select>

                        <label for="">Temp Department</label>
                        <select class="form-control" id="temp_dept">
                            <?php
                            $get_dept = $conn->prepare("select distinct department from user_detail");
                            $get_dept->execute();
                            $get_dept_res = $get_dept->get_result();
                            while ($get_dept_row = $get_dept_res->fetch_assoc())
                            {
                            ?><option value="<?php echo $get_dept_row['department']; ?>"><?php echo $get_dept_row['department']; ?></option><?php
                                                                                                                                        }
                                                                                                                                            ?>

                        </select>
                        <button type="button" id="role_submit">Change Role</button>
                    </div>
                    <div class="col-3">
                        <h5>Team Change</h5>
                        <label for="">Associates</label>
                        <select class="form-control" id="team_assoc">
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

                        <label for="">New Team</label>
                        <select class="form-control" id="new_team_name">
                            <?php

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
                        <button type="button" id="change_team_submit">Change Team</button>
                    </div>
                    <div class="col-3">
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
                    <div class="col-3">
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
            <hr>






















            <!-- [[  selecting general shift associates - newly added code ]] -->


            <div class="container border border-secondary shadow-sm p-3 mb-5 bg-white rounded" id="gen_shift_users" style="display:none;">
                <div class="form-row">
                    <div class="col">
                        <label for="">Select General Shift Team</label>
                        <select class="form-control select" multiple id="genshiftusername" name="genshiftusername[]">
                            <?php
                            // SELECT * FROM `task_id` where model_name='pythagoras' and process_name='Sand belt finishing' and part_name='Corner round after pasting' and batchno='test64646-766'
                            // $select_users = $conn->prepare("SELECT * FROM user_detail where role = 'Associates' and teamname is not null;");
                            $select_users = $conn->prepare("SELECT * FROM user_detail where user_type = 'Associates' or user_type = 'Executives' order by user_type DESC;");
                            $select_users->execute();
                            $select_users_res = $select_users->get_result();
                            while ($select_users_row = $select_users_res->fetch_assoc())
                            {
                                // $barcode[] = $select_users_row['barcode'];
                                // $username[] = $select_users_row['fullname'];
                            ?>
                                <option value="<?php echo $select_users_row['user_name']; ?>"><?php echo $select_users_row['fullname'] . ' - ' . $select_users_row['user_type']; ?></option>
                            <?php
                            }
                            //selected users to be removed from stack
                            ?>
                        </select>
                        <span id="shift1display"></span>
                    </div>
                </div>



                <div class="form-row">
                    <div class="col">
                        <!-- [[ button to assign shift associates general shift]] -->
                        <button type="button" id="gen_submit">Assign Team</button>
                    </div>
                </div>
            </div>


















            <!-- [[ selecting shift 1 associates - use this code in general shift]] -->

            <div class="container border border-secondary shadow-sm p-3 mb-5 bg-white rounded" id="shift_users" style="display:none;">
                <div class="form-row">
                    <div class="col">
                        <label for="">Shift 1 Team</label>
                        <select class="form-control select" multiple id="shift1username" name="shift1username[]">
                            <?php
                            // SELECT * FROM `task_id` where model_name='pythagoras' and process_name='Sand belt finishing' and part_name='Corner round after pasting' and batchno='test64646-766'
                            // $select_users = $conn->prepare("SELECT * FROM user_detail where role = 'Associates' and teamname is not null;");
                            $select_users = $conn->prepare("SELECT * FROM user_detail where user_type = 'Associates' or user_type = 'Executives' order by user_type DESC;");
                            $select_users->execute();
                            $select_users_res = $select_users->get_result();
                            while ($select_users_row = $select_users_res->fetch_assoc())
                            {
                                // $barcode[] = $select_users_row['barcode'];
                                // $username[] = $select_users_row['fullname'];
                            ?>
                                <option value="<?php echo $select_users_row['user_name']; ?>"><?php echo $select_users_row['fullname'] . ' - ' . $select_users_row['user_type']; ?></option>
                            <?php
                            }
                            //selected users to be removed from stack
                            ?>
                        </select>
                        <span id="shift1display"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label for="">Shift 2 Team</label>
                        <select class="form-control select" multiple id="shift2username" name="shift2username[]">
                            <?php
                            // SELECT * FROM `task_id` where model_name='pythagoras' and process_name='Sand belt finishing' and part_name='Corner round after pasting' and batchno='test64646-766'
                            // $select_users = $conn->prepare("SELECT * FROM user_detail where role = 'Associates' and teamname is not null;");
                            $select_users = $conn->prepare("SELECT * FROM user_detail where user_type = 'Associates' or user_type = 'Executives' order by user_type DESC;");
                            $select_users->execute();
                            $select_users_res = $select_users->get_result();
                            while ($select_users_row = $select_users_res->fetch_assoc())
                            {
                                // $barcode[] = $select_users_row['barcode'];
                                // $username[] = $select_users_row['fullname'];
                            ?>
                                <option value="<?php echo $select_users_row['user_name']; ?>"><?php echo $select_users_row['fullname'] . ' - ' . $select_users_row['user_type']; ?></option>
                            <?php
                            }
                            //selected users to be removed from stack
                            ?>
                        </select>
                        <span id="shift2display"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <!-- [[ button to assign shift associates in multi shift - use this in general shift]] -->
                        <button type="button" id="multi_submit">Assign Team</button>
                    </div>
                </div>
            </div>



        </div>

        <br>


        <div id="modellogs"></div>



        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script>
            // var startDateTime = new Date(2024, 0, 29, 16, 46, 0, 0); // YYYY (M-1) D H m s ms (start time and date from DB)
            // var startStamp = startDateTime.getTime();

            // var newDate = new Date();
            // var newStamp = newDate.getTime();

            // var timer; // for storing the interval (to stop or pause later if needed)

            // function updateClock() {
            //     newDate = new Date();
            //     newStamp = newDate.getTime();
            //     var diff = Math.round((newStamp - startStamp) / 1000);

            //     var d = Math.floor(diff / (24 * 60 * 60)); /* though I hope she won't be working for consecutive days :) */
            //     diff = diff - (d * 24 * 60 * 60);
            //     var h = Math.floor(diff / (60 * 60));
            //     diff = diff - (h * 60 * 60);
            //     var m = Math.floor(diff / (60));
            //     diff = diff - (m * 60);
            //     var s = diff;

            //     document.getElementById("time-elapsed").innerHTML = d + " days   " + h + " hours: " + m + " minutes: " + s + " seconds spent in planning";
            // }

            // timer = setInterval(updateClock, 1000);
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
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        // console.log(res);
                        $('#batch_diff').text(res);
                    }
                });
            });

            // ----------shift card display start----------
            // [[old code - no longer needed]]
            $('#gen_shift_submit').on('click', function() {
                $('.shift1_card').show();
            });

            // [[changes made for new code]]
            $('#gen_submit').on('click', function() {
                $('.shift1_card').show();
            });

            // [[ new code added for general shift ]]
            $('#gen_shift_submit_new').on('click', function() {
                $('#gen_shift_users').show();
                $('#shift_users').hide();
            });

            // [[changes made]]
            $('#multi_shift_submit').on('click', function() {
                $('#shift_users').show();
                $('#gen_shift_users').hide();
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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


            // encoding variable in js
            $('#machine_assign').on('click', function() {
                var machin_assoc = $('#machin_assoc').val();
                var machine_workstation = $('#machine_workstation').val();

                var machine_workstation_encoded = encodeURIComponent(machine_workstation);
                console.log(machine_workstation_encoded);

                var data = "machin_assoc=" + machin_assoc + "&machine_workstation=" + machine_workstation_encoded + "&type=machine_assign";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);


                        // var fixdate = $('#fixdate').val();
                        var data1 = "type=machine_assign_refresh";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "shift_plan_next_day_fm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('#machine_workstation').html(res1);
                            }
                        });



                    }
                });

                console.log(machin_assoc);
                console.log(machine_workstation);
            });
            // ---------machine assign end--------






            // -------shift timings toggle start--------
            $('#shift_type').on('change', function() {
                var shift_type = this.value;
                // console.log(shift_type);
                if (shift_type == 2) {
                    $('#shift_time_form').show();
                    $('#gen_shift_time').hide();
                } else {
                    $('#shift_time_form').hide();
                    $('#gen_shift_time').show();

                }
            });
            // ---------shift timings toggle end----------
























            // [[ old ajax for general shift submit to be changed and replicated like multi shift submit]]



            // ---------gen shift create---------
            $('#gen_shift_submit').on('click', function() {
                var fixdate = $('#fixdate').val();
                var gen_shift_start_time = $('#gen_shift_start_time').val();
                var gen_shift_end_time = $('#gen_shift_end_time').val();

                var data = "gen_shift_start_time=" + gen_shift_start_time + "&gen_shift_end_time=" + gen_shift_end_time + "&fixdate=" + fixdate + "&type=gen_shift_create";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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

















            // [[ new ajax for general shift submit ]]


            // ---------gen shift create---------
            $('#gen_submit').on('click', function() {
                var fixdate = $('#fixdate').val();
                var gen_shift_start_time = $('#gen_shift_start_time').val();
                var gen_shift_end_time = $('#gen_shift_end_time').val();

                // new variables of general shift associates
                var gen_shift_user_arr = $('#genshiftusername option:selected').toArray().map(item => item.value);
                var gen_shift_user_arr = JSON.stringify(gen_shift_user_arr);

                var data = "gen_shift_start_time=" + gen_shift_start_time + "&gen_shift_end_time=" + gen_shift_end_time + "&gen_shift_user_arr=" + gen_shift_user_arr + "&fixdate=" + fixdate + "&type=gen_shift_create_new";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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


























            // [[ajax for multi shift submit - use this in general]]


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
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        var data1 = "fixdate=" + fixdate + "&type=shift_cards";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "shift_plan_next_day_fm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                var obj = jQuery.parseJSON(res1);
                                $('.shift1_assign_unassign').html(obj.shift1);
                                $('.shift2_assign_unassign').html(obj.shift2);
                            }
                        });
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
































            // --------- processwise - general shift---------
            $('#processes').on('change', function() {
                var model_name = $('#processes').val();
                var fixdate = $('#fixdate').val();
                var data = "model_name=" + model_name + "&type=model_fetch";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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

                        var data1 = "fixdate=" + fixdate + "&type=shift_cards";
                        console.log(data1);
                        $.ajax({
                            type: "POST",
                            url: "shift_plan_next_day_fm_scripts.php",
                            data: data1,
                            success: function(res1) {
                                console.log(res1);
                                $('.shift_assign_unassign').html(res1);
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

            $('#model_processes').on('change', function() {
                var model_name = $('#processes').val();
                var processes = $('#model_processes').val();

                var data = "model_name=" + model_name + "&processes=" + processes + "&type=get_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#process_parts').html(res);
                    }
                });
            });

            $('#process_parts').on('change', function() {
                var parts = $('#process_parts').val();

                var data = "parts=" + parts + "&type=unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#unit_time_details').html(res);
                    }
                });
            });

            $('#process_users').on('change', function() {
                var model_name = $('#processes').val();
                var process_start_time = $('#process_start_time').val();
                var data = "model_name=" + model_name + "&process_start_time=" + process_start_time + "&type=process_users";
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                var data = "model_name=" + model_name + "&type=model_fetch";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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

            $('#model_processes1').on('change', function() {
                var model_name = $('#processes1').val();
                var processes = $('#model_processes1').val();

                var data = "model_name=" + model_name + "&processes=" + processes + "&type=get_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#process_parts1').html(res);
                    }
                });
            });

            $('#process_parts1').on('change', function() {
                var parts = $('#process_parts1').val();

                var data = "parts=" + parts + "&type=unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                var data = "model_name=" + model_name + "&type=model_fetch";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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

            $('#model_processes2').on('change', function() {
                var model_name = $('#processes2').val();
                var processes = $('#model_processes2').val();

                var data = "model_name=" + model_name + "&processes=" + processes + "&type=get_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    dateType: 'JSON',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#process_parts2').html(res);
                    }
                });
            });

            $('#process_parts2').on('change', function() {
                var parts = $('#process_parts2').val();

                var data = "parts=" + parts + "&type=unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

                var data = "workprocesses=" + workprocesses + "&type=work_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#work_parts').html(res);
                    }
                });
            });

            $('#work_parts').on('change', function() {
                var work_parts = $('#work_parts').val();

                var data = "work_parts=" + work_parts + "&type=work_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

                var data = "workprocesses=" + workprocesses + "&type=work_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#work_parts1').html(res);
                    }
                });
            });

            $('#work_parts1').on('change', function() {
                var work_parts = $('#work_parts1').val();

                var data = "work_parts=" + work_parts + "&type=work_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

                var data = "workprocesses=" + workprocesses + "&type=work_parts";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#work_parts2').html(res);
                    }
                });
            });

            $('#work_parts2').on('change', function() {
                var work_parts = $('#work_parts2').val();

                var data = "work_parts=" + work_parts + "&type=work_unit_time";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

            // ------------material wise data fetch -general-start-----------
            $('#material').on('change', function() {
                var material = $('#material').val();

                var data = "material=" + material + "&type=material_models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_model').html(res);
                    }
                });
                $('#material_process').empty();
                $('#material_username').empty();
                $('#material_start_time').val('00:00');
                $('#material_unit_time').empty();
                $('#material_user_count').val('');
            });

            $('#material_model').on('change', function() {
                var material_model = $('#material_model').val();

                var data = "material_model=" + material_model + "&type=material_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

            // ------------material wise data fetch -shift 1-start-----------
            $('#material1').on('change', function() {
                var material = $('#material1').val();

                var data = "material=" + material + "&type=material_models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_model1').html(res);
                    }
                });
                $('#material_process1').empty();
                $('#material_username1').empty();
                $('#material_start_time1').val('00:00');
                $('#material_unit_time1').empty();
                $('#material_user_count1').val('');
            });

            $('#material_model1').on('change', function() {
                var material_model = $('#material_model1').val();

                var data = "material_model=" + material_model + "&type=material_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

            // ------------material wise data fetch -shift 2-start-----------
            $('#material2').on('change', function() {
                var material = $('#material2').val();

                var data = "material=" + material + "&type=material_models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#material_model2').html(res);
                    }
                });
                $('#material_process2').empty();
                $('#material_username2').empty();
                $('#material_start_time2').val('00:00');
                $('#material_unit_time2').empty();
                $('#material_user_count2').val('');
            });

            $('#material_model2').on('change', function() {
                var material_model = $('#material_model2').val();

                var data = "material_model=" + material_model + "&type=material_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

                $('#team_model').empty();
                $('#team_process').empty();
                $('#team_unit_time').empty();
                $('#team_count').val('');
                $('#team_start_time').val('00:00');
                $('#team_username').empty();

                var data = "team=" + team + "&type=team_model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_model').html(res);
                    }
                });
            });

            $('#team_model').on('change', function() {
                var team_model = $('#team_model').val();

                var data = "team_model=" + team_model + "&type=team_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

                $('#team_model1').empty();
                $('#team_process1').empty();
                $('#team_unit_time1').empty();
                $('#team_count1').val('');
                $('#team_start_time1').val('00:00');
                $('#team_username1').empty();

                var data = "team=" + team + "&type=team_model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_model1').html(res);
                    }
                });
            });

            $('#team_model1').on('change', function() {
                var team_model = $('#team_model1').val();

                var data = "team_model=" + team_model + "&type=team_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

                $('#team_model2').empty();
                $('#team_process2').empty();
                $('#team_unit_time2').empty();
                $('#team_count2').val('');
                $('#team_start_time2').val('00:00');
                $('#team_username2').empty();

                var data = "team=" + team + "&type=team_model";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#team_model2').html(res);
                    }
                });
            });

            $('#team_model2').on('change', function() {
                var team_model = $('#team_model2').val();

                var data = "team_model=" + team_model + "&type=team_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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

                var data = "models=" + models + "&type=models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_process').html(res);
                    }
                });
            });

            $('#model_process').on('change', function() {
                var model_process = $('#model_process').val();

                var data = "model_process=" + model_process + "&type=model_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model').val('').trigger('change');
                        $('#model_process').val('').trigger('change');
                        $('#model_username').val('').trigger('change');
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

                var data = "models=" + models + "&type=models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_process1').html(res);
                    }
                });
            });

            $('#model_process1').on('change', function() {
                var model_process = $('#model_process1').val();

                var data = "model_process=" + model_process + "&type=model_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model1').val('').trigger('change');
                        $('#model_process1').val('').trigger('change');
                        $('#model_username1').val('').trigger('change');
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

                var data = "models=" + models + "&type=models";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model_process2').html(res);
                    }
                });
            });

            $('#model_process2').on('change', function() {
                var model_process = $('#model_process2').val();

                var data = "model_process=" + model_process + "&type=model_process";
                console.log(data);
                $.ajax({
                    type: "POST",
                    url: "shift_plan_next_day_fm_scripts.php",
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
                    url: "shift_plan_next_day_fm_scripts.php",
                    data: data,
                    success: function(res) {
                        console.log(res);
                        $('#model2').val('').trigger('change');
                        $('#model_process2').val('').trigger('change');
                        $('#model_username2').val('').trigger('change');
                    }
                });

                // console.log(model);
                // console.log(model_process);
                // console.log(model_username_arr);

            });
            // ---------model wise shift 2 end---------
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
                    redTo: 30,
                    yellowFrom: 30,
                    yellowTo: 60,
                    greenFrom: 60,
                    greenTo: 90,
                    majorTicks: 40,
                    max: 90
                };

                var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

                chart.draw(data, options);

                // setInterval(function() {
                //   data.setValue(0, 1, 40 + Math.round(60 * Math.random()));
                //   chart.draw(data, options);
                // }, 3000);
                // setInterval(function() {
                //   data.setValue(1, 1, 40 + Math.round(60 * Math.random()));
                //   chart.draw(data, options);
                // }, 3000);
            }
        </script>


        <!-- batch plan counter -->


    </div><!-- row close -->
</div><!-- container close -->
</div><!-- app-main__inner close -->
</div><!-- app-main__outer close -->

<?php include 'footer.php'; ?>





<!-- !! scripts for limits-->


<!-- 1 shift type general shift associates -->

<script>
    $(document).ready(function() {
        var last_valid_selection1 = null;

        // Initialize with zero value or set it directly in the HTML as shown above
        // $('#gen_no_of_associates').val(0);

        // Handle change in number of associates
        $('#gen_no_of_associates').on('input', function() {
            // Since this input only affects conditions but doesn't need immediate action,
            // there's no need to place logic directly here for now.
        });

        // Handle input changes for the username field, dynamically using the number of associates
        $('#genshiftusername').on('input', function() {
            var value1 = parseInt($('#gen_no_of_associates').val(), 10); // Ensure it's an integer
            var currentValue1 = $(this).val();
            if (currentValue1.length > value1) {
                $(this).val(last_valid_selection1);
            } else {
                last_valid_selection1 = currentValue1;
            }
        });
    });
</script>




<!-- 6 shift 1 no of associates -->

<script>
    $(document).ready(function() {
        var last_valid_selection6 = null;
        $('#s1noofassoc').on('input', function() {});
        $('#shift1username').on('input', function() {
            var value6 = parseInt($('#s1noofassoc').val(), 10);
            var currentValue6 = $(this).val();
            if (currentValue6.length > value6) {
                $(this).val(last_valid_selection6);
            } else {
                last_valid_selection6 = currentValue6;
            }
        });
    });
</script>






<!-- 7 shift 2 no of associates -->

<script>
    $(document).ready(function() {
        var last_valid_selection7 = null;
        $('#s2noofassoc').on('input', function() {});
        $('#shift2username').on('input', function() {
            var value7 = parseInt($('#s2noofassoc').val(), 10);
            var currentValue7 = $(this).val();
            if (currentValue7.length > value7) {
                $(this).val(last_valid_selection7);
            } else {
                last_valid_selection7 = currentValue7;
            }
        });
    });
</script>