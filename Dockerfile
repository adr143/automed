FROM php:8.0-fpm

# Install required PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Create nginx config file
RUN mkdir -p /etc/nginx/conf.d && \
    echo 'server { \
        listen 80; \
        server_name _; \
        root /var/www/html; \
        index index.php index.html; \
        \
        location / { \
            try_files $uri $uri/ /index.php?$query_string; \
        } \
        \
        location ~ \.php$ { \
            fastcgi_pass 127.0.0.1:9000; \
            fastcgi_index index.php; \
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
            include fastcgi_params; \
        } \
        \
        location ~ /\.ht { \
            deny all; \
        } \
    }' > /etc/nginx/conf.d/default.conf

# Create startup script
RUN echo '#!/bin/bash\nphp-fpm -D\nnginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
