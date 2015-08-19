# DocRaptor API PHP Wrapper

## 1.2.0
* Add `setDocumentPrinceOptions` method to allow further control of PDF output DocRaptor's [supported prince options](https://docraptor.com/documentation#pdf_options)

## 1.1.1
* Change documentation to use a live test API key
* Fix some syntax issues that were causing problems
* Add an example script to test the live site to make sure basic document creation is working

## 1.1.0 - YANKED
* Add documentation for submitting issues and PRs
* [fix psr-4 compatibility](https://github.com/expectedbehavior/php-docraptor/pull/33). Could be a breaking bug so we are minor version bumping.

## 1.0.1
* Added this changelog
* [Added custom User Agent](https://github.com/expectedbehavior/php-docraptor/pull/31) - `expectedbehavior_php-docraptor/<version> PHP/<php-version>`
* Added `Config` object for future refactoring and (right now) enable/disable of user agent reporting

## 1.0.0
* First official release
* Support for basic synchronous DocRaptor API calls
