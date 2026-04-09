# Prestashop Local Setup

*The following documentation is written for Sezzle internal developers only. Merchant developers should refer to README.md for instructions.*

## Docker Installation Method

> See docker-compose.yml for values. PHPMyAdmin runs on localhost:8085 and can be used for DB management, so you don't have to set up a connection in DBeaver.

### Install Docker

All Sezzle developers should already have completed [Docker](https://gitlab.sezzle.com/sezzle/sezzle-compose) setup for Sezzle-Compose (although Sezzle-Compose will not be used for WooCommerce setup).

### Run Prestashop

1. Clone `prestashop` into `go/src/sezzle` as normal
1. Ensure Docker Desktop is open, then run `docker-compose up -d`

### Configure Prestashop

1. Navigate to http://localhost:8090/install-ps/
1. If prompted to upgrade, click `No thanks`
1. Select Language, then click `Next`
1. Click `I agree to the above terms and conditions` then click `Next`
1. Enter store info:
 - Store name: sezzle-test
 - Country: United States
 - Store Timezone: (any)
 - Enable SSL: No
 - First Name: Admin
 - Last Name: Admin
 - Email Address: admin@admin.com
 - Store password: sezzleadmin
 - Retype to confirm: (same as above)
1. Click `Next`
1. On the next screen, accept defaults by clicking `Next`
1. Enter the Database info:
 - Database server address: presta-mysql
 - Database name: prestashop
 - Database login: root
 - Database password: admin
 - Table prefix: sz_
 - Drop Existing Tables: TRUE
1. Click `Test your database connection now!`
1. Click `Next`
1. Select the `Classic` theme
1. Once Store Installation has completed, click `Manage your store` to open the login screen
 - If Store Installation fails, try again.

### Install Sezzle

1. Run `docker exec prestashop php /var/www/html/bin/console prestashop:module install sezzle`
1. Go to Payment > Payment Methods. Next to Sezzle, click `Configure`
1. Enter `Public Key` and `Private Key` (Use https://sandbox.admin.sezzle.com/merchants/1)
1. Click `Save`

You should now be able to complete sandbox checkouts on the storefront: http://localhost:8090/

## MAMP Installation Method

### Install MAMP

1. [Download MAMP](https://www.mamp.info/en/downloads/)
1. Unzip the downloaded file, then drag & drop to the `Applications` folder

### Install PrestaShop to MAMP

1. Download [PrestaShop](https://prestashop.com/prestashop-offers/classic/)
    - Enter your email address
    - Select `I want to create modules for PrestaShop`
    - Click Download
    - To download a specific version, see https://prestashop.com/versions/
1. `cd /Applications/MAMP/htdocs && mkdir prestashop`
1. Unzip the downloaded file, then drag & drop `prestashop.zip` and `index.php` to the `Applications/MAMP/htdocs/prestashop` folder
    - The installer will unzip this subfolder for you.

### Configure MAMP

1. Open MAMP app
1. Select `Web server`: `Apache` and `PHP version`: `8.4.1` (or later, up to 8.5 for Prestashop 9.1)
    - More information regarding version compatibility [here](https://gitlab.sezzle.com/sezzle/prestashop/-/wikis/home/testing)
1. Click Preferences
1. In the `Ports` tab, set `Apache Port` and `Nginx Port` to `8888` and `MySQL Port` to `8889`
1. In the `Server` tab, select `Use MySQL server`: `8.0.40`
1. For `Document Root`, click `Choose` and navigate to `Applications › MAMP › htdocs > prestashop`. Click `Choose` to save.
1. Click `OK`
1. Click `Start`

### Create Database

*Ensure other MySQL instances are not running.*
*If you already have a `mamp` connection established, skip to the `Create New Database` step*

1. Open DBeaver or equivalent
1. Click `New Database connection`
1. Select `MySQL`, then click `Next`
1. `Port` should be `8889`
1. `Username` and `Password` should each be `root`
1. Click `Finish`
1. Secondary-click on the newly created connection and select `Rename`
1. Enter `mamp` then click `OK`
1. Click the connection to expand, then Secondary-click on `Databases` and select `Create New Database`
1. Enter `Database name`: `prestashop` then click `OK`

### Configure PrestaShop

1. Navigate to http://localhost:8888/
1. If prompted to upgrade, click `No thanks`
1. Select Language, then click `Next`
1. Click `I agree to the above terms and conditions` then click `Next`
1. Enter store info:
    - `Store name`: `sezzle-test`
    - `Country`: `United States`
    - `Store Timezone`: (any)
    - `Enable SSL`: `No`
    - `First Name`: `Admin`
    - `Last Name`: `Admin`
    - `Email Address`: `admin@admin.com`
    - `Store password`: `sezzleadmin`
    - Retype to confirm: (same as above)
1. Click `Next`
1. On the next screen, accept defaults by clicking `Next`
1. Enter the Database info:
   - `Database server address`: `localhost`
   - `Database name`: `prestashop`
   - `Database login`: `root`
   - `Database password`: `root`
   - `Table prefix`: `sz_`
   - `Drop Existing Tables`: `TRUE`
1. Click `Test your database connection now!`
1. Click `Next`
1. Select the Classic theme
1. Once Store Installation has completed, delete the `install` folder from `/Applications/MAMP/htdocs/prestashop` then click `Manage your store` to open the login screen
    - If Store Installation fails, try again.
    - Note: Prestashop's Admin URL now uses an alphanumeric string for security purposes. You can confirm the URL for your store's admin by opening the root folder and finding the `admin.....` folder name(ex: `localhost:8888/admin003dy3ilgmgwiz75kdk`). Alternatively, you can rename the folder here, and use the new name as your URL path - it just can't be called "admin".
> If you get a "Page not found" error, you need to enable mod_rewrites in Apache. Try using this command: `sed -i '' 's/^#\(LoadModule rewrite_module\)/\1/' /Applications/MAMP/conf/apache/httpd.conf` then restart MAMP

### Install Sezzle

1. `ln -s ~/go/src/sezzle/prestashop /Applications/MAMP/htdocs/prestashop/9.1/modules/sezzle`
1. Go to `Modules` > `Module Manager`
1. Under `Payment`, find `Sezzle` and click `Install`
1. Go to `Payment` > `Payment Methods`. Next to `Sezzle`, click `Configure`
1. Enter `Public Key` and `Private Key` (Use https://sandbox.admin.sezzle.com/merchants/1)
1. Click `Save`

You should now be able to complete sandbox checkouts on the storefront: http://localhost:8888/

# Release Process

1. In sezzle.php, find `public function __construct()` item `$this->version` and bump the version as appropriate
1. Bump version in composer.json
1. Add an entry for the new version in CHANGELOG.md
1. Delete the current zip file.
1. In the root directory of this folder, execute the below command, update `<sezzle-module-version>` to reflect the new version:
`cd .. && zip -r v<sezzle-module-version>-sezzle.zip sezzle -x '.git/*' -x 'CODEOWNERS' -x 'DEVELOPERS' -x 'renovate.json' -x '__MACOSX/*' -x '.DS_Store'`
1. In Gitlab, go to `Code` > `Tags` and click `New Tag`
1. Name the tag in the format `vX.X.X` with the message in the format `Version X.X.X`
1. In `Create From`, select the branch name for this release, then click `Create tag`
1. Review, approve, and merge as normal.
1. Once the MR has been merged to production and mirrors to [Github](https://github.com/sezzle/sezzle-prestashop), go to `Releases` on the Github project
1. Click `Draft a new Release`
1. Select the tag that corresponds to the new version
1. Name the release in the format `vX.X.X` and copy+paste the entry from CHANGELOG.md into the `Write` tab
1. Click `Publish Release`
