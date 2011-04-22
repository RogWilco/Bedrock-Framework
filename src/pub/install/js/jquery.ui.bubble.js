/**
 * jQuery UI Bubble
 *
 * @package jQuery.ui
 * @author Nick Williams
 * @version 1.0.0
 * @created 12/08/2008
 * @updated 12/08/2008
 */
(function($) {
	var bubble = undefined;
	var container = undefined;
	
	/**
	 * Bubble UI Widget
	 */
	$.widget('ui.bubble', {
		/**
		 * Initializes a bubble widget.
		 */
		init: function () {
			// Attach Bubble to Parent Element
			this.bubble = $(this.markup.wrapper);
			$('body').prepend(this.bubble);
			//this.element.parent().prepend(this.bubble);
			
			this.bubble.append(this.markup.top);
			this.bubble.append(this.markup.container);
			this.bubble.append(this.markup.bottom);
			
			//this.options.width = undefined;
			//this.options.height = undefined;
			//this.options.width = this.bubble.find('.text').eq(0).outerWidth();
			//this.options.height = this.bubble.find('.text').eq(0).outerHeight();
			
			// Determine: Bubble Direction
			switch(this.options.direction) {
				case 'n':
					this.options.xpos = this.options.xpos !== undefined ? this.options.xpos : this.element.position().left + (this.element.outerWidth()/2);
					this.options.ypos = this.options.ypos !== undefined ? this.options.ypos : this.element.position().top;
					
					this.options.xpos -= this.options.width/2;
					this.options.ypos -= this.options.height;
					
					this.bubble.addClass('ui-bubble-point-s');
					break;
					
				case 'ne':
					this.options.xpos = this.options.xpos !== undefined ? this.options.xpos : this.element.position().left + this.element.outerWidth();
					this.options.ypos = this.options.ypos !== undefined ? this.options.ypos : this.element.position().top;
					
					this.options.ypos -= this.options.height;
					
					this.bubble.addClass('ui-bubble-point-sw');
					break;
					
				case 'e':
					this.options.xpos = this.options.xpos !== undefined ? this.options.xpos : this.element.position().left + this.element.outerWidth();
					this.options.ypos = this.options.ypos !== undefined ? this.options.ypos : this.element.position().top + (this.element.outerHeight()/2);
					
					this.options.ypos -= this.options.height/2;
					
					this.bubble.addClass('ui-bubble-point-w');
					break;
					
				case 'se':
					this.options.xpos = this.options.xpos !== undefined ? this.options.xpos : this.element.position().left + (this.element.outerWidth());
					this.options.ypos = this.options.ypos !== undefined ? this.options.ypos : this.element.position().top + this.element.outerHeight();
					
					this.bubble.addClass('ui-bubble-point-nw');
					break;
					
				case 's':
					this.options.xpos = this.options.xpos !== undefined ? this.options.xpos : this.element.position().left + (this.element.outerWidth()/2);
					this.options.ypos = this.options.ypos !== undefined ? this.options.ypos : this.element.position().top + this.element.outerHeight();
					
					this.options.xpos -= this.options.width/2;
					
					this.bubble.addClass('ui-bubble-point-n');
					break;
					
				case 'sw':
					this.options.xpos = this.options.xpos !== undefined ? this.options.xpos : this.element.position().left;
					this.options.ypos = this.options.ypos !== undefined ? this.options.ypos : this.element.position().top + this.element.outerHeight();
					
					this.options.xpos -= this.options.width;
					
					this.bubble.addClass('ui-bubble-point-ne');
					break;
					
				default:
				case 'w':
					this.options.xpos = this.options.xpos !== undefined ? this.options.xpos : this.element.position().left;
					this.options.ypos = this.options.ypos !== undefined ? this.options.ypos : this.element.position().top + (this.element.outerHeight()/2);
					
					this.options.xpos -= this.options.width;
					this.options.ypos -= this.options.height/2;
					
					this.bubble.addClass('ui-bubble-point-e');
					break;
					
				case 'nw':
					this.options.xpos = this.options.xpos !== undefined ? this.options.xpos : this.element.position().left;
					this.options.ypos = this.options.ypos !== undefined ? this.options.ypos : this.element.position().top;
					
					this.options.xpos -= this.options.width;
					this.options.ypos -= this.options.height;
					
					this.bubble.addClass('ui-bubble-point-se');
					break;
			}
			
			// Determine: Color
			switch(this.options.type) {
				default:
				case 'standard':
					// Do Nothing
					break;
					
				case 'success':
					this.bubble.addClass('ui-bubble-green');
					break;
					
				case 'warn':
					this.bubble.addClass('ui-bubble-yellow');
					break;
					
				case 'error':
					this.bubble.addClass('ui-bubble-red');
					break;
			}
			
			// Determine: Special Sizes
			if(this.options.height < 120) {
				this.bubble.find('.ui-bubble-nw, .ui-bubble-ne, .ui-bubble-sw, .ui-bubble-se, .ui-bubble-top, .ui-bubble-bottom').addClass('ui-bubble-short');
			}
			
			if(this.options.height < 100) {
				this.bubble.find('.ui-bubble-top, .ui-bubble-bottom, .ui-bubble-w .ui-bubble-mid-x, .ui-bubble-e .ui-bubble-mid-x').addClass('ui-bubble-shorter');
			}
			
			// Apply CSS Properties
			this.bubble.css({
				width: this.options.width,
				height: this.options.height,
				left: this.options.xpos,
				top: this.options.ypos
			});
			
			this.bubble.find('.ui-bubble-message').css({
				width: this.options.width - 60,
				height: this.options.height -60
			});
			
			// Add Message
			this.bubble.find('.ui-bubble-text').append(this.options.msg);
			
			// IE Fixes
			if($.browser.msie) {
				$('.ui-bubble .ui-bubble-n .ui-bubble-mid-x').css({ marginTop: '-40px' });
				$('.ui-bubble .ui-bubble-e').css({ marginRight: '-40px' });
				$('.ui-bubble .ui-bubble-s .ui-bubble-mid-x').css({ marginTop: '-40px' });
				$('body').css({ paddingTop: '80px' });
			}
			
			if(this.options.show == 'now') {
				this.show();
			}
		},
		
		/**
		 * Displays the bubble widget.
		 */
		show: function() {
			if(this.bubble.css('display') == 'none') {
				if($.browser.msie) {
					this.bubble.show();
				}
				else {
					this.bubble.show('puff');
				}
			}
		},
		
		/**
		 * Hides the bubble widget.
		 */
		hide: function() {
			if(this.bubble.css('display') == 'block') {
				if($.browser.msie) {
					this.bubble.hide();
				}
				else {
					this.bubble.hide('puff');
				}
			}
		},
		
		/**
		 * Relevant HTML markup for a bubble element.
		 */
		markup: {
			wrapper: '<div class="ui-bubble"></div>',
			container: '<div class="ui-bubble-mid"><div class="ui-bubble-message"><div class="ui-bubble-text"></div></div></div>',
			top: '<div class="ui-bubble-nw"></div><div class="ui-bubble-n"><div class="ui-bubble-left-wrapper"><div class="ui-bubble-left"></div></div><div class="ui-bubble-right-wrapper"><div class="ui-bubble-right"></div></div><div class="ui-bubble-mid-x"></div></div><div class="ui-bubble-ne"></div><div class="ui-bubble-w"><div class="ui-bubble-top-wrapper"><div class="ui-bubble-top"></div></div><div class="ui-bubble-bottom-wrapper"><div class="ui-bubble-bottom"></div></div><div class="ui-bubble-mid-x"></div></div><div class="ui-bubble-e"><div class="ui-bubble-top-wrapper"><div class="ui-bubble-top"></div></div><div class="ui-bubble-bottom-wrapper"><div class="ui-bubble-bottom"></div></div><div class="ui-bubble-mid-x"></div></div><div class="ui-bubble-sw"></div><div class="ui-bubble-s"><div class="ui-bubble-left-wrapper"><div class="ui-bubble-left"></div></div><div class="ui-bubble-right-wrapper"><div class="ui-bubble-right"></div></div><div class="ui-bubble-mid-x"></div></div><div class="ui-bubble-se"></div>',
			bottom: ''
		}
	});
	
	/**
	 * Default Options and Animations
	 */
	$.extend($.ui.bubble, {
		defaults: {
			type: 'standard',
			direction: 'n',
			animation: 'puff',
			msg: '',
			width: 120,
			height: 120,
			xpos: undefined,
			ypos: undefined,
			show: 'now',
			hide: 'click'
		},
		animations: {
			puff: function(e, options) {
				// Implement puff animation.
			},
			fade: function(e, options) {
				// Implement fade animation.
			},
			slide: function(e, options) {
				// Implement slide animation.
			}
		}
	});
})(jQuery);