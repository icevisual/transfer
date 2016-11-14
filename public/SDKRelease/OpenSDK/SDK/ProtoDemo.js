define([ 'protocolStruct' ], function(protocolStruct) {

	var Simple = protocolStruct;
	// 每周1-5 中午 11:30 - 12:30 和 17:30 - 18:30-- === ## (9s) 循环
	var sData = new Simple.PlaySmell({
		'cycleMode' : Simple.SrCycleMode.SCM_cycle_yes,
		'startAt' : [ {
			'mode' : Simple.SrTimeMode.STM_weekday,
			'value' : 1,
			'endValue' : 5
		}, {
			'mode' : Simple.SrTimeMode.STM_daytime,
			'value' : 41400,
			'endValue' : 45000
		}, {
			'mode' : Simple.SrTimeMode.STM_daytime,
			'value' : 63000,
			'endValue' : 66600
		} ],
		'cycleTime' : 0,
		'actions' : [ {
			'bottle' : '0000000001',
			'duration' : 2,
			'power' : 5
		}, {
			'bottle' : '0000000002',
			'duration' : 3,
			'power' : 7
		}, {
			'bottle' : '0000000003',
			'duration' : 2,
			'power' : 5
		}, {
			'bottle' : '0000000004',
			'duration' : 2,
			'power' : 0
		} ],
		'trace' : [ {
			'actionId' : [ 0, 3, 1, 4, 2 ],
			'beforeStart' : 0,
			'cycleMode' : Simple.SrCycleMode.SCM_cycle_yes,
			'interval' : 0,
			'cycleTime' : 278
		} ],
	});
//	console.log(sData);
	// Logger.debug('sData', sData);
	
//	console.log(typeof sData,sData instanceof Simple.PlaySmell);
	
	return sData;
});
