# Single Sign On Setup

This application allows for the setup of Single Sign On (SSO) with other applications. This is done by using the [Laravel Socialite](https://laravel.com/docs/10.x/socialite) package.

Available providers and their setup steps are as follows:

## Google

Setup of Google SSO requires the configuration of a Google API project/application. Details on how to do so can be found [here](https://developers.google.com/identity/protocols/oauth2/openid-connect). Please keep in mind that at any time the link above may be out of date. Setup of the Google API project/application is not fully covered in this document.

Once the Google API project/application is set up, the following steps are required to set up Google SSO:

1. Retrieve the `client_id` and `client_secret` from the Google API project/application.
2. Add the `client_id` and `client_secret` to the `.env` file as `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` respectively.
3. Ensure that the proper Redirect URI is set in the Google API project/application. The Redirect URI should be set to `https://[YOUR_DOMAIN_HERE]/auth/google/callback`.
    1. If you are using a local development environment, the Redirect URI should be set to `http://localhost/auth/google/callback`.
4. The `GOOGLE_REDIRECT_URI` environment variable should be set to the same value as the Redirect URI set in the Google API project/application. This should already be dynamically configured for you based on the applications `APP_URL` environment variable. But it is recommended to double-check.
5. "Login with Google SSO" should now be available on the login page.

---

## Microsoft

Setup of Microsoft SSO requires the configuration of a Microsoft Azure AD application. Details on how to do so can be found [here](https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app). Please keep in mind that at any time the link above may be out of date. Setup of the Microsoft Azure AD application is not fully covered in this document.

Once the Microsoft Azure AD application is set up, the following steps are required to set up Microsoft SSO:

1. Retrieve the `client_id`, `client_secret`, and `tenant_id` from the Microsoft Azure AD application.
2. Add the `client_id`, `client_secret`, and `tenant_id` to the `.env` file as `AZURE_CLIENT_ID`, `AZURE_CLIENT_SECRET`, and `AZURE_TENANT_ID` respectively.
3. Ensure that the proper Redirect URI is set in the Microsoft Azure AD application. The Redirect URI should be set to `https://[YOUR_DOMAIN_HERE]/auth/azure/callback`.
    1. If you are using a local development environment, the Redirect URI should be set to `http://localhost/auth/azure/callback`.
4. The `AZURE_REDIRECT_URI` environment variable should be set to the same value as the Redirect URI set in the Microsoft Azure AD application. This should already be dynamically configured for you based on the applications `APP_URL` environment variable. But it is recommended to double-check.
5. "Login with Azure SSO" should now be available on the login page.
