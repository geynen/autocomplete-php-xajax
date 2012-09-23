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

function listadopersona($nombres){
	Global $ObjPersona;
 	Global $cnx;

	$nombres=utf8_decode($nombres);	
	$rs = $cnx->query("SELECT Distinct persona.idpersona, CONCAT(apellidos,' ',nombres) as nombres FROM persona WHERE  CONCAT(apellidos,' ',nombres) LIKE '%" . $nombres . "%' ");	
    $nr=$rs->rowCount();	
    $cadena="Encontrados: $nr";
    $registros="<table id='tablaPersona' class=registros>
    <tr><th>NOMBRES</th></tr>";
	$cont=0;
    while($reg=$rs->fetchObject()){
	   $cont++;
	   if($cont%2) $estilo="par";
	   else $estilo="impar";
	   $registros.= "<tr id='".$reg->idpersona."' class='$estilo' onClick='mostrarPersona(".$reg->idpersona.")'>";
	   //LO SGTE PARA OBTENER LA PORSION DE TEXTO QUE COINCIDE Y CAMBIARLE DE ESTILO, $cadena2 -> está variable contiene el valor q coincide, al cual lo ubico en una etiqueta span para cambiarle de estilo.
		$posicion  = stripos($reg->nombres, $nombres);
		if($posicion>-1){
			$cadena1 = substr($reg->nombres, 0, $posicion);
			$cadena2 = substr($reg->nombres, $posicion, strlen($nombres));
			$cadena3 = substr($reg->nombres, ($posicion + strlen($nombres)));
			
			$dato = $cadena1.'<span>'.$cadena2.'</span>'.$cadena3;
			$registros.= "<td>".$dato."</td>";
		}else{
			$registros.= "<td>".$reg->nombres."</td>";
		}
	   $registros.= "</tr>";
    }
	$registros.="</table>";

	$registros=utf8_encode($registros);
	$objResp=new xajaxResponse();
	$objResp->assign('divregistrosPersona','innerHTML',$registros);
	return $objResp;
}

function mostrarPersona($id){
  Global $ObjPersona;
  Global $cnx;
  $sql = "SELECT IdPersona,CONCAT(apellidos,' ',nombres) as Nombres FROM Persona WHERE 1=1";
  $sql .= " AND IdPersona=".$id;
  $rs = $cnx->query($sql); 	 	
  $reg= $rs->fetchObject();
  $objResp=new xajaxResponse();
  $objResp->assign('txtIdPersona','value',$reg->IdPersona);
  $objResp->assign('txtNombres','value',utf8_encode($reg->Nombres));
  return $objResp;

}
$xajax->registerFunction('mostrarPersona');
$flistadopersona = & $xajax-> registerFunction('listadopersona');
$flistadopersona->setParameter(0,XAJAX_INPUT_VALUE,'txtNombres');

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
<input type="hidden" id="txtIdPersona" />
<table><tr><td>Buscar por Apellidos y Nombres:</td><td><input name="txtNombres" id="txtNombres" onblur="autocompletar_blur('divregistrosPersona')" onkeyup="javascript:buscarPersona(event)" style="width:230px">
<div id="divregistrosPersona" class="autocompletar"  style="display:none"></div></td></tr></table>
</body>
</html>