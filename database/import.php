<?php
/*
import.php
- liest die Daten aus der jeweiligen Contest-DB mit dem Join von Franta
- es werderden angelegt
  - ein dcl3-Benutzer op
  - ein Eintrag für die call2dok-DB
  - die QSOs - kommen aus logdata.data und haben DXCC und DOK aus logentry
- Parameter sind der contestname und das Jahr
- der contest wird beim qso und bei call2dok abgespeichert - bei Kurzcontesten mit Part
*/
require_once "../lib/dl6mhw.php";

print_r($argv);

$conteste=array('10m','ac','ft4','hsw','shortry','ukw','waecw','waessb','wag','waertty','xmas');
if (isset($argv[1])) $contest=$argv[1]; else $contest=''; 
if (!in_array($contest,$conteste)) {
  print "Contest $contest ist nicht in:";
  print_r($conteste);
  exit;
}	

if (isset($argv[2])) $jahr=$argv[2]; else $jahr='2025'; 
$dbname=$contest;
if ($contest=='10m') $dbname='d10m';
$dblink2=connect2($dbname);

$start = microtime(true);     
importAllLogs($contest,$jahr);
print "   Gesamtzeit:".round(microtime(true)-$start,2)."sec\n";
print "\=== Fertsch OK\n";
exit;


//	read all logs from directory
function importAllLogs($contest,$jahr) {
  global $dblink;	
  global $dblink2;	
  	

  print "Start Import";
  #print_r($_SESSION);
  #exit;

  # Daten werden aus der Contest-DB geholt	
	
  /* check connection */
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }
  if (!$dblink2) {
    die('keine Verbindung möglich: ' . mysqli_error($dblink2));
  }

  #Namen der Quelldatenbanken
  
  if ($jahr<>'all') $jahr_cond="c_year ='$jahr' and "; else $jahr_cond='';  
  $sql="SELECT lid, callsign, dxcc, data, category, logentry.updated,logentry.created, dok, c_edition, c_year 
      FROM logdata LEFT JOIN logentry ON eid = e_id 
      WHERE lid IN (SELECT MAX(lid) 
                      FROM logdata RIGHT JOIN logentry ON eid = e_id 
                      WHERE $jahr_cond l_valid > 0
                      GROUP BY e_id) AND l_valid > 0 order by c_year desc,lid desc limit 100000";
  #für UKW sind mehrer Logs in jedem Contest möglich
  if ($contest=='ukw') $sql="SELECT lid, callsign, dxcc, data, category, logentry.updated,logentry.created, dok, c_edition, c_year 
      FROM logdata LEFT JOIN logentry ON eid = e_id 
                      WHERE $jahr_cond l_valid > 0 
  order by lid desc limit 100000";
 # print "Alle Logs aus Contest \n".$sql."\n";;

  $r=mysqli_query($dblink2,$sql);

  while ($row = mysqli_fetch_row($r)) {
  #print_r($row);
    $callsign=$row[1];
	$lid=$row[0];
	$dxcc=$row[2];
	$dok=$row[7];
	$jahr=$row[9]; #Jahr aus der Abbfrage wegen Mehrjahresanfragen
	if (strlen($row[8])>0) $part=':'.$row[8]; else $part='';
    
	print "\nDB-Call:$callsign $dxcc $dok $contest $part $jahr [$lid]\n";
		$lid=$row[0];
		$call1=$row[1];
		$data=$row[3];
		$created=$row[3];
		$data= str_replace("'", "", $data);
		$cat=$row[4];
		$cr=$row[5];
		#wenn updated timetamp 0 dann cr aus created
		if ($row[5]=='0000-00-00 00:00:00') $cr=$row[6]; 
		$dxcc=$row[2];
		if ($contest=='ukw') analyzeEDI($contest.$part,$call1,$data,$dxcc,$dok);
		else analyzeCBR($contest.$part,$call1,$data,$dxcc,$dok);
  $sql2="insert into call2dok (contest, jahr, callsign, dok, start) values ('$contest$part','$jahr','$callsign','$dok','$cr')";
  $r2=mysqli_query($dblink,$sql2);
  #print "\n$sql\n";
  }
}

function analyzeEDI($contest,$call1,$data,$dxcc,$dok) {
  global $dblink;
  print "\nanalyzeEDI:$contest,$call1,$dxcc,$dok\n";
    $start = microtime(true);     
	$logcall=$call1;
	$mycall=$call1;
	$qsoLines=0;
	$importDupes=0;
  $lines=preg_split('/\n/',$data);
  $email='nomail';	
  $loc='';
  $band='nux';
  $start=0;
  #print $data;
  $call1=$logcall;
  foreach($lines as $line){	
    #print "$line\n";
    #print "$line\n";
    if ($start>0) {
		#print "$start ... ";
		$w=preg_split('/;/',$line);
        if (!isset($w[9])) continue;
		#print_r($w);
		$datum=$w[0];
		$datum='20'.substr($datum,0,2).'-'.substr($datum,2,2).'-'.substr($datum,4,2);
		$utc=$w[1];
		$call2=$w[2];
		$loc=$w[9];
		$dt=formatZeit($datum,$utc);
		$txmode='SSB';
        if ($w[3]=='2') $txmode='CW';
		#print $dt;
		$sql = "insert into qso (band,txmode,utc,call1,call2,contest,dxcc,dok,loc) values ('$band','$txmode','$dt','$call1','$call2','$contest','$dxcc','$dok','$loc')";
		#print "$sql\n";
		$s = mysqli_query($dblink,$sql);
		if	(! $s) {
					$e = mysqli_error($dblink);
					if (preg_match('/Duplicate entry/',$e)) {
						$importDupes++; 
						continue;
					}
		}
		$start++;
	} else {
		#print "$line\n";
		if (preg_match('/RHBBS=\s*([@A-Z0-9\.\/]+)/i',$line,$m)) {
  			$email=strtoupper($m[1]);
		}	
		if (preg_match('/PWWLo=([A-Z0-9]+)/i',$line,$m)) {
  			$loc1=strtoupper($m[1]);
		}	
		if (preg_match('/PCall=([A-Z0-9\/]+)/i',$line,$m)) {
  			$call1=strtoupper($m[1]);
		}	
		if (preg_match('/PBand=([A-Z0-9,\/ ]+)/i',$line,$m)) {
  			$band=strtoupper($m[1]);
		}	
		if (preg_match('/QSORecord/i',$line,$m)) {
  			$start=1;
		}	
	}
  }
  	# zum Schluss ggf. neue OP-Eintrag erzeugen
    $sql="insert into op (callsign, email) values ('$call1', '$email')";
	print "$sql\n";
	$s = mysqli_query($dblink,$sql);
				if	(! $s) {
					$e = mysqli_error($dblink);
					if (preg_match('/Duplicate entry/',$e)) {
						print "  OP mit $logcall bereits vorhanden"; 
					}
				}
	
	return $call1;

}	

function analyzeCBR($contest,$call1,$data,$dxcc,$dok) {
	global $dblink;
	global $debug;
	global $entrynr;
    $start = microtime(true);     
	$logcall=$call1;
	$mycall=$call1;

	$entrynr++;

	$qsoLines=0;
	$importDupes=0;
	
	$cmt = "Start Log import\n";
	$lines=preg_split('/\n/',$data);
    $email='nomail';	
    foreach($lines as $line){	
		$line = str_replace("'", " ", $line);
		$line = str_replace("\\", " ", $line);
		$line = str_replace("?", " ", $line);

		if (preg_match('/^CALLSIGN:\s*([A-Z0-9\/]+)/i',$line,$m)) {
			$logcall=strtoupper($m[1]);
		}
		if (preg_match('/^EMAIL:\s*([@A-Z0-9\.\/]+)/i',$line,$m)) {
			$email=strtoupper($m[1]);
			print "   Email:$email\n";
		}
		if (preg_match('/^X-EMAIL:\s*([@A-Z0-9\.\/]+)/i',$line,$m)) {
			if ($email!=strtoupper($m[1])) {
    			$email=strtoupper($m[1]);
			}	
		}
		elseif (preg_match('/^QSO:\s*([^\n]+)/i', $line, $m)) {
			$w=preg_split('/\s+/',$m[1]);
			#print_r($w);
			$qrg=$w[0];
			$txmode=strtoupper($w[1]);
			$datum=$w[2];
			$utc=$w[3];
			$call1=strtoupper($w[4]);
			$call2=strtoupper($w[7]);
     		$band=qrg2band($qrg);
			$dt=formatZeit($datum,$utc);

			//$mult=mhwPrefix($rxcall);

$sql = "insert into qso (band,txmode,utc,call1,call2,contest,dxcc,dok) values ('$band','$txmode','$dt','$call1','$call2','$contest','$dxcc','$dok')";
				$qsoLines++;
				#print "do:$sql\n"; exit;
				$s = mysqli_query($dblink,$sql);
				if	(! $s) {
					$e = mysqli_error($dblink);
					if (preg_match('/Duplicate entry/',$e)) {
						$importDupes++; 
						continue;
					}
				}

		}
	}	
	print "   QSOs:$qsoLines\tDupes:$importDupes\t".round(microtime(true)-$start,2)."sec\n";
	# zum Schluss ggf. neue OP-Eintrag erzeugen
    $sql="insert into op (callsign, email) values ('$logcall', '$email')";
	#print "$sql\n";
	$s = mysqli_query($dblink,$sql);
				if	(! $s) {
					$e = mysqli_error($dblink);
					if (preg_match('/Duplicate entry/',$e)) {
						print "  OP mit $logcall bereits vorhanden"; 
					}
				}
	
	return $logcall;

}
	
	  
?>
