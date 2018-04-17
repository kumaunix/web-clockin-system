<?
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
<?php echo "$name ($department) requesting for :"; ?>	<select name="comment" required >
		<option></option>
		<option value="sick leave">sick leave</option>
		<option value="pay leave">pay leave</option>
		<option value="absent">leave without pay</option>
		<option value="business travel">business travel</option>
	</select><br><br>
	<input type="hidden" name="act44" value="run"/>
	<input type="submit" name="button" value="Submit" />&nbsp;
	<input type="reset" name="Reset" value="Reset" />
</form>	
<a href="../puleset/users.php"><button>Cancel</button></a>
</center>	
</body>
	
</html>
<?
if (!empty($_POST['act44'])){

$check_today = ("SELECT clockin FROM timecard where date(clockin) = CURDATE() AND employment_id='".$id."' ");	
	$check_query = mysqli_query($conn, $check_today) or die ("ERROR checking attandance :".mysqli_error($conn));
	$number_of_rows = mysqli_num_rows($check_query);
if ($number_of_rows > 0){
	die("<center><font color='red'><h3>WARNING !!!</h3></font> <b>$name</b> is already at work today, <br> check with $department if he is there or somebody else clocked him in</center>");
}
	
	$comment=$_POST['comment'];
	$id = $_GET['id'];
	$name = $_GET['name'];
	$department = $_GET['dept'];
	$process = $_SESSION['fname'].' '.$_SESSION['lname'];
	
	// Retrive the Number of sick days available and used before today`s request
    $sick_day=("SELECT sick_day_available, sick_day_used, payrate FROM users where id='".$id."' ");
	$sick_query=mysqli_query($conn, $sick_day) or die ("Retriving Sick Days :".mysqli_error($conn));
	$sick = mysqli_fetch_assoc($sick_query);
	$sick_available = $sick['sick_day_available'];
	$sick_used = $sick['sick_day_used'];
	$pay_rank = $sick['payrate'];
	
	// if sick day is still available, a pay for regular 8 hours is given
	if($sick_available > 0){
		$payleave = $pay_rank * 8;
	}
	
	//final sick days used and final sick days available are calculated
	$final_sick_days_available = $sick_available - 1;
	$final_sick_days_used = $sick_used + 1;
	
    // Update information in the user table about the user status of why absent today, Update available sick and used days
    $sql=("UPDATE users SET status_today='".$comment."', sick_day_available='".$final_sick_days_available."', sick_day_used='".$final_sick_days_used."'  where id='".$id."' ");
    $query = mysqli_query($conn, $sql) or die ("Error Clocking In Into timesheet:".mysqli_error($conn));
	
	// Record time when the processed was made. And hours is equal to 0
	$call_in_day = date_default_timezone_get();
	$now = date('Y-m-d H:i:s',strtotime($call_in_day));
	$call_in=("Insert into timecard (`employment_id`,`full_name`,`clockin`,`clockout`,`actual_time_in`,`actual_time_out`,`dept`,`comment`,`process_by`, `payroll`) 
			Values ('$id','$name','$now','$now','$now','$now','$department', '$comment', '$process', '$payleave')");
	$call_input = mysqli_query($conn, $call_in) or die ("Requestion Absent Error :".mysqli_error($conn));
    
    
    
    echo "<br><br><br><br><center><h3>$name Requesting absent today . . . </h3></center>";
    echo '<META HTTP-EQUIV=REFRESH CONTENT="2; timesheet.php">';
}
?>