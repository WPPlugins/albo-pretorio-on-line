<?php
/**
 * Gestione FrontEnd.
 * @link       http://www.eduva.org
 * @since      4.1.1
 *
 * @package    ALbo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

if(isset($_REQUEST['action'])){
	switch ($_REQUEST['action']){
		case 'visatto':
			if(is_numeric($_REQUEST['id']))
				VisualizzaAtto($_REQUEST['id']);
			else{
				$ret=Lista_Atti($Parametri);
			}
				
			break;
		case 'addstatall':
			if(is_numeric($_GET['id']) and is_numeric($_GET['idAtto']))
				ap_insert_log(6,5,(int)$_GET['id'],"Download",(int)$_GET['idAtto']);
			break;
		default: 
			if (isset($_REQUEST['filtra']))
			 		$ret=Lista_Atti($Parametri,$_REQUEST['categoria'],(int)$_REQUEST['anno'], htmlentities($_REQUEST['oggetto']),htmlentities($_REQUEST['DataInizio']),htmlentities($_REQUEST['DataFine']));
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
	}else{
		if (isset($_REQUEST['filtra']))
 		$ret=Lista_Atti($Parametri,$_REQUEST['categoria'],(int)$_REQUEST['anno'], htmlentities($_REQUEST['oggetto']),htmlentities($_REQUEST['DataInizio']),htmlentities($_REQUEST['DataFine']));
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
		$Annullato='<p style="background-color: '.$coloreAnnullati.';text-align:center;font-size:1.5em;">Atto Annullato dal Responsabile del Procedimento<br /><br />Motivo: <span style="font-size:1;font-style: italic;">'.stripslashes($risultato->MotivoAnnullamento).'</span></p>';
	else
		$Annullato='';
echo '
<div>
	<p style="margin-bottom:1.5em;"><a href="'.$_SERVER['HTTP_REFERER'].'" title="Torna alla lista degli atti">Torna alla lista</a>
	'.$Annullato.'
	</p>
	<h2 >Dati atto</h2>
	<div class="Grid Grid--withGutter u-padding-all-l">
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Ente titolare dell\'Atto</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.stripslashes(ap_get_ente($risultato->Ente)->Nome).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Numero Albo</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$risultato->Numero."/".$risultato->Anno.'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Codice di Riferimento</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.stripslashes($risultato->Riferimento).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Oggetto</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-margin-top-xs u-padding-left-m u-padding-right-m u-padding-top-xxs u-padding-bottom-s u-border-bottom-xxs">'.stripslashes($risultato->Oggetto).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Data inizio Pubblicazione</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.ap_VisualizzaData($risultato->DataInizio).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Data fine Pubblicazione</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.ap_VisualizzaData($risultato->DataFine).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Data oblio</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.ap_VisualizzaData($risultato->DataOblio).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xxs u-borderRadius-m u-padding-all-m">Note</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-margin-top-xs u-padding-left-m u-padding-right-m u-padding-top-xxs u-padding-bottom-s u-border-bottom-xxs">'.(strlen(stripslashes($risultato->Note))>0?stripslashes($risultato->Note):"&nbsp;&nbsp;").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Categoria</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.stripslashes($risultatocategoria->Nome).'</div>
		</div>';
$MetaDati=ap_get_meta_atto($id);
if($MetaDati!==FALSE){
	$Meta="";
	foreach($MetaDati as $Metadato){
		$Meta.="{".$Metadato->Meta."=".$Metadato->Value."} - ";
	}
	$Meta=substr($Meta,0,-3);
		echo'	
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Meta Dati</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$Meta.'</div>
		</div>';
}
echo'</div>
</div>';
if ($responsabile){
	echo '
	<div>
	<h2 >Responsabile</h2>
	<div class="Grid Grid--withGutter u-padding-all-l">
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Persona</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$responsabile->Cognome." ".$responsabile->Nome.'</div>
		</div>		
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">email</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs"><a href="mailto:'.$responsabile->Email.'">'.$responsabile->Email.'</a></div>
		</div>		
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Telefono</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$responsabile->Telefono.'</div>
		</div>		
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Orario ricevimento</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$responsabile->Orario.'</div>
		</div>';
if ($responsabile->Note)
	echo'
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">Note</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$responsabile->Note.'</div>
		</div>';
}		
echo '</div>
</div>
<div>
	<h2>Allegati</h2>
		<div class="Grid Grid--withGutter u-padding-all-l">';
//print_r($_SERVER);
$TipidiFiles=ap_get_tipidifiles();
foreach ($allegati as $allegato) {
	$Estensione=ap_ExtensionType($allegato->Allegato);
	echo'
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAllegati">
			<div class="u-margin-bottom-xs u-borderRadius-m u-padding-all-m u-border-all-xxs">';
		if(isset($allegato->TipoFile) and $allegato->TipoFile!="" and ap_isExtensioType($allegato->TipoFile)){
			$Estensione=ap_ExtensionType($allegato->TipoFile);
			echo '<img src="'.$TipidiFiles[$Estensione]['Icona'].'" alt="'.$TipidiFiles[$Estensione]['Descrizione'].'" height="30" width="30"/>';
		}else{
			echo '<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30"allegato/>';
		}
	echo " ".strip_tags(($allegato->TitoloAllegato?$allegato->TitoloAllegato:basename( $allegato->Allegato))).'('.ap_Formato_Dimensione_File(filesize($allegato->Allegato)).')</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 FunctionAllegati">
			<div class="u-margin-bottom-xs u-borderRadius-m u-padding-all-m">';
			if (strpos(get_permalink(),"?")>0)
				$sep="&amp;";
			else
				$sep="?";
			if (is_file($allegato->Allegato))
				echo '<a href="'.ap_DaPath_a_URL($allegato->Allegato).'" class="addstatdw noUnderLine" rel="'.get_permalink().$sep.'action=addstatall&amp;id='.$allegato->IdAllegato.'&amp;idAtto='.$id.'" title="Visualizza Allegato"><span class="u-text-r-l Icon Icon-zoom-in"></span></a> '.htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.get_permalink().$sep.'action=dwnalle&amp;id='.$allegato->IdAllegato.'&amp;idAtto='.$id.'" class="noUnderLine" title="Scarica Allegato"><span class="u-text-r-l Icon Icon-download"></span></a>';
				
			else
				echo basename( $allegato->Allegato)." File non trovato, il file &egrave; stato cancellato o spostato!";
	echo '</div>
		</div>';
	}
echo '</div>
</div>';
}
function VisualizzaRicerca($Stato=1,$cat=0,$StatoFinestra="si"){
	$anni=ap_get_dropdown_anni_atti('anno','anno','postform','',(isset($_REQUEST['anno'])?$_REQUEST['anno']:date("Y")),$Stato); 
	$categorie=ap_get_dropdown_ricerca_categorie('categoria','categoria','postform','',(isset($_REQUEST['categoria'])?$_REQUEST['categoria']:0),$Stato); 
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
	$HTML.='		<form id="filtro-atti" action="'.htmlentities($_SERVER['REQUEST_URI']).'" method="post">
	';
			if (strpos(htmlentities($_SERVER['REQUEST_URI']),'page_id')>0){
				$HTML.= '<input type="hidden" name="page_id" value="'.ap_Estrai_PageID_Url().'" />';
			}	
	$HTML.= '<input type="hidden" name="categoria" value="'.$cat.'" />
				<table id="tabella-filtro-atti" class="Table Table--withBorder u-text-r-xs" >';
/*	if($cat!=0){
		$HTML.= '				
					<tr>
						<th scope="row" >Categorie</th>
						<td>'.$categorie.'</td>
					</tr>';
	}*/
	$HTML.= '
					<tr>
						<th scope="row"><label for="anno">Anno</label></th>
						<td>'.$anni.'</td>
					</tr>
					<tr>
						<th scope="row"><label for="oggetto">Oggetto</label></th>
						<td><input type="text" size="40" maxlength="150" name="oggetto" id ="oggetto" value="'.(isset($_REQUEST['oggetto'])?$_REQUEST['oggetto']:"").'"/></td>
					</tr>
					<tr>
						<th scope="row"><label for="Calendario1">da Data</label></th>
						<td><input name="DataInizio" id="Calendario1" type="text" value="'.htmlentities((isset($_REQUEST['DataInizio'])?$_REQUEST['DataInizio']:"")).'" size="10" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="Calendario2">a Data</label></th>
						<td><input name="DataFine" id="Calendario2" type="text" value="'.htmlentities((isset($_REQUEST['DataFine'])?$_REQUEST['DataFine']:"")).'" size="10" /></td>
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
		$DesCategorie="";
		$Categoria="";
		$Categorie=explode(",",$Parametri['cat']);
		foreach($Categorie as $Cate){
			$DesCat=ap_get_categoria($Cate);
			$DesCategorie.=$DesCat[0]->Nome.",";
			$Categoria.=$Cate.",";
		}
		$DesCategorie= substr($DesCategorie,0, strlen($DesCategorie)-1);
		$TitoloAtti.=" Categorie ".$DesCategorie;
		$Categoria=substr($Categoria,0, strlen($Categoria)-1);
		$cat=1;
	}else{
		$Categorie=$Categoria;
		$cat=0;
	}
	if (!isset($_REQUEST['Pag'])){
		$Da=0;
		$A=$N_A_pp;
	}else{
		$Da=($_REQUEST['Pag']-1)*$N_A_pp;
		$A=$N_A_pp;
	}
	$TotAtti=ap_get_all_atti($Parametri['stato'],$Anno,$Categorie,$Oggetto,$Dadata,$Adata,'',0,0,true);
	$lista=ap_get_all_atti($Parametri['stato'],$Anno,$Categorie,$Oggetto,$Dadata,$Adata,'Anno DESC,Numero DESC',$Da,$A); 
	$titEnte=get_option('opt_AP_LivelloTitoloEnte');
	if ($titEnte=='')
		$titEnte="h2";
	$titPagina=get_option('opt_AP_LivelloTitoloPagina');
	if ($titPagina=='')
		$titPagina="h3";
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
//	$colorePari=get_option('opt_AP_ColorePari');
//	$coloreDispari=get_option('opt_AP_ColoreDispari');
	$VisFiltro="";
	if(isset($Parametri['minfiltri']) And $Parametri['minfiltri']=="si"){
		if(isset($_REQUEST['vf']) and  $_REQUEST['vf']=="s"){
			$VisFiltro='<img src="'.Albo_URL.'img/minimize.png" id="maxminfiltro" class="s" alt="icona minimizza finestra filtri"/>';
		}else{
			$VisFiltro='<img src="'.Albo_URL.'img/maximize.png" id="maxminfiltro" class="h" alt="icona massimizza finestra filtri"/>';
		}
	}
$Contenuto=' <div class="Visalbo">
<a name="dati"></a> ';
if (get_option('opt_AP_VisualizzaEnte')=='Si')
		$Contenuto.= '<'.$titEnte.' ><span  class="titoloEnte">'.stripslashes(get_option('opt_AP_Ente')).'</span></'.$titEnte.'>';
$Contenuto.='<'.$titPagina.'><span  class="titoloPagina">'.$TitoloAtti.'</span></'.$titPagina.'>';
if (!isset($Parametri['filtri']) Or $Parametri['filtri']=="si")
	$Contenuto.='<h4 class="filtri">Filtri '.$VisFiltro.'</h4>'.VisualizzaRicerca($Parametri['stato'],$Categoria,$Parametri['minfiltri']);
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
		$Contenuto.='<div style="float:right;" id="risultati">
	<nav role="navigation" aria-label="Navigazione paginata" class="u-layout-prose">
		<ul class="Grid Grid--fit u-text-r-xxs">';
		if (isset($_REQUEST['Pag']) And $_REQUEST['Pag']>1 ){
			$Pagcur=$_REQUEST['Pag'];
			$PagPre=$Pagcur-1;
			$PagSuc=$Pagcur+1;
			$Contenuto.='	
				<li class="Grid-cell u-textCenter">
					<a href="'.$Para.'1" class="u-color-50 u-textClean u-block" title="Prima pagina">
						<span class="u-text-r-m">&laquo;</span>
					</a>
				</li>
				<li class="Grid-cell u-textCenter u-block">
					<a href="'.$Para.$PagPre.'" class="u-color-50 u-textClean u-block" title="Pagina precedente">
						<span class="u-text-r-m">&lsaquo;</span>
					</a>
				</li>';			
		}else{
			$Pagcur=$PagPre=1;
			$PagSuc=$Pagcur+1;
			$Contenuto.='	
				<li class="Grid-cell u-textCenter u-block">
					<span class="u-text-r-m u-color-5">&laquo;</span>
				</li>
				<li class="Grid-cell u-textCenter u-block">
					<span class="u-color-5 u-textClean u-block">&lsaquo;</span>
				</li>';	

		}
		$DisCP=$Pagcur.'/'.$Npag;
		$MR=strlen($DisCP)*0.5;
		$Contenuto.='	<li class="Grid-cell u-textCenter u-block" aria-hidden="true" style="margin-right:'.$MR.'em;">
				<span class="u-block u-color-black">
					<span class="u-text-r-s">'.$DisCP.'</span>
				</span>
			</li>
			<li class="Grid-cell u-textCenter u-block">';
		if($Pagcur==$Npag){
			$Contenuto.='					
				<span class="u-text-r-m u-color-5">&rsaquo;</span>	
			</li>
			<li class="Grid-cell u-textCenter u-block">
					<span class="u-text-r-m u-color-5">&raquo;</span>
				</a>
			</li>';
		}else{
			$PagSuc=$Pagcur+1;
			$Contenuto.='				 
					<a href="'.$Para.$PagSuc.'" class="u-color-50 u-textClean u-block" title="Prima successiva">
					<span class="u-text-r-m">&rsaquo;</span>
				</a>
			</li>
			<li class="Grid-cell u-textCenter u-block">
				<a href="'.$Para.$Npag.'" class="u-color-50 u-textClean u-block" title="Ultima pagina">
					<span class="u-text-r-m">&raquo;</span>
				</a>
			</li>';
		}
		$Contenuto.='			</ul>
	</nav>
</div>';
	}	
$FEColsOption=get_option('opt_AP_ColonneFE',array(
									"Ente"=>0,
									"Riferimento"=>0,
									"Oggetto"=>0,
									"Validita"=>0,
									"Categoria"=>0,
									"Note"=>0,
									"RespProc"=>0,
									"DataOblio"=>0));
if(!is_array($FEColsOption)){
$FEColsOption=json_decode($FEColsOption,TRUE);
}	
$Contenuto.= '	<div class="tabalbo">                               
		<table id="elenco-atti" class="Table Table--withBorder u-text-r-xs"> 
	    <caption class="u-hiddenVisually">Atti</caption>
		<thead>
	    	<tr class="u-border-bottom-xs">
	        	<th scope="col">Prog.</th>';
foreach($FEColsOption as $Opzione => $Valore){
		if($Valore==1){
			$Contenuto.= '			<th scope="col">'.$Opzione.'</th>';
		}
}
echo'	</tr>
	    </thead>
	    <tbody>';
	    $CeAnnullato=false;
	if ($lista){
	 	$pari=true;
		if (strpos(get_permalink(),"?")>0)
			$sep="&amp;";
		else
			$sep="?";
		foreach($lista as $riga){
			$Link='<a href="'.get_permalink().$sep.'action=visatto&amp;id='.$riga->IdAtto.'"  style="text-decoration: underline;">';
			$categoria=ap_get_categoria($riga->IdCategoria);
			$cat=$categoria[0]->Nome;
			$responsabileprocedura=ap_get_responsabile($riga->RespProc);
			$respproc=$responsabileprocedura[0]->Cognome." ".$responsabileprocedura[0]->Nome;
			$NumeroAtto=ap_get_num_anno($riga->IdAtto);
	//		Bonifica_Url();
			$classe='';
			if ($pari) 
				$classe='class="u-background-white"';
			if (!$pari)
				$classe='class="u-background-grey-10"';
			$pari=!$pari;
			if($riga->DataAnnullamento!='0000-00-00'){
				$classe='style="background-color: '.$coloreAnnullati.';"';
				$CeAnnullato=true;
			}
			$Contenuto.= '<tr >
			        <td '.$classe.'>'.$Link.$NumeroAtto.'/'.$riga->Anno .'</a> 
					</td>';
			if ($FEColsOption['Ente']==1)
				$Contenuto.='
					<td '.$classe.'>
						'.$Link.$Link.stripslashes(ap_get_ente($riga->Ente)->Nome) .'</a>
					</td>';
			if ($FEColsOption['Riferimento']==1)
				$Contenuto.='
					<td '.$classe.'>
						'.$Link.stripslashes($riga->Riferimento) .'</a>
					</td>';
			if ($FEColsOption['Oggetto']==1)
				$Contenuto.='			
					<td '.$classe.'>
						'.$Link.stripslashes($riga->Oggetto) .'</a>
					</td>';
			if ($FEColsOption['Validita']==1)
				$Contenuto.='								
					<td '.$classe.'>
						'.$Link.ap_VisualizzaData($riga->DataInizio) .'<br />'.ap_VisualizzaData($riga->DataFine) .'</a>  
					</td>';
			if ($FEColsOption['Categoria']==1)
				$Contenuto.='								
					<td '.$classe.'>
						'.$Link.$cat .'</a>  
					</td>';
			if ($FEColsOption['Note']==1)
				$Contenuto.='
					<td '.$classe.'>
						'.$Link.stripslashes($riga->Informazioni) .'</a>
					</td>';
			if ($FEColsOption['RespProc']==1)
				$Contenuto.='
					<td '.$classe.'>
						'.$Link.$respproc .'</a>
					</td>';	
			if ($FEColsOption['DataOblio']==1)
				$Contenuto.='
					<td '.$classe.'>
						'.$Link.ap_VisualizzaData($riga->DataOblio) .'</a>
					</td>';
		$Contenuto.='	
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