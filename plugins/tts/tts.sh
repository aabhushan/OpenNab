#! /bin/bash

NAME=xx

say $1 -o ${NAME}.aiff
/usr/local/bin/lame ${NAME}.aiff ${NAME}.mp3 2> ${NAME}.log
#afplay ${NAME}.mp3
#rm ${NAME}.{aiff,mp3}
