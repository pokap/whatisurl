.PHONY: test update clean server load

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  test              to run unit tests php"
	@echo "  check             to check if server is ready to host apps"
	@echo "  composer-optimize dumps optimized autoloader"
	@echo "  install           to make a Composer install"
	@echo "  update            to make a Composer update"
	@echo "  clean             to remove and warmup cache"
	@echo "  assets            to install assets"
	@echo "  assets-symlink    to symlink assets"
	@echo "  assets-install    to install assets"
	@echo "  assets-watch      to watch assets"

test:
	phpunit

check:
	php app/check.php

composer-optimize:
	composer dump-autoload -o

install:
	composer install
	cd front && npm install

update:
	composer update

clean:
	rm -rf app/cache/*
	rm -rf app/logs/*

assets: assets-symlink assets-install

assets-symlink:
	app/console assets:install web --symlink

assets-install:
	cd front && bower install
	cd front && gulp publish

assets-watch: assets-install
	cd front && gulp watch
