<?php

require_once('../templates/header.php');


?>
<h2>Todo-Liste</h2>
<pre>
- Todo-anlegen ++-
<h3>Bugs</h3>
- Import ungleiche Email-Warnung wegen nicht upper

<h3>Funktionen</h3>
- Diplomliste ... einfache DB-Tabelle
- Diplomprogramme (OO)
- crosscheck
- EDI-Import - qso um locator erweitern
- ADIF Import
- Clublog Import
- LOTW Import
- Diagramme - Datawarehouse

<h3>Diplome</h3>
- Diplomliste
- Antragsformular 
	+ Prozess
	+ Auswertung
- 
- Backende - Management 
- Jahresdiplome - z.B. Contestübergreifendes Contest-DLD
- Modus - QSOs geprüft/ungeprüft
- WAE
- 1MGB-Suffix-Diplom
- Distriktsdiplom (10%)
- DLD
- Locator mit eingefärbten Locatoren




<h3>Datenbank</h3>
- user Tabelle (++-)
- Behandlung von /P usw.
- qso um status und contest ergänzen (+++)
- qso mit unique index für utc, band, calls, txmode(+++)
- import mit contest als arg (+++)
- import mit jahr (+++)
- import mit zeitmessung und statistik (+++)
- dlc3 passwort und rechte (+)
- darc contest importieren (++-)
- cq-contest importieren
- DXCC aus logentry (+++)
- DOK-Tabelle aus logentry DOK (+++)
- Kurzcontest mit c_edition (part) (+++)
- post-contest DXCC (?)
- post-contest DOK (?)

<h4>Häufige SQL-Anfragen</h4>
select count(*) from qso;
select contest,count(*) from qso group by contest;
select year(utc),count(*) from qso group by year(utc);

<h3>Look&Feel</h3>
- Sticky Footer  - nicht in der Mittw +--
- Leerer Bereich on Top ---
- Sinnvolle Menüs

<?php
require_once('../templates/footer.php');

?>