################################################################################
#
# * This file is part of SplashSync Project.
# *
# * Copyright (C) Splash Sync <www.splashsync.com>
# *
# * This program is distributed in the hope that it will be useful,
# * but WITHOUT ANY WARRANTY; without even the implied warranty of
# * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
# *
# * For the full copyright and license information, please view the LICENSE
# * file that was distributed with this source code.
# *
# * @author Bernard Paquier <contact@splashsync.com>
#
################################################################################

################################################################################
#
# Update Composer Dev requirements to Latest Standard Config
#
################################################################################

################################################################################
# PHPUNIT - Functional Unit Tests
composer require -q --dev --no-update       "phpunit/phpunit"                           "~7.0|~8.0|~9.0"

################################################################################
# GRUMPHP - Automated Unified Testing Tool
composer require -q --dev --no-update       "phpro/grumphp"                             "^0.19|^1.0"
composer require -q --dev --no-update       "composer/composer"                         "^2.0"
composer require -q --dev --no-update       "sensiolabs/security-checker"               "^5.0"

################################################################################
# Files Lint Tools
composer remove -q -n --dev                 "jakub-onderka/php-parallel-lint"
composer require -q --dev --no-update       "php-parallel-lint/php-parallel-lint"       "^1.0"
composer require -q --dev --no-update       "sclable/xml-lint"                          "^0.3"
composer require -q --dev --no-update       "seld/jsonlint"                             "^1.7"
composer require -q --dev --no-update       "j13k/yaml-lint"                            "^1.1"

################################################################################
# PHP - Quality Tools
composer require -q --dev --no-update       "friendsofphp/php-cs-fixer"                 ">2.10"
composer require -q --dev --no-update       "phpmd/phpmd"                               "^2.6"
composer require -q --dev --no-update       "sebastian/phpcpd"                          ">3.0"
composer require -q --dev --no-update       "squizlabs/php_codesniffer"                 "^3.2"

################################################################################
# PHPSTAN - Static Analyze
composer require -q --dev --no-update       "phpstan/phpstan"                           "^0.12"
composer require -q --dev --no-update       "phpstan/phpstan-phpunit"                   "^0.12"

