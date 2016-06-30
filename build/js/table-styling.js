jQuery( function( $ ) {
    
    $( '.edd-compare-products-features-list-labels li, .edd-compare-products-features-list li' ).on( 'mouseenter', function() {
        
        var row = $( this ).index();
            
        $( this ).not( '.buy-button' ).addClass( 'hover' );
        
        $( '.edd-compare-products-features-list-labels, .edd-compare-products-features-list' ).each( function( index, element ) { 
            
            $( element ).children().not( '.buy-button' ).eq( row ).addClass( 'hover' );
            
        } );
        
    } );
    
    $( '.edd-compare-products-features-list-labels li, .edd-compare-products-features-list li' ).on( 'mouseleave', function() {
        
        var row = $( this ).index();
        
        $( this ).not( '.buy-button' ).removeClass( 'hover' );
        
        $( '.edd-compare-products-features-list-labels, .edd-compare-products-features-list' ).each( function( index, element ) { 
            
            $( element ).children().not( '.buy-button' ).eq( row ).removeClass( 'hover' );
            
        } );
        
    } );
    
} );