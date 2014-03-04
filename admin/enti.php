<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Accesso ai dati
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.9
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

$messages[1] = __('Item added.');
$messages[2] = __('Item deleted.');
$messages[3] = __('Item updated.');
$messages[4] = __('Item not added.');
$messages[5] = __('Item not updated.');
$messages[6] = __('Item not deleted.');
$messages[7] = __('Impossibile cancellare Enti che sono collegati ad Atti');
?>
<div class="wrap nosubsub">
<img src="<?php echo Albo_URL; ?>/img/enti32.png" alt="Icona Enti" style="display:inline;float:left;margin-top:10px;"/>
<h2 style="margin-left:40px;">Enti <a href="?page=enti" class="add-new-h2">Aggiungi nuovo</a></h2>

<?php 
if ( (isset($_REQUEST['message']) && ( $msg = (int) $_REQUEST['message']))) {
	echo '<div id="message" class="updated"><p>'.$messages[$msg];
	if (isset($_REQUEST['errore'])) 
		echo '<br />'.stripslashes($_REQUEST['errore']);
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}
if ($_REQUEST['action']=="edit"){
	$risultato=ap_get_ente($_REQUEST['id']);
//	print_r($risultato);exit;
	$edit=True;
}else{
	$edit=False;
}
?>
<br class="clear" />
<div id="col-container">
<div id="col-right">
<div class="col-wrap">
<h3>Elenco Enti</h3>
<table class="widefat" id="elenco-enti"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;">Enti</th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
$lista=ap_get_enti(); 
echo '<tr>
        	<td>
			<ul>';
$shift=1;
if ($lista){
	foreach($lista as $riga){
		echo'<li style="text-align:left;padding-left:1px;">';
		if($riga->IdEnte>0)
			echo '<a href="?page=enti&amp;action=delete-ente&amp;id='.$riga->IdEnte.'" rel="'.$riga->Nome.'" class="dr">
					<img src="'.Albo_URL.'/img/cross.png" alt="Delete" title="Delete" />
					</a>';
		echo '					<a href="?page=enti&amp;action=edit-ente&amp;id='.$riga->IdEnte.'" rel="'.$riga->Nome.'">
					<img src="'.Albo_URL.'/img/edit.png" alt="Edit" title="Edit" />
					</a>';
		echo '<strong>'.$riga->Nome .'</strong>';
		echo '</li>';
	}
} else {
		echo '<li>Nessun Ente Codificato</li>';
}
echo '</td>
	</tr>
</ul>
	</tbody>
</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$righe=ap_get_all_Oggetto_log(7);
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
<form id="addtag" method="post" action="?page=enti" class="<?php if($edit) echo "edit"; else echo "validate"; ?>"  >
<input type="hidden" name="action" value="<?php if($edit || $_REQUEST['action']=="edit_err") echo "memo-ente"; else echo "add-ente"; ?>"/>
<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
<?php wp_nonce_field('add-tag', '_wpnonce_add-tag'); ?>

<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
	<label for="ente-nome">Nome Ente</label>
	<input name="ente-nome" id="ente-nome" type="text" value="<?php if($edit) echo stripslashes($risultato->Nome); else echo $_REQUEST['ente-nome']; ?>" size="30" aria-required="true" />
</div>
<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
	<label for="ente-indirizzo">Indirizzo</label>
	<input name="ente-indirizzo" id="ente-indirizzo" type="text" value="<?php if($edit) echo stripslashes($risultato->Indirizzo); else echo $_REQUEST['ente-indirizzo']; ?>" size="150" aria-required="true" />
</div>
<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
	<label for="ente-url">Url</label>
	<input name="ente-url" id="ente-url" type="text" value="<?php if($edit) echo stripslashes($risultato->Url); else echo $_REQUEST['ente-url'];?>" size="100" aria-required="true" />
</div>
<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
	<label for="ente-email">Email</label>
	<input name="ente-email" id="ente-email" type="text" value="<?php if($edit) echo stripslashes($risultato->Email); else echo $_REQUEST['ente-email'];?>" size="100" aria-required="true" />
</div>
<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
	<label for="ente-pec">Pec</label>
	<input name="ente-pec" id="ente-pec" type="text" value="<?php if($edit) echo stripslashes($risultato->Pec); else echo $_REQUEST['ente-pec'];?>" size="100" aria-required="true" />
</div>
<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
	<label for="ente-telefono">Telefono</label>
	<input name="ente-telefono" id="ente-telefono" type="text" value="<?php if($edit) echo stripslashes($risultato->Telefono); else echo $_REQUEST['ente-telefono']; ?>" size="30" aria-required="true" />
</div>
<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
	<label for="ente-fax">Fax</label>
	<input name="ente-fax" id="ente-fax" type="text" value="<?php if($edit) echo stripslashes($risultato->Fax); else echo $_REQUEST['ente-fax']; ?>" size="30" aria-required="true" />
</div>
<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
	<label for="tag-description">Note</label>
	<textarea name="ente-note" id="ente-note" rows="5" cols="40"><?php if($edit) echo stripslashes($risultato->Note); else echo $_REQUEST['ente-note']; ?></textarea>
	<p>inserire eventuali informazioni aggiuntive</p>
</div>

<?php
if($edit) {
	echo '<input type="submit" name="memo" id="memo" class="button" value="Memorizza Modifiche '.$risultato->Nome.'" rel="'.stripslashes($risultato->Nome).'" />';
}else{
 	if ($_REQUEST['action']=="edit_err")
		echo '<input type="submit" name="memo" id="memo" class="button" value="Memorizza Modifiche '.$_REQUEST['ente-nome'].'" rel="'.stripslashes($_REQUEST['ente-nome']).'" />';
	else
		echo '<input type="submit" name="submit" id="submit" class="button" value="Aggiungi nuovo Ente"  />';	
}
?>
</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->

