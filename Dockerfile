FROM  ubuntu

WORKDIR /var/www/html

RUN  apt-get update 
RUN  apt-get install -y apache2 
RUN  apt-get install nano 
RUN ln -fs /usr/share/zoneinfo/America/New_York /etc/localtime && \  
DEBIAN_FRONTEND=noninteractive apt-get install -y php libapache2-mod-php 
RUN  apt install -y php-cli 
RUN  apt install -y php-cgi 
RUN  apt install -y php-mysql
RUN  apt-get install -y systemctl
#RUN  systemctl restart apache2.service
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY . /var/www/html/kdms
COPY entrypoint.sh /sbin/entrypoint.sh

RUN  systemctl restart apache2.service

EXPOSE 80/tcp 
EXPOSE 443/tcp


# By default, simply start apache.
CMD ["/sbin/entrypoint.sh"]
