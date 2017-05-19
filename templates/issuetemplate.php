### Server configuration
#### Operating system:
&nbsp;&nbsp;&nbsp;&nbsp;<?php p($_['os']); ?>

#### Web server:
&nbsp;&nbsp;&nbsp;&nbsp;<?php p($_['webserver']); ?>

#### Database:
&nbsp;&nbsp;&nbsp;&nbsp;<?php p($_['dbserver']); ?>

#### PHP version:
<?php
        $parts = explode ('Modules loaded: ', $_['php']);
        $phpVersion = $parts[0];
        $modules = explode (', ', $parts[1]); ?>

&nbsp;&nbsp;&nbsp;&nbsp;<?php p($phpVersion); ?>
<details>
        <summary>Modules loaded:</summary>

```
<?php
        foreach ($modules as $module) {
                p(" - " . $module . "\n");
        } ?>
```
</details>

#### Nextcloud version:
&nbsp;&nbsp;&nbsp;&nbsp;<?php p($_['version']); ?>

#### Updated from an older Nextcloud/ownCloud or fresh install:
&nbsp;&nbsp;&nbsp;&nbsp;YOUR ANSWER HERE

#### Where did you install Nextcloud from:
&nbsp;&nbsp;&nbsp;&nbsp;<?php p($_['installMethod']); ?> YOUR ANSWER HERE

#### Signing status:
<details>
        <summary>Signing status</summary>

```
<?php print_unescaped(print_r(json_encode($_['integrity'], JSON_PRETTY_PRINT), true)); ?>

```
</details>

#### List of installed apps:

<details>
        <summary>Enabled:</summary>

```
<?php
        foreach ($_['apps']['enabled'] as $name => $version) {
                p(" - " . $name . ": " . $version . "\n");
        } ?>
```
</details>

<details>
        <summary>Disabled:</summary>

```
<?php
        foreach ($_['apps']['disabled'] as $name => $version) {
                p(" - " . $name . "\n");
        } ?>
```
</details>

#### The content of config/config.php:
<details>
        <summary>Config report</summary>

```
<?php print_unescaped(print_r(json_encode($_['config'], JSON_PRETTY_PRINT), true)); ?>

```
</details>

#### Are you using external storage, if yes which one:
&nbsp;&nbsp;&nbsp;&nbsp;<?php print_unescaped(print_r($_['external'], true)); ?>

#### Are you using encryption:
&nbsp;&nbsp;&nbsp;&nbsp;<?php p($_['encryption']); ?>

#### Are you using an external user-backend, if yes which one:
&nbsp;&nbsp;&nbsp;&nbsp; YOUR ANSWER HERE (LDAP/ActiveDirectory/Webdav/...)

<?php if(array_key_exists('user_ldap', $_['apps']['enabled'])) { ?>
#### LDAP configuration (delete this part if not used)
<details>
        <summary>LDAP config</summary>

```
With access to your command line run e.g.:
sudo -u www-data php occ ldap:show-config
from within your Nextcloud installation folder

Without access to your command line download the data/owncloud.db to your local
computer or access your SQL server remotely and run the select query:
SELECT * FROM `oc_appconfig` WHERE `appid` = 'user_ldap';

Eventually replace sensitive data as the name/IP-address of your LDAP server or groups.
```
</details>
<?php } ?>

### Client configuration
#### Browser:
&nbsp;&nbsp;&nbsp;&nbsp;<?php p($_['browser']); ?>

#### Operating system:
&nbsp;&nbsp;&nbsp;&nbsp;YOUR ANSWER HERE

### Logs
#### Web server error log
<details>
        <summary>Web server error log</summary>

```
Insert your webserver log here
```
</details>

#### Nextcloud log (data/nextcloud.log)
<details>
        <summary>Nextcloud log</summary>

```
Insert your Nextcloud log here
```
</details>

#### Browser log
<details>
        <summary>Browser log</summary>

```
Insert your browser log here, this could for example include:

a) The javascript console log
b) The network log
c) ...
```
</details>
