<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Accesso ai dati
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 1.7
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }


switch ($_REQUEST['action']){
	case 'visatto':
		VisualizzaAtto($_REQUEST['id'],$_REQUEST['rif']);
		break;
	case 'categoria':
		Lista_Atti($Parametri,0,$_REQUEST['cat']);
		break;
	default: 
		switch ($_REQUEST['filtra']){
		 	case 'Filtra':
		 		Lista_Atti($Parametri,$_REQUEST['anno'], 0,$_REQUEST['oggetto'],$_REQUEST['DataInizio'],$_REQUEST['DataFine']);
		 		break;
		 	default:
			 	Lista_Atti($Parametri);
			 	break;
		}
}

function VisualizzaAtto($id,$rif){
	$risultato=ap_get_atto($id);
	$risultato=$risultato[0];
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$allegati=ap_get_all_allegati_atto($id);
	$responsabile=ap_get_responsabile($risultato->RespProc);
	$responsabile=$responsabile[0];
	echo '
<div class="Visalbo">
<h2>Dati atto</h2>
<a href="'.$rif.'" title="Torna alla lista degli atti">Torna alla Lista</a>
<table class="tabVisalbo">
	    <tbody id="dati-atto">
		<tr>
			<th>Numero Albo</th>
			<td>'.$risultato->Numero."/".$risultato->Anno.'</td>
		</tr>
		<tr>
			<th>Data</th>
			<td>'.VisualizzaData($risultato->Data).'</td>
		</tr>
		<tr>
			<th>Codice di Riferimento</th>
			<td>'.stripslashes($risultato->Riferimento).'</td>
		</tr>
		<tr>
			<th>Oggetto</th>
			<td>'.stripslashes($risultato->Oggetto).'</td>
		</tr>
		<tr>
			<th>Data inizio Pubblicazione</th>
			<td>'.VisualizzaData($risultato->DataInizio).'</td>
		</tr>
		<tr>
			<th>Data fine Pubblicazione</th>
			<td>'.VisualizzaData($risultato->DataFine).'</td>
		</tr>
		<tr>
			<th>Note</th>
			<td>'.stripslashes($risultato->Informazioni).'</td>
		</tr>
		<tr>
			<th>Categoria</th>
			<td>'.stripslashes($risultatocategoria->Nome).'</td>
		</tr>
	    </tbody>
	</table>';
if ($responsabile){
	echo '<h3>Responsabile</h3>
	<div class="Visallegato">
			<table class="tabVisResp">
	    		<tbody id="dati-responsabile">
				<tr>
					<th>Persona</th>
					<td>'.$responsabile->Cognome." ".$responsabile->Nome.'</td>
				</tr>
				<tr>
					<th>email</th>
					<td><a href="mailto:'.$responsabile->Email.'">'.$responsabile->Email.'</a></td>
				</tr>
				<tr>
					<th>Telefono</th>
					<td>'.$responsabile->Telefono.'</td>
				</tr>
				<tr>
					<th>Orario ricevimento</th>
					<td>'.$responsabile->Orario.'</td>
				</tr>';
if ($responsabile->Note)
	echo'
				<tr>
					<th>Note</th>
					<td>'.$responsabile->Note.'</td>
				</tr>';
echo'
			    </tbody>
			</table>				    
	</div>';
	
}
echo '<h3>Allegati</h3>';
foreach ($allegati as $allegato) {
	echo '<div class="Visallegato">
			<div style="float:left;display:inline;width: 60px;height:60px;">
				    <img src="'.Albo_URL.'/img/Pdf.png" alt="Icona Visualizza Atto" style="margin-left:10px;margin-top:10px;"/>
			</div>
			<div>
				<p>
					'.$allegato->TitoloAllegato.' <br />
					<a href="'.DaPath_a_URL($allegato->Allegato).'" target="_parent"">'. basename( $allegato->Allegato).'</a>
				</p>
			</div>
		</div>';
	}
echo '
</div>
';	
}
function VisualizzaRicerca(){
$anni=ap_get_dropdown_anni_atti('anno','anno','postform','','',1); 
$categorie=ap_get_dropdown_categorie('categorie','categorie','postform','','',false,true); 
Bonifica_Url();
if (strpos($_SERVER['REQUEST_URI'],"?")>0)
	$sep="&";
else
	$sep="?";
$titFiltri=get_option('opt_AP_LivelloTitoloFiltri');
if ($titFiltri=='')
	$titFiltri="h3";
$HTML='<div class="ricerca">
	<div class="ricerca_col_SX">
		<'.$titFiltri.'>Filtri</'.$titFiltri.'>
		<form id="filtro-atti" action="'.$_SERVER['REQUEST_URI'].'" method="get">';
		if (strpos($_SERVER['REQUEST_URI'],'page_id')>0){
			$HTML.= '<input type="hidden" name="page_id" value="'.Estrai_PageID_Url().'" />';
		}	
$HTML.= '
			<table id="filtro-atti" class="tabella-dati-albo" style="text-align:left;">
				<tr>
					<th>Anno</th>
					<td>'.$anni.'</td>
				</tr>
				<tr>
					<th>Oggetto</th>
					<td><input type="text" size="35" maxlength="150" name="oggetto" /></td>
				</tr>
				<tr>
					<th>da Data</th>
					<td><input name="DataInizio" id="Calendario1" type="text" value="" size="8" /></td>
				</tr>
				<tr>
					<th>a Data</th>
					<td><input name="DataFine" id="Calendario2" type="text" value="" size="8" /></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;"><input type="submit" name="filtra" id="filtra" class="bottoneFE" value="Filtra"  /></td>
				</tr>		
			</table>
		</form>
	</div>
	<div class="ricerca_col_DX">
		<'.$titFiltri.'>Categorie</'.$titFiltri.'>
		<p class="finestra_ricerca">'
		.ap_get_nuvola_categorie($_SERVER['REQUEST_URI'].$sep."action=categoria&cat",1).'
		</p>
	</div>
	<br style="clear:both;" />
</div>';	
return $HTML;
}

function Lista_Atti($Parametri,$Anno=0,$Categoria=0,$Oggetto='',$Dadata=0,$Adata=0){
switch ($Parametri['stato']){
		case 0:
			$TitoloAtti="Tutti gli Atti";
			break;
		case 1:
			$TitoloAtti="Atti in corso di Validit&agrave;";
			break;
		case 2:
			$TitoloAtti="Atti Scaduti";
			break;
		case 3:
			$TitoloAtti="Atti da Pubblicare";
			break;
}
	if (isset($Parametri['per_page'])){
		$N_A_pp=$Parametri['per_page'];	
	}else{
		$N_A_pp=100;
	}
	if (!isset($_REQUEST['Pag'])){
		$Da=0;
		$A=$N_A_pp;
	}else{
		$Da=($_REQUEST['Pag']-1)*$N_A_pp;
		$A=$N_A_pp;
	}
	$TotAtti=ap_get_all_atti($Parametri['stato'],$Anno,$Categoria,$Oggetto,$Dadata,$Adata,'',0,0,true);
	$lista=ap_get_all_atti($Parametri['stato'],$Anno,$Categoria,$Oggetto,$Dadata,$Adata,'',$Da,$A); 
$titEnte=get_option('opt_AP_LivelloTitoloEnte');
if ($titEnte=='')
	$titEnte="h2";
$titPagina=get_option('opt_AP_LivelloTitoloPagina');
if ($titPagina=='')
	$titPagina="h3";
echo' <div class="Visalbo">';
if (get_option('opt_AP_VisualizzaEnte')=='Si')
		echo '<'.$titEnte.' ><span  class="titoloEnte">'.stripslashes(get_option('opt_AP_Ente')).'</span></'.$titEnte.'>';
echo'<'.$titPagina.'><span  class="titoloPagina">'.$TitoloAtti.'</span></'.$titPagina.'>
		'.VisualizzaRicerca().'
		<br class="clear" />';
	if ($TotAtti>$N_A_pp){
	    $Para='';
	    foreach ($_REQUEST as $k => $v){
			if ($k!="Pag")
				if ($Para=='')
					$Para.=$k.'='.$v;
				else
					$Para.='&'.$k.'='.$v;
		}
		if ($Para=='')
			$Para="?Pag=";
		else
			$Para="?".$Para."&Pag=";
		$Npag=(int)$TotAtti/$N_A_pp;
		if ($TotAtti%$N_A_pp>0){
			$Npag++;
		}
		echo '  	<div class="tablenav" style="float:right;" id="risultati">
		<div class="tablenav-pages">
    		<p><strong>N. Atti '.$TotAtti.'</strong>&nbsp;&nbsp; Pagine';
    	if (isset($_REQUEST['Pag']) And $_REQUEST['Pag']>1 ){
			$Pagcur=$_REQUEST['Pag'];
			$PagPre=$Pagcur-1;
			echo '&nbsp;<a href="'.$Para.$PagPre.'" class="next page-numbers">&laquo;</a>';
		}else{
			$Pagcur=1;
		}
		for($i=1;$i<=$Npag;$i++){
			if ($i==$Pagcur){
				echo '&nbsp;<span class="page-numbers current">'.$i.'</span>';
			}else{
				echo '&nbsp;<a href="'.$Para.$i.'" class="page-numbers" >'.$i.'</a>';		
			}
		}
		$PagSuc=$Pagcur+1;
	   	if ($PagSuc<=$Npag){
			echo '&nbsp;<a href="'.$Para.$PagSuc.'" class="next page-numbers">&raquo;</a>';
		}
	echo'			</p>
    	</div>
	</div>';
	}		
echo '	<div class="tabalbo">
		<table id="elenco-atti" class="tabella-dati-albo"> 
	    <thead>
	    	<tr>
	        	<th>progressivo</th>
	        	<th>Riferimento</th>
	        	<th>Oggetto</th>
	        	<th>Validit&agrave;</th>
	        	<th>Categoria</th>
			</tr>
	    </thead>
	    <tbody>';
	    $riferimento=$_SERVER['REDIRECT_URL'];
		if ($_SERVER['QUERY_STRING']) 
			$riferimento.="?".$_SERVER['QUERY_STRING'];
	if ($lista){
		foreach($lista as $riga){
		$categoria=ap_get_categoria($riga->IdCategoria);
		$cat=$categoria[0]->Nome;
		$NumeroAtto=ap_get_num_anno($riga->IdAtto);
		Bonifica_Url();
		if (strpos($_SERVER['REQUEST_URI'],"?")>0)
			$sep="&";
		else
			$sep="?";
		echo '<tr>
		        <td><a href="'.$_SERVER['REQUEST_URI'].$sep.'action=visatto&id='.$riga->IdAtto.'&rif='.$riferimento.'"  >'.$NumeroAtto.'/'.$riga->Anno .'</a><br />'.VisualizzaData($riga->Data).'
				</td>
				<td>
					'.$riga->Riferimento .'
				</td>
				<td>
					'.$riga->Oggetto .'  
				</td>
				<td>
					'.VisualizzaData($riga->DataInizio) .'<br />'.VisualizzaData($riga->DataFine) .'  
				</td>
				<td>
					'.$cat .'  
				</td>
			</tr>'; 
		}
	} else {
			echo '<tr>
					<td colspan="6">Nessun Atto Codificato</td>
				  </tr>';
	}
	echo '
     </tbody>
    </table>';
echo '</div>
</div><!-- /wrap -->	';
}
?>