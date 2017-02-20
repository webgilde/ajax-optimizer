jQuery( document ).ready( function( $ ) {
	$( '#ajax_optimizer_create_mu_plugin, #ajax_optimizer_delete_mu_plugin' ).click( function( event ) {
		event.preventDefault();

		var $loading = $( '#ajax-optimizer-create-mu-plugin-loading' ).show(),
		    $status = $( '#ajax-optimizer-mu-plugin-status' ).hide();
		    $that = $( this );

		// Hide both buttons.
		$that.hide().siblings().hide();

		ajax_optimizer_admin.filesystem.ajax( {
			data: {
				'action': $that.attr( 'id' ),
				'nonce': $that.data( 'nonce' )
			},
			done: function( data, textStatus, jqXHR ) {
				$loading.hide();

				if ( $.isPlainObject( data ) ) {
					var html_class = data.success ? 'updated' : 'error';
					$status.html( '<p>' + data.message + '</p>' ).removeClass().addClass( html_class ).show();

					if ( data.success ) {
						// Show other button.
						$that.siblings().show();
					} else {
						$that.show();
					}
				}
			},
			fail: function( jqXHR, textStatus, errorThrown ) {
				$loading.hide();
				$that.show();
				$status.html( '<p>' + errorThrown + '</p>' ).removeClass().addClass( 'error' ).show();
			},
			on_modal_close: function() {
				$loading.hide();
				$that.show();
				$status.show();
			}
		} );
	} );

	// Toggle row class.
	$( 'input[name^="ajax-optimizer[plugins]"]' ).on( 'change', function () {
		$( this ).parents( 'tr' ).toggleClass( 'active inactive' );
	});
});

window.ajax_optimizer_admin = window.ajax_optimizer_admin || {};
ajax_optimizer_admin.filesystem = {
	/**
	 * Holds the current job while the user writes data in the 'Connection Information' modal.
	 *
	 * @type {obj}
	 */
	_locked_job: false,

	/**
	 * Toggle the 'Connection Information' modal.
	 */
	_requestForCredentialsModalToggle: function() {
		this.$filesystemModal.toggle();
		jQuery( 'body' ).toggleClass( 'modal-open' );
	},

	_init: function() {
		this._init = function() {}
		var self = this;

		self.$filesystemModal = jQuery( '#request-filesystem-credentials-dialog' );

		/**
		 * Sends saved job.
		 */
		self.$filesystemModal.on( 'submit', 'form', function( event ) {
			event.preventDefault();

			self.ajax( self._locked_job, true );
			self._requestForCredentialsModalToggle()
		} );

		/**
		 * Closes the request credentials modal when clicking the 'Cancel' button.
		 */
		self.$filesystemModal.on( 'click', '[data-js-action="close"]', function() {
			if ( jQuery.isPlainObject( self._locked_job ) && self._locked_job.on_modal_close ) {
				self._locked_job.on_modal_close();
			}

			self._locked_job = false;
			self._requestForCredentialsModalToggle();
		} );
	},

	/**
	 * Sends AJAX request. Shows 'Connection Information' modal if needed.
	 *
	 * @param {object} args
	 * @param {bool} skip_modal
	 */
	ajax: function( args, skip_modal ) {
		this._init();

		if ( ! skip_modal && this.$filesystemModal.length > 0 ) {
			this._requestForCredentialsModalToggle();
			this.$filesystemModal.find( 'input:enabled:first' ).focus();

			// Do not send request.
			this._locked_job = args;
			return;
		}

		var options = {
			method: 'POST',
			url: window.ajaxurl,
			data: {
				username:        jQuery( '#username' ).val(),
				password:        jQuery( '#password' ).val(),
				hostname:        jQuery( '#hostname' ).val(),
				connection_type: jQuery( 'input[name="connection_type"]:checked' ).val(),
				public_key:      jQuery( '#public_key' ).val(),
				private_key:     jQuery( '#private_key' ).val()
			}
		};

		options.data = jQuery.extend( options.data, args.data );
		var request = jQuery.ajax( options );

		if ( args.done ) {
			request.done( args.done );
		}

		if ( args.fail ) {
			request.fail( args.fail );
		}
	}
}
