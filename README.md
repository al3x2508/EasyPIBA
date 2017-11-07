# EasyPIBA - A lightweight PHP framework

In order to install EasyPIBA you need to follow these instructions:

1. Download from github using git clone https://github.com/al3x2508/EasyPIBA.git or download a zip file from here
2. Go to your url where you have the project, for example http://www.myurl.com/
3. Go through setup
4. Delete the setup/ folder from the application folder
5. Customize your template by editing the templates/template.html and css/main.css
6. Customize your app
7. Your urls are loaded from the pages table from your database or from the Module folder in the application root directory; If you want to create a static html page you would need to create it from the administration section. If you want to create a dynamic content page (eg: my-orders page) you would need to create a module folder in your Module folder with a Page class (eg: Shop/Page.class.php);
 

Let's say you want to build a simple e-commerce application. Your shop should have the following structure:

Users that are looking for Products inside your shop that are organized into Categories. After they found the specified products they add them into carts and finally are placing Orders.

1. Download the demo sql file for this structure from http://www.easypiba.com/Demo/demo.sql (works only with MySQL >= 5.6).
2. Download from http://www.easypiba.com/Demo/Module.zip the modules used for this demo.
3. Unzip the contents of the zip you downloaded in your Module folder
4. Add the new modules in the database
   - Run the following command: php admin/modules.php reread
   - If you don't have access to CLI then add the following records in your application database:
INSERT INTO `modules` (`name`, `has_frontend`, `has_backend`) VALUES ('Demo', 1, 0), ('Categories', 0, 1), ('Orders', 0, 1), ('Products', 0, 1);
5. Go to http://YourUrl/demo-shop address to generate the sample records for the demo shop
6. Take a look at the administration section and the files from the Module folder
