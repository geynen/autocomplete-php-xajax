<?php
/******************************************
 * Autocompletar creado por Geynen Montenegro Cochas
 * Copyright Geynen. 
 * Fecha: 03-02-2011 Chiclayo - Perú
 * Version: 1.0
 * http://geynen.wordpress.com
 * Ref: http://blog.rarecore.eu/autocompleter-using-xajax-updated.html
 ******************************************/
 
require('xajax/xajax_core/xajax.inc.php');
$xajax= new xajax();
$xajax->configure('javascript URI','xajax/');
//$xajax->configure('debug', true);//ver errores

require("datos/cado.php");

function listadopersona($campo,$frase,$pag,$TotalReg){
	Global $ObjPersona;
 	Global $cnx;
	$EncabezadoTabla=array("Apellidos y Nombres","RUC/DNI");
	$regxpag=10;
	$nr1=$TotalReg;
	$inicio=$regxpag*($pag - 1);
	$limite="";
	$frase=utf8_decode($frase);	
	if($inicio==0){		
		$rs = $cnx->query("SELECT Distinct persona.idpersona, CONCAT(apellidos,' ',nombres) as Nombres, NroDoc FROM persona WHERE ".$campo." LIKE '%" . $frase . "%' ". $limite);	
    	$nr1=$rs->rowCount();
	}
	$nunPag=ceil($nr1/$regxpag);
	$limite=" limit $inicio,$regxpag";
	$rs = $cnx->query("SELECT Distinct persona.idpersona, CONCAT(apellidos,' ',nombres) as Nombres, NroDoc FROM persona WHERE ".$campo." LIKE '%" . $frase . "%' ". $limite);	
    $nr=$rs->rowCount()*($pag);	
	$CantCampos=$rs->columnCount();
    $cadena="Encontrados: $nr de $nr1";
    $registros="
	<table id='tablaPersona' class=registros>
    <tr>";
	for($i=0;$i<count($EncabezadoTabla);$i++){
	$registros.="<th>".$EncabezadoTabla[$i]."</th>";
	}
	$cont=0;
    while($reg=$rs->fetch()){
	   $cont++;
	   if($cont%2) $estilo="par";
	   else $estilo="impar";
	   $registros.= "<tr id='".$reg[0]."' class='$estilo' onClick='mostrarPersona(".$reg[0].")'>";
	   for($i=0;$i<$CantCampos;$i++){
		   if($i<>0){
			   //LO SGTE PARA OBTENER LA PORSION DE TEXTO QUE COINCIDE Y CAMBIARLE DE ESTILO, $cadena2 -> está variable contiene el valor q coincide, al cual lo ubico en una etiqueta span para cambiarle de estilo.
				$posicion  = stripos($reg[$i], $frase);
				if($posicion>-1){
					$cadena1 = substr($reg[$i], 0, $posicion);
					$cadena2 = substr($reg[$i], $posicion, strlen($frase));
					$cadena3 = substr($reg[$i], ($posicion + strlen($frase)));
					
					$dato = $cadena1.'<span>'.$cadena2.'</span>'.$cadena3;
					$registros.= "<td>".$dato."</td>";
				}else{
					$registros.= "<td>".$reg[$i]."</td>";
					}
		   }
	   }
	   $registros.=$RegistroSeleccion;
	   $registros.= "</tr>";
    }
	//PAGINACION
	$registros.="</table>".$cadena."<center>Pag: ";
	for($i=1;$i<=$nunPag;$i++){
		$registros.='<a href="#" onClick="javascript:pagPersona.value='.$i.';buscarPersona(event)">'.$i.' </a>';
	}
	$registros.='</center>';

	$registros=utf8_encode($registros);
	$objResp=new xajaxResponse();
	$objResp->assign('divregistrosPersona','innerHTML',$registros);
	$objResp->assign('TotalRegPersona','value',$nr1);
	return $objResp;
}

function mostrarPersona($id){
  Global $ObjPersona;
  Global $cnx;
  $sql = "SELECT IdPersona,CONCAT(apellidos,' ',nombres) as Nombres, NroDoc FROM Persona WHERE 1=1";
  $sql .= " AND IdPersona=".$id;
  $rs = $cnx->query($sql); 	 	
  $reg= $rs->fetchObject();
  $objResp=new xajaxResponse();
  $objResp->assign('txtIdPersona','value',$reg->IdPersona);
  $objResp->assign('frasePersona','value',utf8_encode($reg->Nombres));
  return $objResp;

}
$xajax->registerFunction('mostrarPersona');
$flistadopersona = & $xajax-> registerFunction('listadopersona');
$flistadopersona->setParameter(0,XAJAX_INPUT_VALUE,'campoPersona');
$flistadopersona->setParameter(1,XAJAX_INPUT_VALUE,'frasePersona');
$flistadopersona->setParameter(2,XAJAX_INPUT_VALUE,'pagPersona');
$flistadopersona->setParameter(3,XAJAX_INPUT_VALUE,'TotalRegPersona');

$xajax->processRequest();
echo"<?xml version='1.0' encoding='UTF-8'?>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ejemplo de Autocompletar</title>
<?php $xajax->printJavascript();?>
<!--FUNCIONES AUTOCOMPLETAR: LAS CUALES PODEMOS REUTILIZAR EN DISTINTOS ARCHIVOS-->
<script type="text/javascript" src="autocompletar.js"></script>
<!---->
<!--AUTOCOMPLETAR: LOS ESTILOS SIGUIENTES SON PARA CAMBIAR EL EFECTO AL MOMENTO DE NAVEGAR POR LA LISTA DEL AUTOCOMPLETAR-->
<style type="text/css">    
        body {
            font:9pt arial, helvetica, sans-serif;
        }
      
		.autocompletar tr:hover, .autocompletar .tr_hover {cursor:default; text-decoration:none; background-color:#999;}
		.autocompletar tr span {text-decoration:none; color:#99CCFF; font-weight:bold; }
		.autocompletar {border:1px solid rgb(0, 0, 0); background-color:rgb(255, 255, 255); position:absolute; overflow:hidden; }
    </style>  
<!--AUTOCOMPLETAR-->  
<script>
<!--LAS SIGUIENTES FUNCIONES LAS USO PARA LLAMAR AL XAJAX Y A LAS FUNCIONES DEL AUTOCOMPLETAR-->
function buscarPersona(e){
  if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;     
    
    if(keyc == 38 || keyc == 40 || keyc == 13) {
        autocompletar_teclado('divregistrosPersona', 'tablaPersona', keyc);
        
    }else{
	  	<?php $flistadopersona->printScript() ?>;
  		divregistrosPersona.style.display="";
		window.setTimeout('divregistrosPersona.style.display="";', 300);
  }
}
function mostrarPersona(id){
   xajax_mostrarPersona(id);
   divregistrosPersona.style.display="none";
}
</script>
</head>

<body>
<!--HOJA DE ESTILO Q NORMALMENTE USO PARA DAR FORMATO A MIS TABLAS-->
<link href="estiloadmin.css" rel="stylesheet" type="text/css">
<!---->
<div id='divBuscarPersona' style="overflow:auto;">
<fieldset><legend><strong>BUSQUEDA PERSONAS:</strong></legend>
<!--CAMPOS OCULTOS PARA EL MANEJO DE LA PAGINACION-->
<input type="hidden" name="pagPersona" id="pagPersona" value="1">
<input type="hidden" name="TotalRegPersona" id="TotalRegPersona">
<table>
<tr><td>Por:</td> 
<td>
  <select name="campoPersona" id="campoPersona" onChange="javascript:pagPersona.value=1;buscarPersona(event)">
    <option value="CONCAT(apellidos,' ',nombres)">Apellidos y Nombres</option>
	<option value="NroDoc">Nro Doc.</option>
  </select>
</td></tr>
<tr><td>
  <input type="hidden" id="txtIdPersona" />
      Descripción:</td>
<td>
  <input name="frasePersona" id="frasePersona" onblur="autocompletar_blur('divregistrosPersona')" onkeyup="javascript:pagPersona.value=1;buscarPersona(event)" style="width:230px">
<div id="divregistrosPersona" class="autocompletar"  style="display:none"></div>
</td></tr></table>
</fieldset></div>
</body>
</html>