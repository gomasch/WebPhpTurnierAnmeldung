# WebPhpTurnierAnmeldung
Simple PHP sign up site for go turnament / Einfache PHP-Webseite für Go-Turnier mit Online-Anmeldung 

# Übersicht
Dies ist PHP-Quellcode für eine einfache Webseite mit Online-Anmeldung für ein Go-Turnier.

# Anpassen für konkretes Turnier
* Basisdaten in logic.php anpassen:
  * Variable TURNIER_NAME anpassen
  * Variable TURNIER_BESCHREIBUNG anpassen
  * Variable SHOWALL_PASSWD anpassen auf einen frisch ausgewürfelten Wert (nur Buchstaben und Zahlen)
* Anmeldungslogik eventuell anpassen (Werte und Überprüfungen, Hinweise zu den Preisen bei den einzelnen Angaben) in logic_anmeldungen.php.
* Design auswählen bzw. anpassen (Dateien im Unterverzeichnis design anpassen oder vorgefertigtes Design nehmen, siehe unten.)
* Ausschreibungstext in design/template_index.html anpassen und eventuell zusätzliche Dateien (z.B. PDF-Version der Ausschreibung) im design-Verzeichnis ablegen und verlinken vom Anmeldungstext.

# Hochladen
Nach den Anpassungen sollten hochgeladen werden: 
* .htaccess-Datei
* .php-Dateien
* Lib-Verzeichnis
* Design-Verzeichnis (inkl. deiner Anpassungen)

Vorraussetzungen: Es sollten PHP und mod_rewrite unterstützt werden, das ist bei den vielen Basis-Angeboten von Web-Hostern unterstützt. Gespeichert wird in eine lokale CSV-Datei, es ist keine Datenbank nötig.

# Anmeldungen anschauen
Die Details der Anmeldungen kann man bei Kenntnis des in SHOWALL_PASSWD angelegten Passwortes anschauen über den Link 'liste?passwd=secret' wobei secret durch das konkrete Passwort ersetzt werden soll.

# Nach erfolgreich gelaufenem Turnier
Ich empfehle, nachdem das Turnier gelaufen ist, die Dateien der Online-Anmeldung komplett von der Anmeldestelle zu löschen. Ich lege dann dort gern eine index.html-Datei, in der steht "Das Turnier ist gelaufen. War schön!".


# Anpassen für neues Design
* Im Verzeichnis Design werden 3 Dateien benötigt, die angepasst werden können.
  * template_index.html soll den Ausschreibungstext enthalten. Bei Anpassungen bitte beachten:
    * Titel sollte lauten: {{ @TURNIER_NAME }}
    * Als Überschrift {{ @TURNIER_NAME }} angeben und als Untertitel dazu {{ @TURNIER_BESCHREIBUNG }}.
  * template_anmeldung.html wird mit dem Anmelde-Formular durch die Logik gefüllt. Bei Anpassungen bitte beachten: 
    * Titel sollte lauten: {{ @TURNIER_NAME }}
    * Als Überschrift {{ @TURNIER_NAME }} angeben und als Untertitel dazu {{ @TURNIER_BESCHREIBUNG }}.
    * Der Abschnitt für das Formular soll enthalten: {{ @MAIN_CONTENT }} {{ @MAIN_CONTENT_RAW | raw }}
  * template_list.html wird mit der Tabelle durch die Logik gefüllt. Bei Anpassungen bitte beachten: 
    * Titel sollte lauten: {{ @TURNIER_NAME }}
    * Als Überschrift {{ @TURNIER_NAME }} angeben und als Untertitel dazu {{ @TURNIER_BESCHREIBUNG }}.
    * Der Abschnitt für die Daten soll enthalten: {{ @MAIN_CONTENT }} {{ @MAIN_CONTENT_RAW | raw }}
* Zusätzliche Dateien können direkt daneben legen, z.B. Bilder und CSS-Dateien. Bitte OHNE Unterordner.
* Zum Bearbeiten und Testen kann man die einzelnen Html-Dateien direkt im Browser lokal anschauen, ohne Webserver.
* Es sind mehrere funktionierende Beispiel-Designs als Beispiel verfügbar (designschwerin etc.). Die Logik erwartet die Dateien im Verzeichnis "design".

# Einschränkungen
* Allgemein gilt:
  * Dies ist für kleine Turniere mit wenigen Anmeldungen (unter 200) gedacht.
  * Es wird angenommen, dass nur wenige Personen pro Tag sich anmelden und praktisch nie zwei Leute exakt gleichzeitig auf "Absenden" drücken.
  * Basis ist nun das PHP Microframework F3 http://fatfreeframework.com/. Es wurden einfach die zwei verwendeten PHP-Dateien hier direkt eingebunden, ohne Management der Abhängigkeiten durch Composer beispielsweise.
* Sicherheit: 
  * Es gibt keine Überprüfung auf Plausibilität oder Duplikate. D.h. Leute können bewusst falsche Angaben machen oder die Anmeldung mit sinnlosen Daten fluten und stören.
  * Es gibt keinen Schutz gegen DDOS oder gegen automatisierte falsche Anmeldungen.
  * Die Eingaben werden gefiltert, d.h. durch die Anmeldung kann (hoffentlich) kein HTML/Javascript-Code in die Anmeldung eingeschlichen werden.

# Herkunft und bisherige Verwendung
  * Die allererste Version (noch ohne F3) wurde von Stefan Reinke nach Vorgaben von Martin Schmidt für mvgo.de vor vielen Jahren (ca. 2008?) programmiert. Dieser Basis-Code wurde für viele Go-Turniere in M/V 2008-2015 von Martin angepasst und eingesetzt.
  * Diese Version ist der Versuch von Martin, die Code-Basis zu modernisieren, um Design- und Logik-Anpassungen einfacher vornehmen zu können.
  * Da eine Online-Anmeldung durchaus etwas Arbeit machen kann, wird dieser neue Quellcode öffentlich und frei verwendbar auf GitHub verfügbar gemacht. 
