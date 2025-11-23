<?php

require_once('../templates/header.php');
require_once('../lib/dl6mhw.php');

$sql='select count(*) from qso';

print "<h2>Basiszahlen</h2>\n";

print "QSOs: ".niceNumber(sql2val('select count(*) from qso'));
print "<br>OPs (User): ".niceNumber(sql2val('select count(*) from op'));
print "<br>call2dok-Einträge (User): ".sql2val('select count(*) from call2dok');
print "<br>DOKs: ".sql2val('select count(*) from (select dok from qso group by dok) as xxx');
print "<br>DXCCs: ".sql2val('select count(*) from (select dxcc from qso group by dxcc) as xxx');


print "<p><h6>Conteste und QSO-Zahlen</h6>";
$sql='select year(utc),contest,count(*) from qso group by contest, year(utc) order by year(utc)';
#print $sql;
#sql2table($sql);
require_once('../templates/footer.php');

?>