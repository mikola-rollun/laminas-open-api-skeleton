FROM maxrollundev/php-fpm-dev-8.0

# RUN apt-get -y install gcc make autoconf libc-dev pkg-config
# RUN apt-get -y install libyaml-dev

# RUN pecl install yaml

# RUN bash -c "echo extension=yaml.so > /usr/local/etc/php/conf.d/yaml.ini"
# RUN service php restart
# RUN bash -c "echo extension=yaml.so > /usr/local/etc/php/conf.d/yaml.ini"

# RUN pecl install yaml


# RUN apt update && apt-get -y install php8.0-yaml

RUN apt-get update -y
RUN apt-get install libyaml-dev -y
RUN  pecl install yaml && echo "extension=yaml.so" > /usr/local/etc/php/conf.d/ext-yaml.ini && docker-php-ext-enable yaml