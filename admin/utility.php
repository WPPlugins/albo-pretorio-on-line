<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Gestione Permessi
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.4
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

if ($_REQUEST['action']=="rip"){
	$Stato=str_replace("%%br%%","<br />",ap_ripubblica_atti_correnti());
}
    get_currentuserinfo();
    print_r($current_user);
echo '<div class="wrap">
	<img src="'.Albo_URL.'/img/ruoli32.png" alt="Icona Permessi" style="display:inline;float:left;margin-top:5px;"/>
		<h2 style="margin-left:40px;">Utility Albo</h2>';
if (isset($Stato)) {
	echo '<div id="message" class="updated"><p>'.$Stato;
	unset($Stato);
}
$TotAtti=ap_get_all_atti(1,0,0,'',0,0,'',0,0,true,false);
echo '
		<div class="postbox-container" style="margin-top:20px;">
			<div class="widefat">
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">Attenzione!!!!!<br />
				Operazione di ripubblicazione degli atti in corso di validit&agrave; a causa di interruzione del servizio di pubblicazione</p>
				<p>Questa operazione Annulla gli atti gi&agrave; pubblicati ed in corso di validit&agrave; con motivazione <span style="font-size:1.1em;font-weight: bold;font-style: italic;color:red;">Annullamento per interruzione del sevizio di pubblicazione</span><br />Ripubblica gli atti in corso di validit&agrave; annullati per un periodo di tempo (n. giorni) uguale a quello degli atti originali</p>
				<p>&nbsp;</p>
				<p style="font-size:1.1em;font-weight: bold;font-style: italic;color:red;">Questa &egrave; una operazione che pu&ograve; modificare una grosa quantit&agrave; di dati, si consiglia di eseguire un backup prima di procedere, per poter recuperare i dati originali in caso di errori.</p>
			</div>
			<p>&nbsp;</p>
			<p><span style="font-size:1.1em;font-style: italic;color:green;">Attualmente ci sono <strong>'.$TotAtti.'</strong> atti pubblicati.</span> <a href="?page=utility&action=rip" class="ripubblica" rel="'.$TotAtti.'">Ripubblica gli atti a causa dell\' interruzione del servizio</a>?</div>';
?>