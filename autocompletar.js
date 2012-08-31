/******************************************
 * Autocompletar creado por Geynen Montenegro Cochas
 * Copyright Geynen. 
 * Fecha: 03-02-2011 Chiclayo - Perú
 * Version: 1.0
 * http://geynen.wordpress.com
 * Ref: http://blog.rarecore.eu/autocompleter-using-xajax-updated.html
 ******************************************/

function autocompletar_blur(div) {
    window.setTimeout('autocompletar_blur2(\'' + div + '\')', 300);
}
        
function autocompletar_blur2(div) {
  document.getElementById(div).style.display="none";
}

function autocompletar_teclado(div, tabladiv, keyc) {
    var child = document.getElementById(tabladiv).rows;    
    var indice = -1;

    for(var i=0; i < child.length; i++) {
        if(child[i].className == 'tr_hover') {
            indice = i;
        }	
        if(i % 2==0){
	        child[i].className = 'impar';
		}else{
			child[i].className = 'par';}
    }

    // return
    if(keyc == 13) {
        var seleccionado = '';
        
        if(child[indice].id) {
            seleccionado = child[indice].id;
        } else {
            seleccionado = child[indice].id;        
        }
    
        mostrarPersona(seleccionado);
    
    } else {
        // abajo
        if(keyc == 40) {
            if(indice == (child.length - 1)) {
                indice = 1;
            } else {
				if(indice==-1) indice=0;
                indice++;
            }
        
        // arriba
        } else if(keyc == 38) {
            indice--;
            if(indice==0) indice=-1;
            if(indice < 0) {
                indice = (child.length - 1);
            }
        }
        
        child[indice].className = 'tr_hover';
    }
}
