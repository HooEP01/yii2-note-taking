FROM ryusoft/jammy-yii2:main

ARG USER_ID
ARG GROUP_ID

RUN if [ ${USER_ID:-0} -ne 0 ] && [ ${GROUP_ID:-0} -ne 0 ]; then \
    userdel -f www-data &&\
    if getent group www-data ; then groupdel www-data; fi &&\
    groupadd -g ${GROUP_ID} www-data &&\
    useradd -l -u ${USER_ID} -g www-data www-data &&\
    install -d -m 0755 -o www-data -g www-data /home/www-data &&\
    chown www-data:www-data -Rf /var/lib/php  &&\
    chown www-data:www-data -Rf /home/www-data \
;fi

# Change document root for Apache
COPY ./site.conf /etc/apache2/sites-enabled/000-default.conf
WORKDIR /var/www/app