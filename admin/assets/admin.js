jQuery( document ).ready( function( $ ) {
	jQuery( '#ajax_optimizer_create_mu_plugin, #ajax_optimizer_delete_mu_plugin' ).click( function( event ) {
		event.preventDefault();
		var $that = jQuery( this );
		var $status = jQuery( '#ajax-optimizer-mu-plugin-status' ).hide();
		var is_activate_button = this.id == 'ajax_optimizer_create_mu_plugin';
		if (is_activate_button) {
			var div = jQuery( '#ajax_optimizer_create_mu_plugin_confirm' );
			if (! div.is(":visible")){
				div.show();
				jQuery( '#ajax_optimizer_create_mu_plugin_div' ).addClass('highlight');
			}
			var checkbox = jQuery( '#ajax_optimizer_create_mu_plugin_cb' );
			var checked = ( checkbox && checkbox.length ) ? jQuery( '#ajax_optimizer_create_mu_plugin_cb' )[0].checked : false;
			if (! checked){
				var p = jQuery('<p/>');
				var title= jQuery('<strong/>').text(ajax_optimizer_translation.enable_mu_confirm_title);
				var text = jQuery('<p/>').text(ajax_optimizer_translation.enable_mu_confirm_text);
				checkbox = jQuery('<input type="checkbox" id="ajax_optimizer_create_mu_plugin_cb"/>').change( function (event) {
					jQuery('#ajax_optimizer_create_mu_plugin').prop('disabled', ! this.checked);
					var btn = jQuery('#ajax_optimizer_create_mu_plugin');
					if (this.checked) btn.addClass('button-primary');
					else btn.removeClass('button-primary');
				});
				var label = jQuery('<label for="ajax_optimizer_create_mu_plugin_cb">').text(ajax_optimizer_translation.enable_mu_checkbox);
				p.append(title).append(text).append(checkbox).append(label);
				$status.html('').append(p).removeClass().addClass( 'error' ).show();
				$that.text(ajax_optimizer_translation.enable_mu2);
				jQuery(this).prop('disabled', ! checked);
				return;
			}
			else{
				$that.text(ajax_optimizer_translation.enable_mu);
				jQuery( '#ajax_optimizer_create_mu_plugin_div' ).removeClass('highlight');
				jQuery( '#ajax_optimizer_create_mu_plugin_cb' )[0].checked = false;
				div.hide();
			}
		}
		var $loading = jQuery( '#ajax-optimizer-create-mu-plugin-loading' ).show();
		$that.removeClass('button-primary')
		
		// Hide both buttons.
	    jQuery( '#ajax_optimizer_create_mu_plugin, #ajax_optimizer_delete_mu_plugin' ).hide();

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
						if (is_activate_button) jQuery( '#ajax_optimizer_delete_mu_plugin ').show();
						else jQuery( '#ajax_optimizer_create_mu_plugin ').show();
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
	
	jQuery('#ajax_optimizer_save_unrecommended_cb').change(function(event){
		jQuery('#ajax_optimizer_submit_settings').prop('disabled', ! this.checked);
	});
	//  check if the page is displayed.
	if (jQuery('#ajax_optimizer_save_unrecommended_cb').length > 0)
		ajax_optimizer_admin.check_for_recommended_settings();
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


ajax_optimizer_admin.set_checked = function(elm, preventCheckForRecommendedSettings){
	var input = document.getElementById('ajax_optimizer_status_' + elm.id);
	var classNew = elm.checked ? 'active' : 'inactive';
	var classOld = elm.checked ? 'inactive' : 'active';
	if (input) input.value = classNew;
	jQuery( elm ).parents( 'tr' ).removeClass(classOld);
	jQuery( elm ).parents( 'tr' ).addClass(classNew);
	
	if (! preventCheckForRecommendedSettings)
		ajax_optimizer_admin.check_for_recommended_settings();
}

ajax_optimizer_admin.get_recommended_status = function(action_id, plugin_path){
	var recs = ajax_optimizer_admin.recommendations[action_id];
	if (recs){
		if (recs[plugin_path]){
			return recs[plugin_path];
		}
		else{
			if (action_id == '_default') return 'active';
			return 'inactive';
		}
	}
	return 'active';
}

ajax_optimizer_admin.set_to_default = function(action_id){
	var recs = ajax_optimizer_admin.recommendations[action_id];
	for (var i in ajax_optimizer_admin.plugin_paths){
		var path = ajax_optimizer_admin.plugin_paths[i];
		var elm = document.getElementById('ajax_optimizer_' + action_id + '_' + path);
		var rec_status = ajax_optimizer_admin.get_recommended_status(action_id, path);
		var checked = rec_status == 'active';
		elm.checked = checked;
		ajax_optimizer_admin.set_checked(elm, true);
	}
	ajax_optimizer_admin.check_for_recommended_settings();
}

ajax_optimizer_admin.check_for_recommended_settings = function(){
	jQuery('#ajax_optimizer_save_unrecommended_cb')[0].checked = false;
	var unrecommended_count = 0;
	for (var i in ajax_optimizer_admin.action_ids){
		var action_id = ajax_optimizer_admin.action_ids[i];
		
		for (var j in ajax_optimizer_admin.plugin_paths){
			var path = ajax_optimizer_admin.plugin_paths[j];
			var cb = document.getElementById('ajax_optimizer_' + action_id + '_' + path);
			var rec_status = ajax_optimizer_admin.get_recommended_status(action_id, path);
			var checked = rec_status == 'active';
			if (checked != cb.checked){
				unrecommended_count++;
			}
		}
	}
	
	if (unrecommended_count > 0) jQuery('#ajax_optimizer_save_unrecommended_notice').show();
	else jQuery('#ajax_optimizer_save_unrecommended_notice').hide();
	jQuery('#ajax_optimizer_submit_settings').prop('disabled', unrecommended_count > 0);
	
}