(function( $ ) {

    var prefix = 'gamipress_badgeos_importer_';

    var first_run = true;
    var current_process = 0;
    var process = [
        'achievements',
        'steps',
        'points',
        //'earnings',   // User is able to select if import it or not
        //'logs'        // User is able to select if import it or not
    ];

    function gamipress_badgeos_migrate( show_info ) {

        if( show_info === undefined )
            show_info = true;

        if( process[current_process] === undefined ) {
            // Remove the spinner
            $('#' + prefix + 'response').find('.spinner').remove();

            // Restore the run import button
            $('#' + prefix + 'run').prop('disabled', false);

            // Restore current process
            current_process = 0;

            $('#' + prefix + 'response').append( '<br>Process finished succesfully!' );
            return;
        } else if( show_info ) {
            $('#' + prefix + 'response').append( '<br>Migrating ' + process[current_process] + '...' );
        }

        $.ajax({
            url: ajaxurl,
            data: {
                action: prefix + 'import_'+ process[current_process],
                first_run: ( first_run ? 1 : 0 ),
                // Tool data
                points_points_type: $('#' + prefix + 'points_points_type').val(),
                override_points: ( $('#' + prefix + 'override_points').prop('checked') ? 1 : 0 ),
            },
            success: function( response ) {

                // Clear first run
                first_run = false;

                var running_selector = $('#' + prefix + 'response #running-' + process[current_process]);

                if( response.data.run_again !== undefined && response.data.run_again ) {
                    // If run again is set, we need to send again the same action

                    if( response.data.message !== undefined ) {

                        // If data message is set like "Remaining items ..." add it to a custom span element t be removed
                        if( ! running_selector.length ) {
                            $('#' + prefix + 'response').append( '<br><span id="running-' + process[current_process] + '"></span>' );
                            // Re-assign running selector
                            running_selector = $('#' + prefix + 'response #running-' + process[current_process]);
                        }

                        // Set the response message
                        running_selector.html( response.data.message );
                    }

                    // Runs again the same process without show the "Migrating {processs}..." text
                    gamipress_badgeos_migrate( false );
                } else {

                    // Check if there is a message from run again to remove it
                    if( running_selector.length ) {
                        running_selector.prev('br').remove();
                        running_selector.remove();
                    }

                    // Add the response message
                    $('#' + prefix + 'response').append( '<br>' + response.data );

                    current_process++;

                    // Run the next process
                    setTimeout( gamipress_badgeos_migrate, 500 );
                }

            },
            error: function( response ) {
                //$('#' + prefix + 'response').css({color:'#a00'});
                $('#' + prefix + 'response').append( '<br>' + '<span style="color: #a00;">' + response.data !== undefined ? response.data : 'Internal server error' + '</span>' );
                return;
            }
        });
    }

    $('#' + prefix + 'run').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $this.prop('disabled', true);

        if( $('#' + prefix + 'response').length )
            $('#' + prefix + 'response').remove();

        // Show the spinner
        $this.parent().append('<div id="' + prefix + 'response"><span class="spinner is-active" style="float: none;"></span></div>');

        // Add user selected process
        if( $('#' + prefix + 'import_earnings').prop('checked') )
            process.push('earnings');

        // Add user selected process
        if( $('#' + prefix + 'import_logs').prop('checked') )
            process.push('logs');

        // On click, set this var to meet that is first time that it runs
        first_run = true;

        gamipress_badgeos_migrate();
    });

})( jQuery );