;(function($) {
	$.fn.snowing = function(param) {
		return this.each(function() {
			var ojjektum = $.extend({
							direction : 0,	// flakes movement direction 0: random; negative(x) # = left #, positive(x) # = right # on each timeout
							yvelocity: 1,	// on each timeout flakes movement # pixel on y axix down
							xvelocity: 2,  // on each timeout flakes movement # pixel on x axis based on direction
							density: 10,	// timeout*density = new flake, for example density = 10 timeout = 10 -> every 100ms a new flake will appear, exept when one flake reach the bottom of the document, in this case this counter will be decreased by 5
							timeout: 150,	// flake movement and new flake appearing will happen # ms, exept....
							maxflake : 10	// maximum flakes number on the screen
						},param || {});
			var ez = this;	// current element in the wrapped set
			var snowflakes = new Array("img/snow/flake1.png","img/snow/flake2.png","img/snow/flake3.png");	//flakes images, numbers and images can be vary
			var flakes = new Array();	// array containing the flakes on the screen
			var kovi = 0;	//	next flake counter, ld. density and timeout and bottom reached flake
			
			/**
				add new flake to the screen
			*/
			var addNewFlake = function() {
				var div = $("<div></div>")
							.css("top",0)
							.css("left",(40+Math.round(Math.random()*($(ez).width()-75))))
							.addClass("snowflake");;
				var img = $("<img></img>");
				$(img).attr("src",snowflakes[Math.round(Math.random()*2)])

				$(div).append(img);
				
				flakes[flakes.length] = div;
				$(ez).append(div);
			}
			
			/**
				move existing flakes on the screen; ld 
			*/
			var moveFlakes = function() {
				//$("#x").text(flakes.length);
				var docy = $(ez).height();	// document height
				var docx = $(ez).width();	// document widht
				for (var i = 0; i < flakes.length; i++) {	// traverse on the flake array to move them
					var x = window.parseInt( $(flakes[i]).css("left") );
					var y = window.parseInt( $(flakes[i]).css("top") );
					if ((y + ojjektum.yvelocity) < (docy-50)) {
						if (ojjektum.direction == 0) {	//refere ojjektum.direction
							if (Math.round(Math.random()) == 1) {
								direction = ojjektum.xvelocity;
							} else {
								direction = -1*ojjektum.xvelocity;
							}
						} else {
							direction = ojjektum.direction;
						}
						
/**to avoid unvisible vertical */if ((x+direction)-30 < docx && (x+direction) > 15) { $(flakes[i]).css("left",(x+direction)); }
/*scrollbar appearing*/		$(flakes[i]).css("top",(y+ojjektum.yvelocity));
					} else {	//in case of one of the flakes has reached the bottom of the document
						$(flakes[i]).css("top",0);
						kovi = kovi -5;
					}
				}
				
				if (++kovi == ojjektum.density && ojjektum.maxflake > flakes.length) {	//add new flake until the ojjektum.maxflake number is not reached
					addNewFlake();
					kovi = 0;	// if a flake reached the bottom then to avoid thicken flakes, decrease a little bit (maybe useless)
				}
			}
			
			addNewFlake();	// Columbus-egg-hen case
			var i = window.setInterval(function() { moveFlakes() },ojjektum.timeout);
		});
	}
})(jQuery)