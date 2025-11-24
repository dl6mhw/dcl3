<?php

require_once('../templates/header.php');
require_once('../lib/dl6mhw.php');

$sql="select call2, loc, utc, status from qso where loc<>'' and call1='DL6MHW' group by call2, loc";
$sql="select 'call2', left(loc,4) locator, utc, status from qso where loc<>'' and call1='DL6MHW' group by left(loc,4)";

print "<h2>Locs von DL6MHW</h2><pre>\n";
$rarr=sql2bigarray($sql);
print "<table>";
$num=0;
foreach ($rarr as $qso) {
	$num++;
  $call2=$qso['call2'];  
  $loc=$qso['locator'];	
  $utc=$qso['utc'];	
  print "<tr><th>$num</th><td>$call2</td><td>$loc</td><td>$utc</td></tr>";
}  
print "<table>";

require_once('../templates/footer.php');

?>