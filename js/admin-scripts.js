( function($) {

    $('.btn-success').on('click', function(){

      var id = $(this).parent().parent().attr('id');
      id = id.slice(5);
        data = {
            'action': 'moreResults_action',
            'bookkmark-meta' : id
        };

        jQuery.post( admin_toplegal_ajax.url, data, function( res ) {
            console.log(res);
            location.reload();
        });
    });

    $('.btn-danger').on('click', function(){

      var id = $(this).attr('id');

        data = {
            'action': 'moreResults_action',
            'bookkmark-meta-delete' : id
        };

        jQuery.post( admin_toplegal_ajax.url, data, function( res ) {
            console.log(res);
            location.reload();
        });
    });

} )(jQuery);