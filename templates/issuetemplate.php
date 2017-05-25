### General server configuration

**Operating system:** <?php p($_['os']); ?>


**Web server:** <?php p($_['webserver']); ?>


**Database:** <?php p($_['dbserver']); ?>


**PHP version:** <?php
        $parts = explode ('Modules loaded: ', $_['php']);
        $phpVersion = $parts[0];
        $modules = explode (', ', $parts[1]);
        p($phpVersion);?>
<details>
        <summary>PHP-modules loaded</summary>

```
<?php
        foreach ($modules as $module) {
                p(" - " . $module . "\n");
        } ?>
```
</details>

### Nextcloud configuration

**Nextcloud version:** <?php p($_['version']); ?>


**Updated from an older Nextcloud/ownCloud or fresh install:** YOUR ANSWER HERE


**Where did you install Nextcloud from:** <?php p($_['installMethod']); ?> YOUR ANSWER HERE


**Are you using external storage, if yes which one:** <?php print_unescaped(print_r($_['external'], true)); ?>


**Are you using encryption:** <?php p($_['encryption']); ?>


**Are you using an external user-backend, if yes which one:** YOUR ANSWER HERE (LDAP/ActiveDirectory/Webdav/...)

<details>
        <summary>Signing status</summary>

```
<?php print_unescaped(print_r(json_encode($_['integrity'], JSON_PRETTY_PRINT), true)); ?>

```
</details>

<details>
        <summary>Enabled apps</summary>

```
<?php
        foreach ($_['apps']['enabled'] as $name => $version) {
                p(" - " . $name . ": " . $version . "\n");
        } ?>
```
</details>

<details>
        <summary>Disabled apps</summary>

```
<?php
        foreach ($_['apps']['disabled'] as $name => $version) {
                p(" - " . $name . "\n");
        } ?>
```
</details>

<details>
        <summary>Content of config/config.php</summary>

```
<?php print_unescaped(print_r(json_encode($_['config'], JSON_PRETTY_PRINT), true)); ?>

```
</details>

<?php if(array_key_exists('user_ldap', $_['apps']['enabled'])) { ?>
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

**Browser:** <?php p($_['browser']); ?>


**Operating system:** YOUR ANSWER HERE

### Logs

<details>
        <summary>Web server error log</summary>

```
Insert your webserver log here
```
</details>

<details>
        <summary>Nextcloud log (data/nextcloud.log)</summary>

```
Insert your Nextcloud log here
```
</details>

<details>
        <summary>Browser log</summary>

```
Insert your browser log here, this could for example include:

a) The javascript console log
b) The network log
c) ...
```
</details>
