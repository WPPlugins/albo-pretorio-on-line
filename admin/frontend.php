<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Accesso ai dati
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.2
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }


switch ($_REQUEST['action']){
	case 'visatto':
		VisualizzaAtto($_REQUEST['id']);
		break;
	case 'categoria':
		Lista_Atti($Parametri,0,$_REQUEST['cat']);
		break;
	case 'addstatall':
		ap_insert_log(6,5,$_GET['id'],"Download",$_GET['idAtto']);
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

function VisualizzaAtto($id){
	$risultato=ap_get_atto($id);
	$risultato=$risultato[0];
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$allegati=ap_get_all_allegati_atto($id);
	$responsabile=ap_get_responsabile($risultato->RespProc);
	$responsabile=$responsabile[0];
	ap_insert_log(5,5,$id,"Visualizzazione");
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	if($risultato->DataAnnullamento!='0000-00-00')
		$Annullato='<p style="background-color: '.$coloreAnnullati.';text-align:center;font-size:1.5em;">Atto Annullato dal Responsabile del Procedimento</p>';
	else
		$Annullato='';
echo '
<div class="Visalbo" >
<h2 >Dati atto</h2>
<a href="'.get_permalink( ).'" title="Torna alla lista degli atti">Torna alla Lista</a>
'.$Annullato.'
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
//print_r($_SERVER);
foreach ($allegati as $allegato) {
 	switch (ExtensionType($allegato->Allegato)){
		case 'pdf':
			$Estensione="Pdf.png";
			$Verifica="";
			break;
		case "p7m":
			$Estensione="firmato.png";
			$Verifica='<br /><a href="http://postecert.poste.it/verificatore/servletverificatorep7m?tipoOp=10" onclick="window.open(this.href,\'_blank\');return false;">Verifica firma con servizio fornito da poste.it</a>';
			break;
	}
	echo '<div class="Visallegato">
			<div class="Allegato">
				    <img src="'.Albo_URL.'img/'.$Estensione.'" alt="Icona Visualizza Atto" style="margin-left:10px;margin-top:10px;"/>
			</div>
			<div>
				<p>
					'.$allegato->TitoloAllegato.' <br />';
			if (strpos(get_permalink(),"?")>0)
				$sep="&amp;";
			else
				$sep="?";
			if (is_file($allegato->Allegato))
				echo '        <a href="'.DaPath_a_URL($allegato->Allegato).'" onclick="window.open(this.href,\'_blank\');return false;" class="addstatdw" rel="'.get_permalink().$sep.'action=addstatall&amp;id='.$allegato->IdAllegato.'&amp;idAtto='.$id.'" >'. basename( $allegato->Allegato).'</a> ('.Formato_Dimensione_File(filesize($allegato->Allegato)).')'.$Verifica;
			else
				echo basename( $allegato->Allegato)." File non trovato, il file &egrave; stao cancellato o spostato!";
echo'				</p>
			</div>
			<div style="clear:both;"></div>
		</div>
		';
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
	$sep="&amp;";
else
	$sep="?";
$titFiltri=get_option('opt_AP_LivelloTitoloFiltri');
if ($titFiltri=='')
	$titFiltri="h3";
$HTML='<div class="ricerca">
	<div class="ricerca_col_SX">
		<'.$titFiltri.' style="margin-bottom:10px;">Filtri</'.$titFiltri.'>
		<form id="filtro-atti" action="'.$_SERVER['REQUEST_URI'].'" method="get">
		<fieldset>';
		if (strpos($_SERVER['REQUEST_URI'],'page_id')>0){
			$HTML.= '<input type="hidden" name="page_id" value="'.Estrai_PageID_Url().'" />';
		}	
$HTML.= '
			<table id="tabella-filtro-atti" class="tabella-dati-albo" >
				<tr>
					<th scope="row">Anno</th>
					<td>'.$anni.'</td>
				</tr>
				<tr>
					<th scope="row"><label for="oggetto">Oggetto</label></th>
					<td><input type="text" size="35" maxlength="150" name="oggetto" id ="oggetto"/></td>
				</tr>
				<tr>
					<th scope="row"><label for="DataInizio">da Data</label></th>
					<td><input name="DataInizio" id="Calendario1" type="text" value="" size="8" id ="DataInizio" /></td>
				</tr>
				<tr>
					<th scope="wor"><label for="DataFine">a Data</label></th>
					<td><input name="DataFine" id="Calendario2" type="text" value="" size="8" id ="DataFine" /></td>
				</tr>
				<tr>
					<td style="text-align:center;"><input type="submit" name="filtra" id="filtra" class="bottoneFE" value="Filtra"  /></td>
					<td style="text-align:center;"><input type="submit" name="annullafiltro" id="annullafiltro" class="bottoneFE" value="Annulla Filtro"  /></td>
				</tr>		
			</table>
			</fieldset>
		</form>
	</div>
	<div class="ricerca_col_DX">
		<'.$titFiltri.'>Categorie</'.$titFiltri.'>
		<p class="finestra_ricerca">'
		.ap_get_nuvola_categorie($_SERVER['REQUEST_URI'].$sep."action=categoria&amp;cat",1).'
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
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$colorePari=get_option('opt_AP_ColorePari');
	$coloreDispari=get_option('opt_AP_ColoreDispari');

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
					$Para.='&amp;'.$k.'='.$v;
		}
		if ($Para=='')
			$Para="?Pag=";
		else
			$Para="?".$Para."&amp;Pag=";
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
		<table id="elenco-atti" class="tabella-dati-albo" summary="atti validi per riferimento, oggetto e categoria"> 
	    <caption>Atti in corso di validit&agrave;</caption>
		<thead>
	    	<tr>
	        	<th scope="col"> progressivo</th>
	        	<th scope="col">Riferimento</th>
	        	<th scope="col" style="width:200px;">Oggetto</th>
	        	<th scope="col">Validit&agrave;</th>
	        	<th scope="col" style="width:150px;">Categoria</th>
			</tr>
	    </thead>
	    <tbody>';
	    $CeAnnullato=false;
	if ($lista){
	 	$pari=true;
		foreach($lista as $riga){
			$categoria=ap_get_categoria($riga->IdCategoria);
			$cat=$categoria[0]->Nome;
			$NumeroAtto=ap_get_num_anno($riga->IdAtto);
	//		Bonifica_Url();
			$classe='';
			if ($pari And $coloreDispari) 
				$classe='style="background-color: '.$coloreDispari.';"';
			if (!$pari And $colorePari)
				$classe='style="background-color: '.$colorePari.';"';
			$pari=!$pari;
			if($riga->DataAnnullamento!='0000-00-00'){
				$classe='style="background-color: '.$coloreAnnullati.';"';
				$CeAnnullato=true;
			}else
				$Annullato='';
			if (strpos(get_permalink(),"?")>0)
				$sep="&amp;";
			else
				$sep="?";
			echo '<tr >
			        <td '.$classe.'><a href="'.get_permalink().$sep.'action=visatto&amp;id='.$riga->IdAtto.'"  >'.$NumeroAtto.'/'.$riga->Anno .'</a><br />'.VisualizzaData($riga->Data).'
					</td>
					<td '.$classe.'>
						'.$riga->Riferimento .'
					</td>
					<td '.$classe.'>
						'.$riga->Oggetto .'  
					</td>
					<td '.$classe.'>
						'.VisualizzaData($riga->DataInizio) .'<br />'.VisualizzaData($riga->DataFine) .'  
					</td>
					<td '.$classe.'>
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
echo '</div>';
	if ($CeAnnullato) 
		echo '<p>Le righe evidenziate con questo sfondo <span style="background-color: '.$coloreAnnullati.';">&nbsp;&nbsp;&nbsp;</span> indicano Atti Annullati</p>';
echo '</div><!-- /wrap -->	';
}
?>