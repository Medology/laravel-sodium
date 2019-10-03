FROM chekote/php:7.1.32-composer

RUN apt-get update && \
    apt-get install -y --no-install-recommends --no-install-suggests \
        #
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-sodium && \
        #
    # Cleanup
    apt-get remove -y && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*
