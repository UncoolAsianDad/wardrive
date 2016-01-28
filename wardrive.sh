#!/bin/bash


if [[ $# -eq 0 ]] ; then
    echo 'Specify your location'
    exit 0
fi

location=$1
sudo iw wlp3s0 scan |sed -e 's#(on w# (w#g' > "$location".raw
gawk -f scan.awk "$location".raw | sed 's/$/,'"$location"'/' > "$location".csv
cp "$location".csv payload.csv && mysql -uwifi wifi < sqlPush.sql && rm payload.csv
