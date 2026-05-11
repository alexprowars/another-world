var x = 0;
var y = 0;
function setCurrentMouse(e){
	oCanvas = document.getElementsByTagName((document.compatMode && document.compatMode == "CSS1Compat") ? "HTML" : "BODY")[0];
	x = window.event ? event.clientX + oCanvas.scrollLeft : e.pageX;
	y = window.event ? event.clientY + oCanvas.scrollTop : e.pageY;
}
document.onmousemove=setCurrentMouse;

function get_div_w(){
	return document.getElementById('dhint').offsetWidth;
}
function get_div_h(){
	return document.getElementById('dhint').offsetHeight;
}
function get_screen_w(){
	var w = 0;
	if (window.innerWidth){
		w = window.innerWidth + document.body.scrollLeft;
	} else if ((document.body) && (document.body.clientWidth)){
		w = document.body.clientWidth + document.body.scrollLeft;
	}
	if (document.documentElement && document.documentElement.clientWidth){
		w = document.documentElement.clientWidth + document.body.scrollLeft;
	}
	return w;
}
function get_screen_h(){
	var h = 0;
	if (window.innerHeight){
		h = window.innerHeight + document.body.scrollTop;
	} else if ((document.body) && (document.body.clientHeight)){
		h = document.body.clientHeight + document.body.scrollTop;
	}
	if (document.documentElement && document.documentElement.clientHeight){
		h = document.documentElement.clientHeight + document.body.scrollTop;
	}
	return h;
}

function set_text_hint(s){
	document.getElementById('hintdata').innerHTML = s;
}
function show_hint(){
	if ( document.getElementById('hintdata').innerHTML != '' && document.getElementById('hintdata').innerHTML != '&nbsp;' ){
		var hint_w = get_div_w();
		var hint_h = get_div_h();
		var screen_w = get_screen_w();
		var screen_h = get_screen_h();
		var hx = x+10;
		var hy = y+18;
		if ( hx + hint_w > screen_w ){
			hx = hx - 10 - hint_w;
			if ( hx < 0 ) hx = 0;
		}
		if ( hy + hint_h > screen_h ){
			hy = hy - 18 - hint_h;
			if ( hy < 0 ) hy = 0;
		}
		set_div_pos('dhint',hx,hy);
		MM_showHideLayers('dhint','','show');
	}
}
function hide_hint(){
	document.getElementById('hintdata').innerHTML = '';
	MM_showHideLayers('dhint','','hide');
}

isOpera = (navigator.userAgent.indexOf("Opera") != -1 && navigator.userAgent.indexOf("Opera/9") == -1);
function set_div_pos(id,x,y){
	var obj = document.getElementById(id);
	if (!obj) return;
	if ( isOpera ){
		obj.style.pixelLeft=x;
		obj.style.pixelTop=y;
	} else {
		obj.style.left=x+"px";
		obj.style.top=y+"px";
	}
}