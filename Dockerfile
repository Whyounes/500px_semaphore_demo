FROM nimmis/apache-php5

MAINTAINER SemaphoreCI <dev@semaphoreci.com>

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
EXPOSE 443

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]