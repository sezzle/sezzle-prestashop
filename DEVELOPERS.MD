# PrestaShop Local Setup

<!-- todo: update PrestaShop documentation in docs.sezzle.com -->
<!-- to do: update readme so instead of repeating usage documentation, we link to docs.sezzle.com -->

*The following documentation is written for Sezzle internal developers only. Merchant developers should refer to README.md for instructions.*

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
1. Select `Web server`: `Apache` and `PHP version`: `8.4.1`
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
1. Once Store Installation has completed, delete the `install` folder from `/Applications/MAMP/htdocs/prestashop`
    - If Store Installation fails, try again.

### Install Sezzle Extension

1. `cd /Applications/MAMP/htdocs/prestashop/modules && git clone ssh://git@gitlab.sezzle.com:10022/sezzle/prestashop.git sezzle && cd ..`

### Sezzle Configuration

Note: Prestashop is fickle - if the page does not load properly, refresh to try again.

1. Navigate to `http://localhost:8888/admin`
    - Note: Prestashop's Admin URL now uses an alphanumeric string for security purposes. To confirm the URL for your store's admin, open the root folder and copy the folder name into the URL path (ex: `localhost:8888/admin003dy3ilgmgwiz75kdk`)
1. Log in with Username `admin@admin.com` and Password `sezzleadmin`
1. Go to `Modules` > `Module Manager`
1. Under `Payment`, find `Sezzle` and click `Install` then click `Configure`
1. Enter `Public Key`: `qxorvmr7zbww7yw8vvhlxi8s3uko18q7`
1. Enter `Private Key`: `eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyaWQiOjEsInNjb3BlIjoibWVyY2hhbnRhcGkiLCJleHAiOjQ2MzM3MTg3MDYsImlzcyI6IlNlenpsZSJ9.Ez4-gbBpb5iTRJ9VCVD97IrjS98v1NalZlL2fHU2KSA`
1. Click `Save`


## Troubleshooting

### OutOfMemory error

`open /Applications/MAMP/bin/php/php8.3.14/conf/php.ini`
Search the document for `memory_limit` and increase the capacity as required
You might need to restart MAMP