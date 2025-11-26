<?php
require_once('../templates/header.php');
require_once('../lib/dl6mhw.php');
?>
<p><h2>Geschichte des DCL</h2>
<h3>Grober Überblick</h3>
<?php print pd("
- 2001 erster Prototyp - Contest-QSOs und Papieranträge für WAE und EUDX
- 2004 DCL2-Projekt mit DARC-Print Service (abgelehnt)
- 2010 DCL reload - Diplomprozess+Manager-Backend, LOTW-Import
- 2011 PDF-Diplome, Paypal-Bezahlung, TRx-Steuerung, QR-Code Verarbeitung
- 2012 Horkheimer Preis
- 2014 DARC Community Logbook als Cloud-Logbuch
- ab 2020 Neuentwicklung DCLNext
");
?>
<h3>Geschichten, Details und Dokumente</h3>
<p>
<?php
print pd("
## Der Start - März 2021
Der erste (im Familienurlaub von DL6MHW entwickelte) Prototyp wurde auf der Tagung des DXHF-Referats vorgestellt. Mit den Daten der Referatsconteste konnten Diplomanträge generiert werden, die geprüfte QSOs enthielten und somit die Antragstellung durch die OMs/YLs und die Prüfung durch den Diplommanager erleichterten. Der WAE-Manager Hajo, DJ9MH, war sehr begeistert und trieb so das DCL-Projekt voran. Insgesamt gab es im Referat Intresse und Zustimmung. 

Der vorgeschlagene Titel *International Contest Logbook* wurde kritisch gesehen weil zu amaßend. Die Situation Anfang 2001 war kritisch. eQSL hatte einen neuen Markt aufgemacht der die Amateurfunkverbände erschreckte. Die ARRL brachte das LOTW an den Start, das aber einige Jahre brauchte, bevor etwas funktioniert. Mit dem DCL war dar DARC deutlich schneller am Markt. 

## Hamradio Juni 2001 
Offizielle Launch 

## AG iQSL Treffen in Leipzig - März 2003
Große Entwürfe, Pflichtenheft, 2 Anträge an den DARC

## AR-Tagung 2003
Der Amateurrat beschließt, dass elektktronische QSLs aus dem DCL als vertrauenswürdig anerkannt werden und für die DARC-Leistungsdiplome (WAE, EUDX-Diplom, Europadiplom, DLD) gezählt werden.

Für die Diskussion eines Weiterentwicklungsprojektes gibt es keine Zeit.

## 2009 - Neuentwicklung
Entwicklung des DCL zum vollständigen Diplom-Mangemtsystem. 

Viele Probleme wegen unzureichender Hardware (500 MB Hauptspeicher) und fehlender Unterstützung durch die DARC-IT bei der Einbettung in Typo3. Zahlreiche Bitten von DCL-Seite. 

Private Anmietung eines VServer für das DCL
- Ergebnis 1: alles läuft Performant
- Ergebnis 2: Beef mit der DARC-IT und Vorsitzenden
*Lieber gebe ich ein Paar Euro privat als Wochenland beim DARC zu betteln*
- Ergebnis 3: Wir bekommen eine ordentlichen Server beim DARC

## 2010 - Blütezeit und viele Neuerungen

- DARC60
- YLWM -Diplom
- Diplome der DIG
- Distrikts-Diplome und einige OV-Diplome

## Über Technologien

In den letzten Jahren gab es viele technologische Neuerungen. Das hatte folgende Konsequenzen
- im DCL findet man einen Mischmasch verschiedener Technologie. Es wurde viel neues ausprobiert.
- der Stand von 2018 enthielt viele Komponenten, die man aktuell anders, einfacher oder eleganter realisieren würde
Zudem hat sich die Amateurfunkwelt und die Anwendersicht weiter entwickelt. Zahllose digitale Sendearten müssen betrachte werden. Es gibt Änderungen bei den ADIFs oder auch bei den Rufzeichenstrukturen.

Ein Neustart mit modernen Technologie (Frameworks, MVC)
");
?>
</p>

<h4>Der Start - März 2021</h4>
Der erste Prototyp
</p>
<?
require_once('../templates/footer.php');

?>