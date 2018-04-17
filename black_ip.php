 $myip = $_SERVER['REMOTE_ADDR'];
 
//Office 2 IP address Restriction  and update ip address to restrick clock to this ip address
// since ip address may change you may put 111.11.00.0 ~ 11.12.00.0 

$add_ip_start ='00.00.00.00'; 
$add_ip_end = '00.00.00.00';
$add_ip = ($myip >= $kumamoto_ip_start && $myip <= $kumamoto_ip_end);

//Office 1 IP address Restriction
$add1_ip_start ='00.00.00.00'; 
$add1_ip_stop = '00.00.00.00';
$add1_ip = ($myip >= $hanoi_ip_start && $myip <= $hanoi_ip_stop);


if ((!$add1_ip) && (!$add_ip))){
	die('<br><b>ERROR CODE 404.</b> The Requested <b>URL</b> Not Found on Server. ');
}
