# Mailchimp for PyroCMS
PyroCMS Mailchimp Integration Module


`thrive/mailchimp-module`

[TOC]


#### Version 1.0.0 (Alpha-3)

*thrive.module.mailchimp*


#### Requirements

- PyroCMS 3.9 
- PHP 7.2^

#### Important Message
```
Please note, while this module is still in alpha, any code updates you receive, 
please uninstall and reinstall the module. There will be no upgrade path 
while under heavy development. Many of the fields may undergo name 
changes and I may not be including an upgrade path 
until I am in Beta.
```

## About
The Mailchimp Module integrates your Mailchimp Campaigns with PyroCMS. The integration allows you to Update, send, and duplicate campaigns all within PyroCMS. It also has a Plugin so you can directly subscribe users and keep your subscribers in sync with Mailchimps Audience/List. Forms Integration is also possible, see below.


## Quick Start
1. You need to get your API key from Mailchimp, you can read about it here [https://mailchimp.com/help/about-api-keys/]
2. You also need to get the Server Prefix which looks like this `us19`, when you log into Mailchimp it will be the first part of the domain.
3. Update your ENV File with these details.
```
THRIVE_MAILCHIMP_API=your-api-key-here
THRIVE_MAILCHIMP_SERVER_PREFIX=server_prefix
```
4. Follow the installatios steps


### Installation

 1. Add to your addons directory as `/addons/default/thrive/mailchimp-module/*`
 2. Dump Autoloader `composer dump-autoload`
 3. Run install `php artisan addon:install thrive.module.mailchimp`
 4. Once you have connected your API key, run this command: `php artisan mailchimp:sync`


## Plugin 

Mailchimp comes with a `Plugin` that is very easy to use and integrate into your themes.

### Subscribe Form
```php
	{{ mailchimp('subscribe').list('list-id')|raw }}
```

### Unsubscribe Form
This works exactly the same way, just set the action to unsubscribe.

```php
	{{ mailchimp('unsubscribe').list('list-id')|raw }}
```

### Advanced Usage
With advanced plugin you can set the `tags` to be posted to Mailchimp,
A title for the form, a complete seperate view file, or even require 
a FNAME for mailchimp.

```php
{{ mailchimp('subscribe')
        .list('list-id')
        .tag('single tag')
        .tags(['more tags', 'third-tag string', 'of arrays'])
        .title('Newsletter Signup')
        .view('thrive.module.mailchimp::public.subscribe')
        .useFname(false)|raw }}

```


### Ping `:)`
The ping function is a wip, not yet developed but will test the api connection and return a string from Mailchimp if all good to go. This could be a good user case for front end development.

```php
Code:
	{{ mailchimp('ping') }}

Output:
        "Everythings chimpy"
```


        
## Integrating with the *Forms Module*
If the Mailchimp Plugin is not suited, or you need more fields, you can link Mailchimp to the Forms-Module.
Using all three extensions below, Just use the `Mailchimp-Form-Extension` (Form Handler) and add the `thrive.field_type.mailchimp` (Field-Type).


#### Supported Forms Modules
- `Anomaly\Forms` (PyroCMS Pro Feature)
- `Thrive\Forms` (Private Repository)

Namespace                         | Type                 | Why                                                                
--------------------------------- | -------------------- | ---------------
`thrive.field_type.mailchimp`     | Field Type           | Links Mailchimp audience and syncs MC Module with Forms.
`thrive.extension.mailchimp`      | Forms Handler        | Linking the Forms Module with Mailchimp Module. This allows you to Subscribe Users using the Forms Module. This will post data to the `MailchimpModule.`
`thrive.module.mailchimp`         | Module               | The Core Mailchimp Integration for PyroCMS  





## Commands & Task Scheduling

What                       | Command                         | Options
-------------------------- | ------------------------------- | -------------------------------
Starts the Task Scheduler  | `php artisan mailchimp:tidy`    |   
Sync All Libraries (once)  | `php artisan mailchimp:sync`    |  
Sync All Libraries (once)  | `php artisan mailchimp:post`    | 
