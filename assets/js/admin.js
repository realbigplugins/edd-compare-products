jQuery(document).ready(function ($) {

    /**
     * Settings screen JS
     */
    var EDD_Compare_Settings = {

        init: function () {
            this.fields();
        },

        fields: function () {
            // Insert new tax rate row
            $('#edd_add_compare_field').on('click', function() {
                var row = $('#edd_compare_fields tr:last');
                var clone = row.clone();
                var count = row.parent().find( 'tr' ).length;
                clone.find( 'td input' ).val( '' );
                clone.find( 'input, select' ).each(function() {
                    var name = $( this ).attr( 'name' );
                    name = name.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');
                    $( this ).attr( 'name', name ).attr( 'id', name );
                });
                clone.insertAfter( row );
                return false;
            });

            // Remove tax row
            $('body').on('click', '#edd_compare_fields .edd_remove_compare_field', function () {
                if (confirm("Are you sure you want to remove this field?")) {
                    var count = $('#edd_compare_fields tr:visible').length;

                    if (count === 2) {
                        $('#edd_compare_fields select').val('');
                        $('#edd_compare_fields input[type="text"]').val('');
                        $('#edd_compare_fields input[type="number"]').val('');
                        $('#edd_compare_fields input[type="checkbox"]').attr('checked', false);
                    } else {
                        $(this).closest('tr').remove();
                    }
                }
                return false;
            });
        }
    };
    EDD_Compare_Settings.init();
});