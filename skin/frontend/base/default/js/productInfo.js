var ProductInfo = Class.create();
ProductInfo.prototype = {
    settings: {
        'loadingMessage': 'Please wait ...'
    },
    
    initialize: function(selector, x_image, settings)
    {
        Object.extend(this.settings, settings);
        this.createWindow();  
        
        var that = this;
        $$(selector).each(function(el, index){
            el.observe('click', that.loadInfo.bind(that));
        })
        $$(x_image).each(function(el, index){
            el.observe('mouseover', that.showButton);
            el.observe('mouseout', that.hideButton);
        })
        
    },
    
    createLoader: function()
    {
        var loader = new Element('div', {id: 'ajax-preloader'});
        loader.innerHTML = "<p class='loading'>"+this.settings.loadingMessage+"</p>";
        document.body.appendChild(loader);
        $('ajax-preloader').setStyle({
            position: 'absolute',
            top:  document.viewport.getScrollOffsets().top + 200 + 'px',
            left:  document.body.clientWidth/2 - 75 + 'px'
        });
    },
    
    destroyLoader: function()
    {
        $('ajax-preloader').remove();
    },
    
    showButton: function(e)
    {
        el = this;
        while (el.tagName != 'P') {
            el = el.up();
        }
        $(el).getElementsBySelector('.ajax')[0].setStyle({
            display: 'block'
        })
    },
    
    hideButton: function(e)
    {
        el = this;
        while (el.tagName != 'P') {
            el = el.up();
        }
        $(el).getElementsBySelector('.ajax')[0].setStyle({
            display: 'none'
        })
    },
    
    createWindow: function()
    {
        var qBackground = new Element('div', {id: 'quick-background', class: 'hidden'});
        document.body.appendChild(qBackground);
		
        var qWindow = new Element('div', {id: 'quick-window'});
        qWindow.innerHTML = '<div id="quickview-header"><a href="javascript:void(0)" id="quickview-close">close</a></div><div class="quick-view-content"></div>';
        document.body.appendChild(qWindow);
        $('quickview-close').observe('click', this.hideWindow.bind(this)); 
    },
    
    showWindow: function()
    {
        $('quick-window').setStyle({
            top:  document.viewport.getScrollOffsets().top + 100 + 'px',
            left:  document.body.clientWidth/2 - $('quick-window').getWidth()/2 + 'px',
            display: 'block'
        });
		jQuery('#quick-background').removeClass('hidden');
		jQuery('ul.tab-group').click(function(event) {
			name = event.target.className;
			if (!/^box-/.test(name)) {// Do not click on tab button
				return;
			}
			
			// Set active box
			jQuery('.box-wrap div[id^="box-"]').each(function() {
				jQuery(this).addClass('hidden');
			});
			jQuery('#' + name).removeClass('hidden');
			
			// Set active tab button
			jQuery('a[class^="box-"]').each(function() {
				jQuery(this).removeClass('active-tab-name');
			});
			jQuery('a.' + name).addClass('active-tab-name');
		});
		
		jQuery('#submitTagForm').click(function() {
			var addTagFormJs = new VarienForm('addTagForm');
			if(addTagFormJs.validator.validate()) {
				addTagFormJs.form.submit();
			}
		});

		jQuery('#submitReview').click(function() {
			var dataForm = new VarienForm('review-form');
			Validation.addAllThese(
			[
			   ['validate-rating', 'Please select one of each of the ratings above', function(v) {
					var trs = $('product-review-table').select('tr');
					var inputs;
					var error = 1;

					for( var j=0; j < trs.length; j++ ) {
						var tr = trs[j];
						if( j > 0 ) {
							inputs = tr.select('input');

							for( i in inputs ) {
								if( inputs[i].checked == true ) {
									error = 0;
								}
							}

							if( error == 1 ) {
								return false;
							} else {
								error = 1;
							}
						}
					}
					return true;
				}]
			]
			);
		});//End of submit review
		
		jQuery('.prev-prd, .next-prd').click(function(e) {
			className = e.currentTarget.className;
			dalink = jQuery('.' + className).attr('dalink');
			if(!dalink) {
				return;
			}

			windowPi.createLoader();
			new Ajax.Request(dalink, {
				onSuccess: function(response) {
					windowPi.clearContent();
					windowPi.setContent(response.responseText);
					windowPi.destroyLoader();
					windowPi.showWindow();
				}
			}); 
		});
    },
    
    setContent: function(content)
    {
        $$('.quick-view-content')[0].insert(content);
    },
    
    clearContent: function()
    {
        $$('.quick-view-content')[0].replace('<div class="quick-view-content"></div>');
    },
    
    hideWindow: function()
    {
        this.clearContent();
        $('quick-window').hide();
		jQuery('#quick-background').addClass('hidden');
    },

    loadInfo: function(e)
    {
        e.stop();
        var that = this;
        this.createLoader();
        new Ajax.Request(e.element().href, {
            onSuccess: function(response) {
                that.clearContent();
                that.setContent(response.responseText);
                that.destroyLoader();
                that.showWindow();
            }
        }); 
    }

}

var windowPi = null;
Event.observe(window, 'load', function() {
    windowPi = new ProductInfo('.ajax', '.product-image', {
    });
});
