#!/bin/bash

find /var/www/epelia/public/img/products/174x136 -type f -exec /bin/rm -f {} \;
mogrify -path /var/www/epelia/public/img/products/174x136/ -resize 174x136 -gravity Center -fill white -quality 90 /var/www/epelia/public/img/products/original/*
find /var/www/epelia/public/img/products/90x68 -type f -exec /bin/rm -f {} \;
mogrify -path /var/www/epelia/public/img/products/90x68/ -resize 90x68 -gravity Center -fill white -quality 90 /var/www/epelia/public/img/products/original/*
find /var/www/epelia/public/img/products/380w -type f -exec /bin/rm -f {} \;
mogrify -path /var/www/epelia/public/img/products/380w/ -resize 380 -gravity Center -fill white -quality 90 /var/www/epelia/public/img/products/original/*
find /var/www/epelia/public/img/products/36x27 -type f -exec /bin/rm -f {} \;
mogrify -path /var/www/epelia/public/img/products/36x27/ -resize 36x27 -gravity Center -fill white -quality 90 /var/www/epelia/public/img/products/original/*
find /var/www/epelia/public/img/products/380x285 -type f -exec /bin/rm -f {} \;
mogrify -path /var/www/epelia/public/img/products/380x285/ -resize 380x285 -gravity Center -fill white -quality 90 /var/www/epelia/public/img/products/original/*
