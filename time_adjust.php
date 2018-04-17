<?
session_start();
require 'db.php';
if($_SESSION['user']!='admin' && $_SESSION['user']!='manager'){
	echo "You have not enough permission to access this page.";	
	exit;	
}

$id = $_GET['id'];

$sql = ("SELECT * FROM timecard where id='".$id."'");
$result = mysqli_query($conn, $sql) or die ("ERROR Adjusting time ".mysqli_error($conn));
$row = mysqli_fetch_assoc($result);

$today = date('Y-m-d',strtotime($row['clockin']));

$in = date('h:i:s a',strtotime($row['clockin']));
$out = date('h:i:s a',strtotime($row['clockout']));
$name = $row['full_name'];
$payrate = $row['rank_rate'];
?>
<html>
<head>
<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/base-min.css">	
<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
</head>
<body>
<br>
<form class="pure-form pure-form-aligned" method="post" action=""  >
    <fieldset>
        <div class="pure-control-group">
            <label for="name">Employee Name: </label>
            <b><? echo $name; ?></b> 
            <span class="pure-form-message-inline"></span>
        </div>
		
        <div class="pure-control-group">
            <label for="timein">In:</label>
            <input id="time" name="timein" type="time" value="<? echo $in; ?>">
            <span class="pure-form-message-inline">clock-in time to adjust be <font color="red"><? echo $in; ?></font></span>
        </div>
        <div class="pure-control-group">
            <label for="timeout"  >Out:</label>
            <input id="time" name="timeout" type="time" value="<? echo $out; ?>">
            <span class="pure-form-message-inline">clock-out time to adjust be <font color="red"><? echo $out; ?></font></span>
        </div>
        <div class="pure-controls">
            <label for="cb" class="pure-checkbox">
                <input id="cb" type="checkbox" required=""> I <b><? echo $_SESSION['fname'] ?></b> hereby made this adjustment.
            </label>
			<input type="hidden" name="act88" value="run" />	
            <button type="submit" class="pure-button pure-button-primary" >Submit</button> <button type="reset" class="pure-button pure-button-primary">Reset</button>  
        </div>
       
    </fieldset>
</form>
<?
if (!empty($_POST['act88'])){
	
	if(!empty($_POST['timein'])){
		$adj_in = $today.' '.date('H:i:s', strtotime($_POST['timein']));
		
		$processor = $_SESSION['fname']. ' '.$_SESSION['lname'];
		$update = ("UPDATE timecard SET clockin='".$adj_in."', actual_time_out='".$adj_out."', recon='".$processor."' WHERE id='".$id."' ");
		$result_update = mysqli_query($conn, $update) or die ("ERROR Updating Timecard".mysqli_error($conn));
		
	}elseif(!empty($_POST['timeout'])){
		$adj_out = $today.' '.date('H:i:s', strtotime($_POST['timeout']));
		
		$processor = $_SESSION['fname']. ' '.$_SESSION['lname'];
		$update = ("UPDATE timecard SET clockout='".$adj_out."', actual_time_out='".$adj_out."', recon='".$processor."' WHERE id='".$id."' ");
		$result_update = mysqli_query($conn, $update) or die ("ERROR Updating Timecard".mysqli_error($conn));
		
	}elseif((!empty($_POST['timeout'])) && (!empty($_POST['timein']))){
		$adj_out = $today.' '.date('H:i:s', strtotime($_POST['timeout']));
		$adj_in  = $today.' '.date('H:i:s', strtotime($_POST['timein']));
		
		$processor = $_SESSION['fname']. ' '.$_SESSION['lname'];
		$update = ("UPDATE timecard SET clockin='".$adj_in."', clockout='".$adj_out."', actual_time_out='".$adj_out."', recon='".$processor."' WHERE id='".$id."' ");
		$result_update = mysqli_query($conn, $update) or die ("ERROR Updating Timecard".mysqli_error($conn));
		
	}
$sql2 = ("SELECT * FROM timecard where id='".$id."'");
$result2 = mysqli_query($conn, $sql2) or die ("ERROR Adjusting time ".mysqli_error($conn));
$row2 = mysqli_fetch_assoc($result);
$a1 = date('H:i:s', strtotime($row2['clockin']));
$a2 = date('H:i:s', strtotime($row2['clockout']));

if($a1 = '08:00:00' && $a2 = '17:10:00'){
	$payfortheday = $payrate * 8;
	$reg = '08:00:00';
}elseif($a1 = '05:50:00' && $a2 = '15:00:00'){
	$payfortheday = $payrate * 8;
	$reg = '08:00:00';
}elseif($a1 = '05:50:00' && $a2 = '12:00:00'){
	$payfortheday = $payrate * 4;
	$reg = '04:00:00';
}

$update2 = ("UPDATE timecard SET payroll='".$payfortheday."', total='".$reg."' WHERE id='".$id."' ");
$result_update2 = mysqli_query($conn, $update2) or die ("ERROR Updating Timecard".mysqli_error($conn));
	
echo "<br><br><br><br><center><img src='/unix/img/clok1.gif' height=50 width=50> <h4> Updating Time Information</h4></center>";
echo '<META HTTP-EQUIV=REFRESH CONTENT="2; filter_search.php">';	
}
?>

<center><a href="filter_search.php"><button class="pure-button pure-button-primary"> Cancel Process </button></a></center>

</body>
</html>