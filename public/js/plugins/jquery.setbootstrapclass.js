/**
 * jQuery setBootstrapClass plugin
 *
 * Remove element classes with wildcard matching. Optionally add classes:
 *   $( '#foo' ).setBootstrapClass( 'btn', 'success' )
 *
 */
(function ( $ ) {
	
$.fn.setBootstrapClass = function ( type, style ) {
	
	var self = this;	
	var methods = {
		getClassName: function(type, style) {
			return type+"-"+style;
		}	
	};
	
	var styleNames = ["danger", "warning", "success"];
	
	var setClassName = false;
	$.each(styleNames, function(index, styleName) {
		var className = methods.getClassName(type, styleName);
		if (style == styleName) {
			if (!self.hasClass(className)) {
				self.addClass(className);
				setClassName = className;
			}
		} else {
			if (self.hasClass(className))
				self.removeClass(className);
		}
	});

	return setClassName;
};

})( jQuery );