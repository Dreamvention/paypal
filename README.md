# PayPal Checkout Integration for OpenCart

## Let your customers pay how they want at checkout

PayPal Checkout comes with PayPal payments, card processing, and country-specific payment options from around the world. We automatically handle updates on our end so upgrading is hassle-free. All you need is a PayPal Business account.

## Installation
> [!IMPORTANT]
> If you currently have an older version than 3.0.0 of **PayPal Commerce Platform** or **PayPal Checkout Integration** installed in your OpenCart, you should uninstall it before installing the **PayPal Checkout Integration**.

### Quick Install via OpenCart Extension Installer (recommended)
1. [**Download**](https://github.com/Dreamvention/paypal/releases) the archive that corresponds to your version of OpenCart.
2. Navigate to Extensions -> Installer and click **Upload**.
3. Select the downloaded **ZIP file** and click **Open**.
4. After the successful installation, navigate to Extensions -> Extensions -> Payments. Find **PayPal Checkout Integration** and click the green **Install** button.

#### AUTOMATIC connection your PayPal account
1. Once installed click the **Edit** button. A setup screen should welcome you.
2. Verify that you are in the **Production Environment** and click **Connect**. Simply follow the instructions.
3. Once the onboarding process is complete, you will be forwarded back to the OpenCart dashboard. Please wait while the installation is finalized and the page is refreshed. You should now see your PayPal Checkout Dashboard.

#### To MANUALLY connect your PayPal account, follow these steps:
1. [Sign in](https://www.paypal.com/signin) to your business PayPal account.
2. Hover over your profile icon and select **Account Settings**.
3. Go to **Business information** and copy your **PayPal Merchant ID** for later use.
4. Visit [the developer section](https://developer.paypal.com/dashboard/applications/live) and click on **Create App** to generate a **Client ID** and **Secret key** for your App.
5. Copy the **Client ID** and **Secret key** from the API credentials section.
6. Access the Admin Dashboard and go to Extensions -> Extensions -> Payments.
7. Look for **PayPal Checkout Integration** and click on the **Edit** button.
8. Change the Authorization Type to **Manual**.
9. Enter the **Merchant ID**, **Client ID**, and **Secret key** in the provided fields.
10. Click on **Connect** button and wait for the connection process to complete. The page will be refreshed once it's done.





