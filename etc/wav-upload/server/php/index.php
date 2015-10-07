<?php

/* ___________________________________________
 *  KANSAS PUBLIC RADIO
 *  _____________________________
 * |    ,  _ |   ____  |   ____  |
 * |  </--¯  |    /  ) |    /  ) |
 * |  /\     |   /--'  |  </--'  |
 * | /  \    |  /      |  / \    |
 *  ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
 *  file: wav-upload/server/php/index.php
 *  author: Dan Mantyla
 *  date: 8/17/2015
 *
 *  Description: 
 *      Instantiates the UploadHandler class and sets some options.
 *      Also, the UploadHandler class is extended to rework the code in our 
 *      favor without hacking it all up.
 *      Uploads the file, sends it to the IceCast server via FTP, then removes it.
 *
 *  Parameters: 
 *      nothing you need to worry about, the jQuery File Upload Plugin 
 *      does all that work.
 *     
 */

/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

//ini_set('display_errors',1);
//ini_set('track_errors', 1);
error_reporting(E_ALL);

require('UploadHandler.php');


/*
 *  custom class for uploading files, simply extends the UploadHander class
 */
class CustomUploadHandler extends UploadHandler {

    /*
     * customize the file name to fit with Steve's naming convention
     *  - The program that converts WAVs to MP3s requires that the name of the WAV be First_[some numbers].wav OR Second_[some numbers].wav
     *  - but! the name must also not bee too long or it will break the WAV-to-mp3 script (probably used an int and not a double or something)
     */
    protected function trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range) {
	$time = substr(str_replace('.','', microtime(true)), 5, 9); // last 9 digits of microtime()
        $name = 'First_' . $time . '.wav';
        return $name;
    }
   
    /*
     * rewrite the handle_file_upload() function to use FTP to send the WAV files to the ICECAST server
     */
    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {
        $file = new stdClass();
        $file->name = $this->get_file_name($uploaded_file, $name, $size, $type, $error,
            $index, $content_range);
        $file->size = $this->fix_integer_overflow((int)$size);
        $file->type = $type;
        if ($this->validate($uploaded_file, $file, $error, $index)) {
            $this->handle_form_data($file, $index);
            $upload_dir = $this->get_upload_path();
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, $this->options['mkdir_mode'], true);
            }
            $file_path = $this->get_upload_path($file->name);
            $append_file = $content_range && is_file($file_path) &&
                $file->size > $this->get_file_size($file_path);
            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                // multipart/formdata uploads (POST method uploads)
                if ($append_file) {
                    file_put_contents(
                        $file_path,
                        fopen($uploaded_file, 'r'),
                        FILE_APPEND
                    );
                } else {
                    move_uploaded_file($uploaded_file, $file_path);         
                }
            } else {
                // Non-multipart uploads (PUT method support)
                file_put_contents(
                    $file_path,
                    fopen('php://input', 'r'),
                    $append_file ? FILE_APPEND : 0
                );
            }
            $file_size = $this->get_file_size($file_path, $append_file);
            if ($file_size === $file->size) {
                $file->url = $this->get_download_url($file->name);
                if ($this->is_valid_image_file($file_path)) {
                    $this->handle_image_file($file_path, $file);
                }
            } else {
                $file->size = $file_size;
                if (!$content_range && $this->options['discard_aborted_uploads']) {
                    unlink($file_path);
                    $file->error = $this->get_error_message('abort');
                }
            }
            $this->set_additional_file_properties($file);
        }
        
        // send it to the icecast server, then delete it
        $this->post_handle_file_upload($file);
        
        return $file;
    }
    
    /*
     * do stuff after it's uploaded
     */
    protected function post_handle_file_upload($file) {
    
        $file_name = $file->name;
        $file_path = $this->get_upload_path($file_name);
    
        // upload it to the icecast server
        $error = $this->ftp_upload($file_path, $file_name);
        
        // now delete it
        unlink($file_path);
        
        if ($error != '') {
          $file->error = $error;
        } else {
          $file->error = 'Uploaded the WAV file and successfuly transfered the data to the KPR streaming server where it will be converted to an MP3.';
        }
    }
    
    
    /*
     * new function for uploading using FTP
     */
    protected function ftp_upload($source_file, $file_name) {
    
        $ftp_server = 'X.X.X.X'; 
        $port_number = 0000;
        $ftp_user_name = '********';
        $ftp_user_pass = '********';
        
        $destination_file = $file_name;
        
        $error = '';
    
        // set up basic connection
        $ftp_conn = ftp_connect($ftp_server, $port_number); 
        if (!$ftp_conn) {
            $error = "FTP connection has failed! Could not connect to the FTP server ftp://$ftp_server.<br />" ;
            //$error .= print_r(error_get_last());
            return $error;
        }

        // login with username and password
        $login_result = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass); 

        // check connection
        if (!$login_result) { 
            $error = "FTP connection has failed! ";
            $error .= "Could not log into fpt://$ftp_server for user $ftp_user_name"; 
            return $error;
        }
        
        // go into passive mode, let the ftp server determine the port to use for data transfer
        ftp_pasv($ftp_conn, true);

        // upload the file
        // (stream, remote file, local file)
        $upload = ftp_put($ftp_conn, $destination_file, $source_file, FTP_BINARY); 

        // check upload status
        if (!$upload) { 
            $error = "FTP upload has failed!";
        }

        // close the FTP stream 
        ftp_close($ftp_conn); 
        
        return $error;
    }
    
}

// options added by KPR for our WAV uploading form
$options = [
   // none I guess  
];
	
// finally, instantiate the new class	
$upload_handler = new CustomUploadHandler($options);


