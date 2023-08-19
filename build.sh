#/bin sh
docker-compose build && docker-compose up -d && \
docker exec laravel-test1-api php -v && \
docker exec laravel-test1-api chmod -R 777 ./storage && \
docker exec laravel-test1-api composer install && \
docker exec laravel-test1-api cp -r ./.env.example ./.env && \
docker exec laravel-test1-api php artisan key:generate && \
docker exec laravel-test1-api php artisan migrate && \
docker exec laravel-test1-api php artisan test && \
docker exec laravel-test1-api npm install && \
docker exec laravel-test1-api npm run build && \
docker exec laravel-test1-api node -v