=== Plugin Name ===
Contributors: Scimone Ignazio
Donate link: www.sisviluppo.info
Tags: Albo Pretorio, Codice Amministrazione Digitale, Upload File
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 2.2
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
= 2.2 =
- <strong>Revisionato</strong> il sistema dei log, adesso è possibile ricostruire cronologicamente tutti gli eventi legati agli oggetti della gestione.
- <strong>Revisionato</strong> il codice XHTML ed il CSS, sono stati eliminati i colori del testo ed altre impostazioni che potevano dare fastidio al template utilizzato.
- <strong>Inserito</strong> in Parametri la gestione del colore di sfondo degli atti Annullati
- <strong>Inserita</strong> la possibilità di ANNULLARE un atto nel periodo di pubblicazione
- <strong>Inserito</strong> il Widget per la visualizzazione degli atti in una sidebar
- <strong>Modificati</strong> i valori di default <em>Utilizza effetti testo Shadowin</em> ed <em> Utilizza effetti CSS3</em> che adesso dopo l'attivazione del plugin saranno disattivati
- <strong>Modificato</strong> il comportameto del plugin nella fase di configurazione, quando viene specificata una cartella che non esiste in <em>Cartella Upload</em>, adesso verr&agrave; creata automaticamente
= 2.1 =
- <strong>Inserito</strong> il codice per garantire la non indicizzazione della pagina dell'albo e conseguentemente dei link (allegati) in essa contenuti
= 2.0 =
- <strong>Sistemato</strong> problema con l'upload degli allegati
= 1.9 =
- <strong>Sistemati</strong> alcuni errori di scrittura del codice HTML che non lo rendevano valido in base al DTD XHTML 1.0 strict
- <strong>Modificato</strong> il CSS, sono stati eliminati le ridefinizioni dei titolo h2, h3 ed h4 e le dimensioni dei caratteri sono stati espressi in em;
= 1.8 =
- <strong>Implementato</strong> la statistica sugli accessi ai singoli atti e sui download degli allegati
- <strong>Modificata</strong> la gestione dei log lato amministrazione nella visualizzazione atto
= 1.7 =
- <strong>Sistemato</strong> problema di aggiornamento della tabella atti che rendevano impossibile, dopo l'aggiornamento alla versione 1.6, di memorizzare gli atti
- <strong>Sistemato</strong> problema di impostazione cartella di upload di default, non veniva riportata alcuna cartella, oraviene impostato il valore di wp-content\uploads  
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
Versione in fase di beta test 
E' consigliato l'uso anche in ambienti operativi, per segnalare errori o problemi di utilizzo usare l'indirizzo email ignazio.scimone@gmail.com segnalando il sito in cui e' installato il plugin, una breve descrizione del problema riscontrato, la persona di riferimento con indirizzo email.
Non prendo in considerazione richieste non corredate da tutti i dati sopraelencati. 
== Uso ==
Per maggiori informazioni e per assistenza il sito di riferimento e' http://www.sisviluppo.info dove e' attivo anche un forum

