<?
session_start();
if($_SESSION['user']!='admin' && $_SESSION['user']!='manager'){
	echo "You have not enough permission to access this page.";	
	exit;	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<title>View Timesheet with Filter</title>
	<meta charset="utf-8">
	  <link rel="stylesheet" href="table.css">
</head>	
<script>
function myFunction() {
    var x = document.getElementById("myDIV");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}
</script>
<body>
<br>
	<center><a href="/puleset/"><button>Admin Setting</button></a>&nbsp;<a
		 href="timesheet.php"><button>Timesheet</button></a>&nbsp;<button onclick="myFunction()"> Filter Show </button><br>
		<div id="myDIV" style="display: none;">
	<br><form method="post" action="" id="search">
		Employee ID : <input type="number" name="employee_id" /><br><br><input type="hidden" name="act25" value="run" />
		<input type="date" name="date1" /> to <input type="date" name="date2" /><br><br>
		<input type="submit" name="search_timesheet" value="Filter" />&nbsp;<input type="reset" name="Reset1" value="Reset" />
	</form><br>	
	</div>
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
				<? if($_SESSION['id']==4 || $_SESSION['id']==1){?>		
				<th>total (¥)</th>
				<? }?>			
			</tr>
		</thead> 
		<tbody>	
<?
require 'db.php';
//if(!empty($_POST['act25'])){
	$emp_id = $_POST['employee_id'];
	$date1  = $_POST['date1'];
	$date2  = $_POST['date2'];
    $begin  = date('Y-m-d', strtotime($date1));
    $end    = date('Y-m-d', strtotime($date2));
	
if($emp_id){
	
	$report=("SELECT timecard.*, users.profile_pic FROM timecard INNER JOIN users ON users.id=timecard.employment_id WHERE employment_id='".$emp_id."' ORDER by clockin DESC");
	$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet 1 :".mysqli_error($conn));
}elseif ($date1 && $date2){
	$report=("SELECT timecard.*, users.profile_pic FROM timecard INNER JOIN users ON users.id=timecard.employment_id WHERE date(clockin) >='".$begin."' AND date(clockin) <='".$end."' ORDER by clockin DESC");
	$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet 2 :".mysqli_error($conn));
}else{

	$report=("SELECT timecard.*, users.profile_pic FROM timecard INNER JOIN users ON users.id=timecard.employment_id ORDER by clockin DESC");
	$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet 3 :".mysqli_error($conn));
}

while($row = mysqli_fetch_assoc($result)){
	$id = $row['id'];
	$name  = $row['full_name'];
	$dept  = $row['dept'];
	$em_id = $row['employment_id'];
	$aho   = date('M-dS-Y', strtotime($row['clockin']));
	$aho1  = date('M-dS-Y', strtotime($row['actual_time_in']));
	$date_display = date('d M Y', strtotime($row['clockin']));
	$process = $row['process_by'];
	$paycheck = $row['payroll'];
	$today_labor_cost += $row['payroll'];
	$file = $row['profile_pic'];
	$fileshow = "../unix/dept/img/profile/$file";
	$approve = $row['recon'];
	
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
				$total_over     = date('H:i:s',strtotime($row['overtime']));
				if($total_over > 0){
					$total_overtime = $total_over;
				}elseif ($total_over == 0){
					$total_overtime = "";
				}					
			}
	// Display those who are absent
	$comment = $row['comment'];
	if($comment ==''){
		$disp23 = "";
		$process_by = "";
		$tab1 = "<td>$actual_in <br><font color='red'>$clockin </font></td>";
		$tab2 = "<td>$actual_out<br><font color='red'>$clockout</font></td>";
	}else{
		$disp23 = "<td style='background-color: #FFFF00'>$comment</td>";
		$actual_total = 0;
		$actual_in ="Processed by<br><b>$process</b>";
		$actual_out = "$comment";
		$clockin = "";
		$clockout = "";
		$total_reg ="";
		$process_by = "<td>Processed by<br><b>$process</b></td>";
		$tab1 = "<td>$actual_in <br><font color='red'>$clockin</font></td>";
		$tab2 = "<td style='background-color: #FFFF00'>$actual_out<br><font color='red'>$clockout</font></td>";
	}
		// Display Today`s clock-in Time		 		
		echo "<tr>";
			echo "<td>$date<br><a href='time_adjust.php?id=$id'><button>Edit</button></a><br><i><font color='red'>$approve</font></i></td>";
			echo "<td><br><img src='$fileshow' height=50 width=50 style='border-radius: 50px;'></td>";	
			echo "<td><b>$name</b><br>$dept<br>$em_id</td>";
			echo $tab1;
			echo $tab2;
			echo "<td><font color='red'>$total_overtime<br>$total_reg</font></td>";
			if($_SESSION['id']==4 || $_SESSION['id']==1){
			echo "<td><p align='right'>￥".number_format($paycheck)."</p></td>";
			}
			//echo "$disp23";
			//echo "$process_by";
		echo "</tr>";
		}
//}	
?>	
	</tbody>
		<tfoot>
			<tr>
			<? if($_SESSION['id']==4 || $_SESSION['id']==1){?>		
				<th colspan="6">Labor Cost</th>
			<th>￥<? echo number_format($today_labor_cost); ?></th>	
			<?}?>
			</tr>
		</tfoot>
	</table>
</center>	
</body>	
</html>