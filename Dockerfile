FROM ubuntu:16.04

RUN apt-get update && \
    apt-get install -y apache2 php7.0 libapache2-mod-php7.0 wget unzip && \
    a2enmod rewrite && \
    wget -c https://github.com/struthio/phpOregonI600/archive/master.zip && \
    unzip /master.zip && \
    mv /phpOregonI600-master /var/www/html/mds && \
    ln -s /var/www/html/mds/config.php.sample /var/www/html/mds/config.php && \
    sed -i "2i<Directory /var/www/html>\nOptions Indexes FollowSymLinks MultiViews\nAllowOverride All\nOrder allow,deny\nallow from all\n</Directory>" /etc/apache2/sites-available/000-default.conf && \
    apt-get remove --purge -y wget unzip && \
    apt-get clean -y && \
    apt-get autoclean -y && \
    apt-get autoremove -y && \
    rm -rf /var/lib/{apt,dpkg,cache,log}

CMD ["apachectl", "-DFOREGROUND"]

EXPOSE 80

