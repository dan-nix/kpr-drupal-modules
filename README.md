# kpr-drupal-modules
Custom modules for Drupal that are designed to work alongside the NPR modules.
http://kansaspublicradio.org

One module, just called KPR, alters the news article creation form to add a WAV upload form to the NPR audio field.
The other module, KPR Jplayer, is a fork of the NPR Player Pack's jplayer module, I just wanted to customize it to fit our website better.
With these two modules, I haven't had to hack the NPR module at all, so the next time there's an update it should be really easy.

There's a few reasons I've had to do things this way.
 * We have a policy at our station to only work with lossless WAV files. Of course the internet's audio players (jplayer, jwplayer, etc) work best (only?) with MP3s.
 * We have a streaming audio server for our live stream and for static audio files. Located on this same server is a program that converts WAV files to MP3 files and moves the files around and stuff.

The NPR audio field just wants the URL of the MP3, so that's pretty easy. I just have the upload form (an instance of the blueimp/jQuery-File-Upload) print out what the URL will be and then the person uploading the audio needs to copy and paste it into the MP3 Link field. The upload script then sends the WAV file to our audio server where it's converted to an MP3. 

Hope this helps other NPR affiliate stations out there!
