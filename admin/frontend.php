<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Accesso ai dati
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 3.1
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
switch ($_REQUEST['action']){
	case 'visatto':
		VisualizzaAtto($_REQUEST['id']);
		break;
	case 'addstatall':
		ap_insert_log(6,5,$_GET['id'],"Download",$_GET['idAtto']);
		break;
	default: 
		if (isset($_REQUEST['filtra']))
		 		$ret=Lista_Atti($Parametri,$_REQUEST['categoria'],$_REQUEST['anno'], $_REQUEST['oggetto'],$_REQUEST['DataInizio'],$_REQUEST['DataFine']);
		else if(isset($_REQUEST['annullafiltro'])){
				 unset($_REQUEST['categoria']);
				 unset($_REQUEST['anno']);
				 unset($_REQUEST['oggetto']);
				 unset($_REQUEST['DataInizio']);
				 unset($_REQUEST['DataFine']);
				 $ret=Lista_Atti($Parametri);
			}else{
				$ret=Lista_Atti($Parametri);
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
		$Annullato='<p style="background-color: '.$coloreAnnullati.';text-align:center;font-size:1.5em;padding:5px;">Atto Annullato dal Responsabile del Procedimento<br /><br />Motivo: <span style="font-size:1;font-style: italic;">'.$risultato->MotivoAnnullamento.'</span></p>';
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
			<th>Ente titolare dell\'Atto</th>
			<td style="font-style: italic;font-size: 1.5em;vertical-align: middle;">'.stripslashes(ap_get_ente($risultato->Ente)->Nome).'</td>
		</tr>
		<tr>
			<th>Numero Albo</th>
			<td style="vertical-align: middle;">'.$risultato->Numero."/".$risultato->Anno.'</td>
		</tr>
		<tr>
			<th>Data</th>
			<td style="vertical-align: middle;">'.ap_VisualizzaData($risultato->Data).'</td>
		</tr>
		<tr>
			<th>Codice di Riferimento</th>
			<td style="vertical-align: middle;">'.stripslashes($risultato->Riferimento).'</td>
		</tr>
		<tr>
			<th>Oggetto</th>
			<td style="vertical-align: middle;">'.stripslashes($risultato->Oggetto).'</td>
		</tr>
		<tr>
			<th>Data inizio Pubblicazione</th>
			<td style="vertical-align: middle;">'.ap_VisualizzaData($risultato->DataInizio).'</td>
		</tr>
		<tr>
			<th>Data fine Pubblicazione</th>
			<td style="vertical-align: middle;">'.ap_VisualizzaData($risultato->DataFine).'</td>
		</tr>
		<tr>
			<th>Note</th>
			<td style="vertical-align: middle;">'.stripslashes($risultato->Informazioni).'</td>
		</tr>
		<tr>
			<th>Categoria</th>
			<td style="vertical-align: middle;">'.stripslashes($risultatocategoria->Nome).'</td>
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
					<td style="vertical-align: middle;">'.$responsabile->Cognome." ".$responsabile->Nome.'</td>
				</tr>
				<tr>
					<th>email</th>
					<td style="vertical-align: middle;"><a href="mailto:'.$responsabile->Email.'">'.$responsabile->Email.'</a></td>
				</tr>
				<tr>
					<th>Telefono</th>
					<td style="vertical-align: middle;">'.$responsabile->Telefono.'</td>
				</tr>
				<tr>
					<th>Orario ricevimento</th>
					<td style="vertical-align: middle;">'.$responsabile->Orario.'</td>
				</tr>';
if ($responsabile->Note)
	echo'
				<tr>
					<th>Note</th>
					<td style="vertical-align: middle;">'.$responsabile->Note.'</td>
				</tr>';
echo'
			    </tbody>
			</table>				    
	</div>';
	
}
echo '<h3>Allegati</h3>';
//print_r($_SERVER);
foreach ($allegati as $allegato) {
 	switch (ap_ExtensionType($allegato->Allegato)){
		case 'pdf':
			$Estensione="Pdf.png";
			$Verifica="";
			break;
		case "p7m":
			$Estensione="firmato.png";
			$Verifica='<br /><a href="http://postecert.poste.it/verificatore/servletverificatorep7m?tipoOp=10" onclick="window.open(this.href);return false;">Verifica firma con servizio fornito da poste.it</a>';
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
				echo '        <a href="'.ap_DaPath_a_URL($allegato->Allegato).'" class="addstatdw" rel="'.get_permalink().$sep.'action=addstatall&amp;id='.$allegato->IdAllegato.'&amp;idAtto='.$id.'" >'. basename( $allegato->Allegato).'</a> ('.ap_Formato_Dimensione_File(filesize($allegato->Allegato)).')'.$Verifica;
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
function VisualizzaRicerca($Stato=1,$cat=0,$StatoFinestra="si"){
	$anni=ap_get_dropdown_anni_atti('anno','anno','postform','',$_REQUEST['anno'],$Stato); 
	$categorie=ap_get_dropdown_ricerca_categorie('categoria','categoria','postform','',$_REQUEST['categoria'],$Stato); 
	ap_Bonifica_Url();
	if (strpos($_SERVER['REQUEST_URI'],"?")>0)
		$sep="&amp;";
	else
		$sep="?";
	$titFiltri=get_option('opt_AP_LivelloTitoloFiltri');
	if ($titFiltri=='')
		$titFiltri="h3";
	//$HTML='<div class="ricerca">';
	$HTML='';
	//		<'.$titFiltri.' style="margin-bottom:10px;">Filtri</'.$titFiltri.'>
	$HTML.='		<form id="filtro-atti" action="'.$_SERVER['REQUEST_URI'].'" method="post">
	';
			if (strpos($_SERVER['REQUEST_URI'],'page_id')>0){
				$HTML.= '<input type="hidden" name="page_id" value="'.ap_Estrai_PageID_Url().'" />';
			}	
	$HTML.= '
				<table id="tabella-filtro-atti" class="tabella-dati-albo" >';
/*	if($cat!=0){
		$HTML.= '				
					<tr>
						<th scope="row" >Categorie</th>
						<td>'.$categorie.'</td>
					</tr>';
	}*/
	$HTML.= '
					<tr>
						<th scope="row">Anno</th>
						<td>'.$anni.'</td>
					</tr>
					<tr>
						<th scope="row"><label for="oggetto">Oggetto</label></th>
						<td><input type="text" size="40" maxlength="150" name="oggetto" id ="oggetto" value="'.$_REQUEST['oggetto'].'"/></td>
					</tr>
					<tr>
						<th scope="row"><label for="Calendario1">da Data</label></th>
						<td><input name="DataInizio" id="Calendario1" type="text" value="'.$_REQUEST['DataInizio'].'" size="10" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="Calendario2">a Data</label></th>
						<td><input name="DataFine" id="Calendario2" type="text" value="'.$_REQUEST['DataFine'].'" size="10" /></td>
					</tr>
					<tr>
						<td style="text-align:center;"><input type="submit" name="filtra" id="filtra" class="bottoneFE" value="Filtra"  /></td>
						<td style="text-align:center;"><input type="submit" name="annullafiltro" id="annullafiltro" class="bottoneFE" value="Annulla Filtro"  /></td>
					</tr>		
				</table>
			</form>
	';
	if($StatoFinestra=="si")
		$stile='style="display:none;"';
	else
		$stile="";
	$HTMLC='<div id="fe-tabs-container" '.$stile.'>
					<ul>
						<li><a href="#fe-tab-1">Parametri</a></li>';
	if($cat==0){
		$HTMLC.='
						<li><a href="#fe-tab-2">Categorie</a></li>';
	}
	$HTMLC.='
					</ul>
					<div id="fe-tab-1">';
	$HTMLC.=$HTML;
	$lista=ap_get_categorie_gerarchica();
	$HTMLL='
	          <div class="ricercaCategoria">
	              <ul style="list-style-type: none;">';
	if ($lista){
		foreach($lista as $riga){
		 	$shift=(((int)$riga[2])*15);
	   		$numAtti=ap_num_atti_categoria($riga[0],$Stato);
		 	if (strpos(get_permalink(),"?")>0)
		  		$sep="&amp;";
	   		else
		   		$sep="?";
	   		if ($numAtti>0)
	      		$HTMLL.='               <li style="text-align:left;padding-left:'.$shift.'px;font-weight: bold;"><a href="'.get_permalink().$sep.'filtra=Filtra&amp;categoria='.$riga[0].'"  >'.$riga[1].'</a> '.$numAtti.'</li>'; 
		}
	}else{
		$HTMLL.= '                <li>Nessuna Categoria Codificata</li>';
	}
	$HTMLL.='             </ul>
	          </div>';
	$HTMLC.= '
	      </div>';
	if($cat==0){	
		$HTMLC.= '
				<div id="fe-tab-2">'.$HTMLL.'
					</div>';			
	}
	$HTMLC.= '
				</div>
	<br class="clear" />';
	return $HTMLC;
}
function Lista_Atti($Parametri,$Categoria=0,$Anno=0,$Oggetto='',$Dadata=0,$Adata=0){
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
		$N_A_pp=10;
	}
	if (isset($Parametri['cat'])){
		$Categoria=$Parametri['cat'];
		$DesCategoria=ap_get_categoria($Categoria);
		$TitoloAtti.=" Categoria ".$DesCategoria[0]->Nome;
		$cat=1;
	}else{
		$cat=0;
	}
	if (!isset($_REQUEST['Pag'])){
		$Da=0;
		$A=$N_A_pp;
	}else{
		$Da=($_REQUEST['Pag']-1)*$N_A_pp;
		$A=$N_A_pp;
	}
	$TotAtti=ap_get_all_atti($Parametri['stato'],$Anno,$Categoria,$Oggetto,$Dadata,$Adata,'',0,0,true);
	$lista=ap_get_all_atti($Parametri['stato'],$Anno,$Categoria,$Oggetto,$Dadata,$Adata,'Anno DESC,Numero DESC',$Da,$A); 
	$titEnte=get_option('opt_AP_LivelloTitoloEnte');
	if ($titEnte=='')
		$titEnte="h2";
	$titPagina=get_option('opt_AP_LivelloTitoloPagina');
	if ($titPagina=='')
		$titPagina="h3";
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$colorePari=get_option('opt_AP_ColorePari');
	$coloreDispari=get_option('opt_AP_ColoreDispari');
	if($Parametri['minfiltri']=="si"){
		if(isset($_REQUEST['vf']) and  $_REQUEST['vf']=="s"){
			$VisFiltro='<img src="'.Albo_URL.'img/minimize.png" id="maxminfiltro" class="s" alt="icona minimizza finestra filtri"/>';
		}else{
			$VisFiltro='<img src="'.Albo_URL.'img/maximize.png" id="maxminfiltro" class="h" alt="icona massimizza finestra filtri"/>';
		}
	}
$Contenuto='';
$Contenuto.=' <div class="Visalbo">
<a name="dati"></a> ';
if (get_option('opt_AP_VisualizzaEnte')=='Si')
		$Contenuto.= '<'.$titEnte.' ><span  class="titoloEnte">'.stripslashes(get_option('opt_AP_Ente')).'</span></'.$titEnte.'>';
$Contenuto.='<'.$titPagina.'><span  class="titoloPagina">'.$TitoloAtti.'</span></'.$titPagina.'>';
if (!isset($Parametri['filtri']) Or $Parametri['filtri']=="si")
	$Contenuto.='<h4>Filtri '.$VisFiltro.'</h4>'.VisualizzaRicerca($Parametri['stato'],$cat,$Parametri['minfiltri']);
//$Contenuto.=  $nascondi;
if ($TotAtti>$N_A_pp){
	    $Para='';
	    foreach ($_REQUEST as $k => $v){
			if ($k!="Pag" and $k!="vf")
				if ($Para=='')
					$Para.=$k.'='.$v;
				else
					$Para.='&amp;'.$k.'='.$v;
		}
		if ($Para=='')
			$Para="?Pag=";
		else
			$Para="?".$Para."&amp;Pag=";
		$Npag=(int)($TotAtti/$N_A_pp);
		if ($TotAtti%$N_A_pp>0){
			$Npag++;
		}
		$Contenuto.= ' 
		<div class="tablenav" style="float:right;" id="risultati">
		<div class="tablenav-pages">
    		<p><strong>N. Atti '.$TotAtti.'</strong>&nbsp;&nbsp; Pagine';
    	if (isset($_REQUEST['Pag']) And $_REQUEST['Pag']>1 ){
			$Pagcur=$_REQUEST['Pag'];
			$PagPre=$Pagcur-1;
				$Contenuto.= '&nbsp;<a href="'.$Para.'1" class="page-numbers numero-pagina" title="Vai alla prima pagina">&laquo;</a>
&nbsp;<a href="'.$Para.$PagPre.'" class="page-numbers numero-pagina" title="Vai alla pagina precedente">&lsaquo;</a> ';
		}else{
			$Pagcur=1;
			$Contenuto.= '&nbsp;<span class="page-numbers current" title="Sei gi&agrave; nella prima pagina">&laquo;</span>
&nbsp;<span class="page-numbers current" title="Sei gi&agrave; nella prima pagina">&lsaquo;</span> ';
		}
		$Contenuto.= '&nbsp;<span class="page-numbers current">'.$Pagcur.'/'.$Npag.'</span>';
		$PagSuc=$Pagcur+1;
	   	if ($PagSuc<=$Npag){
			$Contenuto.= '&nbsp;<a href="'.$Para.$PagSuc.'" class="page-numbers numero-pagina" title="Vai alla pagina successiva">&rsaquo;</a>
&nbsp;<a href="'.$Para.$Npag.'" class="page-numbers numero-pagina" title="Vai all\'ultima pagina">&raquo;</a>';
		}else{
			$Contenuto.= '&nbsp;<span class="page-numbers current" title="Se nell\'ultima pagina non puoi andare oltre">&rsaquo;</span>
&nbsp;<span class="page-numbers current" title="Se nell\'ultima pagina non puoi andare oltre">&raquo;</span>';			
		}
	$Contenuto.='			</p>
    	</div>
	</div>';
	}		
$Contenuto.= '	<div class="tabalbo">                               
		<table id="elenco-atti-OldStyle" class="tabella-dati-albo" summary="atti validi per riferimento, oggetto e categoria"> 
	    <caption>Atti</caption>
		<thead>
	    	<tr>
	        	<th scope="col">Prog.</th>
	        	<th scope="col">Ente</th>
	        	<th scope="col">Rif.</th>
	        	<th scope="col" style="width:200px;">Oggetto</th>
	        	<th scope="col">Validit&agrave;</th>
	        	<th scope="col" style="width:100px;">Categoria</th>
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
			}
			if (strpos(get_permalink(),"?")>0)
				$sep="&amp;";
			else
				$sep="?";
			$Contenuto.= '<tr >
			        <td '.$classe.'>'.$NumeroAtto.'/'.$riga->Anno .'
					</td>
					<td '.$classe.'>
						'.stripslashes(ap_get_ente($riga->Ente)->Nome) .'
					</td>
					<td '.$classe.'>
						'.$riga->Riferimento .'
					</td>
					<td '.$classe.'>
						<a href="'.get_permalink().$sep.'action=visatto&amp;id='.$riga->IdAtto.'"  >'.$riga->Oggetto .'</a>  
					</td>
					<td '.$classe.'>
						'.ap_VisualizzaData($riga->DataInizio) .'<br />'.ap_VisualizzaData($riga->DataFine) .'  
					</td>
					<td '.$classe.'>
						'.$cat .'  
					</td>
				</tr>'; 
			}
	} else {
			$Contenuto.= '<tr>
					<td colspan="6">Nessun Atto Codificato</td>
				  </tr>';
	}
	$Contenuto.= '
     </tbody>
    </table>';
$Contenuto.= '</div>';
	if ($CeAnnullato) 
		$Contenuto.= '<p>Le righe evidenziate con questo sfondo <span style="background-color: '.$coloreAnnullati.';">&nbsp;&nbsp;&nbsp;</span> indicano Atti Annullati</p>';
$Contenuto.= '</div><!-- /wrap -->	';
return $Contenuto;
}
?>