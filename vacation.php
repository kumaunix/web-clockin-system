
<table class="data-table">
		<caption>VACATION</caption>
		<thead>
			<tr>
				<th>Vacation<br> Date</th>
				<th>id</th>
				<th>info</th>
				<? if($_SESSION['id']==4 || $_SESSION['id']==1){?>		
				<th>total (¥)</th>
				<? }?>			
			</tr>
		</thead> 
		<tbody>	
<?
require 'db.php';
$yesderday = date('Y-m-d',strtotime("-1 days"));
$report=("SELECT timecard.*, users.profile_pic FROM timecard INNER JOIN users ON users.id=timecard.employment_id WHERE date(timecard.actual_time_in)>CURDATE() ORDER BY timecard.employment_id");
$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet".mysqli_error($conn));
while($row = mysqli_fetch_array($result)){
	$name = $row['full_name'];
	$file = $row['profile_pic'];
	$fileshow = "../unix/dept/img/profile/$file";
	$dept=$row['dept'];
	$em_id = $row['employment_id'];
	$aho = date('d M Y', strtotime($row['clockin']));
	$comment = $row['comment'];
	$process = $row['process_by'];
	$paycheck = $row['payroll'];
	$vacation_labor_cost +=$row['payroll'];
	
	
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
	$online     = "../unix/img/online.png";
	$offline    = "../unix/img/offline.png";
	
	if($comment==""){
		$disp22 = "";
		$process_by ="";
	}else{
		$disp22 = "<td style='background-color: #00FF00'>$comment</td>";
		$actual_total="";
		$total = "";
		$process_by = "<td>Processed by<br> <b>$process</b></td>";
	}			
							
		echo "<tr>";
			echo "<td>$aho</td>";
			echo "<td><img src='$fileshow' height=50 width=50 style='border-radius: 5px;'></td>";		
			echo "<td><b>$name</b><br>$dept<br>$em_id</td>";	
			if($_SESSION['id']==4 || $_SESSION['id']==1){
			echo "<td><p align='right'>￥".number_format($paycheck)."</p></td>";
			}
			echo "$disp22";
			echo "$process_by";
			
		echo "</tr>";
}	
?>	
	</tbody>
		<tfoot>
			<tr>
			<? if($_SESSION['id']==4 || $_SESSION['id']==1){?>		
			<th colspan="3">Vacation Cost</th>
			<th>￥<? echo number_format($vacation_labor_cost); ?></th>	
			<?}?>
			</tr>
		</tfoot>
	</table>
