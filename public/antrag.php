<?php
require_once('../lib/dl6mhw.php');
class Antrag {
  // Properties
  private $dbh;
  public $id;
  public $did;
  public $callsign;
  public $qsos;
  public $ldate;  

  // Methods
  
  function __construct($dbh,$id,$callsign) {  
    $this->id = $id;
    $this->dbh = $dbh;
	#leere Diplomantrag in DB
	if ($id=='0') mysqli_query($dbh,"insert into antrag (did,callsign) values ('1','$callsign')");else $this->load();
  }

  function load() {
    $tuple=sql2array("select callsign,did,ldate from antrag where id=".$this->id);
    $this->callsign=$tuple['callsign'];
    $this->did=$tuple['did'];
    $this->ldate=$tuple['ldate'];
    $sql="select id, call2, band, txmode from qso where call1='".$this->callsign."' limit 10";
    $this->qsos=sql2bigarray($sql);
  }
  
  function show() {
	print "$this->id | $this->did | $this->callsign | $this->id";  
    #print_r($this->qsos);
	print "<table>";
	foreach ($this->qsos as $qso) {
		print "<tr>";
 		foreach ($qso as $f) print "<td>$f</td>";
		print "</tr>";
	}	
	print "<table>";
 }

  function update() {
  }

  function set_diplom() {
    return $this->diplom;
  }
}


?>