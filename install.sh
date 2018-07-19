cp .env.example .env && cd laradock && docker-compose up -d nginx mysql phpmyadmin && \
sudo echo "127.0.0.1    mysql" >> /etc/hosts && \
cp .env.example .env && cd ..  && \
composer install && \
php artisan key:generate && php artisan migrate --seed && php artisan passport:install