
<table class="data-table">
		<caption>YESTERDAY</caption>
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
require 'db.php';
$yesderday = date('Y-m-d',strtotime("-1 days"));
$report=("SELECT * FROM timecard WHERE date(clockin) = '".$yesderday."' ORDER BY dept");
while($row = mysqli_fetch_array($result)){
	$name = $row['full_name'];
	$dept=$row['dept'];
	$em_id = $row['employment_id'];
	$aho = date('M-dS-Y', strtotime($row['clockin']));
	$aho1 = date('M-dS-Y', strtotime($row['actual_time_in']));
	$process = $row['process_by'];
	
	// Display those who are absent
	$comment = $row['comment'];
	if($comment==''){
		$disp23 = "";
	}else{
		$disp23 = "<td style='background-color: #FFFF00'>$comment</td>";
	}
	
	
	//Pull out Today`s clock in and timesheet
	$current_day = date("M-dS-Y");
	$yesderday = date('M-dS-Y',strtotime("-1 days"));
	$daybefore = date('M-dS-Y',strtotime("-2 days"));
	if($aho==$current_day || $aho1==$current_day){
		$date="Today";
	}elseif ($aho==$yesderday){
		$date="Yesterday";
	}elseif($aho==$daybefore){
	    $date=date('D dS, Y', strtotime($row['clockin']));
	}else{
		$date=$aho;
	}
	$clockin = date('g:i a',strtotime($row['clockin']));
	
	$actual_in = date('g:i a', strtotime($row['actual_time_in']));
	$actual_out = date('g:i a', strtotime($row['actual_time_out']));
	$actual_total = date('H:i', strtotime($row['actual_total']));
	
	$online = "../unix/img/online.png";
	$offline = "../unix/img/offline.png";
	
	
	$in = $row['clockin'];
	$out = $row['clockout']; 
	if($out=="0000-00-00 00:00:00"){
				$clockout = "<img src='$online' height=11 width=11>  On Duty";
				$actual_out = "";
				$actual_total = "";
			
	}elseif ($out!="0000-00-00 00:00:00"){
				$clockout = date('g:i a',strtotime($row['clockout']));
				$total = date('h:i',strtotime($row['total']));
				
				
				$start = new DateTime($in);
				$finish = new DateTime($out);
				$hour = $start->diff($finish);
				$hrs = $hour->format("%H:%i:%s");
				sscanf($hrs,"%d:%d:%d ", $h, $m, $s);
				$d = ($h*3600) + ($m*60) + $s;
				$total_calculation = gmdate('H:i', $d);
				
			}
	// Display those who are absent
	$comment = $row['comment'];
	if($comment==''){
		$disp23 = "";
		$process_by = "";
	}else{
		$disp23 = "<td style='background-color: #FFFF00'>$comment</td>";
		$actual_total =0;
		$total=0;
		$process_by = "<td>Processed by <b>$process</b></td>";
	}
		// Display Today`s clock-in Time		 		
		echo "<tr>";
			echo "<td>$date</td>";
			echo "<td>$em_id</td>";	
			echo "<td>$name</td>";	
			echo "<td>$dept</td>";
			echo "<td>$actual_in <br><font color='red'>$clockin</font></td>";
			echo "<td>$actual_out<br><font color='red'>$clockout</font></td>";
			echo "<td>$actual_total<br><font color='red'>$total</font></td>";
			echo "$disp23";
			echo "$process_by";
		echo "</tr>";
}	
?>	
	</tbody>
		<tfoot>
			<tr>
				
			</tr>
		</tfoot>
	</table>
