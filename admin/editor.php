<?php
/**
 * Albo Pretorio AdminPanel - Editor Foglio di Stile style.css
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.2
 */

if ( !current_user_can('admin_albo') )
	wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this site.').'</p>');

$help = '<p>Editor del foglio di stile del front end dell\'albo pretorio</p>';
add_contextual_help($current_screen, $help);

$file= Albo_DIR.'/css/style.css';

validate_file_to_edit($file);
$file_show = basename( $file );

	require_once(ABSPATH . 'wp-admin/admin-header.php');

	update_recently_edited($file);

	if ( !is_file($file) )
		$error = 1;

	$content = '';
	if ( !$error && filesize($file) > 0 ) {
		$f = fopen($file, 'r');
		$content = fread($f, filesize($file));

		$content = esc_textarea( $content );
	}

	if (isset($_GET['a']))
	 echo '<div id="message" class="updated"><p>File modificato con successo</p></div>';
	
	$description = get_file_description($file);
	
	echo '<div class="wrap">
		<div style="width:40%;display:inline;float:left;">	  	
		<img src="'.Albo_URL.'/img/csseditor32.png" alt="Icona configurazione" style="display:inline;float:left;margin-top:10px;"/>		
	  	<h2 style="margin-left:40px;">Editor Foglio di Stile</h2>
		<h3>style.css</h3>
		
		<form name="template" id="template" action="?page=editorcss" method="get">
			 <input type="hidden" name="action" value="updatecss" />
			 <input type="hidden" name="file" value="'.esc_attr($file).'" />
			 <textarea cols="60" rows="20" name="newcontent" id="newcontent">'.$content.'</textarea>';
		if ( is_writeable( $file ) )
			echo '<p class="submit">
	        	<input type="submit" name="SalvaCSS" value="Salva Modifiche" />
	    	  </p>';
		else 
			echo '<p><em>'._e('You need to make this file writable before you can save your changes. See <a href="http://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.').'</em></p>';
	echo'
		</form>
		</div>
		<div style="width:60%;float:left;">
			<img src="'.Albo_URL.'/img/FE_Ricerca.png" alt="Immagine testa Front End" />
			<img src="'.Albo_URL.'/img/FE_ListaAtti.png" alt="Immagine lista Atti Front End" />
			<img src="'.Albo_URL.'/img/FE_Atto.png" alt="Immagine visualizzazione Atto Front End" />
</div>';


