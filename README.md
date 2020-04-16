MAGENTO 2 REMIBIT MODULE

INSTALLATION AND CONFIGURATION


## Integration Requirements

- A RemiBit merchant account.
- Magento 2, tested with version 2.3.1, 2.3.2, 2.3.4

In order to install the module, it is necessary to access the server where the web files are hosted by ssh. If you don’t know how to do that, please contact your site administrator or your hosting provider.

In this example we will be using an usual Magento 2 configuration, so the website files are located in /var/www/html/magento. Please replace [magento] with the actual name of your website directory.


## Module Installation
 
1/. Go to the magento directory and then to the directory where you have to copy the module

```
cd /var/www/html/magento

cd app/code
```

If that directory doesn't exist yet, create it:

```
sudo mkdir -p app/code
```

2/. Fetch the RemiBit module

```
sudo wget https://github.com/RemiBit/magento2-remibit/releases/download/v1.0/magento2-remibit.zip
```

3/. Uncompress it

```
sudo unzip magento2-remibit.zip
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
php bin/magento setup:upgrade
```
and 
```
php bin/magento cache:clean
```


## Module Configuration

1/. Login to your Magento 2 Admin dashboard and go to `STORES` > `Configuration` > `SALES` (Section) > `Payment Methods` and scroll down to open RemiBit Checkout

2/. With the data from your RemiBit merchant account's `Settings` > `Gateway`, add the following information to RemiBit Checkout configuration form:

* Login ID
* Transaction Key
* Signature Key
* MD5 Hash
