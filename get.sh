#!/bin/bash
# Loop through all the PIDS
while IFS='' read -r line || [[ -n "$line" ]]; do
    echo "Text read from file: $line"
    ./run.sh -i $line -t single
done < "$1"

# Merge Documents
#php scripts/php/src/merge_multiple_csv.php
