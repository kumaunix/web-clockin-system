<?php
require 'db.php';
$id = $_GET['id'];
$full_name = $_GET['name'];
$dept = $_GET['dept'];

// Set users status to offline status
$out_sql = ("Update users set in_or_out=0 where id=$id ");
$clockout = mysqli_query($conn, $out_sql) or die ("Error Clocking OUT Update user status :".mysqli_error($conn));

//Get time now in Japan time
$clock_out_time = date_default_timezone_get();
$now = date('Y-m-d H:i:s',strtotime($clock_out_time));

//Get the punchin in time from the user temporary column punchin
$sql = ("SELECT punchin FROM users where id=$id");
$query=mysqli_query($conn, $sql) or die ("ERROR : ".mysqli_error($conn));
$timing = mysqli_fetch_assoc($query);
$punch=$timing['punchin'];
$punchin = date('Y-m-d H:i:s',strtotime($punch));

// IP addresses Restrictions

require 'black_ip.php';
// Clock-out from Hanoi IP address 
if($add_ip){
	//Determine location base on IP address
	$location = "Office location 1";
	
	//Convert server time (JST) to Hanoi time JST-2
	$time = date('H:i:s', strtotime($now));
	$today = date('Y-m-d', strtotime($now));
	sscanf($time,"%d:%d:%d ", $h, $m, $s);
	$time_in_secs = ($h*3600) + ($m*60) + $s;
	$time_offset = $time_in_secs - 7200;
	$hanoi_time = gmdate('H:i:s', $time_offset);
	$hanoi_time_actual_out = $today.' '.$hanoi_time;
	
	//Calculation of the actual total time
	$start = new DateTime($punchin);
	$finish = new DateTime($hanoi_time_clock_out);
	$hour = $start->diff($finish);
	$hrs = $hour->format("%H:%i:%s");
	sscanf($hrs,"%d:%d:%d ", $h, $m, $s);
	$d = ($h*3600) + ($m*60) + $s;
	$total = gmdate('H:i:s', $d);
	
	$pm_12_10 = '12:10:00';
	$pm_17_10 = '17:10:00';
	
	if($hanoi_time >='12:10:59' && $hanoi_time <= '13:00:00'){
		$hanoi_clockout_time = $today.' '.$pm_12_10;
	 
	}elseif($hanoi_time >='17:10:00' && $hanoi_time <= '18:00:00'){
		$hanoi_clockout_time = $today.' '.$pm_17_10;
	}
		
	// insert into database
	$clock_out = ("UPDATE timecard SET clockout='".$hanoi_clockout_time."', actual_time_out='".$hanoi_time_actual_out."', actual_total='".$total."' where id='".$id."' AND actual_time_in='".$punchin."'");
	$clock_input = mysqli_query($conn, $clock_out) or die ("Error Clocking In Into timesheet:".mysqli_error($conn));
	
	// Calculating total fixed hours
	$clockout_hanoi = date('H:i:s', strtotime($hanoi_clockout_time));
	
	$reg_sql = ("SELECT clockin from timecard where id='".$id."' AND actual_time_in='".$punchin."' ");
	$reg_query = mysqli_query($conn, $reg_sql) or die ("ERROR computing regular hours: ". mysqli_error($conn));
	$reg_hanoi = mysqli_fetch_assoc($reg_query);
	$clockin_hanoi = date('H:i:s', strtotime($reg_hanoi['clockin']));
	
	if ($clockin_hanoi == '13:00:00' && $clockout_hanoi == '17:10:00'){
		$total_hanoi = '04:00:00';
	}elseif ($clockin_hanoi == '08:00:00' && $clockout_hanoi == '12:10:00'){
		$total_hanoi = 	'04:00:00';
	}elseif ($clockin_hanoi == '08:00:00' && $clockout_hanoi == '17:10:00'){
		$total_hanoi = 	'08:00:00';
	}
	
	$fix_sql =("UPDATE timecard SET total='".$total_hanoi."' where id='".$id."'");
	$fix_query = mysqli_query($conn, $fix_sql) or die ("ERROR inserting fix hours total: ".mysqli_error($conn));
	
	
}elseif ($add1_ip){
	//If Kumamoto Office IP address is detected.
	$location = "Office 2";
	$clock_out_time2 = date_default_timezone_get();
	$now2 = date('Y-m-d H:i:s',strtotime($clock_out_time2));
	// calculation of the actual_total time
	$start = new DateTime($punchin);
	$finish = new DateTime($now2);
	$hour = $start->diff($finish);
	$hrs = $hour->format("%H:%i:%s");
	sscanf($hrs,"%d:%d:%d ", $h, $m, $s);
	$d = ($h*3600) + ($m*60) + $s;
	$actual_hours = gmdate('H:i:s', $d);
	$jp_today = date('Y-m-d', strtotime($now2));
	$jp_time = date('H:i:s', strtotime($now2));
	//$jp_time = '17:12:11';                                  // for testing Only
	
	// determined the clockout time
	$pm_12_10 = '12:10:00';
	$pm_15_00 = '15:00:00';
	$pm_17_10 = '17:10:00';
	$pm_18_20 = '18:20:00';
	$pm_19_20 = '19:20:00';
	$pm_21_30 = '21:30:00';
	$pm_16_10 = '16:10:00';
	
	$pm_test = '15:00:00';
	
	if($jp_time >='12:10:00' && $jp_time <= '13:00:00'){		  //clockout right after lunch @ 12:10 pm
		$jp_clockout_time = $jp_today.' '.$pm_12_10;
	}elseif($jp_time >='15:00:00' && $jp_time <= '15:30:59'){     //clockout right after work @ 3:00 pm
		$jp_clockout_time = $jp_today.' '.$pm_15_00;
	}elseif($jp_time >='17:10:00' && $jp_time <= '17:30:59'){     //clockout right after work @ 5:10 pm
		$jp_clockout_time = $jp_today.' '.$pm_17_10;
	}elseif($jp_time >='18:10:00' && $jp_time <= '18:50:59'){     //clockout right after work @ 5:10 pm
		$jp_clockout_time = $jp_today.' '.$pm_18_20;
	}elseif($jp_time >='19:20:00' && $jp_time <= '19:50:59'){     //clockout right after work @ 7:20 pm
		$jp_clockout_time = $jp_today.' '.$pm_19_20;
	}elseif($jp_time >='21:30:00' && $jp_time <= '22:00:59'){     //clockout right after work @ 9:30 pm
		$jp_clockout_time = $jp_today.' '.$pm_21_30;
	}elseif($jp_time >='14:00:00' && $jp_time <= '14:30:59'){     //clockout right after work @ 2:00 pm
		$jp_clockout_time = $jp_today.'14:00:00';
	}elseif($jp_time >='10:00:00' && $jp_time <= '10:30:59'){     //clockout right after work @ 10:00 am
		$jp_clockout_time = $jp_today.'10:00:00';
	}elseif($jp_time >='11:00:00' && $jp_time <= '11:30:59'){     //clockout right after work @ 11:00 am
		$jp_clockout_time = $jp_today.'11:00:00';
	}elseif($jp_time >='16:10:00' && $jp_time <= '16:30:59'){     //clockout right after work @ 9:30 pm
		$jp_clockout_time = $jp_today.'　'.$pm_16_10;
	}else{
		$jp_clockout_time = $jp_today.' '.$pm_test;
	}
	
	
	// Insert clockout, actual_time_out and actual_total to database
	$clock_out=("UPDATE timecard SET clockout='".$jp_clockout_time."', actual_time_out='".$now2."', actual_total='".$actual_hours."' where employment_id='".$id."' AND actual_time_in='".$punchin."'");
	$clock_input = mysqli_query($conn, $clock_out) or die ("Error Clocking In Into timesheet:".mysqli_error($conn));
	
	// Calculating total fixed hours
	
	
	$reg_sql = ("SELECT clockin, clockout, rank_rate, id from timecard where employment_id='".$id."' AND actual_time_in='".$punchin."' ");
	$reg_query = mysqli_query($conn, $reg_sql) or die ("ERROR computing regular hours: ". mysqli_error($conn));
	$reg_jp = mysqli_fetch_assoc($reg_query);
	$clockin_jp = date('H:i:s', strtotime($reg_jp['clockin']));
	$clockout_jp = date('H:i:s', strtotime($reg_jp['clockout']));
	$pay_rate = $reg_jp['rank_rate'];
	$log_id = $reg_jp['id'];
	if ($clockin_jp == '05:50:00' && $clockout_jp == '10:00:00'){       // 5:50 am - 10:00 am
		$total_jp = '04:00:00';	
		$ot_jp = '00:00:00';										// 4 hours work
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '12:10:00'){  // 5:50 am - lunch
		$total_jp = '06:00:00';	
		$ot_jp = '00:00:00';										// 6 hours work
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '15:00:00'){  // 5:50 am - 3:00 pm
		$total_jp = '08:00:00';
		$ot_jp = '00:00:00';												// 8 hours work
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '17:10:00'){  // 5:50 am - 5:10 pm
		$total_jp = '08:00:00';											// 10 hours work
		$ot_jp = '02:00:00';											// w/ 2 hours overtime
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '19:20:00'){  // 5:50 am - 7:20 pm
		$total_jp = '08:00:00';											// 12 hours work
		$ot_jp = '04:00:00';											// w/ 4 hours overtime
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '21:30:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '06:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '06:50:00' && $clockout_jp == '17:10:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '01:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '06:50:00' && $clockout_jp == '15:00:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '00:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '06:50:00' && $clockout_jp == '18:20:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '02:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '06:50:00' && $clockout_jp == '19:20:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '03:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '12:10:00'){
		$total_jp = '04:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '15:00:00'){
		$total_jp = '06:00:00';
		$ot_jp = '00:00:00';	
	}elseif ($clockin_jp == '08:30:00' && $clockout_jp == '17:10:00'){
		$total_jp = '07:30:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '08:30:00' && $clockout_jp == '15:00:00'){
		$total_jp = '05:30:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '17:10:00'){
		$total_jp = '08:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '19:20:00'){
		$total_jp = '08:00:00';
		$ot_jp = '02:00:00';
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '21:30:00'){
		$total_jp = '08:00:00';
		$ot_jp = '04:00:00';
	}elseif ($clockin_jp == '09:00:00' && $clockout_jp == '17:10:00'){
		$total_jp = '07:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '09:00:00' && $clockout_jp == '15:00:00'){
		$total_jp = '06:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '10:00:00' && $clockout_jp == '17:10:00'){
		$total_jp = '06:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '10:00:00' && $clockout_jp == '19:20:00'){
		$total_jp = '08:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '10:00:00' && $clockout_jp == '21:30:00'){
		$total_jp = '08:00:00';
		$ot_jp = '02:00:00';
	}elseif ($clockin_jp == '11:00:00' && $clockout_jp == '17:10:00'){
		$total_jp = '05:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '13:00:00' && $clockout_jp =='17:10:00'){
		$total_jp = '04:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '09:00:00' && $clockout_jp =='19:10:00'){
		$total_jp = '07:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '09:30:00' && $clockout_jp =='16:10:00'){
		$total_jp = '05:30:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '09:30:00' && $clockout_jp =='17:10:00'){
		$total_jp = '06:30:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '15:00:00' && $clockout_jp =='17:10:00'){
		$total_jp = '02:00:00';
		$ot_jp = '00:00:00';
	}
	// ***** Calculating Payroll of each employee after clockout according to payrate *****
	sscanf($total_jp,"%d:%d:%d ", $h, $m, $s);
	sscanf($ot_jp,"%d:%d:%d ", $hr, $min, $sec);
	if($hr > 0){// If there was overtime
		$p   = $h * $pay_rate;         // regular hours 8 X rate please update this three lines to the pay rate
		$ot1 = $hr * $pay_rate;      // overtime X rate (100%)
		$ot2 = $pay_rate * 0.5 * $hr;   // overtime X rate (50%)
		if($m > 0){
			$min_half = $pay_rate * 0.5;
		}	
		$paycheck_for_the_day =$p + $ot1 + $ot2 + $min_half; // regular hours + 150% overtime
	}elseif ($hr == 0){// No overtime 
		if($m > 0){
			$min_half = $pay_rate * 0.5;
		}	
		$paycheck = $h * $pay_rate;
		$paycheck_for_the_day = $paycheck + $min_half;
	} 
	
	$fix_sql =("UPDATE timecard SET total='".$total_jp."', overtime='".$ot_jp."', payroll='".$paycheck_for_the_day."'  where id='".$log_id."'");
	$fix_query = mysqli_query($conn, $fix_sql) or die ("ERROR inserting fix hours total: ".mysqli_error($conn));
	
}// kuma_ip ends here !!!!
elseif ($myip){
	//If Kumamoto Office IP address is detected.
	$location = "Home";
	$clock_out_time2 = date_default_timezone_get();
	$now2 = date('Y-m-d H:i:s',strtotime($clock_out_time2));
	// calculation of the actual_total time
	$start = new DateTime($punchin);
	$finish = new DateTime($now2);
	$hour = $start->diff($finish);
	$hrs = $hour->format("%H:%i:%s");
	sscanf($hrs,"%d:%d:%d ", $h, $m, $s);
	$d = ($h*3600) + ($m*60) + $s;
	$actual_hours = gmdate('H:i:s', $d);
	$jp_today = date('Y-m-d', strtotime($now2));
	$jp_time = date('H:i:s', strtotime($now2));
	//$jp_time = '17:12:11';                                  // for testing Only
	
	// determined the clockout time
	$pm_12_10 = '12:10:00';
	$pm_15_00 = '15:00:00';
	$pm_17_10 = '17:10:00';
	$pm_18_20 = '18:20:00';
	$pm_19_20 = '19:20:00';
	$pm_21_30 = '21:30:00';
	$pm_16_10 = '16:10:00';
	
	$pm_test = '15:00:00';
	
	if($jp_time >='12:10:00' && $jp_time <= '13:00:00'){		  //clockout right after lunch @ 12:10 pm
		$jp_clockout_time = $jp_today.' '.$pm_12_10;
	}elseif($jp_time >='15:00:00' && $jp_time <= '15:30:59'){     //clockout right after work @ 3:00 pm
		$jp_clockout_time = $jp_today.' '.$pm_15_00;
	}elseif($jp_time >='17:10:00' && $jp_time <= '17:30:59'){     //clockout right after work @ 5:10 pm
		$jp_clockout_time = $jp_today.' '.$pm_17_10;
	}elseif($jp_time >='18:10:00' && $jp_time <= '18:50:59'){     //clockout right after work @ 5:10 pm
		$jp_clockout_time = $jp_today.' '.$pm_18_20;
	}elseif($jp_time >='19:20:00' && $jp_time <= '19:50:59'){     //clockout right after work @ 7:20 pm
		$jp_clockout_time = $jp_today.' '.$pm_19_20;
	}elseif($jp_time >='21:30:00' && $jp_time <= '22:00:59'){     //clockout right after work @ 9:30 pm
		$jp_clockout_time = $jp_today.' '.$pm_21_30;
	}elseif($jp_time >='14:00:00' && $jp_time <= '14:30:59'){     //clockout right after work @ 2:00 pm
		$jp_clockout_time = $jp_today.'14:00:00';
	}elseif($jp_time >='10:00:00' && $jp_time <= '10:30:59'){     //clockout right after work @ 10:00 am
		$jp_clockout_time = $jp_today.'10:00:00';
	}elseif($jp_time >='11:00:00' && $jp_time <= '11:30:59'){     //clockout right after work @ 11:00 am
		$jp_clockout_time = $jp_today.'11:00:00';
	}elseif($jp_time >='16:10:00' && $jp_time <= '16:30:59'){     //clockout right after work @ 9:30 pm
		$jp_clockout_time = $jp_today.'　'.$pm_16_10;
	}else{
		$jp_clockout_time = $jp_today.' '.$pm_test;
	}
	
	
	// Insert clockout, actual_time_out and actual_total to database
	$clock_out=("UPDATE timecard SET clockout='".$jp_clockout_time."', actual_time_out='".$now2."', actual_total='".$actual_hours."' where employment_id='".$id."' AND actual_time_in='".$punchin."'");
	$clock_input = mysqli_query($conn, $clock_out) or die ("Error Clocking In Into timesheet:".mysqli_error($conn));
	
	// Calculating total fixed hours
	
	
	$reg_sql = ("SELECT clockin, clockout, rank_rate, id from timecard where employment_id='".$id."' AND actual_time_in='".$punchin."' ");
	$reg_query = mysqli_query($conn, $reg_sql) or die ("ERROR computing regular hours: ". mysqli_error($conn));
	$reg_jp = mysqli_fetch_assoc($reg_query);
	$clockin_jp = date('H:i:s', strtotime($reg_jp['clockin']));
	$clockout_jp = date('H:i:s', strtotime($reg_jp['clockout']));
	$pay_rate = $reg_jp['rank_rate'];
	$log_id = $reg_jp['id'];
	if ($clockin_jp == '05:50:00' && $clockout_jp == '10:00:00'){       // 5:50 am - 10:00 am
		$total_jp = '04:00:00';	
		$ot_jp = '00:00:00';										// 4 hours work
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '12:10:00'){  // 5:50 am - lunch
		$total_jp = '06:00:00';	
		$ot_jp = '00:00:00';										// 6 hours work
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '15:00:00'){  // 5:50 am - 3:00 pm
		$total_jp = '08:00:00';
		$ot_jp = '00:00:00';												// 8 hours work
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '17:10:00'){  // 5:50 am - 5:10 pm
		$total_jp = '08:00:00';											// 10 hours work
		$ot_jp = '02:00:00';											// w/ 2 hours overtime
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '19:20:00'){  // 5:50 am - 7:20 pm
		$total_jp = '08:00:00';											// 12 hours work
		$ot_jp = '04:00:00';											// w/ 4 hours overtime
	}elseif ($clockin_jp == '05:50:00' && $clockout_jp == '21:30:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '06:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '06:50:00' && $clockout_jp == '17:10:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '01:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '06:50:00' && $clockout_jp == '15:00:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '00:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '06:50:00' && $clockout_jp == '18:20:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '02:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '06:50:00' && $clockout_jp == '19:20:00'){  // 5:50 am - 9:30 pm
		$total_jp = '08:00:00';											// 14 hours work
		$ot_jp = '03:00:00';											// w/ 6 hours overtime
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '12:10:00'){
		$total_jp = '04:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '15:00:00'){
		$total_jp = '06:00:00';
		$ot_jp = '00:00:00';	
	}elseif ($clockin_jp == '08:30:00' && $clockout_jp == '17:10:00'){
		$total_jp = '07:30:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '08:30:00' && $clockout_jp == '15:00:00'){
		$total_jp = '05:30:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '17:10:00'){
		$total_jp = '08:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '19:20:00'){
		$total_jp = '08:00:00';
		$ot_jp = '02:00:00';
	}elseif ($clockin_jp == '08:00:00' && $clockout_jp == '21:30:00'){
		$total_jp = '08:00:00';
		$ot_jp = '04:00:00';
	}elseif ($clockin_jp == '09:00:00' && $clockout_jp == '17:10:00'){
		$total_jp = '07:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '09:00:00' && $clockout_jp == '15:00:00'){
		$total_jp = '06:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '10:00:00' && $clockout_jp == '17:10:00'){
		$total_jp = '06:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '10:00:00' && $clockout_jp == '19:20:00'){
		$total_jp = '08:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '10:00:00' && $clockout_jp == '21:30:00'){
		$total_jp = '08:00:00';
		$ot_jp = '02:00:00';
	}elseif ($clockin_jp == '11:00:00' && $clockout_jp == '17:10:00'){
		$total_jp = '05:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '13:00:00' && $clockout_jp =='17:10:00'){
		$total_jp = '04:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '09:00:00' && $clockout_jp =='19:10:00'){
		$total_jp = '07:00:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '09:30:00' && $clockout_jp =='16:10:00'){
		$total_jp = '05:30:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '09:30:00' && $clockout_jp =='17:10:00'){
		$total_jp = '06:30:00';
		$ot_jp = '00:00:00';
	}elseif ($clockin_jp == '15:00:00' && $clockout_jp =='17:10:00'){
		$total_jp = '02:00:00';
		$ot_jp = '00:00:00';
	}
	// ***** Calculating Payroll of each employee after clockout according to payrate *****
	sscanf($total_jp,"%d:%d:%d ", $h, $m, $s);
	sscanf($ot_jp,"%d:%d:%d ", $hr, $min, $sec);
	if($hr > 0){// If there was overtime
		$p   = $h * $pay_rate;         // regular hours 8 X rate
		$ot1 = $hr * $pay_rate;      // overtime X rate (100%)
		$ot2 = $pay_rate * 0.5 * $hr;   // overtime X rate (50%)
		if($m > 0){
			$min_half = $pay_rate * 0.5;
		}	
		$paycheck_for_the_day =$p + $ot1 + $ot2 + $min_half; // regular hours + 150% overtime
	}elseif ($hr == 0){// No overtime 
		if($m > 0){
			$min_half = $pay_rate * 0.5;
		}	
		$paycheck = $h * $pay_rate;
		$paycheck_for_the_day = $paycheck + $min_half;
	} 
	
	$fix_sql =("UPDATE timecard SET total='".$total_jp."', overtime='".$ot_jp."', payroll='".$paycheck_for_the_day."'  where id='".$log_id."'");
	$fix_query = mysqli_query($conn, $fix_sql) or die ("ERROR inserting fix hours total: ".mysqli_error($conn));
	
}// kuma_ip ends here !!!!
echo "<br><br><br><br><center><img src='/img/clok1.gif' height=50 width=50> <h3> $full_name Clocking Out from <font color='red'>$location</font></h3></center>";
echo '<META HTTP-EQUIV=REFRESH CONTENT="2; '.$_SERVER["HTTP_REFERER"].'">';
?>
