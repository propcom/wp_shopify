(function( $ ) {
	'use strict';

	 function Shortcodes () {

		 var self = this;

		 self.parent = $('.wp-shopify-modal');

		 window.currentPage = 1;

		 self.populateProducts = function (jq, ctx, populate) {

			 if(jq.data && jq.data.length > 0) {

				 var html = [ '<ul class="wsm-products">' ];

				 for(var product in jq.data) {

					 html.push('<li><a href="javascript:void(0);" data-id="' + jq.data[product].id + '">' + jq.data[product].title + ' - <span>Price: ' + jq.data[product].variants[0].price + '</span></a></li>');

				 }

				 html.push('</ul>');
				 populate.append(html.join(''));

				 if( ctx.find('.spinsym').length > 0 ) ctx.find('.spinsym').remove();

			 }
		 };

		 self.ajaxIt = function (uri, params, ctx, populate, callback) {

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

					 if(typeof callback === 'function') {
						 callback.call(this, jq, ctx, populate);
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
				 self.ajaxIt( window.endpoint, { id: value }, populate, populate, self.populateProducts );

			 } else {

				 $(this).removeClass('active');
				 populate.find('.wsm-products').remove();

			 }

		 };

		 self.loadMore = function () {

			 var populate = $(this).parents('.wp-shopify-modal');

			 window.currentPage += 1;

			 self.ajaxIt( window.endpoint, { p: window.currentPage }, populate, populate );
		 };

		 self.onChosenProduct = function () {

			 var id = $(this).data('id');

			 if(wp.media.editor) {

				 wp.media.editor.insert('[product id="' + id + '"]');

			 }

		 };

		 self.init = function () {

			 self.parent.find('.wsm-collection a').on('click', self.loadProducts);
			 self.parent.find('.js-load-more').on('click', self.loadMore);

			 $(document).on('click', '.wsm-products a', self.onChosenProduct);

		 };

	 }

	 $(window).load( function () {

		 new Shortcodes().init();

	 } );

})( jQuery );
