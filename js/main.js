jQuery(document).ready(function($){
	$('input[type="checkbox"]').on('change', function(){
		var $this = $(this),
			value = $(this).val();
		$this.parents('li').fadeOut(250, function(){
			$(this).detach();
		});
		$.post(camptix_extend.url, { nonce: camptix_extend.nonce, data: value }, function(data){
			//var result = $.parseJSON(data);
		});
	});
});