<div align="center">
    <a href="https://sezzle.com">
        <img src="https://media.sezzle.com/branding/2.0/Sezzle_Logo_FullColor.svg" width="300px" alt="Sezzle" />
    </a>
</div>

## Sezzle module for PrestaShop

Documentation for the Sezzle payment module can be found on <a href="https://docs.sezzle.com/sezzle-integration/docs/prestashop">docs.sezzle.com</a>.

# Setting up a local PrestaShop store

### INSTALLING MAMP

1. Download MAMP [here](https://www.mamp.info/en/downloads)
2. Open MAMP and make note of Document root (most likely: `Applications > MAMP > htdocs`)
3. Navigate to document root and create a new folder named `test_shop` or something similar.
4. Click `Start` button in top right of MAMP window.
- This will open a window in the browser for http://localhost:8888/MAMP
5. Click on `MySQL` section on the opened page to expand it. 
- You will need the username and password displayed here when installing PrestaShop store

### SETTING UP DATABASE

1. Open http://localhost:8888/phpMyAdmin/ in your browser
2. Create a new database ex. `prestashop_local_db`

### DOWNLOAD AND SET UP PRESTASHOP

1. Visit [download link](https://www.prestashop.com/en/download) and download Prestashop
2. Unzip file and move `index.php` and `prestashop.zip` to the folder you created in the document root for MAMP earlier (most likely `Applications > MAMP > htdocs > test_shop`)
3. In browser navigate to http://localhost:8888/test_shop and installing for PrestaShop will automatically begin.
- Make note of email/password during installing.  They are needed to access admin side of site
- Default port for PrestaShop is `3306`
- The default port for MySQL is `8889`.  Make sure to add to end of Database Server Address `127.0.0.1:8889`
4. Delete installation folder from folder you created in htdocs earlier, now your shop is ready to use!

### CREATING RELEASE PACKAGE

1. If Sezzle is already installed inside PrestaShop, then, go to `<root-dir>/modules/sezzle` else
copy the project contents to a new directory `sezzle` and execute the below command from `sezzle` directory.
```
    cd ../ \
    && zip -r v<sezzle-module-version>-sezzle.zip sezzle -x '.git/*' -x 'CODEOWNERS' -x 'renovate.json' -x '__MACOSX/*' -x '.DS_Store' \
    && mv v<sezzle-module-version>-sezzle.zip sezzle/ \
    && cd sezzle
```
