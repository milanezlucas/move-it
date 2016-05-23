$j = jQuery.noConflict();

var url = get_domain() + '_framework/move-it/';
var url_theme = url + 'wp-content/themes/move-it/';
var url_ajax = url + 'wp-admin/admin-ajax.php';

function get_domain()
{
    var a = window.location.protocol,
        b = window.location.host,
    url = a + '//' + b + '/';

    return url;
}

jQuery( document ).ready( function() {
    if ( $j( document ).find( '.date-field' ).length ) set_datepicker();
    if ( $j( document ).find( '.button_upload' ).length ) set_uploader();
    if ( $j( document ).find( '.column' ).length ) set_sortable();
    if ( $j( document ).find( '.portlet-add' ).length ) add_widget_page();
});

function set_datepicker () {
	$j( '.date-field' ).datepicker({
        dateFormat:         'dd/mm/yy',
        dayNames:           [ 'Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado' ],
        dayNamesMin:        [ 'D','S','T','Q','Q','S','S','D' ],
        dayNamesShort:      [ 'Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom' ],
        monthNames:         [ 'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro' ],
        monthNamesShort:    [ 'Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez' ],
        nextText:           'Próximo',
        prevText:           'Anterior'
    });
}

function set_uploader () {
	var input_id;
    var custom_uploader;
    var input_value;
    var post_id;
    var input_split;

    $j( '.button_upload' ).click( function( e ) {
        e.preventDefault();
        input_id = $j( this ).attr( 'id' );
        input_split = input_id.split( '_button' );
        input_value = $j( '#' + input_split[ 0 ] ).val();

        if ( custom_uploader ) {
            custom_uploader.open();
            return;
        }

        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Escolher arquivo',
            button: {
                text: 'Escolher'
            },
            multiple: true
        });

        custom_uploader.on( 'open', function() {
            var selection = custom_uploader.state().get( 'selection' );
            var ids = input_value.split( ',' );
            var count = ids.length;
            if( count > 0) {
                for ( var i=0; i<count; i++ ) {
                    attachment = wp.media.attachment( ids[ i ] );
                    attachment.fetch();
                    selection.add( attachment ? [attachment] : [] );
                }
            }
        });

        custom_uploader.on( 'select', function() {
            attachment = custom_uploader.state().get( 'selection' ).toJSON();

            var post_id = new Array();
            for ( i in attachment ) {
                post_id.push( attachment[ i ].id );
            }
            $j( '#' + input_split[ 0 ] ).val( post_id );

            var info = {};
            info[ 'action' ]    = 'upload_image';
            info[ 'post_id' ]   = post_id;
            info[ 'input_id' ]  = input_split[ 0 ];
            $j.ajax( {
                type: 'POST',
                url: url_ajax,
                data: info,
                dataType: 'json',
                beforeSend: function() {},
                success: function( r ) {
                    $j( '.upload-field-view-' + input_split[ 0 ] ).html( r.msg );
                    $j( '.remove-file' ).click( function( e ) {
                        e.preventDefault();
                        mi_remove_file( $j( this ).attr( 'rel' ), $j( this ).attr( 'data-input' ) );
                    });
                    add_sortable_files();
                },
                error: function(){}
            });
 
        });

        custom_uploader.open();

    });

    $j( '.remove-file' ).click(function( e ) {
        e.preventDefault();
        mi_remove_file( $j( this ).attr( 'rel' ), $j( this ).attr( 'data-input' ) );
    });

    function mi_remove_file( post_id, input_id ) {
        var input_value;

        input_value = $j( '#' + input_id ).val();
        input_value = input_value.replace( post_id, '' );

        $j( '.upload_' + input_id + '_' + post_id  ).html( '' );
        $j( '#' + input_id ).val( input_value );

        var info = {};
            info[ 'action' ]    = 'upload_image';
            info[ 'post_id' ]   = input_value;
            info[ 'input_id' ]  = input_id;
            $j.ajax( {
                type: 'POST',
                url: url_ajax,
                data: info,
                dataType: 'json',
                beforeSend: function() {},
                success: function( r ) {
                    $j( '.upload-field-view-' + input_id ).html( r.msg );
                    $j( '.remove-file' ).click( function( e ) {
                        e.preventDefault();
                        mi_remove_file( $j( this ).attr( 'rel' ), $j( this ).attr( 'data-input' ) );
                    });
                    add_sortable_files();
                },
                error: function(){}
            });
    }

    function add_sortable_files () {
        $j( '.upload-sortable' ).sortable({
            connectWith:    '.upload-sortable',
            handle:         '.upload-handle',
            update:         function( event, ui ) {
                // console.log( ui );
                $j( '#' + $j( '.' + ui.item.context.className ).attr( 'rel' ) ).val( $j( '.upload-sortable' ).sortable( 'toArray' ).toString() );
            }
        });
    }
    add_sortable_files();
}

function set_sortable () {
    $j( '.column' ).sortable({
        connectWith:    '.column',
        handle:         '.portlet-header',
        cancel:         '.portlet-toggle',
        placeholder:    'portlet-placeholder ui-corner-all',
        update:         function( event, ui ) {
            $j( '#widgets_order' ).val( $j( '.column' ).sortable( 'toArray' ).toString() );
        }
    });

    $j( '.portlet' ).addClass( 'ui-widget ui-widget-content ui-helper-clearfix' ).find( '.portlet-header' ).addClass( 'ui-widget-header' ).prepend( '<span class="ui-icon ui-icon-plusthick portlet-toggle"></span>' );

    $j( '.portlet' ).find( '.portlet-content' ).css( 'display', 'none' );

    $j( '.portlet-toggle' ).click( function() {
        var icon = $j( this );
        icon.toggleClass( 'ui-icon-plusthick ui-icon-minusthick' );
        icon.closest( '.portlet' ).find( '.portlet-content' ).toggle();
    });

    form_widget();
    exclude_widget();
}

function add_widget_page () {
    $j( '.portlet-add' ).click( function ( e ) {
        e.preventDefault();

        var widget = $j( this );
        var info = {};
        info[ 'action' ]    = 'add_widget';
        info[ 'widget' ]    = $j( widget ).attr( 'id' );
        $j.ajax( {
            type: 'POST',
            url: url_ajax,
            data: info,
            dataType: 'json',
            beforeSend: function() {
                $j( widget ).css( 'opacity', '.5' );
            },
            success: function( r ) {
                $j( '.column' ).append( r.widget );
                $j( widget ).css( 'opacity', '1' );
                tb_remove();
                set_sortable();
                $j( '#widgets_order' ).val( $j( '#widgets_order' ).val() + ',' + r.widget_id );
                $j( '#post' ).submit();
            },
            error: function(){}
        });
    });
}

function form_widget () {
    $j( '.form-widget' ).submit( function ( e ) {
        e.preventDefault();

        var form = $j( this );

        if ( $j( form ).find( '.widget_editor' ).length ) {
            $j( '#' + $j( '.widget_editor', this ).attr( 'id' ) + '_text' ).val( tinyMCE.get( $j( '.widget_editor', this ).attr( 'id' ) ).getContent() );
        }

        var info = {};
        var fields = $j( this ).serializeArray();
        var value;
        var field_form;
        var foo = [];

        for ( f in fields ) {
            if ( $j( '#' + fields[f].name, form ).hasClass( 'widget_editor' ) ) {
                value = $j( '#' + fields[f].name + '_text', form ).val();
                field_form = fields[f].name.split( '--' );
                fields[f].name = field_form[ 0 ];
            } else if (  $j( '#' + fields[f].name, form ).hasClass( 'date-field' ) ) {
                field_form = fields[f].name.split( '--' );
                fields[f].name = field_form[ 0 ];
                value = fields[f].value;
            } else if ( $j( '#' + fields[f].name, form ).attr( 'multiple' ) ) {
                $j( '#' + fields[f].name + ' :selected', form ).each( function( i, selected ) { 
                  foo[i] = $j( selected ).val(); 
                });
                value = foo;
            } else if ( $j( 'input[name=' + fields[f].name + ']', form ).attr( 'type' ) == 'checkbox' ) {
                $j( 'input:checkbox[name=' + fields[f].name + ']:checked', form ).each( function( i, checked ) { 
                  foo[i] = $j( checked ).val(); 
                });
                value = foo;
            } else if ( $j( '#' + fields[f].name ).hasClass( 'upload_field' ) ) {
                value = fields[f].value;
                field_form = fields[f].name.split( '--' );
                fields[f].name = field_form[ 0 ];
            } else {
                value = fields[f].value;
            }

            info[ fields[f].name ] = value;
        }

        info[ 'action' ] = 'form_widget';
        $j.ajax( {
            type: 'POST',
            url: url_ajax,
            data: info,
            dataType: 'json',
            beforeSend: function() {
                $j( form ).css( 'opacity', '.5' );
            },
            success: function( r ) {
                $j( form ).css( 'opacity', '1' );
            },
            error: function(){}
        });
    });
}

function exclude_widget () {
    $j( '.exclude-widget' ).click( function ( e ) {
        e.preventDefault();

        var exclude = $j( this );
        var info = {};
        info[ 'action' ] = 'exclude_widget';
        info[ 'widget' ] = $j( exclude ).attr( 'data-widget' );
        $j.ajax( {
            type: 'POST',
            url: url_ajax,
            data: info,
            dataType: 'json',
            beforeSend: function() {
                $j( exclude ).text( 'Excluindo...' );
            },
            success: function() {
                var widgets_order = $j( '#widgets_order' ).val();
                $j( '#widgets_order' ).val( widgets_order.replace( $j( exclude ).attr( 'data-widget' ), '' ) );
                $j( '#' + $j( exclude ).attr( 'data-widget' ) ).fadeOut();
                $j( '#post' ).submit();
            },
            error: function(){}
        });
    });
}
