<?
/*   All sick, vacation and leave workers today and updated @8:00 pm. 
 *   Status are updated. They can clock in tomorrow. 
 */ 
$time = date_default_timezone_get();
$time_format = date('H:i:s', strtotime($time));

if ($time_format > "19:30:00"){
	$sql=("UPDATE users SET status_today='' ");
    $query = mysqli_query($conn, $sql) or die ("Error Clocking In Into timesheet:".mysqli_error($conn));
}
?>