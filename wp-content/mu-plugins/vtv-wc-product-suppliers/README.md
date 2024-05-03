# Vines to Vino Product Suppliers Feature

Adds the ability for an admin to add suppliers to a product and hide groups of products from users based on the same.

## Setup
1. Add suppliers (already added for Raymond) via the UI: https://vinestovino.net/wp-admin/edit-tags.php?taxonomy=suppliers&post_type=product
2. Add suppliers to desired products via quick edit, edit screen, or bulk import (be sure to export a fresh copy from the LIVE site and amend the 'Suppliers' column): https://vinestovino.net/wp-admin/edit.php?post_type=product&page=product_exporter
3. disable suppliers for applicable users on the profile page.

See screenshots for clairty.

## Notes
### Products
- import and export custom column for bulk supplier updates via the product import/exporter.
- Manage new categories via the UI: https://vinestovino.net/wp-admin/edit-tags.php?taxonomy=suppliers&post_type=product
- Manage suppliers assigned to individual products: e.g. https://vinestovino.net/wp-admin/post.php?post=1522&action=edit

### Users
- Suppliers visisble to all users by default
- A check represents visible.
- Manageable via the user profile page: https://vinestovinonet.local/wp-admin/users.php
  - scroll down until you reach the 'Suppliers' heading


```bash


rsync -avz --dry-run --exclude=.DS_Store mu-plugins/vtv-wc-product-suppliers/ wcnadwmy@162.241.225.54:public_html/wp-content/mu-plugins/vtv-wc-product-suppliers/
rsync -avz --exclude=.DS_Store mu-plugins/vtv-wc-product-suppliers/ wcnadwmy@162.241.225.54:public_html/wp-content/mu-plugins/vtv-wc-product-suppliers/