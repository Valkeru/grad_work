FROM ubuntu:bionic
WORKDIR /var/www
RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y unzip git netcat wget mysql-client less vim \
         php7.2-fpm php7.2-cli php7.2-curl php7.2-json php7.2-mysql php7.2-mbstring php7.2-xml php7.2-zip \
    && php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" \
    && php /tmp/composer-setup.php --install-dir=/usr/bin --filename=composer \
    && rm /tmp/composer-setup.php

RUN rm /etc/localtime && ln -s /usr/share/zoneinfo/Europe/Moscow /etc/localtime

RUN wget -qP /tmp/ https://github.com/just-containers/s6-overlay/releases/download/v1.21.4.0/s6-overlay-amd64.tar.gz \
    && tar xzf /tmp/s6-overlay-amd64.tar.gz -C / && rm /tmp/s6-overlay-amd64.tar.gz

RUN wget -qP /tmp/ https://bitbucket.org/ValkeruFox/grpc-pack/downloads/valkeru-protobuf-pack_20180612-git.master-1_amd64.deb \
    && dpkg -i /tmp/valkeru-protobuf-pack_20180612-git.master-1_amd64.deb \
    && rm /tmp/valkeru-protobuf-pack_20180612-git.master-1_amd64.deb

ADD cont-init.d /etc/cont-init.d

#s6-overlay init
ENTRYPOINT ["/init"]
