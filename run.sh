#!/bin/bash
# @author Michael Jones - mij@llgc.org.uk

# Usage help
usage()
{
cat << EOF
usage: $0 options

Usage ./run.sh -t single -i 123

OPTIONS:
   -h      Show this message
   -t      Type - multipart|single
   -i      Manifest ID
EOF
}

# Get the images from the server
# resize, crop and remove greyscale
# place images in ./web_ready
get_images()
{
  # create holding directory for the images
  mkdir -p "llgc_$ID/web_ready/"

  # mkdir -p "llgc_$ID/temp/"

  # Download the images
  php scripts/php/main.php "images " $TYPE $ID

  # resize the images
  # scripts/shell/transform.sh $ID
}


# Generate a PCW csv file for ingest
generate_csv()
{
  php scripts/php/main.php "csv " $TYPE $ID
}


# Check that the parameters are valid
check_valid()
{
  # Check that the TYPE is correct
  if [ "$TYPE" != "single" ] && [ "$TYPE" != "multipart" ]
  then
    echo "not a correct type - Make sure you pass 'single' or 'multipart'"
    exit 1
  fi

  # check that the ID is a number
  re='^[0-9]+$'
  if ! [[ $ID =~ $re ]] ; then
     echo "error: Not a number make sure that '-i' contains a number" >&2; exit 1
  fi

  # Download images
  get_images

  # create csv
  generate_csv
}


# Run through command line arguments
TYPE=
ID=
while getopts ":t:i:" OPTION
do
     case $OPTION in
         t)
             TYPE=$OPTARG
             ;;
         i)
             ID=$OPTARG
             ;;
         ?)
             usage
             exit
             ;;
     esac
done

# Check that the values passed are valid
check_valid 
