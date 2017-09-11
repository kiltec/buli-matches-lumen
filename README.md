# BuLi-Matches

## Description
A small project to explore Laravel Lumen, the [OpenLigaDB API](https://www.openligadb.de/) and to hone my skills,
implemented using a top-down TDD-approach.

It shows two different match overviews from the German Bundesliga which are based on data requested form the OpenLigaDB API.

## How To Get It Running

### Prerequisites
You need to have PHP7, [Composer](https://getcomposer.org/) and [Vagrant](https://www.vagrantup.com/) installed in order
to launch the development environment.

### Installation

- Clone this repository
- Open a terminal and go to the folder of your clone and:

    - `cp .env.DIST .env`
    - `cp Homestead.DIST.yaml Homestead.yaml`
    - Open `Homestead.yaml` in your favorite editor
    - Replace the path in `map: /home/lars/projects/private/buli-matches` with the path to your clone of this repo.
    - Edit your `/etc/hosts/` by adding `192.168.10.10	buli-matches.dev`. If that IP is already taken, change it in `Homestead.yaml`
    - Run `composer install`, at this step you might have to install some PHP modules.
    - Now run `vagrant up` in the folder of your clone, this might take a while to finish
    - Open a browser and navigate to (http://buli-matches.dev/)
    - *Tadaaa*
    
## Run The Tests

### All Tests
Enter the vagrant box

`cd Code`

`phpunit`

### No External Tests
In order to skip the tests hitting the API run, in same location as above run:

`phpunit --exclude external`

### During Development
 Too lazy to manually run the tests after each change? Then run:
 
 `vendor/bin/phpunit-watcher watch`
 
 By default the watcher excludes the external tests if you wish to run them:
 
 `vendor/bin/phpunit-watcher watch --include external`
 
## Contributing
I'd appreciate any feedback about this code! Other than that, however, there's no point to contribute to this project... :)
