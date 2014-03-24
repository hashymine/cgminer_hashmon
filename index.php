<?php
/*########################################################
HashMon CGMINER REMOTE MONITORING SCRIPT WITH ALERTS
Hackedp By: hashymine
Version: 2.420

If you like it please support it with donating:
BTC : 1FEsSfkAwRy6X6CN52R3Pf2NqKGKpypDCF
########################################################*/

<?php
include_once ('./functions.inc.php');
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Your HashMon</title>
	<meta http-equiv="refresh" content="<?php echo SCRIPT_REFRESH?>; URL=<?php echo SCRIPT_URL?>">
	<link rel="stylesheet" type="text/css" href="theme.css">
        
</head>
<body>

<center><h><font size=8 color=black><b>Your HashMon</b></font></h></center>
	<center>Message</center>
<center><?php include 'rigmeter.php'; ?></center>
	<center>Message/Ads</center>
<center><div style="width:73%; margin-bottom:8px;"><?php include 'topmon.php'; ?></div></center>
	<center>Message/Ads</center>
<center><div style="width:47%; margin-top:-18px;"><?php include 'basemon.php'; ?></div></center>
	<br>
<center><font size=3>Current Server Time: <b><?php echo date('Y-m-d H:i:s') ?></b> EST</font></center>
<center><font size=2><b>This Site Will Refresh Every 60 Sec.</b></font></center>
	<center>Banner/Ads</center>
	<center>Banner/Ads</center>
</body>
</html>
