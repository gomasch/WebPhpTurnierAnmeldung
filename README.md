# WebPhpTurnierAnmeldung
Simple PHP sign up site for go turnament / Einfache PHP-Webseite für Go-Turnier mit Online-Anmeldung 

# Übersicht
Dies ist PHP-Quellcode für eine einfache Webseite mit Online-Anmeldung für ein Go-Turnier.

# Anpassen für konkretes Turnier
* in logic.php anpassen:
  * Variable TURNIER_NAME anpassen
  * Variable TURNIER_BESCHREIBUNG anpassen
  * Variable SHOWALL_PASSWD anpassen auf einen frisch ausgewürfelten Wert
* Anmeldung eventuell anpassen (andere Werte und Überprüfungen) in der logic_anmeldungen.php
* template_index.html anpassen und dort den Abschnitt "Ausschreibungstext" durch die tatsächliche Ausschreibung als HTML ersetzen.
* Eventuell zusätzliche Dateien (z.B. PDF-Version der Ausschreibung) anlegen und verlinken.

# Hochladen
Nach den Anpassungen sollten alle Dateien (auch die .htaccess-Datei!) hochgeladen werden.
Vorraussetzungen: Es sollten PHP und mod_rewrite unterstützt werden, das ist bei den vielen Basis-Angeboten von Web-Hostern unterstützt. Gespeichert wird in eine lokale CSV-Datei, es ist kein Datenbank nötig.

# Anmeldungen anschauen
Die Details der Anmeldungen kann man bei Kenntnis des in SHOWALL_PASSWD angelegten Passwortes anschauen über den Link 'liste?passwd=secret' wobei secret durch das konkrete Passwort ersetzt werden soll.

# Anpassen für neues Design
* template_index.html template_anmeldung.html template_list.html anpassen für anderes Layout. Dabei beachten: 
  * Titel sollte lauten: {{ @TURNIER_NAME }}
  * Als Überschrift {{ @TURNIER_NAME }} angeben und als Untertitel dazu {{ @TURNIER_BESCHREIBUNG }}.
* style.css anpassen bzw. ersetzen
* Zusätzliche Dateien daneben legen, auch mit Unterordnern.
