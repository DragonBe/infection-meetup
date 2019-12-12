FROM php:7.3-cli

# Getting some essentials
RUN apt-get update \
    && apt-get install -y \
      curl \
      git \
      unzip \
      zip

# Get the Xdebug extenions
RUN pecl install xdebug-2.7.2 \
    && docker-php-ext-enable xdebug

# Install composer globally
RUN bash -c 'curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer'

# Build it: docker build -t in2itqa:latest .
# Run phpcs: docker run --rm --name in2mutant -v "$PWD":/srv/phpapp -w /srv/phpapp in2itqa bash -c 'vendor/bin/phpcs'
# Run phpunit: docker run --rm --name in2mutant -v "$PWD":/srv/phpapp -w /srv/phpapp in2itqa bash -c 'vendor/bin/phpunit'
# Run infection: docker run --rm --name in2mutant -v "$PWD":/srv/phpapp -w /srv/phpapp in2itqa bash -c 'vendor/bin/infection --configuration=infection.json --coverage=build/coverage-xml'
