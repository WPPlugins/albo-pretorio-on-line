=== Plugin Name ===
Contributors: Scimone Ignazio
Donate link: www.sisviluppo.info
Tags: Albo Pretorio, Codice Amministrazione Digitale, Upload File
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.6
Albo Pretorio On Line permette la gestione dell'albo pretorio on line in base al nuovo Codice dell'Amministrazione Digitale
== Description ==

Albo Pretorio On Line e' un plugin per WordPress che tenta di dare una risposta all'esigenza delle pubbliche amministrazioni di avere a disposizione uno strumento con il quale pubblicare i propri atti in adempimento dell'art. 32 della LEGGE 18 giugno 2009, n. 69 e successive modifiche.

Questa legge prevede che dal 1 gennaio 2011 gli atti soggetti a pubblicazione devono essere pubblicati sul sito internet istituzionale dell'ente per avere efficacia legale.

== Installation ==

Di seguito sono riportati i passi necessari per l'installazione del plugin. la procedura dettagliata la potete trovare sul sito dedicato al plugin http://www.sisviluppo.info


1. Scaricare il plugin dal repository di wordpress o dal sito di riferimento
2. Attivare il plugin dal menu Plugins
3. Inserire gli atti lato amministrazione
4. inserire lo shortcode [Albo stato="1" per_page="5"]

dove stato può assumere i seguenti valori

         0 - tutti gli atti (per ovvi motivi si scoglia l'uso se non per motivi di test)
         1 - solo gli atti in corso di validita'
         2 - solo gli atti scaduti (storico)
         3 - solo gli atti da pubblicare (per ovvi motivi si scoglia l'uso se non per motivi di test)

per_page indica il numero massimo di atti che vengono visualizza in ogni pagina
== Changelog ==
= 1.6 =
- <strong>Aggiunta</strong> la gestione del responsabile del trattamento, che pu&ograve; essere associato ad ogni atto e visualizzato, quando indicato, insieme ai dati dell'atto
= 1.5 =
- <strong>Aggiunta</strong> la gestione dinamica degli effetti nel front end. Si possono attivare e disattivare gli effetti sul testo e gli effetti di smussamento degli angoli delle tabelle di filtro
- <strong>Aggiunta</strong> la possibilita' di gestire il livello dei titoli da h2 ad h4 di:
	- Nome Ente
	- Titolo Pagina
	- Titoli aree filtro
- <strong>Modificata</strong> la gestione della cartella di download, adesso bisogna specificare una cartella del file system partendo dalla cartella root di Wordpress.
= 1.4 =
- <strong>Aggiunto</strong> l'editor per il file CSS
= 1.3 =
- <strong>Risolto</strong> problema cancellazione atto
= 1.2 =
- <strong>Risolti</strong> problemi lato utente nella visualizzazione dell'atto, paginazione e filtri
- <strong>Aggiunto</strong> lato utente ora viene riportato il nome dell'ente
- <strong>Modificato</strong> in fase di attivazione viene ora riportato la cartella di Upload /home/sisvilup/public_html/wp-content/plugins/albo-pretorio-on-line/allegati
= 1.1 =
- <strong>Sistemati</strong> i problemi con le icone lato amministrazione
- <strong>Migliorato</strong> lato utente
 == Upgrade Notice ==
Aggiornare sempre il plugin all'ultima versione fini a che non si arriva ad una versione stabile ed operativa

<strong>Aggiornamento alla versione 1.5:
Aggiornare in Parametri, la cartella di Upload.</strong>
== Note ==
Versione ancora in fase di sviluppo 
NON UTILIZZARE IN AMBIENTI OPERATIVI
L'AUTORE NON SI ASSUME NESSUNA RESPONSABILITA' SUL FUNZIONAMENTO DEL PLUGIN
== Uso ==
Per maggiori informazioni e per assistenza il sito di riferimento e' http://www.sisviluppo.info dove e' attivo anche un forum

