
<table class="data-table">
		<caption>BEFORE YESTERDAY</caption>
		<thead>
			<tr>
				<th>date</th>
				<th>id</th>
				<th>Info</th>
				<th>In</th>	
				<th>out</th>	
				<th>OT hours<br>REG HOURS</th>	
				<? if($_SESSION['username']=='admin'){?>		
				<th>total (¥)</th>
				<? }?>						
			</tr>
		</thead> 
		<tbody>	
<?
require 'db.php';
$yesderday = date('Y-m-d',strtotime("-1 days"));
$today = date('Y-m-d');
$report=("SELECT timecard.*, users.profile_pic FROM timecard INNER JOIN users ON timecard.employment_id=users.id WHERE date(clockin)<'".$yesderday."' ORDER BY clockin DESC");
$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet AfterDay :".mysqli_error($conn));
while($row = mysqli_fetch_array($result)){
	$file  = $row['profile_pic'];
	$fileshow = "img/profile/$file"; // update to where your profile imgs are stored.
	$name  = $row['full_name'];
	$dept  =$row['dept'];
	$em_id = $row['employment_id'];
	$aho   = date('d M, Y', strtotime($row['clockin']));
	$comment = $row['comment'];
	$process = $row['process_by'];
	$paycheck = $row['payroll'];
	
	//Display Yesterday
	$yesderday = date('M-dS-Y',strtotime("-1 days"));

	if ($aho < $yesderday){
		$date="$aho";
	}
	$clockin = date('g:i a',strtotime($row['clockin']));
	$actual_in = date('g:i a',strtotime($row['actual_time_in']));
	$clockout = date('g:i a',strtotime($row['clockout']));
	$actual_out = date('g:i a',strtotime($row['actual_time_out']));
	$total = date('H:i:s',strtotime($row['total']));
	$total_over = date('H:i:s',strtotime($row['overtime']));
	if($total_over > 0){
					$total_overtime = $total_over;
				}elseif ($total_over == 0){
					$total_overtime = "";
				}		
	$actual_total = date('g:i',strtotime($row['actual_total']));
	$in = $row['clockin'];
	$out = $row['clockout'];
	$online = "img/online.png"; // Update url to your online and offline light
	$offline = "img/offline.png";
	
	if($comment==""){
		$tab1 = "<td>$actual_in <br><font color='red'>$clockin</font></td>";
		$tab2 = "<td>$actual_out<br><font color='red'>$clockout</font></td>";
	}else{
		$actual_total = 0;
		$actual_in ="Processed by<br><b>$process</b>";
		$actual_out = "$comment";
		$clockin = "";
		$clockout = "";
		$total ="";
		$tab1 = "<td>$actual_in <br><font color='red'>$clockin</font></td>";
		$tab2 = "<td style='background-color: #FFFF00'>$actual_out<br><font color='red'>$clockout</font></td>";
	}			
		echo "<tr>";
			echo "<td>$date</td>";
			echo "<td><img src='$fileshow' height=50 width=50 style='border-radius: 50px;'></td>";		
			echo "<td><b>$name</b><br>$dept<br>$em_id</td>";	
			//echo "<td>$actual_in<br><font color='red'>$clockin</font></td>";
			//echo "<td>$actual_out<br><font color='red'>$clockout</font></td>";
			echo $tab1;
			echo $tab2;
			echo "<td><font color='red'>$total_overtime<br>$total</font></td>";
			if($_SESSION['username']=='admin'){
			echo "<td><p align='right'>￥".number_format($paycheck)."</p></td>";
			}
			
		echo "</tr>";
}	
?>	
	</tbody>
		<tfoot>
			<tr>
				
			</tr>
		</tfoot>
	</table>
