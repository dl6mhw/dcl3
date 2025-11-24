<?php

require_once('../templates/header.php');
require_once('../lib/dl6mhw.php');

$sql='select count(*) from qso';

print "<h2>Conteste im DCL3</h2>\n";

print "<p><h6>Conteste und QSO-Zahlen</h6>";
$sql='select contest from qso group by contest';
$rarr=sql2bigarray($sql);
#Conteste werden aus der Anfrage gelesen
foreach ($rarr as $tuple) $conteste[]=$tuple['contest'];

#print_r($conteste);
#Jahre werden mit Schleife generiert
for ($jahr=2012; $jahr<=2026;$jahr++) $jahre[]=$jahr;
#print_r($jahre);

$sql='select contest,year(utc) as jahr,count(*) anz from qso group by contest,year(utc)';
$rarr=sql2bigarray($sql);
foreach ($rarr as $tuple) #print_r($tuple);
$qsos[$tuple['contest']][$tuple['jahr']]=$tuple['anz'];

print "<table>";
print "<tr><th></th>";
foreach ($jahre as $jahr) {
  print "<th>$jahr</th>";	
}	
print "</tr>\n";
foreach ($conteste as $contest) {
  print "<tr><th>$contest</th>";
  foreach ($jahre as $jahr) {
    $data='';
    if (isset($qsos[$contest][$jahr])) $data=$qsos[$contest][$jahr];
	$data=niceNumber($data);
	print "<td align=right>".$data."</td>";

  }	  
  print "</tr>\n";	
}	
print "</table>";


require_once('../templates/footer.php');

?>