<?php
class Antrag {
  // Properties
  public $name;
  public $color;

  // Methods
  
  function view() {
	print "View vom Antrag";   
  }	  

  function pdf() {
	print "PDF Urkunde zum Antrag";   
  }	  
  
  function qsoListe() {
	  
  }	  
  
  function set_name($name) {
    $this->name = $name;
  }
  function get_name() {
    return $this->name;
  }
}
?>