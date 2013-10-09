# Changelog

<table>
	<tr>
		<th>Project:</th>
		<td id="meta_project">Bedrock Framework</td>
	</tr>
	<tr>
		<th>Version:</th>
		<td id="meta_version">0.3.1</td>
	</tr>
	<tr>
		<th>Build:</th>
		<td id="meta_build">80</td>
	</tr>
	<tr>
		<th>Released:</th>
		<td id="meta_released">08-10-2012</td>
	</tr>
</table>

## Version 0.3.1 \[10-09-2013\]

Minor release, adding composer support.

> ### Improvements
> * Added initial version of composer.json in order to support loading via
>   Composer.

## Version 0.2.1 Build 79 \[03-15-2010\]

Maintenance release, fixing some minor bugs related to the installer.

> ### Bugs
> * Fixed bug with logger settings not being saved during installation.
> * Fixed bug where defining a custom namespace did not result in correctly
>   generated source files and configuration settings.

## Version 0.2.0 Build 69 \[07-02-2009\]

Second public alpha release. Changes include improved importing/exporting of
model layer data and structure. Additoin of some communication services as well
as the expansion of the data API.

> ### Bugs
> * Fixed bug where YAML indentation would not be consistent in the YAML data
>   class. Nested arrays and other data types are now handled properly.
> 
> ### Improvements
> * PHP error reporting is now configurable through the main config file.
> * Controller delegation has been expanded to support multiple class
>   hierarchies. For example, if the application doesn't have a matching
>   controller, it should look for a built-in Bedrock controller, etc.
> * Added CSV, XML, and YAML support to the model layer for importing and
>   exporting data and structure.
> * Changed the current implementation of the logger to use "target" classes
>   instead of "output streams."
> * Added a common "Target" interface that can be implemented to add
>   third-party support to the logger.
> 
> ### New Features
> * Added sample application files to framework files, including recommended
>   directory structure.
> * Added initial version of the plugin API.
> * Added basic user authentication support with initial support for
>   database-based authentication.
> * Added support for REST based services.
> * Added a Twitter service mapping all official Twitter API calls.

## Version 0.1.2 Build 58 \[05-07-2009\]

A number of bug fixes for the installer were added, as well as the introduction
of a unified data formatting library.

> ### Bugs
> * Updated installer UI for improved compatibility with Internet Explorer.
> * Shortened the timezone dropdown in the installer so that it doesn't
>   overflow its container.
> * Fixed problem with installer navigation breaking after viewing step 4.
>
> ### Improvements
> * Added automatic creation of base application classes and files during
>   installation.
> * Added permissions check to installer validation process following
>   user-specified directories (step 2).
>
> ### New Features
> * Added a unified library for handling data formats (to replace other
>   classes and reduce redundancy). Includes support for CSV, JSON, and YAML.

## Version 0.1.1 Build 49 \[04-16-2009\]

An incremental release, adding a simple installer for basic installations.

> ### New Features
> * Added an installer app that automates basic configuration settings.

## Version 0.1.0 Build 41 \[03-30-2009\]

Initial public alpha release of the framework. Includes the basic core
components as well as some extras.
