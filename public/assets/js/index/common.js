var undefined;

Number.prototype.toFixed = Number.prototype.toFixed || function(fractionDigits){
	return Math.floor( this * Math.pow(10, fractionDigits) + .5) / Math.pow(10, fractionDigits)
}

function gebi(id){
	return document.getElementById(id)
}

function jsquote(str){
	return str.replace(/'/g,'&#39;').replace(/>/g,'&gt;').replace(/</g,'&lt;').replace(/&/g,'&amp;') //'
}

function copyBoard(txt){
	if(document.body.createTextRange) {
		var d=document.createElement('INPUT')
		d.type='hidden'
		d.value=txt
		document.body.appendChild(d).createTextRange().execCommand("Copy")
		document.body.removeChild(d)
		return
	} else try {
		netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect')
		var gClipboardHelper = Components.classes["@mozilla.org/widget/clipboardhelper;1"].getService(Components.interfaces.nsIClipboardHelper)
		gClipboardHelper.copyString(txt)
	} catch (e) {}
}



function preloadImages() {
	var d = document;
	if(!d._prImg) d._prImg = new Array();
	var i, j = d._prImg.length, a = preloadImages.arguments;
	for (i=0; i<a.length; i++) {
		d._prImg[j] = new Image;
		d._prImg[j++].src = a[i];
	}
}

function checkbox_set(pfx, val) {
  var chk=document.getElementsByTagName('INPUT');
  for(var i=0;i<chk.length;i++){
    if(chk[i].name.indexOf(pfx)==0 || chk[i].getAttribute('grp')==pfx){
      chk[i].checked = (val == undefined ? !chk[i].checked: val);
    }
  }
}

function js_money_input_assemble(id_prefix) {
	var m1 = gebi(id_prefix+'1').value;
	var m2 = gebi(id_prefix+'2').value;
	var m3 = gebi(id_prefix+'3').value;

	if (m1.match(/[^0-9.]/)) m1 = m1.replace(/[^0-9].*$/, '');
	if (m2.match(/[^0-9.]/)) m2 = m2.replace(/[^0-9].*$/, '');
	if (m3.match(/[^0-9.]/)) m3 = m3.replace(/[^0-9].*$/, '');
	v = m1/100.0 + m2*1.0 + m3*100.0;
	res = (isNaN(v) || v <= 0) ? 0 : (1.0 * (1.0*v).toFixed(2)).toFixed(2);
	return res*1.0;
}

function js_money_input_fill(id_prefix, amount) {
	var m1 = gebi(id_prefix+'1');
	var m2 = gebi(id_prefix+'2');
	var m3 = gebi(id_prefix+'3');

    var str = ' ';
	var t=[];
	amount = amount * 100;
	for (i = 0; i < 2; i++) {
		t[i] = (amount % 100);
		amount = (amount - t[i]) / 100;
	}
	t[2] = amount;
	m1.value = t[0].toFixed(0);
	m2.value = t[1].toFixed(0);
	m3.value = t[2].toFixed(0);
}

// ========= swf data transfer functions ===============================================================

function getSWF(name) {
	var win = window;
	try { win = dialogArguments || window } catch(e) {}
	while (win.opener) win = win.opener;
	if (win.closed) return;
	win =
		(name == 'events' ? win.top.frames[0]:
		(name == 'map' ? win.top.frames[0].frames[0]:
		(name == 'InnerLife' ? win.top.frames[0].frames[0]:
		(name == 'Eggs' ? win.top.frames[0]:
		(name == 'userinfo' ? win.top.frames[0]:
		window)))));
	if (navigator.appName.indexOf("Microsoft") != -1) {
		return win[name];
	} else {
		return win.document[name];
	}
}

function commonDecline(n, d1, d234, d_pl) {
	var s=d_pl;
	var c=n%100;
	if ((c<10)
	||	(c>20)) {
		var l=n%10;
		if (l==1) s=d1;
		if ((l>=2) && (l<=4)) s=d234;
	}
    return s;
}

function toggle_quest(num) {
	var current = $('q'+num);
	$$('.q_title').each(function(elt) {
		if (current != elt.parentNode) {
			$(elt.parentNode).addClass('collapsed');
		}
	});
	current.toggleClass('collapsed');
}