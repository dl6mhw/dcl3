<?php
require_once "../lib/dl6mhw.php";

print_r($argv);

$start = microtime(true);     
$sql= "select call1, count(*) from qso where call1 like 'D%' group by call1"; 
$r=mysqli_query($dblink,$sql);

while ($row = mysqli_fetch_row($r)) {
  $callsign=$row[0];
  $anz=$row[1];
  $sql2= "select call2, band, txmode, utc, id from qso where call1= '$callsign' and status='*' order by call2"; 
  $r2=mysqli_query($dblink,$sql2);
  print "\n$callsign [$anz]\n";
  $start2 = microtime(true);     
  while ($row2 = mysqli_fetch_row($r2)) {
	$call2=$row2[0];
    $band=$row2[1];	
    $txmode=$row2[2];	
    $utc=$row2[3];
    $id=$row2[4];
    #print "   $call2\t$utc\t$txmode\t$band\n";
    print "."
	;$sql3="select count(*) from qso where call1='$call2' and call2='$callsign' and band='$band' and txmode='$txmode' and utc='$utc'";
    #print "$sql3\n"; 	
#	 print "update qso set status = 'c' where id=$id)") ;
	if (sql2val($sql3)>0) mysqli_query($dblink,"update qso set status = 'c' where id=$id") ;
	else mysqli_query($dblink,"update qso set status = '-' where id=$id") ;
  }	  
  print "\n   Gesamtzeit:".round(microtime(true)-$start2,2)."sec\n";


}
print "   Gesamtzeit:".round(microtime(true)-$start,2)."sec\n";
print "\=== Fertsch OK\n";
exit;
