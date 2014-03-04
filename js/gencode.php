<?php

//Check for rights
$path  = '';

if (!defined('WP_LOAD_PATH')) {
	$root = dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/';
	if (file_exists($root.'wp-load.php') ) {
		define('WP_LOAD_PATH', $root);
	} else {
		if (file_exists($path.'wp-load.php'))
			define('WP_LOAD_PATH', $path);
	}
}

//Load wp-load.php
if (defined('WP_LOAD_PATH'))
	require_once(WP_LOAD_PATH.'wp-load.php');
	
if ( !is_user_logged_in() || !current_user_can('edit_posts') )
	wp_die(__("You are not allowed to access this file."));

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Albo Pretorio on Line</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript">
		function init() {
			tinyMCEPopup.resizeToInnerSize();
		}

		function insertAlboShortCode() {
			var stato   		 = document.getElementById('StatoAtti').value;
			var attipp  		 = document.getElementById('AttiPerPagina').value;
			var categoria 		 = document.getElementById('Categoria').value;
			var tagtext = "[Albo ";
			tagtext = tagtext + " stato=\"" + stato+ "\"";
			if ((!isNaN(attipp) && !isNaN(parseFloat(attipp))) && attipp>0 )
				tagtext = tagtext + " per_page=\""+attipp+"\"";
			else
				tagtext = tagtext + " per_page=\"10\"";
			if ((!isNaN(categoria) && !isNaN(parseFloat(categoria))) && categoria>0 )
				tagtext = tagtext + " cat=\"" + categoria+"\"";
			
			tagtext = tagtext + "]";
			if(window.tinyMCE) {
				window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
				/* tinyMCEPopup.editor.execCommand('mceRepaint'); */
			}
			tinyMCEPopup.close();
			return;
		}
	</script>
</head>
<body onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
	<div class="mceActionPanel">
		<form action="#" method="get" accept-charset="utf-8">
			<p>
				<label for="StatoAtti"><strong>Sato Atti</strong></label>
					<select id="StatoAtti" name="StatoAtti">
						<option value="0">Tutti</option>
						<option value="1">Atti Correnti</option>
						<option value="2">Atti Scaduti, Storico</option>
					</select>
				</p>
				<p>
					<label for="AttiPerPagina"><strong>Numero Atti per Pagina</strong></label>
					<input name="AttiPerPagina" id="AttiPerPagina" size="5" value="10"/>
				</p>
				<p>
					<label for="Categoria"><strong>Categoria</strong></label>
					<input name="Categoria" id="Categoria" size="5"/ value="0"><br />
					inserire 0 per visualizzare tutte le categorie
				</p>
		</form>
	</div>
	<br style="clear: both;" />
	<div class="mceActionPanel">
		<div style="float: left">
			<input type="submit" id="insert" name="insert" value="Inserisci" onclick="insertAlboShortCode();" />
		</div>
		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="Annulla" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</body>
</html>