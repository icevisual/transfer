<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Fomatter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="flatUI/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="flatUI/css/flat-ui.css" rel="stylesheet">
<link href="flatUI/css/demo.css" rel="stylesheet">

<link rel="shortcut icon" href="images/favicon.ico">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->
    <style>
    .demo-col{
        margin-top:30px;
    }
    [result]{
        font-size:20px;
        font-weight:25px;
    }
    </style>
</head>
<body>
	<div class="container">
		<form action="" method="post" id="form-01">
		       <input type="submit" value="GZ-SUBMIT" class="btn  btn-primary">
		       <a onclick="$('#result-field').format({method: 'xml'});" class="btn  btn-primary">XML-FORMAT</a>
		       <a onclick="$('#result-field').format({method: 'json'});" class="btn  btn-primary">JSON-FORMAT</a>
		       <a onclick="StrFormat('FBSDK')" class="btn  btn-primary">STR-FORMAT-FBSDK</a>
			   <a onclick="StrFormat('base64decode')" class="btn  btn-primary">BASE-64-DECODE</a>
		       <a onclick="StrFormat('stripslashes')" class="btn  btn-primary">STRIPSLASHES</a>
		       <a onclick="StrFormat('table2Array')" class="btn  btn-primary">TABLE2ARRAY</a>
		       <a onclick="$('#result-field').val(CSSencode($('#result-field').val()));" class="btn  btn-primary">CSS-FORMAT-HORIZONTAL </a>
		       <a onclick="$('#result-field').val(CSSdecode($('#result-field').val()));" class="btn  btn-primary">CSS-FORMAT-VERTICAL</a>
		
			   <textarea rows="33" result=1  spellcheck="false"  id="result-field"  name="content"  placeholder="" class="form-control">{{$result}}</textarea>
		</form>
	</div>
	<!-- /.container -->
	<!-- Load JS here for greater good =============================-->
	<script src="flatUI/js/jquery-1.8.3.min.js"></script>
	<script src="flatUI/js/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="flatUI/js/jquery.ui.touch-punch.min.js"></script>
	<script src="flatUI/js/bootstrap.min.js"></script>
	<script src="flatUI/js/bootstrap-select.js"></script>
	<script src="flatUI/js/bootstrap-switch.js"></script>
	<script src="flatUI/js/flatui-checkbox.js"></script>
	<script src="flatUI/js/flatui-radio.js"></script>
	<script src="flatUI/js/jquery.tagsinput.js"></script>
	<script src="flatUI/js/jquery.placeholder.js"></script>
	<script src="flatUI/js/jquery.format.js"></script>
	
	<script>
    var StrFormat = function(type){

        var content = $('#result-field').val();
        $.post('/format',{
            'content' : content,
            'type' : type,
            '_token' :'{{ csrf_token() }}'
        },function(d){
        	$('#result-field').val(d);
        },'text');
    }

    function CSSencode(code) 
    { 
     code = code.replace(/\r\n/ig,''); 
     code = code.replace(/(\s){2,}/ig,'$1'); 
     code = code.replace(/\t/ig,''); 
     code = code.replace(/\n\}/ig,'\}'); 
     code = code.replace(/\n\{\s*/ig,'\{'); 
     code = code.replace(/(\S)\s*\}/ig,'$1\}'); 
     code = code.replace(/(\S)\s*\{/ig,'$1\{'); 
     code = code.replace(/\{\s*(\S)/ig,'\{$1'); 
     return code; 
    } 
    function CSSdecode(code) 
    { 
     code = code.replace(/(\s){2,}/ig,'$1'); 
     code = code.replace(/(\S)\s*\{/ig,'$1 {'); 
     code = code.replace(/\*\/(.[^\}\{]*)}/ig,'\*\/\n$1}'); 
     code = code.replace(/\/\*/ig,'\n\/\*'); 
     code = code.replace(/;\s*(\S)/ig,';\n\t$1'); 
     code = code.replace(/\}\s*(\S)/ig,'\}\n$1'); 
     code = code.replace(/\n\s*\}/ig,'\n\}'); 
     code = code.replace(/\{\s*(\S)/ig,'\{\n\t$1'); 
     code = code.replace(/(\S)\s*\*\//ig,'$1\*\/'); 
     code = code.replace(/\*\/\s*([^\}\{]\S)/ig,'\*\/\n\t$1'); 
     code = code.replace(/(\S)\}/ig,'$1\n\}'); 
     code = code.replace(/(\n){2,}/ig,'\n'); 
     code = code.replace(/:/ig,':'); 
     code = code.replace(/  /ig,' '); 
     return code; 
    } 

    
	
    $(function(){
        
    	@if($result)
    	    $('#result-field').format({method: 'xml'});
        @endif
    		   
    	
    })
	</script>
	
	
</body>
</html>
