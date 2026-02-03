FROM debian:stable

RUN apt-get update
RUN apt-get install php-mysql -y
RUN apt-get install 7zip -y
RUN apt-get install composer -y

VOLUME /backend

WORKDIR /backend

RUN composer require lcobucci/jwt
RUN composer require lcobucci/clock
RUN openssl rand -out secret.key -base64 128

ENTRYPOINT ["php", "-S", "0.0.0.0:8000"]
