To install:

```
mkdir -p /path/to/store/thirdparty/Fandi
cd /path/to/store/thirdparty/Fandi
git clone git@github.com:fiksani/GoogleShopping.git
ln -s /path/to/store/app/code/Fandi /path/to/store/thirdparty/Fandi
php bin/magento module:enable Fandi_GoogleShopping
php bin/magento setup:upgrade
```