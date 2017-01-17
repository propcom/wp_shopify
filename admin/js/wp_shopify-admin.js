(function( $ ) {
	'use strict';

	 function Shortcodes () {

		 var self = this;

		 self.parent = $('.wp-shopify-modal');

		 self.ajaxIt = function (uri, params, ctx, populate) {

			 var props = {

				 type: 'GET',
				 url: uri,
				 dataType: 'json',
				 data: $.param(params),
				 cache: false,

				 beforeSend: function () {

					 if( ctx.find('.spinner').length <= 0 ) {

						 ctx.append('<span class="spinsym"></span>');

					 }

				 },

				 success: function (jq) {

					 if(jq.data && jq.data.length > 0) {

						 var html = [ '<ul class="wsm-products">' ];

 						 for(var product in jq.data) {

 							 html.push('<li><a href="javascript:void(0);" data-id="' + jq.data[product].id + '">' + jq.data[product].title + ' - <span>Price: ' + jq.data[product].variants[0].price + '</span></a></li>');

 						 }

 						 populate.append(html.join(''));

						 html.push('</ul>');

 						 if( ctx.find('.spinsym').length > 0 ) ctx.find('.spinsym').remove();

					 }

				 },

				 error: function (error) {

					 if( ctx.find('.spinsym').length > 0 ) ctx.find('.spinsym').remove();

					 if(error.message) {

						 alert(error.message);
						 console.error(error.message);

					 }

				 }

			 };

			 $.ajax(props);

		 };

		 self.loadProducts = function () {

			 var value = $(this).data('id');
			 var populate = $(this).parents('.wsm-collection');

			 if( !$(this).hasClass('active') ) {

				 $(this).addClass('active');
				 self.ajaxIt( window.endpoint, { id: value }, populate, populate );

			 } else {

				 $(this).removeClass('active');
				 populate.find('.wsm-products').remove();

			 }

		 };

		 self.onChosenProduct = function () {

			 var id = $(this).data('id');

			 if(wp.media.editor) {

				 wp.media.editor.insert('[product id="' + id + '"]');

			 }

		 };

		 self.init = function () {

			 self.parent.find('.wsm-collection a').on('click', self.loadProducts);
			 $(document).on('click', '.wsm-products a', self.onChosenProduct);

		 };

	 }

	 $(window).load( function () {

		 new Shortcodes().init();

	 } );

})( jQuery );
