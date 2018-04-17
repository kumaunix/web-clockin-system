<?php
require 'db.php';
$id = $_GET['id'];
$full_name = $_GET['name'];
$dept = $_GET['dept'];
$rate = $_GET['vahe'];
$office = $_GET['area'];

$clock_in_time = date_default_timezone_get();
$now = date('Y-m-d H:i:s',strtotime($clock_in_time));

//Update users to On Duty after clock-in
$in = ("UPDATE users SET in_or_out=1, punchin='".$now."' where id=$id ");
$clockin = mysqli_query($conn, $in) or die ("Error Clocking In Insert into user status :".mysqli_error($conn));

//IP address Restriction
//include 'hanoi_ip.php';
//include 'kuma_ip.php';
include 'black_ip.php';
// If clockin from Hanoi IP address
if($add1_ip){
	//Determine location base on IP address
	$location = "Office 2 Location";
	
//	$date = new DateTime("now", new DateTimeZone('Asia/Ho_Chi_Minh') ); 
//	echo $date->format('Y-m-d H:i:s');
	
	//Convert server time (JST) to Hanoi time JST-2
	$time = date('H:i:s', strtotime($now));     
	$today = date('Y-m-d', strtotime($now));  
	
	sscanf($time,"%d:%d:%d ", $h, $m, $s);
	$time_in_secs = ($h*3600) + ($m*60) + $s;
	$time_offset = $time_in_secs - 7200;
	$hanoi_time = gmdate('H:i:s', $time_offset);
	$hanoi_time_clock_in = $today.' '.$hanoi_time;  //Date and Time now in Hanoi
		
	//Hanoi date and time determined
	$hanoi_current_time = date('H:i:s', strtotime($hanoi_time_clock_in)); // today in Hanoi
	$hanoi_current_day = date('Y-m-d', strtotime($hanoi_time_clock_in));  // current time in Hanoi
	
	// Please update the following to the usuall time slot clock in. 
	$am_5_50 = '05:50:00';
	$am_6_20 = '06:20:00'; 
	$am_6_50 = '06:50:00';
	$am_7_30 = '07:30:00';
	$am_8_00 = '08:00:00';
	$am_8_30 = '08:30:00';
	$am_9_00 = '09:00:00';
	$am_10_00 = '10:00:00';
	$noon = '13:00:00';
	
		if ($hanoi_current_time <= '06:50:59' && $hanoi_current_time > '06:20:59'){    // If clockin between 6:21 am - 6:50 am
			$hanoi_right_time_clockin = $today.' '.$am_6_50;                                    // Time will be recorded as 6:50 am
			
	    }elseif($hanoi_current_time <= '07:30:59' && $hanoi_current_time > '06:50:59'){ 	    // Late clockin after 6:50 am
  			$hanoi_right_time_clockin = $today.' '.$am_7_30;								// Time will be recorded as 7:30 am
  			    																			 
		}elseif ($hanoi_current_time <= '08:00:59' && $hanoi_current_time > '07:30:59'){    // If clockin between 7:30 - 8:00 am  
			$hanoi_right_time_clockin = $today.' '.$am_8_00;									// Time will be recorded as 8:00 am
			
		}elseif ($hanoi_current_time <= '08:30:59' && $hanoi_current_time > '08:00:59'){    // Late clockin after 8:00 am
			$hanoi_right_time_clockin = $today.' '.$am_8_30;									// Time will be recorded as 8:30 am			
		
		}elseif ($hanoi_current_time <= '09:00:59' && $hanoi_current_time >= '08:30:59'){   // If clockin between 8:30 am - 9:00 am
			$hanoi_right_time_clockin = $today.' '.$am_9_00;									// Time will be recorded as 9:00 am			
		
		}elseif ($hanoi_current_time <= '10:00:59' && $hanoi_current_time >= '09:30:59'){   // If clockin between 9:30 am - 10:00 am
			$hanoi_right_time_clockin = $today.' '.$am_10_00;									// Time will be recorded as 10:00 am	
		}else{   
		
			}
	
		/*if ($hanoi_current_time <= $am_9_00 && $hanoi_current_time >= '08:30:00'){
			$hanoi_right_time_clockin = $today.' '.$am_8_30;
		}*/
	
	$hanoi_clock_in=("Insert into timecard (`employment_id`,`full_name`,`clockin`,`actual_time_in`,`dept`,`rank_rate`, `location`) 
			Values ('$id', '$full_name', '$hanoi_right_time_clockin', '$hanoi_time_clock_in', '$dept', '$rate', '$office')");
	$hanoi_clock_input = mysqli_query($conn, $hanoi_clock_in) or die ("Error Clocking In Into timesheet from Hanoi :".mysqli_error($conn));

// Clocking in from Kumamoto Office	
}elseif ($add2_ip || $myip){
	$location = "Office Location 1";
	//Japan time and date
	$jp_time = date('H:i:s', strtotime($now));     
	$jp_today = date('Y-m-d', strtotime($now)); 
	
	
	$am_5_50  = '05:50:00';
	$am_6_20  = '06:20:00'; // Late clockin
	$am_6_50  = '06:50:00';
	$am_7_30  = '07:30:00';
	$am_8_00  = '08:00:00';
	$am_8_30  = '08:30:00';
	$am_9_00  = '09:00:00';
	$am_10_00 = '10:00:00';
	$am_11_00 = '11:00:00';
	$pm_13_00 = '13:00:00';
	$pm_15_00 = '15:00:00';
	
	$noon = '15:00:00';
	
		if ($jp_time <= '05:50:59' && $jp_time >= '05:20:00'){  	 // if clockin between 5:30 am - 5:50 am
			$jp_right_time_clockin = $jp_today.' '.$am_5_50;  									// time will be recorded as 5:50 am
			
  		}elseif($jp_time <= '06:20:59' && $jp_time > '05:50:59'){ 	 // Late clockin after 5:50 am
  			$jp_right_time_clockin = $jp_today.' '.$am_6_20;									// Time will be recorded as 6:20 am
  			   
		}elseif ($jp_time <= '06:50:59' && $jp_time > '06:20:59'){    // If clockin between 6:21 am - 6:50 am
			$jp_right_time_clockin = $jp_today.' '.$am_6_50;                                    // Time will be recorded as 6:50 am
			
	    }elseif($jp_time <= '07:30:59' && $jp_time > '06:50:59'){ 	  // Late clockin after 6:50 am
  			$jp_right_time_clockin = $jp_today.' '.$am_7_30;								// Time will be recorded as 7:30 am
  			    																			 
		}elseif ($jp_time <= '08:00:59' && $jp_time > '07:30:59'){    // If clockin between 7:30 - 8:00 am  
			$jp_right_time_clockin = $jp_today.' '.$am_8_00;									// Time will be recorded as 8:00 am
			
		}elseif ($jp_time <= '08:30:59' && $jp_time > '08:00:59'){    // Late clockin after 8:00 am
			$jp_right_time_clockin = $jp_today.' '.$am_8_30;									// Time will be recorded as 8:30 am			
		
		}elseif ($jp_time <= '09:00:59' && $jp_time >= '08:30:59'){   // If clockin between 8:30 am - 9:00 am
			$jp_right_time_clockin = $jp_today.' '.$am_9_00;									// Time will be recorded as 9:00 am			
		
		}elseif ($jp_time <= '09:30:59' && $jp_time >= '08:31:00'){   // If clockin between 8:30 am - 9:00 am
			$jp_right_time_clockin = $jp_today.' 09:30:00';									// Time will be recorded as 9:00 am			
		
		}elseif ($jp_time <= '10:00:59' && $jp_time >= '09:30:59'){   // If clockin between 8:30 am - 9:00 am
			$jp_right_time_clockin = $jp_today.' '.$am_10_00;	
			
		}elseif ($jp_time <= '11:00:59' && $jp_time >= '10:30:59'){   // If clockin between 8:30 am - 9:00 am
			$jp_right_time_clockin = $jp_today.' '.$am_11_00;	
			
		}elseif ($jp_time <= '13:00:59' && $jp_time >= '11:00:59'){   // If clockin between 8:30 am - 9:00 am
			$jp_right_time_clockin = $jp_today.' '.$pm_13_00;	
			
		}elseif ($jp_time <= '15:00:59' && $jp_time >= '14:30:59'){   // If clockin between 8:30 am - 9:00 am
			$jp_right_time_clockin = $jp_today.' '.$pm_13_00;	
			
		}else{   // Testing.............
			$jp_right_time_clockin = $jp_today.' '.$noon;	
		
			}
	
	//check if the person already clockin
	$sql_chk = ("SELECT clockin, actual_time_in FROM timecard WHERE employment_id ='".$id."' AND date(clockin)=CURDATE()");
	$result_chk = mysqli_query($conn, $sql_chk) or die ("Error Checking Clocking :".mysqli_error($sql_chk));
	$num_of_row_affected = mysqli_num_rows($result_chk);
	if($num_of_row_affected == 1){
		echo '<br><br><b><center><font color="red">You are already Logged In. Please try again after this message.</font> <br>The system will reset your status</b></center>';
		$in = ("UPDATE users SET in_or_out=1, punchin='".$now."' where id=$id ");
		$clockin = mysqli_query($conn, $in) or die ("Error Clocking In Insert into user status :".mysqli_error($conn));
		echo '<META HTTP-EQUIV=REFRESH CONTENT="3; '.$_SERVER["HTTP_REFERER"].'">';
	}else{
		$clock_in=("Insert into timecard (`employment_id`,`full_name`,`clockin`,`actual_time_in`,`dept`,`rank_rate`, `location`) 
			Values ('$id','$full_name','$jp_right_time_clockin','$now','$dept','$rate', '$office')");
		$clock_input = mysqli_query($conn, $clock_in) or die ("Error Clocking In Into timesheet:".mysqli_error($conn));
	
		echo "<br><br><br><br><center><img src='/unix/img/clok1.gif' height=50 width=50> <h3> $full_name Clocking In from <font color='red'>$location</font> </h3></center>";
		echo '<META HTTP-EQUIV=REFRESH CONTENT="2; '.$_SERVER["HTTP_REFERER"].'">';
	}
		
	
}


?>
