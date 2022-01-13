var BooksOnline = (function(window,$){
			
			var showBookContent = function(chapter,section){
				section = section || {anchor:'top'};
				$('#content').html('<a id="top" />'+chapter.text);
				if(section){
					location.hash = "#" + section.anchor;
				}
			};
			
			var loadContent = function(html,anchor){
				anchor = anchor || 'top';
				$('#content').html('<a id="top" />'+html);
				location.hash = "#" + anchor;
			};
			
			var showSearchForm = function(form){
				form = form || '<a id="top" /><h2>Books Online Search</h2><form onsubmit="return BooksOnline.doSearch(this);" id="books-offline-search-form"><div><input type="text" size="30" name="search-terms" value="" placeholder="Enter search terms here..." /><input type="submit" value="search" /></div></form>';
				form += '<div id="books-offline-search-results">&nbsp;</div>';
				loadContent(form);
				var search = document.getElementById('books-offline-search-results');
				search.addEventListener('click',function(e){
				var docId, section, chapter;
					if(!e.target.dataset.docId) return true;
					docId = e.target.dataset.docId;
					e.preventDefault();
					e.stopPropagation();
					section = BooksOnline.getSectionContent(docId);
					console.log('related section for search result is: ');
					console.log(section);
					chapter = BooksOnline.getChapter(docId.split('-')[0]);
					BooksOnline.showBookContent(chapter,section);
				},false);
				jQuery('#drawer-toggle-label').click();
			};
			
			var doSearch = function(form){
				var index,terms,results;
				terms = form.elements['search-terms'].value || 'boat';

					index = BooksOnline.getIndex();
					console.log(index);
					console.log('Will search for: '+terms);
					results = index.search(terms);
					if(typeof results[0] === 'object') {
						BooksOnline.displaySearchResults(BooksOnline.getSectionContent(index.getDocIds(results)),terms);
					} else {
						BooksOnline.displaySearchResultsNotFound(terms);					
					}

	//				console.log(e);
//				}
				return false;
			};
			
			var getChapter = function(docId){
				return JSON.parse(localStorage.getItem(docId));
			};
			
			var storeBookContent = function(chapter){
				localStorage.setItem(chapter.id,JSON.stringify(chapter));
			};
			
			var storeSectionContent = function(section){
				localStorage.setItem(section.docId,JSON.stringify(section));
			};
			
			var getSectionContent = function(docId){
				if(!Array.isArray(docId)) docId = [docId];
				var results = docId.map(function(docId){ return JSON.parse(localStorage.getItem(docId)); });
				return results.length>1 ? results : results[0];
			};
			
			var formatSearchResult = function(result,terms){
				return '<div class="offline-search-result"><h3><a data-doc-id="'+result.docId+'" href="/'+result.parentTitle+'#'+result.anchor+'">'+result.parentTitle+'</a></h3><h5>'+result.title+'</h5><p class="search-result-snippet">'+highlight(result.content,terms)+'</p></div>';
			};
			var i = 0;
			var highlight = function(content,terms){
				try {
					var r = 'a'+(i++);
					var f = document.createDocumentFragment();
					var p = document.createElement('p');
					p.setAttribute('id',r);
					p.appendChild(document.createTextNode(content));
					f.appendChild(p);
					var t = jQuery.parseHTML('<p id="'+r+'">'+content+'</p>');
					// var elem = document.getElementById(r);
				var instance = new Mark(f.firstChild,{debug:true});
				instance.mark(terms);
				
//				console.log(f.firstChild.innerHTML);
				return f.firstChild.innerHTML.substring(0,250);
				} catch(e) {
					console.log(e);
				}
				return 'error...';
			};
			
			var displaySearchResults = function(results,terms){
				var fn, display;
				try {
					if(!results) {
						joined = '<h3>Your search for <em>'+terms+'</em> didn\'t return any results.</h3>';
					} else {
						results = Array.isArray(results) ? results : [results];
						fn = function(r){ return formatSearchResult(r,terms);};
						display = results.map(fn).join('');
					}
				} catch(e) {
					display = '<h3>There was an error searching for <em>'+terms+'</em><p class="error">'+e+'</p></h3>';
				}
				$('#books-offline-search-results').html(display);
			};
			
			var displaySearchResultsNotFound = function(terms) {
				var display;
				display = '<h3>Your search for <em>'+terms+'</em> didn\'t return any results.</h3>';
				$('#books-offline-search-results').html(display);
			};
			
			return {
				showBookContent: showBookContent,
				getChapter: getChapter,
				storeBookContent: storeBookContent,
				storeSectionContent: storeSectionContent,
				getSectionContent: getSectionContent,
				displaySearchResults: displaySearchResults,
				displaySearchResultsNotFound: displaySearchResultsNotFound,
				showSearchForm: showSearchForm,
				doSearch: doSearch
			};
})(window,jQuery);