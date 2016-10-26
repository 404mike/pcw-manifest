#!/bin/bash
# @author Mike Jones - National Library of Wales

# Script to move greyscale patches to another directory

var=$(pwd)

BASE="$var/llgc_$1/temp"
DIR="$var/llgc_$1/"
TRANSFORMED=$BASE"/transformed"
HOLDING=$TRANSFORMED"/holding"
FINAL=$TRANSFORMED"/final"
TRASH=$FINAL"/trash"
WEBREADY="$var/llgc_$1/web_ready"

# Loop through all jpg
for a in $FINAL/*.jpg
do

 FILE=$(basename "$a")

 # make trash directory if there isn't one already
 mkdir -p $TRASH
 # get the width of the image
 width=`identify -format "%w" $a`
 height=`identify -format "%h" $a`
 
 # check to see if the width of the image is less than 400px
 # if it is move the image to the trash directory
 if [ $width -lt 500 ] || [ $height -lt 500 ]; then

  original_string=$FILE
  new_string=''
  result_string="${original_string/transformed\/final\//$new_string}"
  echo $result_string

  mv $a $TRASH/$result_string
 fi
done

 # rename the file
 # the transform script will generate files with the following naming convention
 # $file-001.jpg or $file(1)-001.jpg - what we want is the original filename 
 # replace any (1) and -001 strings in the filename
 echo "Rename files"
 rename -v 's/(.?)(\(\d\))?-(\d{3})/$1/' $FINAL/*

 # cp "$FINAL/*.jpg $WEBREADY/"
