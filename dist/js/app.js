;(function($){
	var CustomAssets = function(){
		this.initialize();
	}
	CustomAssets.prototype.initialize = function()
	{
		if( pagenow == 'custom-assets_page_add-js-inline' ){
			this.jsEditor();
		}
		if( pagenow == 'custom-assets_page_add-css-file' ){
			this.cssEditor();
		}

		if(pagenow == 'custom-assets_page_edit_custom-assets'){
			var id = $('.wrap').find('pre')[0].id;
			if( id == 'js-editor' ){
				this.jsEditor();
			}else{
				this.cssEditor();
			}
		}
		
		this.handleUI();
	};
	CustomAssets.prototype.handleUI = function()
	{
		var form_type = $('#save-css').length == 1 ? 'css':'js';

		var dataContent = {},
		menu_list = $('#toplevel_page_custom-assets').find('.wp-submenu').find('li');

		$(menu_list[4]).css('display', 'none');
		$(menu_list[5]).css('display', 'none');

		this.itemDisplay( 'add-remote-js', 'remote-js' );
		this.itemDisplay( 'add-remote-css', 'remote-css' );

		$('.save-code-to-db').on('click', function(){
			if( form_type == 'css' ){
				dataContent = this.serializeAsObject('save-css');
			}else{
				dataContent = this.serializeAsObject('save-js');
			}
			
			this.sendData(dataContent);
		}.bind(this));
		

		$('.delete-item').on('click', function(){
			var id = $(this).data('id');
			$('.item-edit-'+id).css('display', 'none');
			$('.item-delete-comfirm-'+id).show();
			$(this).hide();
		});
		$('.cancel-exlusion').on('click', function(){
			var id = $(this).data('id');
			$('.item-edit-'+id).css('display', 'inline');
			$('.item-delete-'+id).css('display', 'inline');
			$('.item-delete-comfirm-'+id).hide();
		});
		$('.yes-remove').on('click', function(){
			var id = $(this).parent().data('id')
			window.location.href = 'admin.php?page=delete_custom-assets&_id='+id;
		});

	}
	CustomAssets.prototype.cssEditor = function()
	{
		this.instanceEditor( 'css-editor', { 
			lang: 'css'
			, elasticEditor: true 
		});
	};
	CustomAssets.prototype.jsEditor = function()
	{
		this.instanceEditor( 'js-editor',  {
			theme: 'monokai'
			, lang: 'javascript'
			, elasticEditor: true  
		});
	};

	CustomAssets.prototype.instanceEditor = function(id, options)
	{
		var editor = ace.edit(id)
		, session = editor.getSession();
		
		_global = editor;

		if(pagenow == 'custom-assets_page_edit_custom-assets'){
			$('#temp_code').val(session.getValue());
		}
		if( options.theme !== null ){
			editor.setTheme("ace/theme/"+options.theme);
		}
		session.setMode("ace/mode/"+options.lang);

		if( options.elasticEditor == true ){
			editor.setOptions({
				maxLines: Infinity,
			});
		}else{
			$('#'+id).css({
				height: 200,
			});
		}

		editor.on("change", function(e) {
			if (editor.curOp && editor.curOp.command.name) {
				$('#temp_code').val(session.getValue());
			}else{
				//code...
			} 
				
		});
		editor.on("input", function(e) {
				$('#temp_code').val(session.getValue());	
		});
	};
	CustomAssets.prototype.itemDisplay = function(ctx, container)
	{
		var context = $('#'+ctx)
		, container = $('#'+container);

		context.on('click', function(){
			if( ! container.is(':visible') ){
				context.addClass('remove').html('&times; '+ajax_utils.strings.cancel);
				container.show().find('input').focus();
			}else{
				context.removeClass('remove').html(ajax_utils.strings.addRemoteJs);
				container.hide();
			}
		});

		return;
	};

	CustomAssets.prototype.sendData = function(data)
	{
		var form_type = $('#save-css').length == 1 ? 'css':'js';

		if( $('#temp_code').val() == '' &&  $('.remote-resource').val() == '' ){
			if( form_type == 'js' ){
				this.setValue('//code...');	
			}else{
				this.setValue('/* code... */');	
			}
			return;
		}

		$.ajax({
			url: ajax_utils.ajaxurl,
			data: {
				type: form_type
				, inner:data
				, action: 'wpaci17089_add'
			},
			type: 'POST',
			beforeSend: function(){
				$('.save-code-to-db').text('Saving').attr('disabled', true);
				$('.spinner-saving').fadeIn('slow');
			}
		}).complete(function(xhr, responseText){
			setTimeout(function(){
				$('.save-code-to-db').text('Saved!').text('Save').removeAttr('disabled');
				$('.spinner-saving').fadeOut(function(){
					if( $('#item-added').is(':visible') ){
						$('#item-added').fadeOut(function(){
							$('#item-added').fadeIn();
						});
					}else{
						$('#item-added').fadeIn();
					}
					
				});									
			}, 2000);
		});
			

	};
	CustomAssets.prototype.serializeAsObject = function(ctx)
	{
		var form = $('#'+ctx).serializeArray()
		, data = {};

		for (var item in form) 
			data[form[item].name] = encodeURIComponent( form[item].value );

		return data;

		// Percentual: (40*927)/100 = 370[Percentual]

		// Total a receber: ( 927[Sacado no Caixa] - 370[Percentual] ) = 557 [Total a receber referente à 40% de 927 R$]

		// Valor Recebido: ( 927[Sacado no Caixa] - 520[Entregue na empresa] ) = 407 [Valor recebido]

		// Valor negativo: (557[Valor a receber] - 407[Valor recebido]) = 150,00 R$ [Total a pagar]

		

 
		
	};
	CustomAssets.prototype.setValue = function( value ){
		_global.setValue( value , 1)
	}

	$(function(){
		return( new CustomAssets() || {} );
	})
}(jQuery));