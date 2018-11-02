
FROM php:5.6-fpm


RUN apt-get update \
	&& apt-get install -y \
		zip \
		unzip \
		vim \
		wget \
		curl \
		git \
		mysql-client \
		moreutils \
		dnsutils \
		zlib1g-dev \
		libicu-dev \
		libmemcached-dev \
		g++ \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Bucharest /etc/localtime
RUN "date"

RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql opcache intl


RUN usermod -u 1000 www-data

CMD php-fpm -F
