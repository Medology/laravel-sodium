ARG PHP_BASE_IMAGE_VERSION
FROM chekote/php:${PHP_BASE_IMAGE_VERSION}-composer

RUN apt-get update && \
    apt-get install -y --no-install-recommends --no-install-suggests \
        #
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-mbstring && \
        #
    # Cleanup
    apt-get remove -y && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*
