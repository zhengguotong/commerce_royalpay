Commerce Alipay Global

Developer: Guotong Zheng (Tony)
Developer Website:http://visionsoft.com.au


Implements [Royal Pay](https://www.royalpay.com.au/) payment services for use with
[Drupal Commerce](http://drupal.org/project/commerce).

Installation and configuration
------------------------------
a. Prerequisites:
Requires Drupal Commerce to be installed and more particularly the Commerce
Payment module to be enabled (more Commerce modules would also be required:
Commerce UI and Order).
More information at: Installation and uninstallation guide for Drupal Commerce
[https://drupal.org/node/1007434]

b. Download the module and copy it into your contributed modules folder:
[for example, your_drupal_path/sites/all/modules] and enable it from the
modules administration/management page.
More information at: Installing contributed modules (Drupal 7)
[http://drupal.org/documentation/install/modules-themes/modules-7]

2 - Configuration:
After successful installation, browse to the "Payment Methods" management page
under: Home » Administration » Store » Configuration » Payment methods
Path: admin/commerce/config/payment-methods or use the "Configure" link
displayed on the Modules management page.

Enable the Royalpay payment method, as described in the Drupal Commerce Payments
User Guide at: http://www.drupalcommerce.org/user-guide/payments
Follow all other steps as described in the Payments User Guide, edit the Alipay
payment method (Rule) and then edit the Action "Enable payment method: Alipay".

Configure the form Payment Settings as required with:
- Currency Code(Currently Royal Pay only accept AUD or CNY )
- partner Code and credential_code royalpay API after account registration for
the corresponding type of Service.
