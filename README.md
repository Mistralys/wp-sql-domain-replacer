# Wordpress SQL Domain Replacer

PHP script that can be used to safely replace a domain name in a WordPress SQL dump. 

Useful when migrating to a new domain, or to convert a live site dump to a local development database.

## Safe conversion

WordPress uses serialized arrays in some database columns, so a simple search & replace does not work (unless the new domain has exactly the same character length as the new one). The script adjusts serialized arrays as well, so that the converted SQL is entirely functional.

## Usage

[Download the PHP file](https://raw.githubusercontent.com/Mistralys/wp-sql-domain-replacer/master/wp-sql-domain-replacer.php) (right-click > Save as)

Move the file to the target folder, edit it to adjust the configuration, save it and you're good to go.

You may call it from a browser (if it's accessible in the webserver), or via the command line, like this (if you have PHP in your path):

```
php wp-sql-domain-replacer.php
```

The script creates a new file, `output-domainname.sql` where `domainname` is the target domain name.
