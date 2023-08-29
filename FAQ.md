## How to connect your account?
The most effective method to log in is by using the Chrome browser.
Make sure you have Environment set to Production mode

Click the Connect button and log in to the system.
Then follow all the necessary steps that PayPal will require from you.
At the last step, you will be prompted to return to your site.

Click on the "Go back to ..." button and you will be redirected to your site, where you will be assigned all the necessary details and you can start working with the payment.

If, for some reason, some parameter was left blank, then you should repeat the entire authorization again.

## On the last step, I don't see the "Go back to ..." button to return to my site.
This is due to the fact that you are using Apache and the .htaccess file contains the lines
``` nginx
<IfModule> ... </IfModule>
```
We advise you to remove these lines from the file and then reauthorize again. After successful authorization, you can return the lines.

## After returning to my site, instead of a successful authorization, I get the error
```
Authentication failed due to invalid authentication credentials or a missing Authorization header.
```
Please try to authorize with a different browser, such as Google Chrome.

## I am using the Journal theme and the PayPal buttons and the Card Payment form do not work correctly.
Please open All Settings on the General tab and set Checkout Mode to One Button.

This mode is compatible with all modern payment checkout modules. Initially, only one button will be visible, similar to other payment options. Upon clicking on it, a pop-up will appear displaying all the other buttons and a card payment form.

## After updating the payment, PayPal stopped working on the Checkout, Product, and Cart pages.
Please reinstall the payment method. Click Disconnect on the General tab, then exit and click Uninstall. Then install it again.

## I use a custom theme and PayPal doesn't work on the Checkout, Product, and Cart pages.
There are templates like Journal or BurnEngine that prevent the display of styles and scripts. This usually occurs when styles and scripts are minified or cached.

Please try disabling minification, bundling, and caching and check if PayPal is working.

### BurnEngine

### Journal 3



