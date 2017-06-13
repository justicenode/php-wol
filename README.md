# PHP WakeOnLan
A simple PHP WakeOnLan UI

![Screenshot01](https://raw.githubusercontent.com/justicenode/php-wol/master/Doc/img/Screenshot01.png)

### How to install

###### Requirements

- A Linux Server/Machine
- A mysql Database
- A PHP capable webserver (Apache, nginx, ...)

###### Installation

1. Clone the repo and copy the `html` folder into your webroot
2. Change the lines below in `db.php` to fit your mysql setup

```
private $servername = "localhost";
private $username = "root";
private $password = "";
private $database = "php-wol";
```
###### Notes

The database gets created as soon as you load the page for the first time.
