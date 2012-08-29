<?php
/******************************************
 * Autocompletar creado por Geynen Montenegro Cochas
 * Copyright Geynen. 
 * Fecha: 03-02-2011 Chiclayo - Perú
 * Version: 1.0
 * http://geynen.wordpress.com
 * Ref: http://geynen.wordpress.com
 ******************************************/
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ejemplo de Autocompletar</title>
<!--FUNCIONES AUTOCOMPLETAR: LAS CUALES PODEMOS REUTILIZAR EN DISTINTOS ARCHIVOS-->
<script type="text/javascript" src="autocompletar.js"></script>
<!---->
<!--LAS SIGUIENTES FUNCIONES LAS USO PARA LLAMAR AL AJAX Y A LAS FUNCIONES DEL AUTOCOMPLETAR-->
<script>
function listadoPersona(div,idtipofamiliar,nombres){
	var recipiente = document.getElementById(div);
	var g_ajaxPagina = new AW.HTTP.Request;  
	g_ajaxPagina.setURL("vista/ajaxPersona.php");
	g_ajaxPagina.setRequestMethod("POST");
	g_ajaxPagina.setParameter("action", "BuscaPersona");
	g_ajaxPagina.setParameter("nombres", nombres);
	g_ajaxPagina.response = function(xform){
		recipiente.innerHTML = xform;
	};
	g_ajaxPagina.request();
}

function buscarPersona(e,div){
  if(!e) e = window.event; 
    var keyc = e.keyCode || e.which;     
    
    if(keyc == 38 || keyc == 40 || keyc == 13) {
        autocompletar_teclado(div, 'tablaPersona', keyc);
        
    }else{
		if(div=='divregistrosPersona'){
			//si presiona retroceso o suprimir
			if(keyc == 8 || keyc == 46) {
				document.getElementById('txtIdPersona').value="";
			}
			listadoPersona(div,1,document.getElementById('txtPersona').value);
		}
  		eval(div+'.style.display="";');
		window.setTimeout(div+'.style.display="";', 300);
  }
}
function mostrarPersona(id,div){
		var g_ajaxPagina = new AW.HTTP.Request;  
		g_ajaxPagina.setURL("vista/xajaxAlumnoMaestro.php");
		g_ajaxPagina.setRequestMethod("POST");
		g_ajaxPagina.setParameter("action", "mostrarFamiliar");
		g_ajaxPagina.setParameter("id", id);
		g_ajaxPagina.response = function(xform){
			eval(xform);
			if(div=='divregistrosPersona'){
				document.getElementById('txtIdPersona').value = id;
				document.getElementById('txtPersona').value = vNombres;
				divregistrosPersona.style.display="none";
			}
		};
		g_ajaxPagina.request();
}
</script>
<!--AUTOCOMPLETAR: LOS ESTILOS SIGUIENTES SON PARA CAMBIAR EL EFECTO AL MOMENTO DE NAVEGAR POR LA LISTA DEL AUTOCOMPLETAR-->
<style type="text/css">    
		.autocompletar tr:hover, .autocompletar .tr_hover {cursor:default; text-decoration:none; background-color:#999;}
		.autocompletar tr span {text-decoration:none; color:#99CCFF; font-weight:bold; }
		.autocompletar {border:1px solid rgb(0, 0, 0); background-color:rgb(255, 255, 255); position:absolute; overflow:hidden; }
    </style>  
<!--AUTOCOMPLETAR--> 
</head>
<body>
<!--HOJA DE ESTILO Q NORMALMENTE USO PARA DAR FORMATO A MIS TABLAS-->
<link href="estiloadmin.css" rel="stylesheet" type="text/css">
<!---->
<div id='divBuscarPersona' style="overflow:auto;">
<fieldset><legend><strong>BUSQUEDA PERSONAS:</strong></legend>
        <input name="txtIdPersona" id="txtIdPersona" type="hidden" value="<?php if($_GET["accion"]=="ACTUALIZAR") echo $dato[$value["Descripcion"]]?>">
        <input name="txtPersona" id="txtPersona" onBlur="autocompletar_blur('divregistrosPersona')" onKeyUp="buscarPersona(event,'divregistrosPersona')" style="width:230px" value="<?php if($_GET["accion"]=="ACTUALIZAR") echo $dato["Persona"]?>">
  <button id="btnPersona" type="button" class="boton" onClick="window.open('vista/listFamiliar.php?id_clase=39','_blank','width=380,height=480');">...</button><br>
<div id="divregistrosPersona" class="autocompletar"  style="display:none"></div>
</fieldset></div>
</body>
</html>