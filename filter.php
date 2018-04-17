<?
session_start();
if($_SESSION['user']!='admin' && $_SESSION['user']!='manager'){
	echo "You have not enough permission to access this page.";	
	exit;	
}

echo $_SESSION['time'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<meta charset="utf-8">
	  <link rel="stylesheet" href="table.css">
</head>	
<body>
<br>
	<center><a href="/puleset/"><button>Admin Setting</button></a>&nbsp;<a href="timesheet.php"><button>Timesheet</button></a>&nbsp;<a href="filter1.php"><button>Filter 2</button></a>
		
		
	<table class="data-table">
		<caption>TIMESHEET</caption>
		<thead>
			<tr>
				<th>date</th>
				<th>id</th>
				<th>Name</th>
				<th>In</th>	
				<th>out</th>	
				<th>OT hours<br>reg hours</th>	
				<th>total (¥)</th>		
			</tr>
		</thead> 
		<tbody>	
<?
require 'db.php';
$report=("SELECT timecard.*, users.id, users.profile_pic FROM timecard INNER JOIN users ON users.id=timecard.employment_id ORDER by clockin DESC");
$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet".mysqli_error($conn));
while($row = mysqli_fetch_assoc($result)){
	$name  = $row['full_name'];
	$dept  = $row['dept'];
	$em_id = $row['employment_id'];
	$aho   = date('M-dS-Y', strtotime($row['clockin']));
	$aho1  = date('M-dS-Y', strtotime($row['actual_time_in']));
	$date_display = date('d-M-Y', strtotime($row['clockin']));
	$process = $row['process_by'];
	$paycheck = $row['payroll'];
	$today_labor_cost += $row['payroll'];
	$file = $row['profile_pic'];
	$fileshow = "../unix/dept/img/profile/$file";
	
	//Pull out Today`s clock in and timesheet
	$current_day = date("M-dS-Y");
	$yesterday   = date('M-dS-Y',strtotime("-1 days"));
	$daybefore   = date('M-dS-Y',strtotime("-2 days"));
	if($aho == $current_day){
		$date = "Today";
	}elseif ($aho == $yesterday){
		$date = "Yesterday";
	}else{
		$date = $date_display;
	}
	$clockin      = date('g:i a',strtotime($row['clockin']));
	$actual_in    = date('g:i a', strtotime($row['actual_time_in']));
	$actual_out   = date('g:i a', strtotime($row['actual_time_out']));
	$actual_total = date('H:i', strtotime($row['actual_total']));
	
	$online  = "../unix/img/online.png";
	$offline = "../unix/img/offline.png";
	
	$in  = $row['clockin'];
	$out = $row['clockout']; 
	if($out=='0000-00-00 00:00:00'){
				$clockout       = "<img src='$online' height=11 width=11>  On Duty";
				$actual_out     = "";
				$actual_total   = "";
			    $total_reg      = "";
				$total_overtime = "";
	}elseif ($out!='0000-00-00 00:00:00'){
				$clockout       = date('g:i a',strtotime($row['clockout']));
				$total_reg      = date('H:i:s',strtotime($row['total']));
				$total_overtime = date('H:i:s',strtotime($row['overtime']));			
			}
	// Display those who are absent
	$comment = $row['comment'];
	if($comment ==''){
		$disp23 = "";
		$process_by = "";
	}else{
		$disp23 = "<td style='background-color: #FFFF00'>$comment</td>";
		$actual_total = 0;
		$total = 0;
		$process_by = "<td>Processed by<br><b>$process</b></td>";
	}
		// Display Today`s clock-in Time		 		
		echo "<tr>";
			echo "<td>$date</td>";
			echo "<td><br><img src='$fileshow' height=50 width=50 style='border-radius: 5px;'></td>";	
			echo "<td><b>$name</b><br>$dept<br>$em_id</td>";	
			echo "<td>$actual_in <br><font color='red'>$clockin</font></td>";
			echo "<td>$actual_out<br><font color='red'>$clockout</font></td>";
			echo "<td><font color='red'>$total_overtime<br>$total_reg</font></td>";
			echo "<td><p align='right'>￥".number_format($paycheck)."</p></td>";
			echo "$disp23";
			echo "$process_by";
		echo "</tr>";
}	
?>	
	</tbody>
		<tfoot>
			<tr>
			<th colspan="6">Today Labor Cost</th>
			<th>￥<? echo number_format($today_labor_cost); ?></th>	
			</tr>
		</tfoot>
	</table><br><br>

<br><br>

</center>	
</body>	
</html>