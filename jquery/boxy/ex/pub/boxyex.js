jQuery.extend(Boxy, {
	  afterLoad : Boxy.EF
	, load: function(url, options) {
		options = options || {};
		
		var boxy = new Boxy('<div style="width:250px;text-align:center;"><img src="/ext/jquery/boxy/ex/pub/loading.gif"/></div>', options);
		var ajax = {
			url: url, type: 'GET', dataType: 'html', cache: false, success: function(html) {
				html = jQuery(html);
				if (options.filter) html = jQuery(options.filter, html);
				boxy.setContent(html);
				boxy.center();
				boxy._fire('afterLoad');
			}
		};
		
		jQuery.each(['type', 'cache'], function() {
			if (this in options) {
				ajax[this] = options[this];
				delete options[this];
			}
		});
		
		jQuery.ajax(ajax);
	}
});

var BoxyWait = {
	show: function(msg) {
		this.hide();
		if (!msg) msg = 'Подождите...';
		this.wait = new Boxy('<h1>' + msg + '</h1>', { modal: true });
	}
	
	, hide : function() {
		if (this.wait) this.wait.hide();
	}
};