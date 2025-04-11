FROM php:8.4-fpm

# install git client, unzip libs pour les packages composer et artisan.
RUN apt-get update && apt-get install -y git libzip-dev unzip

# Je le mets sur une autre ligne, ca passe pas avec les autres...??
RUN apt-get install -y wget vim