/* ___________________________________________
 *  KANSAS PUBLIC RADIO
 *  _____________________________
 * |    ,  _ |   ____  |   ____  |
 * |  </--¯  |    /  ) |    /  ) |
 * |  /\     |   /--'  |  </--'  |
 * | /  \    |  /      |  / \    |
 *  ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
 *  file: wav-upload/js/main.php
 *  author: Dan Mantyla
 *  date: 8/17/2015
 *
 *  Description: 
 *      The "main" js file for the app. Insantiates the JS classes and sets 
 *      options like valid file formats and size and all that.
 *
 *     
 * jQuery File Upload Plugin JS Example 8.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        xhrFields: {withCredentials: true},
        url: 'server/php/',
    });

    $('#fileupload').fileupload(
        'option', {
            // Enable iframe cross-domain access via redirect option:
            'redirect': 
            window.location.href.replace(
                /\/[^\/]*$/,
                '/cors/result.html?%s'
            ),
		
            // more options
            maxFileSize: 976563000, // 976563 kilabytes = 1000000 kibibytes = 1 gigabyte ??
            acceptFileTypes: /(\.|\/)(wav|WAV)$/i,
            maxNumberOfFiles: 1
        }
    );
	
	
   
    /* chunk it, if ya want, but I don't know if it's a good idea
    $('#fileupload').fileupload({
        maxChunkSize: 10000000 // 10 MB
    });
    */
   
    /* not working, don't know why :(
    if ($.support.cors) {
        $.ajax({
            url: 'http://streaming.kansaspublicradio.org:8000',
            type: 'HEAD',
            dataType: 'jsonp', // circomvents the Cross Origin Support problem
            complete: function(xhr, textStatus) {
                if (xhr.status == 404) {
                   $('<div class="alert alert-danger"/>').text('Upload server currently unavailable - ' + new Date()).appendTo('#fileupload');
                }
            }
        });
    }
    */

    // Load existing files:
    /* DON'T DO IT!!!!!
    $('#fileupload').addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
    });
    */

});
