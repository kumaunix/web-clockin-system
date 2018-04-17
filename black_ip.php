<?
/*   All Access to Unix Website are restricted to IP Address of Unix Offices Only
 *   
 */ 
 // GET User IP address 
 $myip = $_SERVER['REMOTE_ADDR'];
 
//Kumamoto IP address Restriction  
$kumamoto_ip_start ='222.10.0.1'; 
$kumamoto_ip_end = '222.10.171.222';
$kumamoto_ip = ($myip >= $kumamoto_ip_start && $myip <= $kumamoto_ip_end);

//Vietnam IP address Restriction
$hanoi_ip_start ='117.0.252.1'; 
$hanoi_ip_stop = '117.0.252.50';
$hanoi_ip = ($myip >= $hanoi_ip_start && $myip <= $hanoi_ip_stop);

$homeip_start ='60.41.248.38'; 
$homeip_stop = '60.41.248.41';
$homeip = ($myip >= $myip_start && $myip <= $myip_stop);


//Only Unix Office in Kumamoto and Hanoi can Access the Time Clock
if ((!$hanoi_ip) && (!$kumamoto_ip) && (!$homeip)){
	die('<br><b>ERROR CODE 404.</b> The Requested <b>URL</b> Not Found on Server. ');
}

