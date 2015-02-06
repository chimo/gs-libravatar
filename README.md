Plugin to enable a [GNU social](http://gnu.io/social/) site to use avatar images from libravatar.org (or federated libravatar instances) rather than gravatar.

## Instructions

Make sure the files are in a folder called `Libravatar` if they're not already  
Put the folder in your `/plugins/` directory  
Tell `/config.php` to use it with the following:
```php
addPlugin('Libravatar', array());
```