define([ 'protocolStruct' ], function(protocolStruct) {

	var Simple = protocolStruct;
	// 每周1-5 中午 11:30 - 12:30 和 17:30 - 18:30-- === ## (9s) 循环
	var sData = new Simple.PlayRequest({
		'cycle_mode' : Simple.SrCycleMode.SCM_CYCLE_YES,
		'start_at' : [ {
			'mode' : Simple.SrTimeMode.STM_WEEKDAY,
			'value' : 1,
			'end_value' : 5
		}, {
			'mode' : Simple.SrTimeMode.STM_DAYTIME,
			'value' : 41400,
			'end_value' : 45000
		}, {
			'mode' : Simple.SrTimeMode.STM_DAYTIME,
			'value' : 63000,
			'end_value' : 66600
		} ],
		'cycle_time' : 0,
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
			'action_id' : [ 0, 3, 1, 4, 2 ],
			'before_start' : 0,
			'cycle_mode' : Simple.SrCycleMode.SCM_CYCLE_YES,
			'interval' : 0,
			'cycle_time' : 278
		} ],
	});
//	console.log(sData);
	// Logger.debug('sData', sData);
	
//	console.log(typeof sData,sData instanceof Simple.PlaySmell);
	
	return sData;
});
