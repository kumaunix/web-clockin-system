<?
//Kumamoto IP address Restriction  
$kumamoto_ip_start ='222.10.170.1'; 
$kumamoto_ip_stop = '222.10.170.99';
$kumamoto_ip = ($_SERVER['REMOTE_ADDR'] >= $kumamoto_ip_start && $_SERVER['REMOTE_ADDR'] <= $kumamoto_ip_stop);