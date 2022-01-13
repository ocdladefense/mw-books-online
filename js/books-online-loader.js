
/**
* Initialize the documents drawer
* 
* 
*/
AppCore.registerApp('drawer');
AppCore.registerView('drawer',drawerView);

AppCore.onAppReady('drawer',function(view,controller,data){
	$fetch = jQuery.ajax({url:'/wikinamespace.php',dataType:'json'})
	.done(function(nsData){
	
		nsData.forEach(function(nsObj) {
			var book = view.getTemplate('bookElement')(nsObj);
			var bNode = elementRender(book);
			var cHook = bNode.children[bNode.children.length-1];
			
			if(nsObj.nsPages.length>0) {
				var container = document.createElement('ul');
				container.setAttribute('class','sub-menu');
				nsObj.nsPages.forEach(function(p){
					var chapter = view.getTemplate('chapterElement')(p);
					var chapterNode = elementRender(chapter);
					cHook.appendChild(chapterNode);
				});				
			}
			
			jQuery('#drawer-contents').append(bNode);
		});
	
		/*
		jQuery('#drawer-contents').accordion({
			header:"li.book",
			collapsible:true,
			active:false,
			heightStyle:"content",
			autoHeight:false
		});
		*/
	});
});


/*
jQuery((function(view) {
	return function(){
		var index = Indexer.initIndex();
	};
	
})(drawerView));
*/


/*
AppCore.onAppReady('cache',function(view,controller,data){
	applicationCache.addEventListener("error", function(event) {
		if (navigator.onLine == true) { //If the user is connected to the internet.
				console.log("Error - Please contact the website administrator if this problem consists.");
		} else {
				console.log("You aren't connected to the internet. Some things might not be available.");
		}
	}, false);

	applicationCache.addEventListener("downloading", function(event) {
			console.log("Started Download.");
	}, false);

	applicationCache.addEventListener("progress", function(event) {
		console.log(event);
			//console.log(event.loaded + " of " + event.total + " downloaded."); doesn't work
	}, false);

});
*/



/*
AppCore.onAppReady('drawer',function(view,controller,data){
	var drawer = document.getElementById('drawer');
	drawer.addEventListener('click',function(e){		
		var docId, chapter;
		console.log('selected a drawer option...');
		docId = e.target.dataset.docId;
		if(docId=='search-form') {
			BooksOnline.showSearchForm();
			e.stopPropagation();
			e.preventDefault();
			return false;
		}
	
		e.stopPropagation();
		e.preventDefault();
	
		chapter = BooksOnline.getChapter(docId);
		if(chapter){
			BooksOnline.showBookContent(chapter);
			jQuery('#drawer-toggle-label').click();
		} else {
			$fetch = jQuery.ajax({url:'/wikipage.php',data:{'id':docId},dataType:'json'})
			.done(function(chapter){
				// console.log(chapter);
				BooksOnline.storeBookContent(chapter);
				BooksOnline.showBookContent(chapter);
				jQuery('#drawer-toggle-label').click();
			});
		}
		// $fetch.error(function(){			var theText = getBook(docId)['text'];}

		$indexer = jQuery.ajax({url:'/wikisections.php',data:{'id':docId},dataType:'json'})
		.done(function(mwSections){
			var index, results;
			try {
				// Add each document to the localStorage
				mwSections.forEach(function(doc){ BooksOnline.storeSectionContent(doc); });
	
				// Add each document to the Index
				index = Indexer.getIndex();
				mwSections.forEach(function(doc){ index.addDocument(doc); });
			} catch(e){
				alert(e);
			}
		});

		return false;

		},false);
});
*/