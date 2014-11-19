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
	<base target="_self" />
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript">

		function init() {
			tinyMCEPopup.resizeToInnerSize();
		}

		function insertAlboShortCode() {
			var stato   		 = document.getElementById('StatoAtti').value;
			var categoria 		 = document.getElementById('Categoria').value;
			var filtri  		 = document.getElementById('Filtri');
			var minfiltri  		 = document.getElementById('MinFiltri');
			
			var tagtext = "[Albo ";
			tagtext = tagtext + " stato=\"" + stato+ "\"";
			if ((!isNaN(categoria) && !isNaN(parseFloat(categoria))) && categoria>0 )
				tagtext = tagtext + " cat=\"" + categoria+"\"";
			//if (!isNaN(filtri) && filtri!="si" )
			if (filtri.checked)
				tagtext = tagtext + " filtri=\"si\"";
			else
				tagtext = tagtext + " filtri=\"no\"";
			if (minfiltri.checked)
				tagtext = tagtext + " minfiltri=\"no\"";
			else
				tagtext = tagtext + " minfiltri=\"si\"";
			tagtext = tagtext + "]";
			if(window.tinyMCE) {
				window.tinyMCE.activeEditor.execCommand('mceInsertContent', 0, tagtext);
			}
			tinyMCEPopup.close();
			return;
		}
	</script>
</head>
<body onload="tinyMCEPopup.executeOnLoad('init();');">
<?php 
$Ele_Cate=ap_get_dropdown_categorie("Categoria","Categoria","","")
?>
	<div class="mceActionPanel">
		<form action="#" method="get" accept-charset="utf-8">
				<p>
				<label for="StatoAtti"><strong>Sato Atti</strong></label>
					<select id="StatoAtti" name="StatoAtti">
						<option value="1">Atti Correnti</option>
						<option value="2">Atti Scaduti, Storico</option>
					</select>
				</p>
				<p>
					<label for="Categoria"><strong>Categoria</strong></label>
					<?php echo $Ele_Cate; ?>
				</p>
				<p>
					<label for="Filtri"><strong>Visualizza Filtri</strong></label>
					<input type="checkbox" name="Filtri" id="Filtri" value="si"/>
				</p>
				<p>
					<label for="MinFiltri"><strong>Finestra Filtri sempre visibile</strong></label>
					<input type="checkbox" name="MinFiltri" id="MinFiltri" value="si"/>
				</p>
		</form>
	</div>
		<div style="float: left">
			<input type="submit" id="insert" name="insert" value="Inserisci" onclick="insertAlboShortCode();" />
		</div>
		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="Annulla" onclick="tinyMCEPopup.close();" />
		</div>
</body>
</html>