(function( $ ) {
	'use strict';

	 function Shortcodes () {

		 var self = this;

		 self.parent = $('.wp-shopify-modal');
		 self.loading = false;
		 self.paginate = 50;

		 window.currentProductPage = 1;
		 window.currentCollectionPage = 1;

		 window.collectionType = 'custom';

		 self.populateProducts = function (jq, ctx, populate) {

			 var parent = (populate.attr('class') === 'wsm-products' ? populate.parents('.wsm-collection') : populate);

			 if(jq.data && jq.data.length > 0) {

				 var html = (populate.attr('class') === 'wsm-products' ? [] : [ '<ul class="wsm-products">' ]);

				 for(var product in jq.data) {

					 html.push('<li><a href="javascript:void(0);" data-id="' + jq.data[product].id + '">' + jq.data[product].title + ' - <span>Price: ' + jq.data[product].variants[0].price + '</span></a></li>');

				 }

				 if(populate.attr('class') !== 'wsm-products') html.push('</ul>');

				 populate.append(html.join(''));

				 if(parent.find('.load-more-wrapper').length === 0 && jq.data.length === self.paginate) {
					 parent.append('<div class="load-more-wrapper"><button class="button  button--more  js-load-more" name="More Collections" data-type="product">More Products</button></div>');
				 }

				 ctx.find('.js-load-products').addClass('active');

				 if( ctx.find('.spinsym').length > 0 ) ctx.find('.spinsym').remove();

			 }
		 };

		 self.populateCollections = function (jq, ctx, populate) {

			 if(jq.data && jq.data.length > 0) {

				 var html = [];

				 for(var collection in jq.data) {

					 html.push(
						 '<div class="wsm-collection">'
						 + '<a class="js-load-products" href="javascript:void(0);" data-id="' + jq.data[collection].id + '">'
						 +  '<div class="title">'
						 +	 '<h3>' + jq.data[collection].title + '</h3>'
						 +  '</div>'
						 + '</a>' +
						 '</div>'
					 );
				 }

				 populate.find('.wsm-wrapper').append(html.join(''));

				 if( ctx.find('.spinsym').length > 0 ) ctx.find('.spinsym').remove();

			 } else {

				 self.parent.find('.load-more-wrapper').addClass('hide');
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

					 self.loading = true;

					 if( ctx.find('.spinner').length <= 0 ) {

						 ctx.append('<span class="spinsym"></span>');
					 }
				 },

				 success: function (jq) {

					 if(typeof callback === 'function') {
						 callback.call(this, jq, ctx, populate);
					 }

					 self.loading = false;
				 },

				 error: function (error) {

					 if( ctx.find('.spinsym').length > 0 ) ctx.find('.spinsym').remove();

					 if(error.message) {

						 alert(error.message);
						 console.error(error.message);

					 }

					 self.loading = false;
				 }

			 };

			 $.ajax(props);

		 };

		 self.loadProducts = function () {
			 if(self.loading) return;

			 var value = $(this).data('id');
			 var populate = $(this).parents('.wsm-collection');

			 window.currentProductPage = 1;

			 if( !$(this).hasClass('active') ) {

				 self.ajaxIt( window.endpoint, { p: window.currentProductPage, id: value }, populate, populate, self.populateProducts );
			 }

			 self.parent.find('.js-load-products').removeClass('active');

			 self.parent.find('.wsm-products').remove();
			 self.parent.find('.wsm-collection .load-more-wrapper').remove();
		 };

		 self.loadMore = function () {
			 if(self.loading) return;

			 var page = 1,
			 		 data = {},
					 callback = null,
			 		 restEndpoint = null,
					 parent = self.parent,
			 		 wrapper = $(this).parents('.load-more-wrapper'),
					 collectionType = $('.js-collection-type');

			 if($(this).attr('data-type') === 'product') {

				 restEndpoint = window.endpoint;
				 window.currentProductPage += 1;

				 page = window.currentProductPage;
				 data['id'] = $(this).parents('.wsm-collection').find('.js-load-products').attr('data-id');

				 parent = $(this).parents('.wsm-collection').find('.wsm-products');
				 callback = self.populateProducts;

			 } else if($(this).attr('data-type') === 'collection') {

				 restEndpoint = window.endpointCollections;
				 window.currentCollectionPage += 1;

				 page = window.currentCollectionPage;
				 data['type'] = window.collectionType;

				 callback = self.populateCollections;

			 }

			 data['p'] = page;

			 self.ajaxIt( restEndpoint, data, wrapper, parent, callback );
		 };

		 self.onCollectionTypeChange = function () {
			 if(self.loading) return;

			 window.currentCollectionPage = 1;
			 window.collectionType = $(this).val();

			 $('.wsm-wrapper').html('');
			 self.parent.find('.load-more-wrapper').removeClass('hide');

			 var ctx = self.parent.find('.collection-types'),
					 collectionType = $('.js-collection-type');

			 self.ajaxIt( window.endpointCollections, { p: window.currentCollectionPage, type: window.collectionType }, ctx, self.parent, self.populateCollections );
		 };

		 self.onChosenProduct = function () {

			 var id = $(this).data('id');

			 if(wp.media.editor) {

				 wp.media.editor.insert('[product id="' + id + '"]');
			 }
		 };

		 self.init = function () {

			 self.parent.find('.js-collection-type').on('change', self.onCollectionTypeChange);

			 $(document).on('click', '.wsm-collection .js-load-products', self.loadProducts);
			 $(document).on('click', '.wsm-products a', self.onChosenProduct);
			 $(document).on('click', '.js-load-more', self.loadMore);

		 };

	 }

	 $(window).load( function () {

		 new Shortcodes().init();

	 } );

})( jQuery );
