
<table class="data-table">
		<caption>YESTERDAY</caption>
		<thead>
			<tr>
				<th>date</th>
				<th>id</th>
				<th>info</th>
				<th>In</th>	
				<th>out</th>	
				<th>OT hours<br>REG HOURS</th>	
				<? if($_SESSION['id']==4 || $_SESSION['id']==1){?>		
				<th>total (¥)</th>
				<? }?>	
			</tr>
		</thead> 
		<tbody>	
<?
require 'db.php';
$yesterday = date('Y-m-d',strtotime("-1 days"));
$report=("SELECT timecard.*, users.profile_pic FROM timecard INNER JOIN users ON timecard.employment_id=users.id WHERE date(clockin) = '".$yesterday."' ORDER BY dept");
$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet".mysqli_error($conn));
while($row = mysqli_fetch_array($result)){
	$name = $row['full_name'];
	$file = $row['profile_pic'];
	$fileshow = "img/profile/$file"; // update to where profile pictures are stored !!!
	$dept=$row['dept'];
	$em_id = $row['employment_id'];
	$aho = date('M-dS-Y', strtotime($row['clockin']));
	$comment = $row['comment'];
	$process = $row['process_by'];
	$paycheck = $row['payroll'];
	$yesterday_labor_cost +=$row['payroll'];
	
	//Display Yesterday
	$yesderday = date('M-dS-Y',strtotime("-1 days"));

	if ($aho==$yesderday){
		$date="Yesterday";
	}
	$clockin    = date('g:i a',strtotime($row['clockin']));
	$actual_in  = date('g:i a',strtotime($row['actual_time_in']));
	$clockout   = date('g:i a',strtotime($row['clockout']));
	$actual_out = date('g:i a',strtotime($row['actual_time_out']));
	$total      = date('H:i:s',strtotime($row['total']));
	$total_over   = date('H:i:s',strtotime($row['overtime']));
	if($total_over > 0){
					$total_overtime = $total_over;
				}elseif ($total_over == 0){
					$total_overtime = "";
				}		
	$actual_total = date('g:i',strtotime($row['actual_total']));
	$in         = $row['clockin'];
	$out        = $row['clockout'];
	$online     = "img/online.png";
	$offline    = "img/offline.png";
	
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
			echo $tab1;
			echo $tab2;
			echo "<td><font color='red'>$total_overtime<br>$total</font></td>";
			if($_SESSION['id']==4 || $_SESSION['id']==1){
			echo "<td><p align='right'>￥".number_format($paycheck)."</p></td>";
			}
		echo "</tr>";
}	
?>	
	</tbody>
		<tfoot>
			<tr>
			<? if($_SESSION['id']==4 || $_SESSION['id']==1){?>		
				<th colspan="6">Today Labor Cost</th>
			<th>￥<? echo number_format($yesterday_labor_cost); ?></th>
			<? }?>	
			</tr>
		</tfoot>
	</table>
