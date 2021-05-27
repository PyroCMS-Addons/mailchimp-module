# Mailchimp for PyroCMS
PyroCMS Mailchimp Integration Module


`thrive/mailchimp-module`

[TOC]


#### Version 1.0.0 (Alpha-2)

*thrive.module.subscriptions*


#### Requirements

- PyroCMS 3.9 
- PHP 7.2^

#### Build Status
Please note, while this module is still in alpha, any code updates you receive, please uninstall and reinstall the module. There will be no upgrade path while under heavy development.


#### About
The Mailchimp Module integrates your Mailchimp Campaigns and allows you to Update campaign, send, and duplicate campaigns all within PyroCMS admin. It also has a Plugin so you can directly subscribe users and keep your subscribers in sync with Mailchimps Audience/List.


# Quick Start
1. You need to get your API key from Mailchimp, you can read about it here [https://mailchimp.com/help/about-api-keys/]
2. You also need to get the Server Prefix which looks like this `us19`, when you log into Mailchimp it will be the first part of the domain.
3. Update your ENV File with these details.
```
THRIVE_MAILCHIMP_API=your-api-key-here
THRIVE_MAILCHIMP_SERVER_PREFIX=server_prefix
```
4. Follow the installatios steps
5. Once installed, go to PyroCMS admin, and goto Mailchimp, under each section there will be a button to `Sync`

## Installation

 1. Add to your addons directory as `/addons/default/thrive/mailchimp-module/*`
 2. Dump Autoloader `composer dump-autoload`
 3. Run install `php artisan addon:install thrive.module.mailchimp`
 4. Once you have connected your API key, run this command: `php artisan mailchimp:sync`


## Plugin Usage

The plugin is very powerful however for the minimalist usage, use the below

### Subscribe Form
```
	{{ mailchimp('subscribe').list('list-id')|raw }}
```

### Unsubscribe Form
This works exactly the same way, just set the action to unsubscribe.

```
	{{ mailchimp('unsubscribe').list('list-id')|raw }}
```

### Advanced Usage
With advanced plugin you can set the `tags` to be posted to Mailchimp,
A title for the form, a complete seperate view file, or even require 
a FNAME for mailchimp.
```
{{ mailchimp('subscribe')
        .list('list-id')
        .tag('single tag')
        .tags(['more tags', 'third-tag string', 'of arrays'])
        .title('Newsletter Signup')
        .view('thrive.module.mailchimp::public.subscribe')
        .useFname(false)|raw }}

```

### Plugin Limitation
The plugin can send `tags` to Mailchimp during a subscribe, however these tags will not be stored on the local system. Development is planned to integrate the tags locally, however its not yet baked.
Check back soon.



# Task Scheduling

`php artisan mailchimp:tidy`

Mailchimp Module uses Laravel + Pyro task scheduler to keep data in sync autoamtically.

To Start the Schedule, run the above command.

Future builds will allow you to set the frequence within the command and also via the settins.