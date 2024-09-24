# neoweb-auto-updater
Auto updater &amp; update scheduler for Wordpress

## Functionality

#### Automatic forced Update

This plugin will override your update settings and enforce automatic updates of:
- Plugins
- Themes
- Translations
- WP-Core

#### Set scheduler for 2 am

Any scheduled updates will be overriden to enforce plugin updates at 2 am (Wordpress time settings). This is achieved by using wp-cron. Please ensure external cron jobs are enabled for your Wordpress site [Link](https://wpspeedmatters.com/external-cron-jobs-in-wordpress/)