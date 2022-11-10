php artisan passport:install
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client Secret: nGH9wStKeXVxD8fcaOzlpuzljvOurL6isjzr6AHN
Password grant client created successfully.
Client ID: 2
Client Secret: i3BoNFfbPG16fjNNjscD44ocV8GvpE9GmdDj86Xi

For deploy frontend
1. npm install
2. npm run production or npm run dev

# compressor
do sox "$file" "$file-compresssed.mp3" compand 0.3,1 6:-70,-60,-20 -5 -90 0.2 ;


  
ffmpeg -i in.mp3 -filter_complex \
 "compand=attacks=0:points=-80/-900|-45/-15|-27/-9|0/-7|20/-7:gain=5" out.mp3  
  
  https://medium.com/@jud.dagnall/dynamic-range-compression-for-audio-with-ffmpeg-and-compand-621fe2b1a892
  http://sox.10957.n7.nabble.com/Question-about-mcompand-tp2383p2384.html
  https://ffmpeg.org/ffmpeg-filters.html#compand

ffmpeg can generate wave form images. both channels combined into a single channel (mono).
ffmpeg -i in.mp3 -filter_complex ""aformat=channel_layouts=mono,showwavespic=s=1000x200"" -frames:v 1 in.png

https://trac.ffmpeg.org/wiki/Waveform

pwgen -sn 8
printf "LOGINNAME:$(openssl passwd -crypt PASSWORD)\n" >> USERNAMEorDOMAIN.htpasswd

Intelligent error page
https://blog.adriaan.io/one-nginx-error-page-to-rule-them-all.html
