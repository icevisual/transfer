$(function() {
	$(".demo span").on('click', function() {
		$("#con").val($(this).text());
		$("#btn").click();
	});
	//                    $("#con").on('focus',function(){
	//                        $(_colorPicker).show();
	//                    });
	$("body *").click(function(e) {
		//                        if(e.target.is(_colorPicker))
		if ($(this).is("#con") || $(this).is(_colorPicker)) {
			if (!ltIe9)
				$(_colorPicker).show();
			e.stopPropagation();
		} else
			$(_colorPicker).hide();

	});
	if (!ltIe9) {
		ColorPicker(document.getElementById('color-picker'), function(hex, hsv,
				rgb) {
			document.body.style.backgroundColor = hex; // #HEX
			$("#con").val(hex);
		});
	}

	$("#con").popover({
		"html" : true,
		"trigger" : "hover"
	});

	var off = $("#first_td").position();
	debug(off.left);
	debug(off.top);
	//$("#color_boxes").css({"left": off.left + 325 , "top" : off.top +1  });
	//$("#color_boxes").css({"left": 335 , "top" : 94 });
	setCopy('#cp1', '#re1_h'); //16进制                       m=1
	setCopy('#cp2', '#re2_h'); //RGB    151,151,151     m=2
	setCopy('#cp3', '#re3_h'); //ARGB  8位                   m=3
	setCopy('#cp4', '#re4_h'); //RGBA  html5                m=4
	setCopy('#cp5', '#re5_h'); //HSL                                m=5
	setCopy('#cp6', '#re6_h'); //HSV
	$("#reset").click(function() {
		resetResult();
	});
	$("#random").click(function() {
		var r = parseInt(Math.random() * 255);
		var g = parseInt(Math.random() * 255);
		var b = parseInt(Math.random() * 255);
		var a = Math.random().toFixed(1);
		var rgba = r + "," + g + "," + b + "," + a.substring(1);
		$("#con").val(rgba);
		$("#btn").click();
	});

	$("#btn").click(function() {
		var con = $("#con").val();
		con = con.replace(/ /g, '');
		con = con.toLowerCase();
		var m = getModel(con);

		if (m == 0)
			notice("输入错误!", "#btn");
		else {
			debug(m[1]);
			var re = parseColor(m[1], m[0]);
			document.body.style.backgroundColor = re[0];

			$(re).each(function(k, v) {
				$("#re" + (k + 1)).html(v);
				$("#re" + (k + 1) + "_h").val(v);
			});

			$("#colorbox1").css("background-color", re[0]);
			$("#colorbox2").css("background-color", re[1]);
			$("#colorbox3").css({
				"background-color" : re[3]
			});
			$("#colorbox4").css("background-color", re[3]);
			$("#colorbox5").css("background-color", re[1]);
			$("#colorbox6").css("background-color", re[0]);

		}
		return false;
	});
});

function parseColor(con, model) {
	var a, r, g, b;
	switch (model) {
	case 1:
		r = parseInt(con.substring(0, 2), 16);
		g = parseInt(con.substring(2, 4), 16);
		b = parseInt(con.substring(4, 6), 16);
		a = 1;
		break;
	case 2:
		var cons = con.split(',');
		r = cons[0];
		g = cons[1];
		b = cons[2];
		a = 1;
		break;
	case 3:
		r = parseInt(con.substring(2, 4), 16);
		g = parseInt(con.substring(4, 6), 16);
		b = parseInt(con.substring(6, 8), 16);
		a = (parseInt(con.substring(0, 2), 16) / 255).toFixed(3);
		break;
	case 4:
		cons = con.split(',');
		r = cons[0];
		g = cons[1];
		b = cons[2];
		a = parseFloat(cons[3]);
		break;
	default:
		break;
	}
	debug('a = ' + a);
	debug('r = ' + r);
	debug('g = ' + g);
	debug('b = ' + b);
	var re = [];

	var cr = parseInt(255 * (1 - a) + r * a);
	var cg = parseInt(255 * (1 - a) + g * a);
	var cb = parseInt(255 * (1 - a) + b * a);

	var cr16 = setLengthTwo(cr.toString(16));
	var cg16 = setLengthTwo(cg.toString(16));
	var cb16 = setLengthTwo(cb.toString(16));
	re[0] = ("#" + cr16 + cg16 + cb16).toUpperCase();
	re[1] = "rgb(" + cr + "," + cg + "," + cb + ")";

	var aa = setLengthTwo(parseInt(a * 255).toString(16));
	var ar = setLengthTwo(parseInt(r).toString(16));
	var ag = setLengthTwo(parseInt(g).toString(16));
	var ab = setLengthTwo(parseInt(b).toString(16));

	re[2] = ("#" + aa + ar + ag + ab).toUpperCase();
	re[3] = "rgba(" + r + "," + g + "," + b + "," + a + ")";
	debug(re[0]);
	debug(re[1]);
	debug(re[2]);
	debug(re[3]);

	var hsl = rgb2hsl(cr, cg, cb);
	re[4] = "hsl(" + hsl[0] + ", " + hsl[1] + ", " + hsl[2] + ")";

	var hsv = rgb2hsv(cr, cg, cb);
	re[5] = "hsv(" + hsv[0] + ", " + hsv[1] + ", " + hsv[2] + ")";
	return re;

}

function setLengthTwo(str) {
	if (str.length == 1)
		return "0" + str;
	return str;
}

function getModel(con) {
	con = con.replace(/ /g, '');
	con = con.toLowerCase();
	var l = con.length;
	//判断模式2 和 模式4
	if (con.indexOf(',') > -1) {
		var cons = con.split(',');
		if (cons.length == 3 || cons.length == 4) {
			for (var i = 0; i < 3; i++) //判断前3位是否是小于255的
			{
				if (isNaN(cons[i]) || parseInt(cons[i]) > 255) {
					return 0;
				}
			}

			if (cons.length == 3)
				return [ 2, con ];
			else {
				if (parseFloat(cons[3]) <= 1 && parseFloat(cons[3]) >= 0)
					return [ 4, con ];
			}
			return 0;
		}
		return 0;
	}
	//判断模式1
	else if (l == 6 || l == 3 || l == 8
			|| (con.substring(0, 1) == '#' && (l == 7 || l == 4 || l == 9))) {
		//支持：ffffff  #ffffff  fff #fff   #ff123455 ff123456
		if (l == 7 || l == 4 || l == 9)
			con = con.substring(1);

		if (con.match(/^[0-9,a-f]+$/i)) {
			if (con.length == 6)
				return [ 1, con ];
			else if (con.length == 3) {
				var red = con.substring(0, 1);
				var green = con.substring(1, 2);
				var blue = con.substring(2, 3);
				return [ 1, red + red + green + green + blue + blue ];
			} else
				return [ 3, con ];
		}
		return 0;
	}
	return 0;
}

function inputError(str) {
	if (str == undefined)
		notice("颜色错误!", "#btn");
	else
		notice(str, "#btn");
	resetResult();
}

function rgb2hsl(r, g, b) {
	r /= 255, g /= 255, b /= 255;
	var max = Math.max(r, g, b), min = Math.min(r, g, b);
	var h, s, l = (max + min) / 2;

	if (max == min) {
		h = s = 0; // achromatic
	} else {
		var d = max - min;
		s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
		switch (max) {
		case r:
			h = (g - b) / d + (g < b ? 6 : 0);
			break;
		case g:
			h = (b - r) / d + 2;
			break;
		case b:
			h = (r - g) / d + 4;
			break;
		}
		h /= 6;
	}
	h = Math.round(360 * h) + "°";
	s = (s * 100).toFixed(2) + "% ";
	l = (l * 100).toFixed(2) + "% ";
	return [ h, s, l ];
}

function hslToRgb(h, s, l) {
	var r, g, b;

	if (s == 0) {
		r = g = b = l; // achromatic
	} else {
		function hue2rgb(p, q, t) {
			if (t < 0)
				t += 1;
			if (t > 1)
				t -= 1;
			if (t < 1 / 6)
				return p + (q - p) * 6 * t;
			if (t < 1 / 2)
				return q;
			if (t < 2 / 3)
				return p + (q - p) * (2 / 3 - t) * 6;
			return p;
		}

		var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
		var p = 2 * l - q;
		r = hue2rgb(p, q, h + 1 / 3);
		g = hue2rgb(p, q, h);
		b = hue2rgb(p, q, h - 1 / 3);
	}

	return [ Math.round(r * 255), Math.round(g * 255), Math.round(b * 255) ];
}

function rgb2hsv(r1, r2, r3) {
	var maxJ = Math.max(r1, r2, r3);
	var minJ = Math.min(r1, r2, r3);
	var _max = maxJ - minJ;
	var H, S, V;
	if (r1 == r2 && r2 == r3) {
		H = 0;
		S = 0;
	} else {
		switch (maxJ) {
		case r1:
			H = (r2 - r3) / _max;
			break;
		case r2:
			H = 2 + (r3 - r1) / _max;
			break;
		case r3:
			H = 4 + (r1 - r2) / _max;
			break;
		}
		H *= 60;
		if (H < 0)
			H += 360;
		H = (H).toFixed(2);
		S = ((maxJ - minJ) / maxJ).toFixed(2);
	}
	V = (maxJ / 255).toFixed(2);
	return [ H, S, V ];
}

function resetResult() {

	$("#con").val('');
	for (var i = 1; i < 6; i++) {
		$("#re" + i).html('');
		$("#re" + i + "_h").val('')
		$("#colorbox" + i).css("background-color", "");
	}

}

function testModel() {
	debug(getModel('fff'));
	debug(getModel('#EEE'));
	debug(getModel(' #ffaacc'));
	debug(getModel(' #ffaacc '));
	debug(getModel('125,125,125'));
	debug(getModel('125, 125, 125'));
	debug(getModel('125, 125, 255 ,0.5'));
	debug(getModel('125, 125, 255 ,0'));
	debug(getModel(' #ffaaccdd '));
	debug(getModel(' #12aaccdd '));

	debug(getModel('fffa'));
	debug(getModel('ffg'));
	debug(getModel('#ffg'));
	debug(getModel('#ffgg'));
	debug(getModel('#ffgggg'));
	debug(getModel('555,555,555'));
	debug(getModel('125, 125, 2558'));
	debug(getModel('125, 125, 2558 ,0.5'));
	debug(getModel('125, 125, 255 ,1.5'));
	debug(getModel('ee, 125, 255 ,1.5'));
	debug(getModel('256, 125, 255 ,1.5'));
	debug(getModel('256, 125, 255 ,0.5'));
}

//testModel();
