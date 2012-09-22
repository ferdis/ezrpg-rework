jQuery.run = function(runlevel, callback) {
	
	$ = this;
	
	// prase the calback string and grab the "actual" callback.
	if (typeof callback !== 'function') {
		if (callback.indexOf('.') !== -1) {
			var parts = callback.split('.');
			callback = eval(parts[0]+'.'+parts[1]);
		} else {
			callback = callback;
		}
	}
	
	switch(runlevel) {
		
		case 'load' :
			$(document).load(callback);
			break;
		
		case 'ready' :
		default :
			$(document).ready(callback);
			break;
	}
	
	return true;

};