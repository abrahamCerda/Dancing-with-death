# Dancing-with-death
Laravel 5.6 + VueJs 2.x app for the Application Challenge Full-Stack Dev-Asimov Consultores. It uses the following external libraries:

+ MomentJs: https://momentjs.com/ .
+ SweetAlert: https://sweetalert.js.org .

# Demo
The demo of the app is available in: https://dancing-with-death.herokuapp.com/ . It uses the ClearDb add-on for the database service. 

# Running on your machine
To run the project in your machine, you have to do the following tasks:

	1) Clone the repo in your machine.
	2) In the project folder, run: composer install.
	3) In the project folder, run: npm install.
	4) In the project folder, create the .env file using the .env.example file template, and complete it with your own machine configuration.
	5) Generate the app_key, by running: php artisan key:generate in the project folder.
	6) Generate the schema of the database, by running: php artisan migrate in the project folder.
	7) Run: php artisan serve to run the app in localhost:8000. 


