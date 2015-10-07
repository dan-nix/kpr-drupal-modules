<?php

/* ___________________________________________
 *  KANSAS PUBLIC RADIO
 *  _____________________________
 * |    ,  _ |   ____  |   ____  |
 * |  </--¯  |    /  ) |    /  ) |
 * |  /\     |   /--'  |  </--'  |
 * | /  \    |  /      |  / \    |
 *  ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
 *  file: wav-upload/index.php
 *  author: Dan Mantyla
 *  date: 10/6/2015
 *
 *  Description: for really large wav uploads, the web server was timing out. The solution? A .bat file that 
 *     sends the WAV file directly to the streaming audio server from the client machine. 
 *     What THIS script does is help with that a little by giving a file name and instrucitons to the user.
 *
 */

$file_name = "First_" . substr(str_replace('.','', microtime(true)), 5, 9);
$mp3_url = 'http://streaming.kansaspublicradio.org:8000/mp3/' . $file_name . ".mp3";

?>


<html>
<head>
<title>Large WAV Uploads</title>
</head>
<body>

<h1>Large WAV Uploads</h1>

<p>If your WAV file is real big, like half an hour of audio or larger, then you will probably need to use this method of uploading the audio to the streaming audio server. You will need a special file installed on your PC that you can <a href='WAV-UPLOAD.bat' download>download here</a>. You will also need to follow the instructions bellow for it to work correctly. </p>

<h2>Instructions:</h2>
<ol>
<li>Rename your audio file to: <input type='text' value="<?php echo $file_name; ?>.wav" /> If the file doesn't follow this naming convention then it will not be converted to an MP3. </li>
<li><a target=_blank href='https://www.youtube.com/watch?v=TTKpNk3uHos'>Drag and drop</a> the audio into the WAV-UPLOAD.bat file located on your PC. (Don't have the batch file? <a href='WAV-UPLOAD.bat' download>Click here to download it.</a>)</li>
<li>Copy this link and paste it in the MP3 Link field of the new article form: <input type='text' value='<?php echo $mp3_url; ?>' size=75 /></li>
<li>Done! It will take a few minutes for the MP3 to be created.</li>
</ol>

</body>
</html>