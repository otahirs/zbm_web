# Views Plugin

The **Views** Plugin is for [Grav CMS](http://github.com/getgrav/grav) version 1.6+. This is a simple views count tracking plugin.  You can use it several ways, but by default it will automatically track all site page requests and use the **page route** as the identifying key.  There is no limiting, tracking, or refresh detection, it simply tracks the number of times a page has been loaded.

## Installation

Installing the Views plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

It has a requirement of the Grav **Database** plugin as it stores the views in a simple, file-based sqlite database file.  This will automatically be installed if you use GPM.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install views

## Requirements

Other than standard Grav requirements, this plugin requires the **Database** plugin, which in turn has some system requirements:

* **SQLite3** Database
* **PHP pdo** Extension
* **PHP pdo_sqlite** Driver

| PHP by default comes with **PDO** and the vast majority of linux-based systems already come with SQLite.  

## Configuration

Before configuring this plugin, you should copy the `user/plugins/views/views.yaml` to `user/config/plugins/views.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
autotrack: true    
```

The configuration options are as follows:

* `enabled` - enable or disable the plugin instantly
* `autotrack` - by default, views will track all pages using the `onPageInitialized` event, disable this to track manually

## Usage

The default behavior is for the **view** plugin to track all page requests and keep a running total of how many times the pages have been hit.  You can change this behavior by first _disabling_ the `autotrack:` configuration option, then using the Twig function to track a page hit, or if you want to track via another plugin, you can use a simple PHP command.

> In Views `1.0.1` we re-worked the database structure to allow multiple view types. If you installed and tested version `1.0.0`, please delete your `user/data/views/views.db` so it can be regenerated

### Twig Tracking

To track via Twig, you can use the default `track_views(id)` twig function, an `id` is required, and a `type` is optional because it defaults to `pages`.  For example, to track the current page from a twig template:

```twig
{% do track_views(page.route) %}
```

or specify a custom type

```twig
{% do track_views(page.route, 'widgets') %}
```

### PHP Tracking

To track via PHP, you can use the default `views` object with a required `id` attribute. A `type` is optional because it defaults to `pages`.  For example, to track the current page from a PHP file:

```php
Grav::instance()['views']->track($page->route());
```

or

```php
Grav::instance()['views']->track($page->route(), 'widgets');
```

## Viewing

Via the administrator plugin, you can view the current counts of the top 20 views by Visiting the **Reports** tab in the **Tools** section.  There will be an entry called `Grav Views`.  Alternatively you can query via the CLI or accessing the `Views` object directly and iterating over the values:

```php
Grav::instance()['views']->getAll(null, 20, 'desc')
```

## CLI Commands

There are currently two built in commands:

#### List (ls) Command

```bash
bin/plugin views ls
```

There are some optional parameters: 

```bash
bin/plugin views ls '/typography'
```

This will return the information for a specific `slug`

```bash
bin/plugin views ls --limit 10 --sort asc
```

This will return the information for a specific `type`

```bash
bin/plugin views ls --type widgets --limit 10 --sort asc
```

the `limit`, will limit the amount of rows returned, and the `sort` will can be either `asc` or `desc`

#### Set Command

The set command allows you to set a specific value for an entry:

```bash
bin/plugin views set /bar 17
```

Will set the number of views with id '/bar' to the value `17` and defaults to type `pages`

or 

```bash
bin/plugin views set /bar 17 foos
```

Will set the number of views with id '/bar' to the value `17` and defaults to type `foos`







