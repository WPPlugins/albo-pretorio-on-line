<?php
/**
 * Gestione Allegati.
 * @link       http://www.eduva.org
 * @since      4.0.3
 *
 * @package    ALbo On Line
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

?>
<div class="wrap">
	<div class="HeadPage" style="margin-bottom: 30px;">
		<h2 class="wp-heading-inline">Atti</h2>
		<a href="<?php echo site_url().'/wp-admin/admin.php?page=atti';?>" class="add-new-h2 tornaindietro">Torna indietro</a>
		<h3>Upload nuovo Allegato</h3>	
	</div>
<div id="col-container">
	<form id="allegato" enctype="multipart/form-data" method="post" action="?page=atti" class="validate">
	<input type="hidden" name="operazione" value="upload" />
	<input type="hidden" name="action" value="memo-allegato-atto" />
	<input type="hidden" name="uploallegato" value="<?php echo wp_create_nonce('uploadallegato')?>" />
	<input type="hidden" name="id" value="<?php echo (int)$_REQUEST['id']; ?>" />
<?php 
	if (isset($_REQUEST['ref']))
		echo '<input type="hidden" name="ref" value="'.htmlentities($_REQUEST['ref']).'" />';
?>	
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="3" style="text-align:center;font-size:2em;">Dati Allegato</th>
		</tr>
	    </thead>
	    <tbody id="dati-allegato">
		<tr>
			<th>Descrizione Allegato</th>
			<td><textarea  name="Descrizione" rows="4" cols="100" wrap="ON" maxlength="255"></textarea></td>
		</tr>
		<tr>
			<th>File:</th>
			<td><input name="file" type="file" size="80" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="submit" id="submit" class="button" value="Aggiungi Allegato"  />
			<input type="submit" name="annulla" id="annulla" class="button" value="Annulla Operazione"  />
			</td>
		</tr>
	    </tbody>
	</table>
	</form>
</div>
</div>