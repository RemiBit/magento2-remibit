MAGENTO 2 REMIBIT MODULE


## Integration Requirements

- A RemiBit merchant account.
- Magento 2, tested with version 2.3.1, 2.3.2, 2.3.4

INSTALLATION AND CONFIGURATION

In order to install the module, it is necessary to access the server where the web files are hosted by ssh. If you don’t know how to do that, please contact your site administrator or your hosting provider.

In this example we will use an usual Magento 2 configuration, so the website files are located in /var/www/html/magento and they are owned by the default user www-data. Please replace [magento] with the actual name (if different) of your website directory and [www-data] with the owner (if different) of your web files directory.

## Module Installation
 
1/. Go to the magento directory and then to the directory where you have to copy the module

```
cd /var/www/html/magento

cd app/code
```

If that directory doesn't exist yet, create it:

```
sudo -u www-data mkdir -p app/code
```

2/. Fetch the RemiBit module

```
sudo -u www-data wget https://github.com/RemiBit/magento2-remibit/releases/download/v1.01/magento2-remibit.zip
```

3/. Uncompress it

```
sudo -u www-data unzip magento2-remibit.zip
```

4/. Fix ownership and permissions

```
sudo chown -R www-data:www-data /var/www/html/magento

sudo chmod -R 755 /var/www/html/magento
```

5/. Install it

 ```
cd /var/www/html/magento
```
Run:
```
sudo -u www-data php bin/magento setup:upgrade
```
and 
```
sudo -u www-data php bin/magento cache:clean
```


## Module Configuration

1/. Login to your Magento 2 Admin dashboard, go to `STORES` > `Configuration` > `SALES` (Section) > `Payment Methods` and scroll down to open RemiBit Checkout

2/. With the data from your RemiBit merchant account's `Settings` > `Gateway`, add the following information to RemiBit Checkout configuration form:

* Login ID
* Transaction Key
* Signature Key
* MD5 Hash Value
