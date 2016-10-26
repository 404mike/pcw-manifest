#!/bin/bash
# @author Mike Jones - National Library of Wales

# Script to straighten images

# Get manifest ID
var=$(pwd)

BASE="$var/llgc_$1/temp"
TRANSFORMED=$BASE"/transformed"
HOLDING=$TRANSFORMED"/holding"
FINAL=$TRANSFORMED"/final"
TRASH=$FINAL"/trash"

# Loop through all the iamges
for a in $BASE"/"*.jpg
do

 FILE=$(basename "$a")

 # make new directory if one doesn't already exist
 mkdir -p $TRANSFORMED $HOLDING $FINAL $TRASH

 # convert the image and set the background colour
 echo "Converting $FILE"
 #inspired by http://greyproc.blogspot.co.uk/2012/03/batch-straighteningdeskewing-and.html
 #convert $a -set filename:f "%t" -background '#c7b39b' -deskew 60%  $HOLDING/%[filename:f].jpg;

 convert $a -set filename:f "%t" -background '#000000' -deskew 60%  $HOLDING/%[filename:f].jpg;

 # run the multicrop script
 echo "Running crop tool"

 $var/scripts/shell/./multicrop.sh -u 3 $HOLDING/$FILE $FINAL/$FILE
done

# because the multicrop keeps the greyscale patch as a seperate image we need to move
# image < 400px to a trash folder

echo "Running move script"
$var/scripts/shell/./move.sh $1