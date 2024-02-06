
Welcome to the Snow Tricks project:

Here are the steps to set up the project:

1 - Retrieve the project.

Clone the project from the following URL:

https://github.com/Sh1r0w/P6_Snowtricks.git

or download it here:

https://github.com/Sh1r0w/P6_Snowtricks/archive/refs/heads/master.zip

2 - Provide the necessary information:

Database Name:

In the .env file at the root of the project, at line 28, you will find "DATABASE_URL," which contains the name of the database (in this case, "snowtricks"). Modify it as needed before launching the project.

(Note: Any changes made after the project initialization may result in the loss of existing data.)

After the server name, you'll find the MySQL version; modify it as needed (e.g., "10.4.28").

Email:

In the same file, at line 32, you'll find the variable for email sending in this format:
"smtp://username:password@mail.server.smtp.provider.com:port"

For example, for a user at OVH, it could be represented as:

"smpt://jean-dupond:monsuperpassword@ssl0.ovh.fr:587"

Replace it with your credentials.

3 - Project Initialization:

Once the project is open in your editor (such as Visual Studio), open a terminal at the project's root and type the following command:

"composer install"

Then create the database with the command:

"symfony console doctrine:database:create"

And create the database schema with:

"php bin/console doctrine:schema:create"

4 - Load Sample Data:

To load the site with the first snowboard figures, use the following command:

"php bin/console doctrine:fixtures:load --no-interaction"

An administrator account will be automatically created:

Login: admin
Password: admin (password can be modified in the profile section)

5 - Launch the site: 
type the following command in the terminal 

"symfony server:start" 

and then connect to the address 127.0.0.1:8000.


