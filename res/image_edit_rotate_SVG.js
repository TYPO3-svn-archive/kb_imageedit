
	// Variables
var sensitivity = 0.5;
var angle = 0;

	// Rotation via 2D-matrix transformation
function rotate_point(point, angle)	{
	var angle = angle * Math.PI / 180.0;
	var nx = point[0]*Math.cos(angle)-point[1]*Math.sin(angle);
	var ny = point[0]*Math.sin(angle)+point[1]*Math.cos(angle);
	return Array(nx, ny);
}

function rotate_points(points, center, angle)	{
	var h = Array.concat(points);
	for (var i in h)	{
		h[i][0] -= center[0];
		h[i][1] -= center[1];
		h[i] = rotate_point(h[i], angle);
		h[i][0] += center[0];
		h[i][1] += center[1];
	}
	return h;
}

function get_size(rota)	{
	var xmin = 1000000;
	var xmax = -1000000;
	var ymin = 1000000;
	var ymax = -1000000;
	for (var i in rota)	{
		if (rota[i][0] < xmin)	{
			xmin = rota[i][0];
		}
		if (rota[i][0] > xmax)	{
			xmax = rota[i][0];
		}
		if (rota[i][1] < ymin)	{
			ymin = rota[i][1];
		}
		if (rota[i][1] > ymax)	{
			ymax = rota[i][1];
		}
	}
	var xs = xmax-xmin;
	var ys = ymax-ymin;
	return Array(xs, ys);
}

function rotate_to(angle)	{
	angle = parseInt(angle);
	if (isNaN(angle))	{
		angle = 0;
	}
	var corners = Array(
		Array(0, 0),
		Array(###ZOOM_X###, 0),
		Array(###ZOOM_X###, ###ZOOM_Y###),
		Array(0, ###ZOOM_Y###)
	);
	var im = document.getElementById("kb_imageedit-svgimg");
	var sv = document.getElementById("kb_imageedit-svgtag");
	var svbg = document.getElementById("kb_imageedit-svgbg");
	var cont = document.getElementById("kb_imageedit-mainimage");
	var rota = rotate_points(corners, Array(###ZOOM_X_2###, ###ZOOM_Y_2###), -angle);
	var size = get_size(rota);
	cont.style.width = Math.round(size[0])+"px";
	cont.style.height = Math.round(size[1])+"px";
	sv.setAttribute("width", Math.round(size[0])+"px");
	sv.setAttribute("height", Math.round(size[1])+"px");
	var xos = (size[0]-###ZOOM_X###)/2;
	var yos = (size[1]-###ZOOM_Y###)/2;
	im.setAttribute("transform", "translate("+xos+", "+yos+") rotate("+angle+", ###ZOOM_X_2###, ###ZOOM_Y_2###)");
	svbg.setAttribute("width", size[0]+"px");
	svbg.setAttribute("height", size[1]+"px");
}

var drag = false;
var dragx = 0;
var dragy = 0;
var xmove = 0;
var ymove = 0;

function mouse_down(ev)	{
	if ((ev.type=="mousedown")&&(ev.which==1))	{		// Left mouse click
		var cont = document.getElementById("kb_imageedit-mainimage");
		drag = true;
		dragx = ev.pageX;
		dragy = ev.pageY;
		cx = dragx-cont.offsetLeft;
		cy = dragy-cont.offsetTop;
		if (cx<(cont.offsetWidth/2))	{
			ymove = -1;
		} else	{
			ymove = 1;
		}
		if (cy<(cont.offsetHeight/2))	{
			xmove = 1;
		} else	{
			xmove = -1;
		}
	}
}

function mouse_move(ev)	{
	if (drag)	{
		var diffx = xmove*(ev.pageX-dragx)*sensitivity;
		var diffy = ymove*(ev.pageY-dragy)*sensitivity;
		rotate_to(angle+diffx+diffy);
		var a = angle+diffx;
		while (a >= 360)	{
			a -= 360;
		}
		while (a < 0)	{
			a += 360;
		}
		document.forms[0]["angle"].value = a;
	}
}

function mouse_up(ev)	{
	if (drag)	{
		var diffx = xmove*(ev.pageX-dragx)*sensitivity;
		var diffy = ymove*(ev.pageY-dragy)*sensitivity;
		angle += diffx + diffy;
		rotate_to(angle);
		document.forms[0]["angle"].value = angle;
		drag = false;
		dragx = 0;
		dragy = 0;
	}
}

function update_bg()	{
	var svgbg = document.getElementById("kb_imageedit-svgbg");
	var col_r = parseInt(document.forms[0]["back_red"].value);
	var col_g = parseInt(document.forms[0]["back_green"].value);
	var col_b = parseInt(document.forms[0]["back_blue"].value);
	var col_a = parseInt(document.forms[0]["back_alpha"].value);
	if (isNaN(col_r) || col_r<0 || col_r>255)	{
		col_r = 0;
	}
	if (isNaN(col_g) || col_g<0 || col_g>255)	{
		col_g = 0;
	}
	if (isNaN(col_b) || col_b<0 || col_b>255)	{
		col_b = 0;
	}
	if (isNaN(col_a) || col_a<0 || col_a>255)	{
		col_a = 0;
	}
	svgbg.setAttribute("fill", "rgb("+col_r+","+col_g+","+col_b+")");
	svgbg.setAttribute("fill-opacity", 1-(col_a/255));
	
	document.forms[0]["back_red"].value = col_r;
	document.forms[0]["back_green"].value = col_g;
	document.forms[0]["back_blue"].value = col_b;
	document.forms[0]["back_alpha"].value = col_a;
}

	function initRotate()	{
		var svgimg = document.getElementById("kb_imageedit-svgimg");
		svgimg.onmousedown = mouse_down;
		window.onmousemove= mouse_move;
		window.onmouseup= mouse_up;
		###SETTINGS_ROTATE###
	}


