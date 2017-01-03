jQuery( function( $ ) {
    
    function setHighlightDifferences( row, highlight ) {
        
        $( '.edd-compare-products-features-list-labels, .edd-compare-products-features-list' ).each( function( index, element ) { 
            
            if ( highlight ) {
                
                $( element ).children().not( '.buy-button' ).eq( row ).addClass( 'difference' );
                
            }
            else {
                
                $( element ).children().not( '.buy-button' ).eq( row ).removeClass( 'difference' );
                
            }
            
        } );
        
    }
    
    function getHighlightDifferences( container ) {
        console.log( container );
        
        var pastValues = [],
            featuresLists = $( container ).find( '.product:visible .edd-compare-products-features-list' );
        
        for ( var productIndex = 0; productIndex < featuresLists.length; productIndex++ ) {
            
            var productFeatures = $( featuresLists[ productIndex] ).find( 'li' ).not( '.buy-button' );
            
            for ( var row = 0; row < productFeatures.length; row++ ) {
                
                if ( ( pastValues[ row ] !== undefined ) && ( pastValues[ row ] !== $( productFeatures[ row ] ).text() ) && ( $( productFeatures[ row ] ).text() !== '' ) || ( pastValues[ row ] === false ) ) {
                    
                    setHighlightDifferences( row, true );
                    pastValues[ row ] = false; // Once there's a difference, set to false. 
                                                // This way n - 1 checks that happen to balance out at the end of a row don't throw things off.
                    
                }
                else {
                    
                    setHighlightDifferences( row, false );
                    pastValues[ row ] = $( productFeatures[ row ] ).text();
                    
                }
                
            }
            
            console.log( pastValues );
            
        }
        
    }
    
    $( '.edd-compare-products' ).each( function( index, element ) {
        
        getHighlightDifferences( element );
        
    } );
    
    $( document ).on( 'filtered resetFilters', '.edd-compare-products', function() {
        
        getHighlightDifferences( this );
        
    } );
    
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