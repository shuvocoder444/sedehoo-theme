/* Sedehoo — customizer-preview.js */
( function( $ ) {
    var root = document.documentElement;
    var pairs = {
        'sedehoo_accent_color':   '--accent',
        'sedehoo_accent_color_2': '--accent2',
        'sedehoo_bg_color':       '--bg',
        'sedehoo_bg2_color':      '--bg2',
        'sedehoo_bg3_color':      '--bg3',
        'sedehoo_border_color':   '--border',
        'sedehoo_text_color':     '--text',
        'sedehoo_muted_color':    '--muted',
    };
    Object.keys( pairs ).forEach( function( setting ) {
        wp.customize( setting, function( value ) {
            value.bind( function( newVal ) {
                root.style.setProperty( pairs[ setting ], newVal );
            } );
        } );
    } );
    wp.customize( 'sedehoo_sidebar_width', function( value ) {
        value.bind( function( newVal ) {
            root.style.setProperty( '--sidebar-w', newVal + 'px' );
        } );
    } );
    wp.customize( 'sedehoo_logo_text', function( value ) {
        value.bind( function( newVal ) {
            $( '.logo-text' ).text( newVal );
        } );
    } );
    wp.customize( 'sedehoo_logo_subtext', function( value ) {
        value.bind( function( newVal ) {
            $( '.logo-sub' ).text( newVal );
        } );
    } );
    wp.customize( 'sedehoo_footer_copyright', function( value ) {
        value.bind( function( newVal ) {
            $( '.footer-copyright' ).html( newVal );
        } );
    } );
} )( jQuery );
