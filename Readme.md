Qasto PHP SDK
================

The [Qasto Webwall Platform](http://www.qasto.com/developer/apps/) is
a set of APIs that enable developers to create a paywall and create private services for premium social for their users. 
Read more about
[integrating Qasto with your web site](http://www.qasto.com/developer/apps/)
on the Qasto developer site.

This repository contains the open source PHP SDK that allows you to utilize the
above on your website. Except as otherwise noted, the Qasto PHP SDK
is licensed under the Apache Licence, Version 2.0
(http://www.apache.org/licenses/LICENSE-2.0.html)


Usage
-----

The [examples][examples] are a good place to start. The minimal you'll need to
have is:

    <?php

    require 'qasto.sdk.php';

    $qasto = new Qasto(array(
      'client_id'  => 'YOUR CLIENT ID',
      'client_secret' => 'YOUR CLIENT SECRET'
    ));

To make [API][API] calls:

 //User Infomation //
  
      $me = $qasto->api('info');
 
 //User Subscriptions //
     
     $me = $qasto->api('subscriptions');

Logged in vs Logged out:

    if ($qasto->getStatus()) {
      echo '<a href="' . $qasto->getLoginURL() . '">Logout</a>';
    } else {
      echo '<a href="' . $qasto->getLogoutURL() . '">Login</a>';
    }

[examples]: http://github.com/qasto/php-sdk/demo.php
[API]: http://www.qasto.com/developer/docs/api/


Feedback
--------

We are relying on the [GitHub issues tracker][issues] linked from above for
feedback. File bugs or other issues [here][issues].

[issues]: http://github.com/qasto/php-sdk/issues



