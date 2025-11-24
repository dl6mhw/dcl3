<?php
require_once "../../../dcl3conf.php";
require_once('../lib/ParseDownMHW.php');

function pd($inhalt) {
	return Parsedown::instance()->text($inhalt);	  
}
# sorry - I call the file dl6mhw.php to have a place for all the 
# personal stuff/rotuines/etc 


#Some global data structures
/*
#entryCalls für schnelle Levenstein Suchen
$sql="select mycall, sum_qso from t_entry";
$result = mysqli_query($dblink,$sql);
$entryCalls=array();
while ($row = mysqli_fetch_row($result)) {
  $entryCalls[$row[0]]=$row[1];
}

#rxCalls für schnelles Suchen, mehrere Exchanges ---> höhere Zahl zuerst
$sql="select callsign, cnt from t_call order by callsign, cnt desc";
$result = mysqli_query($dblink,$sql);
$rxCalls=array();
while ($row = mysqli_fetch_row($result)) {
  $rxCalls[$row[0]]=$row[1];
}

#rxCalls für schnelles suchen
$sql="select wrtc_call from t_wrtc";
$result = mysqli_query($dblink,$sql);
$wrtcCalls=array();
while ($row = mysqli_fetch_row($result)) {
  $wrtcCalls[$row[0]]=1;
}

#hqCalls für schnelles suchen
$sql="select hqcall, hqinfo hqcall from t_hq";
$result = mysqli_query($dblink,$sql);
$hqCalls=array();
while ($row = mysqli_fetch_row($result)) {
  $hqCalls[$row[0]]=$row[1];
}
*/
function setError($qso_id,$mycall,$rxcall,$match_id,$error_typ,$error_info) {
  global $dblink;


  ## DXX checker added ##
  if (isset($_SERVER['PHP_AUTH_USER'])) $checker=strtoupper($_SERVER['PHP_AUTH_USER']);
  else $checker="script";
  ## DXX qso_id should never be 0, match_id can be empty ##
  $sql = "insert into t_error (qso_id,mycall,rxcall";
  if ($match_id) $sql .= ",match_id";
  $sql .= ",error_typ,error_info, checker)";
  $sql .= " values ($qso_id,'$mycall','$rxcall'";
  if ($match_id) $sql .= ",'$match_id'";
  $sql .= ",'$error_typ','$error_info', '$checker')";
  #print "$sql\n";
  #print "ERR:$error_info\n\t$sql\n";
  $s = mysqli_query($dblink,$sql);
  if (! $s) print "$sql<br>";
}

# schlechte Kopie wegen extra QTC Error Tabelle
function setErrorQTC($qso_id,$mycall,$rxcall,$match_id,$error_typ,$error_info) {
  global $dblink;

  ## DXX wrtc_flag added ##
  $wrtc_flag=isWRTC($mycall);

  ## DXX checker added ##
  $checker=strtoupper($_SERVER['PHP_AUTH_USER']);

  ## DXX qso_id should never be 0, match_id can be empty ##
  $sql = "insert into t_error_qtc (qso_id,mycall,rxcall";
  if ($match_id) $sql .= ",match_id";
  $sql .= ",error_typ,error_info,wrtc_flag, checker)";
  $sql .= " values ($qso_id,'$mycall','$rxcall'";
  if ($match_id) $sql .= ",'$match_id'";
  $sql .= ",'$error_typ','$error_info',$wrtc_flag, '$checker')";

  #print "ERR:$error_info\n\t$sql\n";
  $s = mysqli_query($dblink,$sql);
  if (! $s) print "$sql<br>";
}


#print_r($wrtcCalls);

function nearCalls($callsign) {
  global $entryCalls;
  $calls=array();
  $dist=1;
  if (strlen($callsign)>3) $dist=2;
  #print "NEAR $callsign $dist ...";
  foreach ($entryCalls as $xcall=>$xanz) 
    if (levenshtein($xcall,$callsign)<=$dist) $calls[$xcall]=$xanz;
  return $calls;   
}




function formatZeit($datum,$utc) {
  $zeit='2099-12-31 12:57';
  if (preg_match('/([12][0-9][0-9][0-9])([0-9][0-9])([0-9][0-9])/',$datum,$tw)) {
              $datum="$tw[1]-$tw[2]-$tw[3]";
  }
  if (preg_match('/([0-9][0-9])([0-9][0-9])/',$utc,$tw)) {
     $zeit=$datum." $tw[1]:$tw[2]";
  }
  return $zeit;
}

# funktioniert für nur Contest-QSOs
function qrg2band($qrg) {
	if ($qrg<100) $qrg=1000*$qrg; # falls nur MHz angegeben

	if ($qrg>=1800 && $qrg<=2000)	return	160;
	if ($qrg>=3500 && $qrg<=4000) 	return 80;
	if ($qrg>=7000 && $qrg<=7400) 	return 40;
	if ($qrg>=14000 && $qrg<=14550) return 20;
	if ($qrg>=21000 && $qrg<21550) 	return 15;
	if ($qrg>=28000 && $qrg<29900) 	return 10;
	return	0;
}


#full match 10 Min with time window without rst and rxinfo
function fullMatch($mycall,$rxcall,$dt,$band,$txmode) {
	$full = "select qso_id, myinfo, rxinfo 
			 from t_qso 
			 where 	rxcall='$mycall' and mycall='$rxcall'
				and status!='+' 
				and abs(unix_timestamp(dt)-unix_timestamp('$dt'))<601 
				and band='$band' 
				and txmode='$txmode' 
			order by dt";
    ###print "\n$full\n";
    $row =sql2array($full);
	return $row;
}

#full match 10 Min with time window without rst and rxinfo
function fullMatch2($mycall,$rxcall,$dt,$band,$txmode) {
    $full="select qso_id, myinfo, rxinfo from t_qso where rxcall='$mycall' and mycall='$rxcall' 
    and status!='+' 
    and abs(unix_timestamp(dt)-unix_timestamp('$dt'))<121 and 
    band='$band' and txmode='$txmode' order by dt";
    #if ($rxcall=='K1A' or $mycall=='K1A') print "\n$full\n";
    $row =sql2array($full);
    return $row;
}

function matchNoBand($mycall,$rxcall,$dt,$txmode) {
    $sql="select qso_id, band from t_qso where rxcall='$mycall' and mycall='$rxcall' and status='-' 
    and abs(unix_timestamp(dt)-unix_timestamp('$dt'))<601 
    and txmode='$txmode' order by dt";
    #if ($rxcall=='K1A' or $mycall=='K1A') 
    #print "\n$sql\n";
    $row =sql2array($sql);
    return $row;
}

function matchNoMode($mycall,$rxcall,$dt,$band) {
    $sql="select qso_id, txmode from t_qso where rxcall='$mycall' and mycall='$rxcall'  and status='-' 
    and abs(unix_timestamp(dt)-unix_timestamp('$dt'))<601 
    and band='$band' order by dt";
    #if ($rxcall=='K1A' or $mycall=='K1A') print "\n$full\n";
    $row =sql2array($sql);
    return $row;
}


#SQL Query with one row and one column
function sql2val($sql) {
  global $dblink;
  #print $sql;
  $result = mysqli_query($dblink,$sql);
  if (! $result) {	print	"\nERR: $sql\n"; exit();} 
  $row = mysqli_fetch_array($result);
  return $row[0];
}

#SQL Query with one row and more columns
function sql2array($sql) {
  global $dblink;
  #print "$sql\n";
  $result = mysqli_query($dblink,$sql);
  $row = mysqli_fetch_array($result);
  return $row;
}


function sql2print($sql) {
  global $dblink;
  #print "$sql\n";
  $result = mysqli_query($dblink,$sql);
  while ($row=mysqli_fetch_row($result)) {
    foreach($row as $f) {
      if ($f=='') $f='NULL';
	  
      print "\t$f";
    }  
    print "\n";
  }
}

function sql2bigarray($sql) {
  global $dblink;
  $result = mysqli_query($dblink,$sql);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}  

function sql2table($sql) {
  global $dblink;
  #print "$sql\n";
  $result = mysqli_query($dblink,$sql);
  print "<table>\n";
  while ($row=mysqli_fetch_row($result)) {
    print "<tr>";
    foreach($row as $f) {
      if ($f=='') $f='NULL'; 
      print "<td  style='border: 1px solid green;'>$f</td>";
    }  
    print "</tr>\n";
  }
  print "</table>\n";
}

#Test if Call ist WRTC-Calls
function isWRTC($callsign) {
  global $dblink;
  $result = mysqli_query($dblink,"select count(*) from t_wrtc where wrtc_call = '$callsign'");
  $row = mysqli_fetch_array($result);
  return $row[0];
}

#Some function translate adif, prefix, cont
function prefix2adif($prefix) {
	global $dblink;
	
	if ($prefix == "")
		return	0;
	
	###$sql = "select adif from cl_cty where prefix = '$prefix' and deleted = 'n'";
	$sql="select	adif_nr
			from	t_dxcc
			where	dxcc	= '$prefix'";
	$result = mysqli_query($dblink,$sql);
	if (! $result)
		print $sql;
  
	if ($row = mysqli_fetch_array($result)) 
		return $row[0];
	else 
	  {
		#print "ERROR prefix2adif: $prefix not match\n";
	  }	
	return 0;
}

function prefix2cont($prefix) {
	global $dblink;
	
	if ($prefix == "")
		return	NULL;

	if ($prefix == "TA1") return EU;
  
	###$sql = "select cont from cl_cty where prefix = '$prefix'";
	$sql = "select continent from t_dxcc where dxcc	= '$prefix'";
	$result = mysqli_query($dblink,$sql);
	if (! $result)
		print $sql;
	
	if ($row = mysqli_fetch_array($result)) 
		return $row[0];
	else 
		print "ERROR prefix2cont: $prefix not match\n";
	return NULL;
}

function adif2cont($adif) {
	global $dblink;
	
	if ($adif == 0)
		return	NULL;
	
	###$sql = "select cont from cl_cty where adif = '$adif'";
	$sql = "select	continent
			from	t_dxcc
			where	adif_nr = $adif";
	$result = mysqli_query($dblink,$sql);
	if (! $result)
		print $sql;
	
	if ($row = mysqli_fetch_array($result)) 
		return $row[0];
	else 
		print "ERROR adif2cont: $adif not match\n";
	return NULL;
}

#makes a value list from an array
function array2string($arr) {
  $s='';
  foreach($arr as $v) $s.="'$v',";
  return substr($s,0,strlen($s)-1);
}

function niceNumber($number) {
  if ($number<1000) return $number;	
  $snumber=(string)$number;
  while (strlen($snumber)<10) $snumber=' '.$snumber;
  if ($number<1000000) return substr($snumber,4,3).'.'.substr($snumber,7,3);
  return substr($snumber,0,4).'.'.substr($snumber,4,3).'.'.substr($snumber,7,3);
  return "$snumber";
  
}	



#old function for determining prefix from call using ctc.dat import in ctyprefix
function mhwPrefix($call) {
  global $dblink;
  $cty='';
  #print "<br>---------<br>Suche für $call<br>\n";
  #vorher /P /QRP /M /A abtrennen
  $call=strtoupper($call);

  #2022 RA0-Calls
  if (strpos(' RL20LH R7AB/P ',$call)>0) {
    return('UA0');
  }

	  
  if (preg_match('/^4U1A/',$call)) {
    return('4U1V');
  }


  if (preg_match('/DP0GVN/',$call)) {
    return('CE9');
  }

  if (preg_match('/TC100TC/',$call)) {
    return('TA1');
  }

  if (preg_match('/^TA1/',$call)) {
    return('TA1');
  }
  if (preg_match('/TO7K/',$call)) {
    return('FR');
  }


  if (preg_match('/^KG4/',$call)) {
      if (preg_match('/^KG4[A-Z][A-Z]$/',$call)) return('KG4'); #2 stelliger Suffix liefert KG4 sonst K4
      else return('K4');
  }
  
  //	DXX		also for /AM stations
  if (preg_match('/\/AM$/',$call)) return ('');
  if (preg_match('/\/MM$/',$call)) return ('');
  
  $call=preg_replace('/\/QRP$/','',$call);
  $call=preg_replace('/\/QRPP$/','',$call);
  $call=preg_replace('/\/M$/','',$call);
  $call=preg_replace('/\/P$/','',$call);
  $call=preg_replace('/\/A$/','',$call);
  $call=preg_replace('/\/LH$/','',$call);

  #print "  nach Abschneiden: $call\n";
  #erst das ganze Call
  #	print "..test ganzes Call... ";
  $result = mysqli_query($dblink,"select * from ctyprefix where callprefix = '$call'");
  if ($row = mysqli_fetch_array($result)) $cty=$row[0];
  $result = mysqli_query($dblink,"select * from ctyprefix where callprefix = '=$call'");
  if ($row = mysqli_fetch_array($result)) $cty=$row[0];
  #print_r($row);
  #print " select * from ctyprefix where callprefix = '=$call' NUX";
  $c=$call;
  ##MM/DL6MHW
  $n='';
  #bei / den kleineren Teil nutzen
  if (strpos($c,'/')>0) {
    $p=preg_split('/\//',$c);
	#print_r($p);
    if (strlen($p[1])<strlen($p[0]))
    {
		$c=$p[0]; //	call 
		$n=$p[1]; //	extension
	}
    else {
		$c=$p[1]; //	call
		$n=$p[0]; //	extension
	}
    #print "Rest nach /: $c  $n<br>";
  }
  $c.='#';
  #print "c=$c n=$n p0=$p[0] p1=$p[1]\n";
  
  #wenn Rest nur Ziffer dann erste Zahl im Suffix ersetzen
  #ra1aa/9 --> ra9aa
  #7N0MM/1 --> 7M1MM
  $number_only = false;
  if (preg_match('/^[0-9]$/',$n)) {
    if (preg_match("/([0-9]*[A-Z]+)([0-9])([0-9A-Z]+)/", $c, $matches)) {
      #echo "Match was found <br />";
      ##echo ":$matches[1]:$matches[2]:$matches[3]:<br>";
      $c=$matches[1].$n.$matches[3];
	  #print "Nur -Ziffer-->:$c\n";
	  $number_only	= true;
    }
   }
   else if ($n != '') {
	   $c=$n;
  	   #print "Mehr als nur Ziffer-->:$c\n";
   }

  
	#print "... weiter Teilcall $c...";
    $fix_c=$c;	
	while (strlen($c)>0) {
		$sql = "select * from ctyprefix where callprefix = '$c'";
 		$result = mysqli_query($dblink,$sql);
		#print "\n$sql\n";
		if	(! $result) {
			print	"ERR: $sql\n";
			#exit();
		}
		if ($row = mysqli_fetch_array($result)) {
			$cty=$row[0];
			
			//	DXX
			if ($number_only) {
			//	KH.../0-9 = USA
			//	KL.../0-9 = USA
			//	KP.../0-9 = USA
			if (substr($cty,0,2) == "KH" or
				substr($cty,0,2) == "KL" or
				substr($cty,0,2) == "KP") {
					$cty = "K";
			}
			//	UA.../9 = UA9 (but UA9X,F,G = EU)
			else
			if ($cty == "UA" and $n == "9")
				$cty = "UA9";
			}	

#ugly 2021++ für RT80XXX und Co

if ($cty=='UA9') {
 if (preg_match('/^[UR][A-Z]([890])/',$fix_c.'#',$n)) {
     $ziffer=$n[1];
	 return "UA".$ziffer;
   }
}


#ugly 2020
$ziffer='';
   if (preg_match('/([0-9])([A-Z\/]*)#/',$fix_c.'#',$n)) {
     $ziffer=$n[1];
   }
   #print ".. $cty ...$c=Ziffer:$ziffer\n";

 if ($contest="WAE") {
       if ($cty=='UA9') $cty='UA'.$ziffer;
       if (strpos(' K,VE,ZL,PY,ZS,VK,JA,BY, ',$cty)>0) {
        $cty=$cty.$ziffer;
       }
      }

			
			
			return $cty;
		}  
		$c=substr($c,0,strlen($c)-1);
		#print "   $c \n";
	}
  return($cty);	
}   
?>