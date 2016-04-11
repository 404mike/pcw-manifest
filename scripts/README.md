Some scripts that I use to convert tiff files and to crop and rotate images.

# tiffToJpg.sh
Converts all .tif files in a directory to .jpg

# transform.sh
Takes .jpg files, straightens the image and crops the picture, removing the grey scale patch. Uses http://www.fmwconcepts.com/imagemagick/multicrop/

# other
Remove greyscale patch from top of the image.
```shell
#!/bin/bash

for f in *.jpg
do
  convert $f -gravity North -trim +repage -chop 0x150 $f
done
```
