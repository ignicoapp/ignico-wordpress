# Contribution guide
I’m really excited that you are interested in contributing to Ignico for WordPress. Before submitting your contribution though, please make sure to take a moment and read through the following guidelines.

## Development setup

### Clone project
```
git clone git@github.com:ignicoapp/ignico-wordpress.git
cd ignico-wordpress
```

### Copy dotenv and fill with your properties
```
cp .env.example .env
```

### Install dependencies
```
composer install
```
During installation WordPress is downloaded to wordpress directory and current directory is self symlinked to wordpress/wp-content/plugins. Pointing your webserver vhost to wordpress directory give you fully working WordPress instance with Ignico WordPress plugin installed.

### Setup WordPress
```
./vendor/bin/phing wp:init
```

This command will install WordPress with configuration from .env file. After installation you should have fully working WordPress instance with Ignico WordPress plugin activated.

### Code inspection and tests
Be sure to execute code inspection and test before before making a pull request.
```
./vendor/bin/phing inspect tests
```
