<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Gestione Permessi
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.6
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
if ($_REQUEST['action']=="memoPermessi"){
	$lista=ap_get_users(); 
// Azzera capacit࠵tenti di gestione ed amministrazione Albo Pretorio
	foreach($lista as $riga){
		if (!(user_can( $riga->ID, 'create_users') or user_can( $riga->ID, 'manage_network'))) {
			$users = new WP_User( $riga->ID);
			$users->remove_cap("gest_atti_albo");
			$users->remove_cap("admin_albo");
		}
	}	
// Crea capacit࠵tenti di gestione ed amministrazione Al Pretorio in base a quanto scelto dall'Utente
	foreach($_REQUEST as $key=>$val){
		$UID=substr($key,1);
		if (is_numeric($UID)){
			$users = new WP_User($UID);
			if ($val=="Amministratore"){
				$users->add_cap("admin_albo");
				$users->add_cap("gest_atti_albo");
			}
			if ($val=="Gestore")
				$users->add_cap("gest_atti_albo");
			}
		}
}

echo '<div class="wrap">
	<img src="'.Albo_URL.'/img/ruoli32.png" alt="Icona Permessi" style="display:inline;float:left;margin-top:5px;"/>
		<h2 style="margin-left:40px;">Permessi Utente</h2>';
if ( (isset($_REQUEST['message']) && ( $msg = (int) $_REQUEST['message']))) {
	echo '<div id="message" class="updated"><p>'.$messages[$msg];
	if (isset($_REQUEST['errore'])) 
		echo '<br />'.$_REQUEST['errore'];
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}
echo '
		<div class="postbox-container" style="margin-top:20px;">
			<div class="widefat">
			<form id="gestPermessi" method="post" action="?page=permessi"  >
			<input type="hidden" name="action" value="memoPermessi"/>
				<table style="width:100%;">
					<caption>Permessi</caption>
					<thead>
					<tr>
						<th>Utente</th>
						<th>Azzera Capacit&agrave; Utente</th>
						<th>Capacit&agrave; di Amministrare l\'Albo</th>
						<th>Capacit&agrave; di Gestire l\'Albo</th>
						<th>Ruolo Amministratore</th>
						<th>Ruolo Gestore</th>
					</tr>
					</thead>
					<tbody>';
$lista=ap_get_users(); 
foreach($lista as $riga){
 	$users = new WP_User( $riga->ID);
 	$Utente=false;
	if ($users->has_cap('gestore_albo') or $users->has_cap('amministratore_albo'))
		$Utente=true;
 	if (!(user_can( $riga->ID, 'create_users') or user_can( $riga->ID, 'manage_network'))) {
		$Stato='';
		$StatoGestore='';
		echo '<tr>
		<td>'.$riga->user_login.'</td>';
	 	if (user_can( $riga->ID, 'admin_albo'))
	 		$Stato='checked="checked"';
	 	if (user_can( $riga->ID, 'gest_atti_albo'))
	 		$StatoGestore='checked="checked"';
		if (!$Utente)
			echo '				  <td><input type="radio" value="Nullo" '.$Stato.' name="U'.$riga->ID.'" /></td>
				  <td><input type="radio" value="Amministratore" '.$Stato.' name="U'.$riga->ID.'" /></td>
				  <td><input type="radio" value="Gestore" '.$StatoGestore.' name="U'.$riga->ID.'" /></td>';
		else
			echo '				  <td>&nbsp;</td>
			      <td>&nbsp;</td>
				  <td>&nbsp;</td>';
		if ($users->has_cap('amministratore_albo'))
			echo '<td>si</td>';
		else
			echo '<td>--</td>';
		if ($users->has_cap('gestore_albo'))
			echo '<td>si</td>';
		else
			echo '<td>--</td>';
		echo '	</tr>';
	}
}
echo '					</tbody>
				</table>
				
				<div style="margin-left:auto;width:140px;margin-right:auto;">
					<p>
					<input type="submit" name="memo" id="memo" class="button" value="Memorizza Permessi" />
					</p>
				</div>
				</form>
			</div>
		</div>
	</div>
';
/*global $wp_roles;
foreach( $wp_roles->role_names as $role => $name ) {
  $name = translate_with_context($name);
  echo '<p>List of users in the role '.$role .' ('. $name . '):</p>';
  $this_role = "'[[:<:]]".$role."[[:>:]]'";
  $query = "SELECT * FROM $wpdb->users WHERE ID = ANY (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'wp_capabilities' AND meta_value RLIKE $this_role) ORDER BY user_nicename ASC LIMIT 10000";
  $users_of_this_role = $wpdb->get_results($query);
  if ($users_of_this_role) {
    foreach($users_of_this_role as $user) {
      $curuser = get_userdata($user->ID);
      $author_post_url=get_author_posts_url($curuser->ID, $curuser->nicename);
      echo '<p>--User nicename: '.$curuser->user_nicename .', display Name: '. $curuser->display_name . ', link to author posts <a href="' . $author_post_url . '" title="' . sprintf( __( "Posts by %s" ), $curuser->user_nicename ) . '" ' . '>' . $curuser->user_nicename .'</a></p>';
    }
  }
}
*/