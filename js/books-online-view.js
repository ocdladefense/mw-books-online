(function(window,undefined){

	/**
	 * A constructor is simply a function that returns an object.
	 * 
	 * So here, we could create a function that returns:
	 *
	 * 		var prototype = extend(null,{... some instance methods here ...});
	 *  			var View = function(){ return new prototype; };
	 *  			not sure if this would set up the prototype chain correctly though.
	 */

	var drawerView = extendView({module:'drawer'},{	
		evts: ['salesOrderDetailsLoad'],
	
		evtCallbacks: {
			'salesOrderDetailsLoad': function(e){ 
				// Show some details about the new Sales Order.
			}
		},
	
		/**
		 * Add event listeners for this view
		 */
		init: function(){
			this.templates = {};
		
			this.templates['bookElement'] = function(props) {
				return el('li',{id: props.nsName, className:'book book_ns '+props.nsName},[
					el('a',{title: 'Click to expand '+props.nsNamePretty, href: '#'+props.nsName},[
						props.nsNamePretty,
						el('span',{},[props.numChapters+''])
					]),
					el('ul',{className: 'sub-menu'})
				]);
			};
		
			// <ul class="sub-menu">
			this.templates['chapterElement'] = function(props) {
				return el('li',{className:'chapter'},[
					el('a',{'title':'Open "'+props.prettyTitle+'"','data-doc-id': props.page_id, href: props.link},[props.prettyTitle])
				]);
			};	
		},
		
		getTemplate: function(tName){
			return this.templates[tName] || null;
		}
	
	});

	window.drawerView = drawerView;

})(window,undefined);