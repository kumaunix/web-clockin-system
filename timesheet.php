<?php
session_start();
if($_SESSION['user']!='admin' && $_SESSION['user']!='manager'){
	echo "You have not enough permission to access this page.";	
	exit;	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<title> TimeSheet View Admin</title>
	<meta charset="utf-8">
	  <link rel="stylesheet" href="table.css">
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
 <?php
 if (isset($_SESSION['user'])) {
	 echo sprintf("_paq.push(['setUserId', '%s']);", $_SESSION['mail']); 
}?>
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//unx.co.jp/piwik/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '1']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Piwik Code -->	  
</head>	
<body>
<br>
	<center><a href="/puleset/"><button>Admin Setting</button></a>&nbsp;<a href="filter_search.php"><button>Search Timesheet</button></a>
		<?php require 'vacation.php'; // Vacation Requested for Future shown ?>
	<table class="data-table">
		<caption>TODAY</caption>
		<thead>
			<tr>
				<th>date</th>
				<th>id</th>
				<th>Info</th>
				<th>In</th>	
				<th>out</th>	
				<th><font color="blue">OT hours</font><br><font color="red">reg hours</font></th>	
		<? if($_SESSION['id']==4 || $_SESSION['id']==1){?>		
				<th>total (¥)</th>
		<? }?>				
			</tr>
		</thead> 
		<tbody>	
<?
require 'db.php';
$report=("SELECT timecard.*, users.profile_pic FROM timecard INNER JOIN users ON users.id=timecard.employment_id WHERE date(clockin)=CURDATE() ORDER BY timecard.dept");
$result=mysqli_query($conn, $report) or die ("Error Retrieving Timesheet".mysqli_error($conn));
while($row = mysqli_fetch_array($result)){
	$name  = $row['full_name'];
	$dept  = $row['dept'];
	$em_id = $row['employment_id'];
	$aho   = date('M-dS-Y', strtotime($row['clockin']));
	$aho1  = date('M-dS-Y', strtotime($row['actual_time_in']));
	$process = $row['process_by'];
	$paycheck = $row['payroll'];
	$today_labor_cost +=$row['payroll'];
	$file = $row['profile_pic'];
	$fileshow = "../unix/dept/img/profile/$file";
	
	//Pull out Today`s clock in and timesheet
	$current_day = date("M-dS-Y");
	$yesderday   = date('M-dS-Y',strtotime("-1 days"));
	$daybefore   = date('M-dS-Y',strtotime("-2 days"));
	if($aho == $current_day || $aho1 == $current_day){
		$date = "Today";
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
		$tab1 = "<td>$actual_in <br><font color='red'>$clockin</font></td>";
		$tab2 = "<td>$actual_out<br><font color='red'>$clockout</font></td>";
	}else{
		$actual_total = 0;
		$actual_in ="Processed by<br><b>$process</b>";
		$actual_out = "$comment";
		$clockin = "";
		$clockout = "";
		$total_reg ="";
		$tab1 = "<td>$actual_in <br><font color='red'>$clockin</font></td>";
		$tab2 = "<td style='background-color: #FFFF00'>$actual_out<br><font color='red'>$clockout</font></td>";
	}
		// Display Today`s clock-in Time		 		
		echo "<tr>";
			echo "<td>$date</td>";
			echo "<td><br><img src='$fileshow' height=50 width=50 style='border-radius: 50px;'></td>";	
			echo "<td><b>$name</b><br>$dept<br>$em_id</td>";
			echo $tab1;
			echo $tab2;
			echo "<td><font color='blue'>$total_overtime</font><br><font color='red'>$total_reg</font></td>";
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
			<th>￥<? echo number_format($today_labor_cost); ?></th>
		<? }?>	
				
			</tr>
		</tfoot>
	</table><br><br>
<? require 'yesterday.php'; ?>	
<br><br>
<? require 'afterday.php'; ?>	
</center>	
</body>	
</html>