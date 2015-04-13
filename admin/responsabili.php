<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Accesso ai dati
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 3.1.1
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

$messages[1] = __('Item added.');
$messages[2] = __('Item deleted.');
$messages[3] = __('Item updated.');
$messages[4] = __('Item not added.');
$messages[5] = __('Item not updated.');
$messages[6] = __('Item not deleted.');
$messages[7] = __('Impossibile cancellare Responsabili che sono collegati ad Atti');
?>
<div class="wrap nosubsub">
<img src="<?php echo Albo_URL; ?>/img/resp.png" alt="Icona Responsabili Procedimento" style="display:inline;float:left;margin-top:10px;"/>
<h2 style="margin-left:40px;">Responsabili Procedimento <a href="?page=responsabili" class="add-new-h2">Aggiungi nuovo</a></h2>

<?php
$NC="";
if ($_REQUEST['action']=="delete-responsabile"){
	$risultato=ap_del_responsabile($_REQUEST['id']);
	if(is_array($risultato)){
		$NC="Il Responsabile non pu&oacute; essere cancellato perch&egrave; ci sono ".$risultato["atti"]." atti a lui assegnati";
	}
	} 
if ( (isset($_REQUEST['message']) && ( $msg = (int) $_REQUEST['message'])) or $NC!="") {
	echo '<div id="message" class="updated"><p>'.$messages[$msg]. $NC;
	if (isset($_REQUEST['errore'])) 
		echo '<br />'.$_REQUEST['errore'];
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}
if ($_REQUEST['action']=="edit"){
	$risultato=ap_get_responsabile($_REQUEST['id']);
	$edit=True;
}else{
	$edit=False;
}

?>
<br class="clear" />
<div id="col-container">
<div id="col-right">
<div class="col-wrap">
<h3>Elenco Responsabili Procedimento</h3>
<table class="widefat" id="elenco-responsabili"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;">Responsabili</th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
$lista=ap_get_responsabili(); 
echo '<tr>
        	<td>
			<ul>';
if ($lista){
	foreach($lista as $riga){
		echo'<li style="text-align:left;padding-left:'.$shift.'px;">
			<a href="?page=responsabili&amp;action=delete-responsabile&amp;id='.$riga->IdResponsabile.'" rel="'.$riga->Cognome.'" class="dr">
			<img src="'.Albo_URL.'/img/cross.png" alt="Delete" title="Delete" />
			</a>
			<a href="?page=responsabili&amp;action=edit-responsabile&amp;id='.$riga->IdResponsabile.'" rel="'.$riga->Cognome.'">
			<img src="'.Albo_URL.'/img/edit.png" alt="Edit" title="Edit" />
			</a>
			('.$riga->IdResponsabile.') '.$riga->Cognome .'
			</li>'; 
	}
} else {
		echo '<li>Nessun Responsabile Codificato</li>';
}
echo '</td>
	</tr>
</ul>
	</tbody>
</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$righe=ap_get_all_Oggetto_log(4);
echo'
	<table class="widefat">
	    <thead>
		<tr>
			<th style="font-size:1.2em;">Data</th>
			<th style="font-size:1.2em;">Operazione</th>
			<th style="font-size:1.2em;">Informazioni</th>
		</tr>
	    </thead>
	    <tbody id="righe-log">';
foreach ($righe as $riga) {
	switch ($riga->TipoOperazione){
	 	case 1:
	 		$Operazione="Inserimento";
	 		break;
	 	case 2:
	 		$Operazione="Modifica";
			break;
	 	case 3:
	 		$Operazione="Cancellazione";
			break;
	}
	echo '<tr  title="'.$riga->Utente.' da '.$riga->IPAddress.'">
			<td >'.$riga->Data.'</th>
			<td >'.$Operazione.'</th>
			<td >'.stripslashes($riga->Operazione).'</td>
		</tr>';
}
echo '    </tbody>
	</table>
</div>';
?>
</div><!-- /col-right -->

<div id="col-left">
<div class="form-wrap">
<h3><?php echo $tax->labels->add_new_item; ?></h3>
<form id="addtag" method="post" action="?page=responsabili" class="<?php if($edit) echo "edit"; else echo "validate"; ?>"  >
<input type="hidden" name="action" value="<?php if($edit || $_REQUEST['action']=="edit_err") echo "memo-responsabile"; else echo "add-responsabile"; ?>"/>
<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
<?php wp_nonce_field('add-tag', '_wpnonce_add-tag'); ?>

<div class="form-field form-required">
	<label for="resp-cognome">Cognome</label>
	<input name="resp-cognome" id="resp-cognome" type="text" value="<?php if($edit) echo stripslashes($risultato[0]->Cognome); else echo $_GET['resp-cognome']; ?>" size="20" aria-required="true" />
</div>
<div class="form-field form-required">
	<label for="resp-nome">Nome</label>
	<input name="resp-nome" id="resp-nome" type="text" value="<?php if($edit) echo stripslashes($risultato[0]->Nome); else echo $_GET['resp-nome']; ?>" size="20" aria-required="true" />
</div>
<div class="form-field form-required">
	<label for="resp-email">Email</label>
	<input name="resp-email" id="resp-email" type="text" value="<?php if($edit) echo stripslashes($risultato[0]->Email); else echo $_GET['resp-email'];?>" size="100" aria-required="true" />
</div>
<div class="form-field form-required">
	<label for="resp-telefono">Telefono</label>
	<input name="resp-telefono" id="resp-telefono" type="text" value="<?php if($edit) echo stripslashes($risultato[0]->Telefono); else echo $_GET['resp-telefono']; ?>" size="30" aria-required="true" />
</div>
<div class="form-field form-required">
	<label for="resp-orario">Orario ricevimento</label>
	<input name="resp-orario" id="resp-orario" type="text" value="<?php if($edit) echo stripslashes($risultato[0]->Orario);  else echo $_GET['resp-orario'];?>" size="60" aria-required="true" />
</div>
<div class="form-field">
	<label for="resp-description">Note</label>
	<textarea name="resp-note" id="resp-note" rows="5" cols="40"><?php if($edit) echo stripslashes($risultato[0]->Note); else echo $_GET['resp-note']; ?></textarea>
	<p>inserire eventuali informazioni aggiuntive</p>
</div>

<?php
if($edit) {
	echo '<input type="submit" name="memo" id="memo" class="button" value="Memorizza Modifiche Responsabile '.$risultato[0]->Cognome.'" rel="'.stripslashes($risultato[0]->Cognome).'" />';
}else{
 	if ($_REQUEST['action']=="edit_err")
		echo '<input type="submit" name="memo" id="memo" class="button" value="Memorizza Modifiche Responsabile '.$_GET['resp-cognome'].'" rel="'.stripslashes($_GET['resp-cognome']).'" />';
	else
		echo '<input type="submit" name="submit" id="submit" class="button" value="Aggiungi nuovo Responsabile"  />';	
}
?>
</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->

