<?
// Restrict Access to Kumamoto, Hanoi, Fukuoka and Kitakanto
include 'black_ip.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Unix | Clock In System</title>
	<link rel="stylesheet" href="table.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="js/time.js"></script>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<!-- Bootstrap -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<!-- jQuery.NumPad -->
		<script src="js/jquery.numpad.js"></script>
		<link rel="stylesheet" href="js/jquery.numpad.css">
		<link rel="stylesheet" href="compiled/flipclock.css">
		<script src="compiled/flipclock.js"></script>
<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/base-min.css">	
<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
		<script type="text/javascript">
			// Set NumPad defaults for jQuery mobile. 
			// These defaults will be applied to all NumPads within this document!
			$.fn.numpad.defaults.gridTpl = '<table class="table modal-content"></table>';
			$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
			$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" />';
			$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default"></button>';
			$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="width: 100%;"></button>';
			$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};
			
			// Instantiate NumPad once the page is ready to be shown
			$(document).ready(function(){
				$('#text-basic').numpad();
				$('#password').numpad({
					displayTpl: '<input class="form-control" type="password" />',
					hidePlusMinusButton: true,
					hideDecimalButton: true	
				});
				$('#numpadButton-btn').numpad({
					target: $('#numpadButton')
				});
				$('#numpad4div').numpad();
				$('#numpad4column .qtyInput').numpad();
				
				$('#numpad4column tr').on('click', function(e){
					$(this).find('.qtyInput').numpad('open');
				});
			});
		</script>
		<style type="text/css">
			.nmpd-grid {border: none; padding: 30px;}
			.nmpd-grid>tbody>tr>td {border: none;}
			
			/* Some custom styling for Bootstrap */
			.qtyInput {display: block;
			  width: 100%;
			  padding: 6px 15px;
			  color: #555;
			  background-color: white;
			  border: 1px solid #ccc;
			  border-radius: 4px;
			  -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
			  box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
			  -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
			  -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
			  transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
			}
		</style>
<style>
	.div2 {
    width: 800px;
    height: 400px;    
    padding: 50px;
    border: 1px solid black;
}
</style>	
	</head>
<body>
	<br>
<center>	
<div class="div2" align="center">	
<h2><body onload=display_ct();>
		
<div class="clock" align="center" style="margin:2em; left: 70px;"></div>
<script type="text/javascript">
			var clock;
			
			$(document).ready(function() {
				clock = $('.clock').FlipClock({
					clockFace: 'TwelveHourClock'
				});
			});
		</script>
		<form method="post" action="">
			<div class="form-group">
				<label for="password">PLEASE ENTER ID</h2></label>
				<input type="password" class="form-control" id="password" name="id"  placeholder="Employee ID"><br>
				<input type="hidden" name="act" value="run"/>
				<h3><input type="submit" class="pure-button pure-button-primary" name="button" value="CHECK" /></h3>
			</div>
		</form>	 
<?php
require 'db.php';
$id = $_POST['id'];
if (!empty($_POST['act'])){

$precheck = ("Select id, lname, gname, status_today, request_vacation, request_vacation1, request_vacation2 FROM users where id='".$id."'");
$query1 = mysqli_query($conn, $precheck) or die ("precheck : ".mysqli_error($conn));
$rowcheck = mysqli_fetch_assoc($query1);
$person = $rowcheck['gname'].' '.$rowcheck['lname'];

/*
 * Check if ID is valid
 */ 
if($rowcheck['id']!=$id){
	echo "<center><h4><font color='red'>Please Enter Valid ID</font></h4></center>";
	exit;
}
/*
 * Check if the person is absent, incase someone else tries to clockin for him/her
 */
if($rowcheck['status_today']!=''){
	echo "<center><h4><font color='red'>$person is absent today</font></h4></center>";
	exit;
}
$now = date_default_timezone_get(); 
$today = date('Y-m-d', strtotime($now));
$time = date('H:i:s', strtotime($now));
//$time  = '17:15:00';						// for testing Only

/*
 * System can take vacation up to 3 days at the same time. 
 * 
 */
// ****** Vacation Day 1 *******
if($rowcheck['request_vacation']==$today){
	if($time < '17:10:59'){
		echo "<center><h4><font color='red'>$person is on Vacation today</font></h4></center>";
	exit;
	}elseif($time > '17:10:59'){
		$reset_vacation    = ("UPDATE users SET request_vacation='0000-00-00'");
		$reset_query_vacation  = mysqli_query($conn, $reset_vacation) or die ("Error Resetting Vacation Day 1: ".mysqli_error($conn));
	}
}
// ****** Vacation Day 2 *******
if($rowcheck['request_vacation1']==$today){
	if($time < '17:10:59'){
		echo "<center><h4><font color='red'>$person is on Vacation today</font></h4></center>";
	exit;
	}elseif($time > '17:10:59'){
		$reset_vacation    = ("UPDATE users SET request_vacation1=''");
		$reset_query_vacation  = mysqli_query($conn, $reset_vacation) or die ("Error Resetting Vacation Day 2: ".mysqli_error($conn));
	}
}
// ****** Vacation Day 3 *******
if($rowcheck['request_vacation2']==$today){
	if($time < '17:10:59'){
		echo "<center><h4><font color='red'>$person is on Vacation today</font></h4></center>";
	exit;
	}elseif($time > '17:10:59'){
		$reset_vacation    = ("UPDATE users SET request_vacation2=''");
		$reset_query_vacation  = mysqli_query($conn, $reset_vacation) or die ("Error Resetting Vacation Day 2: ".mysqli_error($conn));
	}
}

$check4 = ("SELECT clockin FROM timecard WHERE employment_id='".$id."' AND date(clockin)=CURDATE()");
$query4 = mysqli_query($conn, $check4) or die ("Error Selecting Employees ID : ".mysqli_error($conn));
$row = mysqli_fetch_assoc($query4);
	$clockin_time = $row['clockin'];
	$online    	= "../unix/img/online.png";
	$offline   	= "../unix/img/offline.png";
	$num_rows_affected = mysqli_num_rows($query4);
	if($num_rows_affected == 1){
		$status = "<img src='$online' height=15 width=15> On Duty"; 
	}elseif($num_rows_affected == 0){
		$status = "<img src='$offline' height=15 width=15> Out of Duty"; 
	}	
	

?>	
<center><table class="data-table">
		<caption class="title">Clock In System</caption>
	<thead>
			<tr>
				<th>Profile</th>		
			</tr>
	</thead>
<tbody>	
<?
if(empty($_POST['id'])){ // change from $id to $_POST
		echo "Please enter valid ID";
		exit;
}elseif (!empty($_POST['id'])){	 // change from $id to $_POST
$check = ("SELECT lname, gname, dept, profile_pic,id, in_or_out, payrate, location FROM users WHERE id='".$id."'");
$query = mysqli_query($conn, $check) or die ("Error Selecting Employees ID : ".mysqli_error($conn));
while ($row = mysqli_fetch_assoc($query)){
	$first_name = $row['gname'];
	$last_name = $row['lname'];
	$rank      = $row['payrate'];
	$dept      = $row['dept'];
	$office    = $row['location'];
	$file      = $row['profile_pic'];
	$profile   = "../unix/dept/img/profile/$file";
	$icon      = $row['in_or_out'];	
	$id        = $row['id'];
	$online    = "../unix/img/online.png";
	$offline   = "../unix/img/offline.png";


echo "<tr>"; // To include time of clockin
      echo "<td><center><img src='$profile' height=80 width=90 style='border-radius: 5px;'></center><br>
      <p align='left'>
      <b>NAME</b> : $first_name $last_name<br>
      <b>DEPARTMENT</b> : $dept<br>
      <b>STATUS</b> : $status<br>
      
      </p></td>";
	  if($num_rows_affected == 0){ // change from $icon
	  	echo "<br><a href='clockin.php?id=$id&name=$first_name $last_name&dept=$dept&vahe=$rank&area=$office'>
	  			  <h4><button class='pure-button pure-button-primary'><font color='white'>Clock In</font></button></a>&nbsp;&nbsp;<a href='index.php'>
	  			  <button class='pure-button pure-button-primary'><font color='white'>Clear</font></button></a></h4>";	
	  }elseif ($num_rows_affected == 1){ // change from $icon
	  	echo "<br><a href='clockout.php?id=$id&name=$first_name $last_name&dept=$dept'>
	  		      <h4><button class='pure-button pure-button-primary'><font color='white'>Clock Out</font></button></a>&nbsp;&nbsp;<a href='index.php'>
	  		      <button class='pure-button pure-button-primary'><font color='white'>Clear</font></button></a></h4>";
	  }  
	    
echo "</tr>";
}	
}
?>
</tbody>
		<tfoot>
			<tr>
				<th colspan="2"></th>
			</tr>
		</tfoot>
	</table></center>	
<?
}
/*
 * Reset the status of absentees getting ready to clock-in the next day if they come to work
 * Reset time is set to 5:15 pm everyday when other workers clock in, they reset the status of others
 */ 
$reset_time   = date_default_timezone_get();
$reset_format = date('H:i:s', strtotime($reset_time));

if ($reset_format >= "17:10:00"){
	$reset_sql    = ("UPDATE users SET status_today=''");
	$reset_query  = mysqli_query($conn, $reset_sql);
}
?>	
</center></body></div>
</html>
