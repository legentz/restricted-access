# Restricted-access

Restrict access of a Wordpress based website using the Google Sign-In button and checking user's email domain (G-Suite or G-Apps).

### Make it works

Add this line right after the **<?php** declaration:
```
require( dirname( __FILE__ ) . '/restrict-access.php' );
```

Make sure you have placed all the files places as mentioned by their folder name.

**Update the Redirect URI**, download and rename the JSON as below. Move it into:
> google-api-php-client-2.1.1/json/credentials.json

Be sure you have **PHP >= 5.4**

### Login page
Horrible example that have to be changed... But pay attention to php variables!

### Logout
It's possible to manually logout, if needed:

> ?logout=true

### Flow
Blog (local) -> Google Sign-in (public) ->
Redirect URI (public) -> Auth by the public server (the one who's called by the Redirect URI) ->
Javascript link (public) -> window.open() (local) -> Blog (local) or Error