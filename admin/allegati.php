<?php
/**
 * Albo Pretorio AdminPanel - Gestione Allegati Atto
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 0.0.1
 */
?>
<div class="wrap">
<img src="<?php echo Albo_URL;?>/img/up32.png" alt="Icona Upload File" style="display:inline;float:left;margin-top:10px;"/>
<h2 style="margin-left:40px;">Allegati</h2>
<br class="clear" />
<div id="col-container">
	<h3>Allogato</h3>
	<form id="allegato" enctype="multipart/form-data" method="post" action="?page=atti" class="validate">
	<input type="hidden" name="operazione" value="upload" />
	<input type="hidden" name="action" value="memo-allegato-atto" />
	<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
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
			<td colspan="2"><input type="submit" name="submit" id="submit" class="button" value="Aggiungi Allegato"  /></td>
		</tr>
	    </tbody>
	</table>
	</form>
</div>
</div>