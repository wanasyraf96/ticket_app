sail-up:
	./vendor/bin/sail up -d --remove-orphans

sail-down:
	./vendor/bin/sail down -v

artisan-migrate:
		docker exec ticket_app-laravel.test-1 php artisan migrate
artisan-migrate-seed:
		docker exec ticket_app-laravel.test-1 php artisan migrate --seed

artisan-migrate-fresh:
		docker exec ticket_app-laravel.test-1 php artisan migrate:fresh
