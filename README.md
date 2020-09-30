# Setup
*  Run "composer install"
*  Ensure all files to be converted are in the `var/in` directory
*  To start converting files e.g convert from json to csv, Enter this command in the console   **`bin/console trivago:convert hotels.json`**
<br> 
# PHPUnit Testing
* To run test and code coverage **`./vendor/bin/simple-phpunit --coverage-html reports/`**

# Commands
### Required Inputs
* Default command: **`bin/console trivago:convert {filename}`**
### Optional Inputs
- Filtering data: <br> **`-f {argument}`** OR **`--filter {argument}`**
   
    - Usage:<br>  **`bin/console trivago:convert hotels.json -f stars`** (Then enter the required value)

- Sorting data: <br> **`-s {argument}`** OR **`--sort {argument}`**
  
    - Usage: <br> **`bin/console trivago:convert hotels.json -s name`**
* Grouping data: <br> **`-g {argument}`** OR **`--group {argument}`**
    - Usage: <br> **`bin/console trivago:convert hotels.json -s name`**
# Using Commands
- To filter and sort the hotels data: - 
<br>
**`bin/console trivago:convert hotels.json --sort name -f stars`** 
<br>OR
<br> 
**`bin/console trivago:convert hotels.json -s name -f stars`**
<br>
- Using filter and group -
<br>
**`bin/console trivago:convert hotels.json -f stars -g stars`** 
<br>OR
<br> 
**`bin/console trivago:convert hotels.json --filter stars --group stars`**
<br> 
- Using group and sorting hotel data:-
<br> 
**`bin/console trivago:convert hotels.xml -g stars --sort name`** 
OR **`bin/console trivago:convert hotels.xml --group stars -s name`**

# Guide
 * When using the **`-f`** or **`--filter`** optional input argument, it's required that a value is added.
 ![Using Filter Argument](public/images/filterexample.png "Example Images")

 * Using **`-s`** or **`--sort`** 
  ![Using Sorting Argument](public/images/sortexample.png "Example Images")