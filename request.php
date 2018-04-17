<?php
//Access to DB
require 'db.php';

//Retrick IP address by ip addresses Only
require 'black_ip.php';

session_start();
if($_SESSION['user']!='admin' && $_SESSION['user']!='manager'){
	echo "You have not enough permission to access this page.";	
	exit;	
}

$id = $_GET['id'];
$name = $_GET['name'];
$department = $_GET['dept'];
$sick_available = $_GET['sa'];
?>
<html>
<head>
	
</head>	
<body>
	<br><br>
<center>
<form method="post" action="">
<?php echo "$name ($department) requesting for
 Vacation on :"; ?>	
	<input type="date" name="request"  />
	<input type="hidden" name="act100" value="run"/>
	<input type="submit" name="button" value="Approve" />&nbsp;
	<input type="reset" name="Reset" value="Reset" />
</form>	
<a href="../puleset/users.php" ><button>Cancel</button></a>
</center>	
</body>
	
</html>
<?
if (!empty($_POST['act100'])){
	$request    =  $_POST['request'];
	$id         =  $_GET['id'];
	$name       =  $_GET['name'];
	$department =  $_GET['dept'];
	$process    =  $_SESSION['fname'].' '.$_SESSION['lname'];
	
	// Retrive the Number of sick days available and used before today`s request
    $sick_day=("SELECT sick_day_available, sick_day_used, payrate, request_vacation, request_vacation1, request_vacation2 FROM users where id='".$id."' ");
	$sick_query=mysqli_query($conn, $sick_day) or die ("Retriving Sick Days :".mysqli_error($conn));
	$sick = mysqli_fetch_assoc($sick_query);
	$sick_available = $sick['sick_day_available'];
	$sick_used = $sick['sick_day_used'];
	$pay_rank = $sick['payrate'];
	$vacation_day1 = $sick['request_vacation'];
	$vacation_day2 = $sick['request_vacation1'];
	$vacation_day3 = $sick['request_vacation2'];
	
	
	// if sick day is still available, a pay for regular 8 hours is given
	if($sick_available > 0){
		$payleave = $pay_rank * 8;
	}
	       
	//final sick days used and final sick days available are calculated
	$final_sick_days_available = $sick_available - 1;
	$final_sick_days_used = $sick_used + 1;
	
	if($vacation_day1 == "0000-00-00"){
    	// Update information in the user table about the user status of why absent today, Update available sick and used days
    	$sql=("UPDATE users SET request_vacation='".$request."', sick_day_available='".$final_sick_days_available."', sick_day_used='".$final_sick_days_used."'  where id='".$id."' ");
    	$query = mysqli_query($conn, $sql) or die ("Error Clocking In Into timesheet:".mysqli_error($conn));
	}elseif($vacation_day1 != "0000-00-00" && $vacation_day2 == "0000-00-00"){
		// Update information in the user table about the user status of why absent today, Update available sick and used days
    	$sql=("UPDATE users SET request_vacation1='".$request."', sick_day_available='".$final_sick_days_available."', sick_day_used='".$final_sick_days_used."'  where id='".$id."' ");
    	$query = mysqli_query($conn, $sql) or die ("Error Clocking In Into timesheet:".mysqli_error($conn));
	}elseif($vacation_day1 != "0000-00-00" && $vacation_day2 != "0000-00-00" && $vacation_day3 == "0000-00-00"){
		// Update information in the user table about the user status of why absent today, Update available sick and used days
    	$sql=("UPDATE users SET request_vacation2='".$request."', sick_day_available='".$final_sick_days_available."', sick_day_used='".$final_sick_days_used."'  where id='".$id."' ");
    	$query = mysqli_query($conn, $sql) or die ("Error Clocking In Into timesheet:".mysqli_error($conn));
	}elseif($vacation_day1 != "0000-00-00" && $vacation_day2 != "0000-00-00" && $vacation_day3 != "0000-00-00"){
		echo "<br><br><br><br><center><h3><font color='red'>Cannot Request anymore vacation for $name. He has reached the 3 days vacation requested</font><br> Please check with Managers</h3></center>";
    	echo '<META HTTP-EQUIV=REFRESH CONTENT="7; timesheet.php">';
    	exit;
	}	
	
	
	// Record time when the processed was made. And hours is equal to 0
	$call_in_day = date_default_timezone_get();
	$now = date('Y-m-d g:i:s a',strtotime($call_in_day));
	
	$call_in=("Insert into timecard (`employment_id`,`full_name`,`clockin`,`clockout`,`actual_time_in`,`actual_time_out`,`dept`,`comment`,`process_by`, `payroll`) 
			Values ('$id','$name','$request','$request','$request','$request','$department', 'vacation requested <br> @ $now', '$process', '$payleave')");
	$call_input = mysqli_query($conn, $call_in) or die ("Requestion Absent Error :".mysqli_error($conn));
    
    
    
    echo "<br><br><br><br><center><h3>$name Requesting for Vacation $request . . . </h3></center>";
    echo '<META HTTP-EQUIV=REFRESH CONTENT="2; timesheet.php">';
}
?>