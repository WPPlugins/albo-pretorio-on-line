=== Plugin Name ===
Contributors: Scimone Ignazio
Donate link: www.sisviluppo.info
Tags: Albo Pretorio, Codice Amministrazione Digitale, Upload File
Requires at least: 3.5
Tested up to: 4.2
Stable tag: 3.3
Albo Pretorio On Line permette la gestione dell'albo pretorio on line in base al nuovo Codice dell'Amministrazione Digitale
== Description ==

Albo Pretorio On Line e' un plugin per WordPress che tenta di dare una risposta all'esigenza delle pubbliche amministrazioni di avere a disposizione uno strumento con il quale pubblicare i propri atti in adempimento dell'art. 32 della LEGGE 18 giugno 2009, n. 69 e successive modifiche.

Questa legge prevede che dal 1 gennaio 2011 gli atti soggetti a pubblicazione devono essere pubblicati sul sito internet istituzionale dell'ente per avere efficacia legale.

Si ringrazia Alessandro Cingolani per la consulenza in ambito sicurezza informatica.
== Installation ==

Di seguito sono riportati i passi necessari per l'installazione del plugin. la procedura dettagliata la potete trovare sul sito dedicato al plugin http://www.sisviluppo.info

1. Scaricare il plugin dal repository di wordpress o dal sito di riferimento
2. Attivare il plugin dal menu Plugins
3. Inserire gli atti lato amministrazione
4. inserire lo shortcode attraverso il pulsante dell'editor della pagina/articolo oppure manualmente [Albo stato="1" per_page="10" cat="1" filtri="si" minfiltri="no"]

<strong>stato</strong> può assumere i seguenti valori

         1 - solo gli atti in corso di validita'
         2 - solo gli atti scaduti (storico)

<strong>per_page</strong> indica il numero massimo di atti che vengono visualizza in ogni pagina
<strong>cat</strong> opzionale l' ID della categoria di cui verranno visualizzati gli atti 
<strong>filtri</strong> opzionale indica se deve essere visualizzata la finestra dei filtri. Se non specificato i filtri vengono visualizzati. utile nel caso si utizza lo shortcode nelle pagine dell'Amministrazione Trasparente
<strong>minfiltri</strong> opzionale indica se deve essere attivata la minimizzazione della finestra dei filtri. Se non specificato viene attivata la minimizzazione della finestra filtri.
== Changelog ==
= 3.3 =
- <strong>Risolti</strong> vari problemi dii sicurezza
= 3.2 =
- <strong>Introdotta</strong> la possibilità di disattivare il tracciamento delle operazioni attraverso il log delle operazioni, tutte o solo quelle di gestione degli atti, con la possibilità di mantenere il tracciamento delle visite e dei download
- <strong>Introdotta</strong> la possibilità di svuotare il file di log, tutte le registrazioni o solo squelle di gestione degli atti, con la possibilità di mantenere quelle relative al tracciamento delle visite e dei download
= 3.1.1 =
- <strong>Modificata</strong> la libreria di funzioni per la gestione del diritto all'oblio, nello specifico il file .htaccess, che veniva riconosciuto come virus da alcuni ISP
= 3.1 =
- <strong>Modificata</strong> la visualizzazione dell'elenco degli atti, adesso gli atti vengono visualizzati solo con numero atto ed oggetto con la possibilità di visualizzare il resto delle informazioni cliccando su un'iconcinaposta alla sinistra della lista
- <strong>Riorganizzati</strong> i nomi dei files
= 3.0.9 =
- <strong>Corretti</strong> bug sui permessi di accesso alle pagine di configurazione
= 3.0.8 =
- <strong>Corretto</strong> problema sulla visualizzazione delle statistiche
- <strong>Implementata</strong> nuova funzionalità in utility <strong>Procedura post trasferimento sito</strong> che permette la rigenerazione dei file .htaccess, index.php e robots.txt ed il riallineamento degli allegati agli atti sul nuovo percorso. 
= 3.0.7 =
- <strong>Corretto</strong> problema quando si memorizzavano i permessi
= 3.0.6 =
- <strong>Corretto</strong> errata dimensione tabella forntend
- <strong>Corretta</strong> doppia visualizzazione nell'elenco plugin della riga Albo Pretorio Online Widget
= 3.0.5 =
- <strong>Corretti</strong> vari Bugs creati dall'introduzione nel FrontEnd delle DataTables che permettono una migliore gestione dell'elenco degli atti
- <strong>Ripristinato</strong> il pulsante per l'inserimento dello shortcode, è stato riorganizzato, sono stati eliminati gli elementi obsoleti
= 3.0.4 =
- <strong>Corretto</strong> problema di compatibilità con Pasw2015
= 3.0.3 =
- <strong>Corretti</strong> vari bugs: Errori di validazione Front End
- <strong>Corretto</strong> errore di visualizzazione allegati con il browser Internet Explorer.
= 3.0.2 =
- <strong>Corretti</strong> vari bugs: Errori di validazione Front End, Errore di paginazione
- <strong>Implementata</strong> opzione nello shortcode che permette di visualizzare la finestra dei filtri fissa come avveniva fino alla versione 2.9
= 3.0.1 =
- <strong>Implementato</strong> diritto all'oblio da attivare dopo l'aggiornamento.
- <strong>Aggiunta</strong> la possibilità di cancellare gli atti dopo la loro scadenza
- <strong>Modificata</strong> l'interfaccia degli atti, nella lista ora vengono visualizzati separatamente gli atti da pubblicare da quelli pubblicati,
- <strong>Aggiunta</strong> l'opzione nello shortcode <em>Filtri</em> che permette di escludere dalla pagina la finestra dei filtri
- <strong>Revisionata</strong> l'interfaccia pubblica del plugin 
- <strong>Sostituito</strong> il calendario con il plugin di Ajax
- <strong>Corretti</strong> vari bugs
= 2.9 =
- <strong>Aggiunto</strong> elemento (<strong>OPZIONALE</strong>) dello shortcode che permete di specificare la categoria degli atti da visualizzare.
- <strong>Aggiunto</strong> pulsante, nella barra dell'editor delle pagine e degli articoli, che permette di inserire attraverso la compilazione di un form lo shortcode necessario per la visualizzazione degli atti.
= 2.8 =
- <strong>Risolto</strong> problema mancanza immagini utility
- <strong>Risolto</strong> errore creazione Sql Backup
= 2.7 =
- <strong>Modificato</strong> l'ordine di visualizzazione degli atti nel front end, adesso vengono visualizzati per anno/numero in ordine inverso
- <strong>Eliminato</strong> l'editor del foglio di stile
- <strong>Eliminato</strong> il parametro Effetti Testo Shadow
- <strong>Eliminato</strong> il parametro Effetti CSS3
- <strong>Modificata</strong> la dimensione del riferimento dell'atto, passato da 20 a 100 caratteri 
- <strong>Modificata</strong> la dimensione dell'OGGETTO dell'atto, passato da 150 a 200 caratteri 
- <strong>Modificata</strong> la dimensione del riferimento dell'atto, passato da 20 a 100 caratteri 
- <strong>Modificata</strong> la dimensione del Motivo dell'annullamento dell'atto, passato da 100 a 200 caratteri 
- <strong>Inserita</strong> la funzione di backup dei dati dell'albo accessibile da utility, viene creata una cartella in wp-content/plugins/albo-pretorio-on-line/BackupDatiAlbo in cui vengono posizionati i files zippati il cui nome contiene la date e l'ora di creazione.
Il file zippato contiene uno script Sql per la ricostruzione delle tabelle dell'albo con i dati e le impostazioni delle opzioni dell'albo compreso anche il numero progressivo e tutti gli allegati agli atti.
- <strong>Inserita</strong> la funzione di verifica del Data Base, della struttura e di congruità dei dati.
= 2.6 =
- <strong>Corretti</strong> bugs relativi alla visualizzazione nel widget nel momento in cui non ci sono atti pubblicati
- <strong>Corretto</strong> comportamento della procedura di pubblicazione.
- <strong>Corretti</strong> i link Torna Indietro nella gestione degli atti
= 2.5 =
- <strong>Corretti</strong> bugs relativi al passaggio alla versione 3.5 di Wordpress
= 2.4 =
- <strong>Risolto</strong> il problema di validazione del front end della pagina in cui viene visualizzato l'elenco degli atti
- <strong>Risolto</strong> il problema di indicizzazione del sito quando veniva inserita la pagina dell'albo
= 2.3 =
- <strong>Implementata</strong> la gestione degli enti titolari dei singoli Atti. Negli atti verr&agrave; aggiunto un campo in cui specificare l'ente che ha emesso l'atto, mentre per gli atti gi&agrave; codificati verr&agrave; riportato il valore 0 utilizzato per l'ente titoalere del sito. L'ente 0 non si pu&ograve; cancellare, si pu&ograve;solo modificare
- <strong>Implementata</strong> la procedura di ripubblicazione massiva degli atti attivi in caso di interruzione del servizio di pubblicazione, cio&egrave; nel caso in cui il sito va fuori servizio e risulta non accessibile.
- <strong>Revisionata</strong> l'interfaccia pubblica, ora il filtro in base alla categoria è stato inserito in unacasella di riepilogo.
- <strong>Revisionata</strong> nel back end è stata inserita la paginazione degli atti.
- <strong>Revisionata</strong> la gestione dei meta tag del front end che prima venivano riportati in tutto il sito, adesso vengono inseriti dal plugin solo nelle pagine dedicate all'albo.  
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
E' consigliato l'uso anche in ambienti operativi, per segnalare errori o problemi di utilizzo usare l'indirizzo email ignazio.scimone@gmail.com segnalando il sito in cui e' installato il plugin, una breve descrizione del problema riscontrato, la persona di riferimento con indirizzo email.
Non prendo in considerazione richieste non corredate da tutti i dati sopraelencati. 
== Uso ==
Per maggiori informazioni e per assistenza il sito di riferimento e' http://plugin.sisviluppo.info

