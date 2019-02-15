
install: init book

init: docker
	docker-compose exec web bash -c "composer install"
	docker-compose exec web bash -c "php bin/console d:s:u --force"
	docker-compose exec web bash -c "php bin/console hautelook:fixtures:load -q"

docker: .env
	docker-compose up -d

.env:
	cp .env.dist .env;

book:docker-compose.yml
	#
	# Projet Conferences
	#
	# Application: http://localhost:8000
	# phpMyAdmin: http://localhost:8080
	# Mailhog: http://localhost:8025
	#
