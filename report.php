<?php
require 'db.php';
session_start();
if($_SESSION['user']!='admin'){
	echo "You have not enough permission to access this page.";	
	exit;	
}
$report=("Select * from info");
$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet".mysqli_error($conn));
while($timesheet = mysqli_fetch_array($result)){
	$name = $timesheet['fullname'];
	$dept=$timesheet['department'];
	$clock = $timesheet['inout'];
	$date = date('D, M jS, Y',strtotime($timesheet['clock_in']));
	$in = date('h:i:s a',strtotime($timesheet['clock_in']));
	$out = date('Y-m-d H:i:s',strtotime($timesheet['clock_out']));  
	 
	 /*If ($clock==1){
		$in = date('H:i:s',strtotime($timesheet['timestamp']));
	}else{
		$in = "absent today";
	}
	
	if ($clock==0){
		$out = date('H:i:s',strtotime($timesheet['timestamp']));
	}else{
		$out = "On duty";
	}*/


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<meta charset="utf-8">
	  <link rel="stylesheet" href="table.css">
</head>	
<body>
<br>
	<center><a href="../unix/table.php"><button>Back to Database</button></a>
	<table class="data-table">
		<caption class="title">Edit Settings below as needed</caption>
		<thead>
			<tr>
				<th>date</th>
				<th>Name</th>
				<th>department</th>
				<th>In</th>	
				<th>out</th>				
			</tr>
		</thead>
		<tbody>	
		<tr>
			<td><? echo $date; ?></td>	
			<td><? echo $name; ?></td>
			<td><? echo $dept; ?></td>
			<td><? echo $in; ?></td>	
			<td><? echo $out; ?></td>		
		</tr>
	
	
	</tbody>
		<tfoot>
			<tr>
				<th colspan="2"></th>
				<th></th>
			</tr>
		</tfoot>
	</table>
<?php
}
?>
</center>	
</body>	
</html>