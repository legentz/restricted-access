# Restricted-access

Restrict access of a Wordpress based website using the Google Sign-In button and checking user's email domain (G-Suite or G-Apps).

### Make it works

Add this line right after the **<?php** declaration:
```
require( dirname( __FILE__ ) . '/restrict-access.php' );
```

Make sure you have placed all the files in the main Wordpress folder. By 'all the files' I mean:
- google-api-php-client-2.1.1 (folder)
- gapps-login.php
- restrict-access.php

Move credentials.json into:
> google-api-php-client-2.1.1/json/credentials.json

Be sure you have **PHP >= 5.4**

### Login page
Horrible example that have to be changed... But pay attention to php variables!

### Live example
> http://leandrogentili.me/swag-blog/