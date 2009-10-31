
var reqCnt = 0;
function rotate_to(angle)	{
	reqCnt++;
	if (###USE_AJAX###) {
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
		ajax_doRequest("###AJAX_LINK###&a="+angle+"&c="+reqCnt+"&back_red="+col_r+"&back_green="+col_g+"&back_blue="+col_b+"&back_alpha="+col_a);
	}
}

	function update_bg()	{
		rotate_to(document.forms[0]["angle"].value);
	}

	function getTagValue(result, tag)	{
		var tagsAr = result.getElementsByTagName(tag);
		var tagObj = tagsAr[0];
		if (tagObj)	{
			return tagObj.textContent;
		}
		return "";
	}

	var recvCnt = -1;

	function updateImage(result)	{
		var file = getTagValue(result, "file");
		var count = parseInt(getTagValue(result, "count"));
		var width = getTagValue(result, "width");
		var height = getTagValue(result, "height");
		var orig_width = getTagValue(result, "orig_width");
		var orig_height = getTagValue(result, "orig_height");
		if (count>recvCnt)	{
			var img = document.getElementById("kb_imageedit-imageself");
			var cont = document.getElementById("kb_imageedit-mainimage");
			img.src = file;
			img.style.width = width+"px";
			img.style.height = height+"px";
			cont.style.width = width+"px";
			cont.style.height = height+"px";
			recvCnt = count;
		}
	}

	function initRotate()	{
		###SETTINGS_ROTATE###
	}


