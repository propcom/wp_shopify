(function( $ ) {
	'use strict';

	 function Shortcodes () {

		 var self = this;

		 self.parent = $('.shortcodes');

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

						 var html = [ '<option value="">Select Product</option>' ];

 						 for(var product in jq.data) {

 							 html.push('<option value="' + jq.data[product].id + '">' + jq.data[product].title + '</option>');

 						 }

 						 populate.find('select').html(html.join(''));
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

		 self.onCollection = function () {

			 var value = $(this).val();
			 var parent = $(this).parents('.collection');
			 var populate = self.parent.find('.products');
			 var shortcode = self.parent.find('.js-shortcode-text');

			 if(shortcode.length > 0) {

				 shortcode.val( '[products id="' + value + '"]' );

			 }

			 if(window.endpoint) {

				 var params = { id: value };
				 var callback = function () {};

				 self.ajaxIt( window.endpoint, params, parent, populate );

			 }

		 };

		 self.onProduct = function () {

			 var value = $(this).val();
			 var shortcode = self.parent.find('.js-shortcode-text');

			 if(shortcode.length > 0) {

				 shortcode.val( '[product id="' + value + '"]' );

			 }

		 };

		 self.doCopy = function () {

			 var shortcode = self.parent.find('.js-shortcode-text');

			 if(shortcode.length > 0) {

				 shortcode.focus();
				 shortcode.select();

				 var support = document.execCommand('copy');

				 if(support) {

					 self.addNotice('Coppied');

				 } else {

					 alert('Cannot copy, please copy manually Ctrl + c');

				 }

			 }

		 };

		 self.addNotice = function (text) {

			 var html = [];

			 if(self.parent.length > 0) {

				 html.push('<div class="notice notice-success is-dismissible"><p>' + text + '</p></div>');

				 if(self.parent.next('.notice').length <= 0) {

					 self.parent.after(html.join(''));

				 }

			 }

		 };

		 self.init = function () {

			 self.parent.find('.js-copy-shortcode').on('click', self.doCopy);
			 self.parent.find('.js-product-select').on('change', self.onProduct);
			 self.parent.find('.js-collection-select').on('change', self.onCollection);

		 };

	 }

	 $(window).load( function () {

		 new Shortcodes().init();

	 } );

})( jQuery );
