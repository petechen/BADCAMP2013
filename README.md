BADCAMP2013
===========

Code examples for "Be a Drupal Lion Tamer! Extending the settings.php $conf array...."

[10/28/13]


If you watched the BADCAMP DEMO, you will have noted that I use symlinks
to keep things a bit tidier and, to keep the actual settings file in a
separate folder/repo outside the sites folder.  This helps me, as a
Dev/Ops system owner, to stay a bit more on top of all the site that I
maintain instead of having to cd into all the individual Drupal site
folders in order to review/modify the settings.php files.

You can simply use the settings files that are in BC2013SettingsEX and 
configure and substitute it for your existing settings.php files - I don't
have to tell most people this but as a reminder, 

PLEASE TEST THIS IN A NON-PRODUCTION ENVIRONMENT FIRST 

- and graduate to using the $siteinfo array and include siteinfosettings.php to see how the streamlined system works.  

If you would prefer to recreate the demo as I showed at BADCAMP2013, instead of messing with one of your existing sites, you can substitute the "sites" folder (or just the contents of it) in a new Drupal7 distribution along with the settings files in BC2013SettingsEx for review and practice.  Here are the setup instructions to recreate that experience:

1. To use this properly, you will first need to set up a new Drupal 7 core for yourself and drop this "sites" folder in place of the one provided in the core distribution.  Set up your host configs accordingly (*AMP, Apache, etc.). This example uses a drupal multisite install so you can set it for up to 3 hosts.  If you want to do single site, reconfigure for the sites/default folder accordingly. 

2. Next, set up three empty databases.  By default, they are set up in the ex*.settings.php files as follows:
    - BADLT_site1  username:admin  password:password  host: localhost
    - BADLT_site2  username:admin  password:password  host: localhost
    - BADLT_site3  username:admin  password:password  host: localhost
        
Change details as you need but make sure to make corresponding changes in your ex*.settings.php files.
   
3. Choose a URI for your site(s), and symlink to them from the sites/ folder to the corresponding "vertical" folder (site1, site2, site3). I would suggest using the same name as I already set up so that:
    - site1.bc2013.local --> /site1/site1.bc2013.local
    - site2.bc2013.local --> /site2/site2.bc2013.local
    - site3.bc2013.local --> /site3/site3.bc2013.local

The dev, stage, and prod folders are just put there for your reference so you can see the model of how I typically set up a vertical. Logically, this same sites folder on dev has site1-dev.yourdomain.com --> /site1/site-dev.yourdomain.com and so forth.  If you are not familar with how Drupal Multisites are setup, review in information in sites/example.sites.php on how this works.

4.  Symlink the settings.php file for each corresponding site folder.  In the demo, those settings.php files are named ex1.settings.php, ex2.settings.php, and ex3.settings.php and are located in BC2013SettingsEx so if you were to drop both that fodler and sites into the docroot of your drupal site, you would symlink as follows:
    - sites/site1/site1.bc2013.local/settings.php --> BC2013SettingsEX/ex1.settings.php
    - sites/site2/site2.bc2013.local/settings.php --> BC2013SettingsEX/ex2.settings.php
    - sites/site3/site3.bc2013.local/settings.php --> BC2013SettingsEX/ex3.settings.php

n.b. The symlink destination name can alwasys be anything you like but always name the symlink settings.php because that is what Drupal is looking for in your sites/* folder.  As a general practice, because I keep all my settings.php files in a repo, I name each  of them according to the site name.

5. Double check to see that the $databases array in each of your settings files is set correctly for the corresponding database(s) that you set up in step (2).

6. Install your site.  I prefer to do it with drush:
       - drush si --accouunt-name="admin" --account-pass="password"


7. Lastly, review and enable needed modules for this exercies.  When you get to the backup and migrate settings, you'll want to make sure bam is enabled.  Again, I like using drush.  To review:
      - drush pm-list  (this will show you all modules avaialbe and their status)
      To enable backup and migrate:
      - drush pm-enable backup_migrate
      
      BONUS, get rid of overlay, Drupal core menu, and enable admin_menu
      - drush pm-disable overlay, menu
      - drush pm-enable admin_menu
      
      
(Admittedly, I made a few theme changes, substituting the Stanford Engineering theme that I used in the demo for zem, omega, and adaptive theme since those are publicly available.)
                                                                     
                                                                     




