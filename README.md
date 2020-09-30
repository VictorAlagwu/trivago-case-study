# Setup
*  Run "composer install"
*  Ensure all files to be converted are in the `var/in` directory
*  To start converting files e.g convert from json to csv, Enter this command in the console   `bin/console trivago:convert hotels.json` 

# PHPUnit Testing
* To run test and code coverage `./vendor/bin/simple-phpunit --coverage-html reports/` 
# Commands
### Required Inputs
* `bin/console trivago:convert {filename}`
### Optional Inputs
* `-f {argument}` OR `--filter {argument}` 
* `-s {argument}` OR `--sort {argument}`
* `-g {argument}` OR `--group {argument}`
# Using Commands
*  `bin/console trivago:convert hotels.json --sort name -f stars` OR 
`bin/console trivago:convert hotels.json -s name -f stars`
* `bin/console trivago:convert hotels.json -f stars` OR
`bin/console trivago:convert hotels.json --filter stars`
* `bin/console trivago:convert hotels.xml -g stars` OR `bin/console trivago:convert hotels.xml --group stars`

# Guide
 * When using the `-f` or `--filter` optional input argument, it's required that a value is added.
 ![Using Filter Argument](public/images/filterexample.png "Example Images")

 * Using `-s` or `--sort` 
  ![Using Sorting Argument](public/images/sortexample.png "Example Images")