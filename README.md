# Setup
*  Run "composer install"
*  Ensure all files to be converted are in the `var/in` directory
*  To start converting files e.g convert from json to csv, Enter this command in the console   `bin/console trivago:convert hotels.json` 

* To run test and code coverage `./vendor/bin/simple-phpunit --coverage-html reports/` 

# Commands
*  `bin/console trivago:convert hotelValidate.json -s name -f stars`
* `bin/console trivago:convert hotelValidate.json -f stars` ===
`bin/console trivago:convert hotelValidate.json --filter stars`