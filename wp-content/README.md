# VinestoVino WooCommerce Website

## Send to Staging
rsync -avz --dry-run mu-plugins/ dbrftsmy@162.241.252.236:public_html/wp-content/mu-plugins/

## Send to live
rsync -avz --dry-run mu-plugins/ wcnadwmy@162.241.225.54:public_html/wp-content/mu-plugins/



To do a comparison between the live site content and my local site.
Assuming you are in the wp-content folder locally...
``` bash
    rsync -avz --dry-run --exclude=uploads/ --exclude=ai1wm-backups/ . wcnadwmy@162.241.225.54:public_html/wp-content/
```


```bash 
wp search-replace --dry-run vinestovino.net vinestovinonet.local  --all-tables
wp search-replace vinestovino.net vinestovinonet.local  --all-tables

rsync -avz  --exclude=uploads/ --exclude=ai1wm-backups/ wcnadwmy@162.241.225.54:public_html/wp-content/ wp-content/             
rsync -avz  --exclude=uploads/ --exclude=ai1wm-backups/ wp-content/themes/ wcnadwmy@162.241.225.54:public_html/wp-content/themes/

rsync -avz --dry-run --exclude=uploads/ --exclude=ai1wm-backups/ themes/ wcnadwmy@162.241.225.54:public_html/wp-content/themes/
rsync -avz --exclude=uploads/ --exclude=ai1wm-backups/ themes/ wcnadwmy@162.241.225.54:public_html/wp-content/themes/


rsync -avz --dry-run --exclude=uploads/ --exclude=ai1wm-backups/ mu-plugins/loyverse-sync.php wcnadwmy@162.241.225.54:public_html/wp-content/mu-plugins/loyverse-sync.php


rsync -avz  --exclude=uploads/ --exclude=ai1wm-backups/ wcnadwmy@162.241.225.54:public_html/wp-content/themes/ wp-content/themes/

https://r.loyverse.com/dashboard/#/goods/edit/135991093
```

XQJQM148D2GLY9BTMUXMMK6Q



