

function dblClick() {
	document.kb_imageedit_form.submit();
}

function my_DragFunc()
{
	if (zoomLevel==100)	{
		document.kb_imageedit_form.offsetx.value = dd.obj.x-dd.obj.defx;
		document.kb_imageedit_form.offsety.value = dd.obj.y-dd.obj.defy;
	} else	{
		document.kb_imageedit_form.offsetx.value = Math.round((dd.obj.x-dd.obj.defx)/(zoomLevel/100));
		document.kb_imageedit_form.offsety.value = Math.round((dd.obj.y-dd.obj.defy)/(zoomLevel/100));
	}
	dd.obj.maxw = actualXsize -(dd.obj.x-dd.obj.defx);
	dd.obj.maxh = actualYsize -(dd.obj.y-dd.obj.defy);
}
function my_ResizeFunc()
{
	if (zoomLevel==100)	{
		document.kb_imageedit_form.width.value = dd.obj.w;
		document.kb_imageedit_form.height.value = dd.obj.h;
	} else	{
		document.kb_imageedit_form.width.value = Math.round(dd.obj.w / (zoomLevel/100));
		document.kb_imageedit_form.height.value = Math.round(dd.obj.h / (zoomLevel/100));
	}
	dd.obj.maxoffr = actualXsize-dd.obj.w;
	dd.obj.maxoffb = actualYsize-dd.obj.h;
}


function redraw_div(dimension)	{
	var x = parseInt(document.kb_imageedit_form.offsetx.value);
	var y = parseInt(document.kb_imageedit_form.offsety.value);
	var w = parseInt(document.kb_imageedit_form.width.value);
	var h = parseInt(document.kb_imageedit_form.height.value);
	var div = dd.elements["cropdiv"];
	
	if (zoomLevel!=100)	{
		x = Math.floor(x*zoomLevel/100);
		y = Math.floor(y*zoomLevel/100);
		w = Math.ceil(w*zoomLevel/100);
		h = Math.ceil(h*zoomLevel/100);
	}
	
	if (isNaN(x))	{
		x = 0;
	}
	if (isNaN(y))	{
		y = 0;
	}
	if (isNaN(w))	{
		w = ###X_2###;
	}
	if (isNaN(h))	{
		h = ###Y_2###;
	}
	
	if(x<0) x=0;
	if(y<0) y=0;
	if(w < div.minw) w = div.minw;
	if(h < div.minh) h = div.minh;
	
	//x or y is grater than the image
	if(x >= ###ZOOM_X### - div.minw ) { x = ###ZOOM_X### - div.minw;  }
	if(y >= ###ZOOM_Y### - div.minh ) { y = ###ZOOM_Y### - div.minh; }
	
	if(dimension == "x"){
	/* the x parameter was changed, so we respect that and change the width */
		if (x > (###ZOOM_X###-w))	{
			w = ###ZOOM_X###-x;
		}
	}else 
		if(dimension == "y"){
			
			if (y > (###ZOOM_Y###-h) )	{
				h = ###ZOOM_Y###-y;
	}
	}else 
		if(dimension == "w"){
			if (w > ###ZOOM_X###){
			w = ###ZOOM_X###;
			x=0;
			}else
			if (w > (###ZOOM_X###-x))	{
			x = ###ZOOM_X###-w;
			}
			if(w == ###ZOOM_X###)
			x=0;
			
	}else 
		if(dimension == "h"){
		if (h > ###ZOOM_Y###){
			h = ###ZOOM_Y###;
			y=0;
			}else
		if (h > (###ZOOM_Y###-y))	{
		y = ###ZOOM_Y###-h;
		}
		if(h == ###ZOOM_Y###)
			y=0;
	}else  { document.write("wrong parameter! in redraw_div");
		return false;
		}
		
		if((x+w) > ###ZOOM_X###) x= ###ZOOM_X### -w;
		if((y+h) > ###ZOOM_Y###) x= ###ZOOM_Y### -h;
		
	document.kb_imageedit_form.offsetx.value = Math.round(x/(zoomLevel/100));
	document.kb_imageedit_form.offsety.value = Math.round(y/(zoomLevel/100));
	document.kb_imageedit_form.width.value = Math.round(w/(zoomLevel/100));
	document.kb_imageedit_form.height.value = Math.round(h/(zoomLevel/100));
		
	dd.elements["cropdiv"].moveTo(x+dd.elements["cropdiv"].defx, y+dd.elements["cropdiv"].defy);
	dd.elements["cropdiv"].resizeTo(w, h);
	div.maxoffr = actualXsize-div.w;
	div.maxoffb = actualYsize-div.h;
	
	return true;
}

