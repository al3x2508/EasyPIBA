<p align="center"><img src="http://www.easypiba.com/img/easypiba.png"></p>

# EasyPIBA - A lightweight PHP framework

Install EasyPIBA
In order to install EasyPIBA you need to follow these instructions:

1. Choose one of the following methods:
   - Download from github using ```git clone [https://github.com/al3x2508/EasyPIBA.git]() .``` or
   - Download a zip file from https://github.com/al3x2508/EasyPIBA/archive/master.zip and unzip it in your html folder or
   - Run the following command ```wget -O /tmp/z.$$ https://github.com/al3x2508/EasyPIBA/archive/master.tar.gz && tar xvf /tmp/z.$$ -C /var/www/html/dbf --strip-components 1 && rm /tmp/z.$$```
2. Go to your url where you have the project, for example http://www.myurl.com/
3. Go through setup
4. Delete the setup/ folder from the application folder
5. Customize your template by editing the templates/template.html and css/main.css
6. Customize your app
7. Your urls are loaded from the pages table from your database or from the Module folder in the application root directory; If you want to create a static html page you would need to create it from the administration section. If you want to create a dynamic content page (eg: my-orders page) you would need to create a module folder in your Module folder with a Page class (eg: Shop/Page.class.php);

Let's say you want to build a simple e-commerce application. Your shop should have the following structure:

Users that are looking for Products inside your shop that are organized into Categories. After they found the specified products they add them into carts and finally are placing Orders.

1. Download from http://www.easypiba.com/Demo/Module.zip the modules used for this demo.
2. Unzip the contents of the zip you downloaded in your Module folder
3. Go to ```Clear modules``` cache administration section and click ```Reread modules```
4. Go to http://YourUrl/demo-shop address to generate the sample records for the demo shop
5. Take a look at the administration section and the files from the Module folder

# Documentation - Create your own modules
Go to http://www.easypiba.com/creating-modules
