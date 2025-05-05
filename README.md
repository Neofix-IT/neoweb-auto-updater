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

Any scheduled updates will be overriden to enforce plugin updates at 2 am (Wordpress time settings). This is achieved by using wp-cron.

> [!NOTE]  
> Please ensure external cron jobs are enabled for your Wordpress site in order to ensure auto-updates will be performed at 2am [Read more about external cron-jobs](https://wpspeedmatters.com/external-cron-jobs-in-wordpress/).
