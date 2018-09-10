<?php
//Allow Access to DB
require 'db.php';

//Update status who are absent @9:00pm
require 'update_yasumi_status.php';

//Restrict Allowed Locations Only
require 'black_ip.php';

//refresh page every 3 seconds to update clockin list of today
$page = $_SERVER['PHP_SELF'];
$sec = "3600";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<meta charset="utf-8">
	<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
	  <link rel="stylesheet" href="table.css">
      <script src="js/time.js"></script>
      <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
<style>
	.div2 {
    width: 800px;
    height: 800px;    
    padding: 50px;
    border: 1px solid red;
}
</style>
</head>	
<body>
	<div class="div2" align="center">		
<center><h2><body onload=display_ct();>
<span id='ct' ></h2></span></center>
<br>

	<table class="data-table">
		<caption>TODAY</caption>
		<thead>
			<tr>
				<th>date</th>
				<th>id</th>
				<th>Name</th>
				<th>department</th>
				<th>In</th>	
				<th>out</th>	
				<th>total</th>		
				
			</tr>
		</thead> 
		<tbody>	
<?
$report=("SELECT * FROM timecard WHERE date(clockin)=CURDATE() ORDER BY dept");
$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet".mysqli_error($conn));
while($row = mysqli_fetch_array($result)){
	$name = $row['full_name'];
	$dept=$row['dept'];
	$em_id = $row['employment_id'];
	$aho = date('M-dS-Y', strtotime($row['clockin']));
	//Display Today/Yesterday and day before
	$current_day = date("M-dS-Y");
	$yesderday = date('M-dS-Y',strtotime("-1 days"));
	$daybefore = date('M-dS-Y',strtotime("-2 days"));
	if($aho==$current_day){
		$date="Today";
	}elseif ($aho==$yesderday){
		$date="Yesterday";
	}elseif($aho==$daybefore){
	    $date=date('D dS, Y', strtotime($row['clockin']));
	}else{
		$date=$aho;
	}
	$clockin = date('G:ia',strtotime($row['clockin']));
	$in = $row['clockin'];
	$out = $row['clockout'];
	$online = "../unix/img/online.png";
	$offline = "../unix/img/offline.png";
	$comment = $row['comment'];
	if($comment==''){
		$disp = "";
	}else{
		$disp = "<td style='background-color: #FFFF00'>$comment</td>";
	}
	 
	if($out=="0000-00-00 00:00:00"){
				$clockout = "<img src='$online' height=11 width=11>  On Duty";
				$total = ""; 
			}elseif ($icon == 0){
				$clockout = date('G:ia',strtotime($row['clockout']));
				$start = new DateTime($in);
				$finish = new DateTime($out);
				$hour = $start->diff($finish);
				$hrs = $hour->format("%H:%i:%s");
				sscanf($hrs,"%d:%d:%d ", $h, $m, $s);
				$d = ($h*3600) + ($m*60) + $s;
				$total = gmdate('H:i:s', $d);
				
			} 		
		echo "<tr>";
			echo "<td>$date</td>";
			echo "<td>$em_id</td>";	
			echo "<td>$name</td>";	
			echo "<td>$dept</td>";
			echo "<td>$clockin</td>";
			echo "<td>$clockout</td>";
			echo "<td>$total</td>";
			echo "$disp";
		echo "</tr>";
}	


?>	
	</tbody>
		<tfoot>
			<tr>
				
			</tr>
		</tfoot>
	</table><br><br>

</center>	
</body>	
</html>
