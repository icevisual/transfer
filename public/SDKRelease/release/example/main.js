require.config({
	paths : {
		'SDK' : '../dist/OpenSDK-v1.0.0.min',
		'jquery' : '../dist/jquery-1.9.1.min',
	},
});

require([ 'SDK','jquery'], function(SDK,$) {
	SDK.connect({
		'accessKey' : 'testIAzDhpyc0z9yGFajKp2P',
		'accessSecret' : 'HNKGRV2O2oeK7W2jtmFC',
		'logLevel' : 'info',
	});
	var tmp = '<tr><td>气味{$sid}</td><td><input type="text" name="t" value="5"/></td>' + 
	    '<td><button data-sid="{$sid}" class="play-btn">播放</button></td>' + 
	    '<td><div class="process-{$sid}"><div>&nbsp;</div></div></td>' +
	    '</tr>';
	var _mainTable = $('.the-table');
	for(var i = 1 ; i <= 10 ; i ++ ){
		_mainTable.append(tmp.split('{$sid}').join(i));
	}
	
	var dev,isConnected = false;
	var _deviceIDInput = $('input[name=deviceID]');
	$('.connect').click(function(){
		
		var did = _deviceIDInput.val();
		if(!did){
			return _deviceIDInput.focus();
		}
		dev = SDK.usingDevice(did);
		
		_deviceIDInput.attr('disabled','disabled');
		$('.connect').addClass('hide');
		$('.connecting').removeClass('hide');
		
		dev.wakeup(function(){
			// connect success
			isConnected = true;
			$('.disconnect').removeClass('hide');
			$('.connecting').addClass('hide');
			alert('设备连接成功');
		},function(){
			// connect failed
			_deviceIDInput.removeAttr('disabled');
			$('.connect').removeClass('hide');
			$('.connecting').addClass('hide');
			alert('设备连接失败');
		});
	});
	
	$('.disconnect').click(function(){
		if(false === isConnected){
			return alert('设备尚未连接');
		}
		dev.disconnect();
		isConnected = false;
		_deviceIDInput.removeAttr('disabled');
		$('.disconnect').addClass('hide');
		$('.connect').removeClass('hide');
		$('.connecting').addClass('hide');
	});
	
	$('.play-btn').click(function(){
		var sid = $(this).data('sid');
		var _playT = $('input[name=t]').eq(sid - 1);
		var pt = _playT.val();
		var _thisBtn = $(this);
		if(false === isConnected){
			return alert('设备尚未连接');
		}
		
		if(!pt){
			return _playT.focus();
		}
		
		var Simple = SDK.protoRoot;
			
		dev.playSmell({
			'actions' : [ {
				'bottle' : sid + '',
				'duration' : parseInt(pt),
				'power' : 5
			}]
		},function(){
			var length = 0;
			var processSpan = $('.process-' + sid);
			var maxWidth = 200;
			var step = maxWidth / pt;
			_thisBtn.attr('disabled','disabled');
			processSpan.css({
				'width' : maxWidth + 'px',
				'border' : '#ccc solid 1px',
			});  
			
			processSpan.find('div').css({
				'width' : '0px',
				'background' : '#ccc',
			});    
			var timer = setInterval(function(){
				processSpan.find('div').css({'width' : (step *  ++ length ) + 'px'});
				if(length == pt) {
					processSpan.find('div').css({
						'width' : maxWidth + 'px',
						'background' : '#00EE00',
					});
					_thisBtn.removeAttr('disabled');
					clearInterval(timer);
				}
			},1000);
//			alert('play success');
		},function(){
			alert('play failed');
		});
	});
	
})
