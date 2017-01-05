jQuery( function( $ ) {
    
    $( '.edd-compare-products-features-list-labels li, .edd-compare-products-features-list li' ).on( 'mouseenter', function() {
        
        var row = $( this ).index();
            
        $( this ).not( '.buy-button' ).not( '[id^="edd_price_option_"]' ).addClass( 'hover' );
        
        if ( $( this ).hasClass( '.buy-button' ) || ( $( this ).attr( 'id' ) !== undefined && $( this ).attr( 'id' ).indexOf( 'edd_price_option_' ) > -1 ) ) return false;
        
        $( '.edd-compare-products-features-list-labels, .edd-compare-products-features-list' ).each( function( index, element ) { 
            
            $( element ).children().not( '.buy-button' ).not( '[id^="edd_price_option_"]' ).eq( row ).addClass( 'hover' );
            
        } );
        
    } );
    
    $( '.edd-compare-products-features-list-labels li, .edd-compare-products-features-list li' ).on( 'mouseleave', function() {
        
        var row = $( this ).index();
        
        $( this ).not( '.buy-button' ).not( '[id^="edd_price_option_"]' ).removeClass( 'hover' );
        
        if ( $( this ).hasClass( '.buy-button' ) || ( $( this ).attr( 'id' ) !== undefined && $( this ).attr( 'id' ).indexOf( 'edd_price_option_' ) > -1 ) ) return false;
        
        $( '.edd-compare-products-features-list-labels, .edd-compare-products-features-list' ).each( function( index, element ) { 
            
            $( element ).children().not( '.buy-button' ).not( '[id^="edd_price_option_"]' ).eq( row ).removeClass( 'hover' );
            
        } );
        
    } );
    
} );