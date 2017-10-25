# Issue Template

Nextcloud app to prefill github issue with current server information.

![Screenshot](https://raw.githubusercontent.com/juliushaertl/issuetemplate/master/screenshot.png)

Place this app in **nextcloud/apps/**

## Building the app

The app can be built by using the provided Makefile by running:

    make

This requires the following things to be present:
* make
* which
* tar: for building the archive
* curl: used if phpunit and composer are not installed to fetch them from the web
* npm: for building and testing everything JS, only required if a package.json is placed inside the **js/** folder

The make command will install or update Composer dependencies if a composer.json is present and also **npm run build** if a package.json is present in the **js/** folder. The npm **build** script should use local paths for build systems and package managers, so people that simply want to build the app won't need to install npm libraries globally, e.g.:

**package.json**:
```json
"scripts": {
    "test": "node node_modules/gulp-cli/bin/gulp.js karma",
    "prebuild": "npm install && node_modules/bower/bin/bower install && node_modules/bower/bin/bower update",
    "build": "node node_modules/gulp-cli/bin/gulp.js"
}
```


## Publish to App Store

First get an account for the [App Store](http://apps.nextcloud.com/) then run:

    make && make appstore

The archive is located in build/artifacts/appstore and can then be uploaded to the App Store.

## Running tests
You can use the provided Makefile to run all tests by using:

    make test

This will run the PHP unit and integration tests and if a package.json is present in the **js/** folder will execute **npm run test**

Of course you can also install [PHPUnit](http://phpunit.de/getting-started.html) and use the configurations directly:

    phpunit -c phpunit.xml

or:

    phpunit -c phpunit.integration.xml

for integration tests

## Integration for app developers

Apps will appear automatically in the issue template app once their appinfo.xml contains a `<bugs>` tag with an URL to the GitHub issue tracker.

Adding custom details to your issue report:
```
$dispatcher = \OC::$server->getEventDispatcher();
$dispatcher->addListener('\OCA\IssueTemplate::queryAppDetails', function(GenericEvent $event) {
    if($event->getArgument('app') === 'deck') {
        $manager = \OC::$server->query(\OCA\IssueTemplate\DetailManager::class);
        $section = new \OCA\IssueTemplate\Section('server-config', 'Server configuration');
        $section->createDetail('Operating system', php_uname());
        $section->createDetail('PHP version', PHP_VERSION);
        $manager->addSection($section);
    }
});
```

Sections provided by default can be disabled using `\OCA\IssueTemplate\DetailManager::removeSection($sectionName)` method. Currently the following 3 section names are provided by default:

- server-detail
- log-detail
- client-detail
