# QComb - generator of random combinations of characters.
Mostly used for passwords.

(v1.0)

### Prerequisites:
- Linux
- PHP >= 7.2
- PHP extensions:
  - php-xml (for html & xml work)
  - php-gmp (for arbitrary-length integers work)

- a web server (tested with Apache, IIS)
- unsupported browsers:
  - Internet Explorer

### Installation:

1. Unpack the archive into web server root or some other place which is accessible by the web server (additional web server configuration may be necessary in the latter case).
2. Set recursively read&write rights for the web server user (apache, www-data, etc.) on the directory (chown -R).
  - If your Linux uses SELinux (Red Hat like), use such commands:
```
chcon -R -t httpd_sys_content_t /path-to web-content/qcomb
chcon -R -t httpd_sys_rw_content_t /path-to web-content/qcomb
```
(The above applies only to writing the log file, if you don't use that option you will never need it.)

### Access the web interface by the following link:

http(s)://IP_OR_SERVER_ADDRESS/qcomb

### To use the script only:

  - Enter the qcomb directory
  - edit id2passgenerate.ini for the settings
  - type:
```
php id2passgenerate.php --help
```

### Usage (both web & script):

You need to define a character set, requirements, number of combination characters, number of combinations and uniqueness necessity.

There are defaults which values are in the web controls. The character set is limited to the 95 ascii printables (for now).

There is config.xml file in which you can set debug logging in a log file (qlog.txt). It is off by default and not recomended for ordinary use.

You can download the results into tab delimited csv file on your computer.

Unique for the script - it uses an input file with id-s and generates a tab delimited csv with the results mapped by id-s (it is for usefulness, id-s can be serial numbers, computer names, usernames, etc.)

### Short description of the software:

It uses an algorithm based on considering combinations of characters as numbers in a specific bijective number system.
The bijective feature (without character meaning zero - 0) assures comprehensiveness of the connection combination - number.
Then the program chooses the margins - from user decision and gets a random number, which represents as a combination of characters.
The randomness is cryptographically secure due the usage of PHP random_bytes function for generation which is officially documented as CS.
