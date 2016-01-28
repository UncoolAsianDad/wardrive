<?php
if (isset($_GET['location']))
	$location = $_GET['location'];
else
	$location = '%';

$wfonly = isset($_GET['wfonly']);
//$twofouronly = isset($_GET['twofouronly']);

mysql_connect("localhost", "wifi");
mysql_select_db("wifi");

	$SQL = "SELECT distinct location from aps";
	$result = mysql_query($SQL);
	while ($obj = mysql_fetch_object($result)) {
		$locations[] = $obj;
	}
	mysql_free_result($result);

$SQL = "
SELECT
	substr(mac,1,15) AS mac_prefix,	
	location,
	avg(`signal`) AS avgStr,
	substr(freq,1,1) AS freq,
	group_concat(ssid SEPARATOR '<br/>') as ssid,
	count(*) AS count
FROM aps "
." WHERE "
." `location` = '$location' AND "
//."	freq < 5000 AND "
.( $wfonly ? "	ssid like 'Wavefront - %' AND " : "")
." true "
."GROUP BY
	location,
	substr(freq,1,1),
	substr(mac,1,15)
ORDER BY
	location, freq, avgStr desc, mac_prefix;
";

$result = mysql_query($SQL);
while ($obj = mysql_fetch_object($result)) {
	$aps[] = $obj;
}

mysql_free_result($result);
mysql_close();

$mac = array();
$two = array();
$fiveeight = array();

foreach ($aps as $ap):
	$mac[$ap->mac_prefix][] = $ap;
	if ($ap->freq == 2)
		$two[]=$ap;
	else 
		$fiveeight[] = $ap;
endforeach;

?>

<html>
	<head>
	<style type="text/css">
	table thead {
		font-weight: bold;
	}
	div.locations {
		padding:4px;
	}
	</style>
	</head>
	<body>

<div class="locations">
<?php
foreach ($locations as $loc) {
	printf("%s<a href='?location=%s%s'>%s</a>", $sep, $loc->location, ($wfonly?"&wfonly":""), $loc->location);
	$sep = " | ";
}
?>
</div>

<?php if (isset($location)): ?>
<table>
<tr>
<td valign="top">
	<table border=1 cellpadding="4" style=" border-collapse: collapse;">
		<thead>
			<tr>
				<td>location</td>
				<td>freq</td>
				<td>mac prefix</td>		
				<td>avg strength</td>				
				<td>ssid</td>
				<td>count</td>
			</tr>
			</thead>
			<tbody>
<?php foreach ($two as $ap): ?>
			<tr>		
				<td><?php echo $ap->location ?></td>
				<td><?php echo $ap->freq ?>Ghz</td>
				<td><?php echo $ap->mac_prefix ?></td>		
				<td><?php echo $ap->avgStr ?></td>				
				<td><?php echo $ap->ssid ?></td>
				<td><?php echo $ap->count ?></td>
			</tr>
<?php endforeach;?>
		</tbody>
	</table>
	</td>
	<td valign="top">
	<table border=1 cellpadding="4" style=" border-collapse: collapse;">
		<thead>
			<tr>
				<td>location</td>
				<td>freq</td>
				<td>mac prefix</td>		
				<td>avg strength</td>				
				<td>ssid</td>
				<td>count</td>
			</tr>
			</thead>
			<tbody>
<?php foreach ($fiveeight as $ap): ?>
			<tr>		
				<td><?php echo $ap->location ?></td>
				<td><?php echo $ap->freq ?>Ghz</td>
				<td><?php echo $ap->mac_prefix ?></td>		
				<td><?php echo $ap->avgStr ?></td>				
				<td><?php echo $ap->ssid ?></td>
				<td><?php echo $ap->count ?></td>
			</tr>
<?php endforeach;?>
		</tbody>
	</table>
<?php endif ?>
</td>
</tr>
</table>
	</body>
</html>