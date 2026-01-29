# Google Calendar

<!-- TODO: Update this documentation for how to properly setup local Google Calendar connection -->

> [!WARNING]
> This article is out of date and does not properly reflect how to setup Google calendar integrations locally. It will be updated soon.

## Google Setup

Visit https://console.cloud.google.com/ to get started.

From the top left dropdown select to create a new project then click the `New Project` button.

Fill in the fields as required and click `Create`.

On the left navigation make sure you are on `Enabled APIs & services` and then click the `+  ENABLE APIS AND SERVICES` button.

In the search box type `calendar` and select the `Google Calendar API` result.

On the product details page click `ENABLE`.

On the left navigation select `OAuth consent screen`. Select `Internal` as the User Type and click `CREATE`. Under `App information -> App name` enter a name, e.g. `Canyon Google Calendar Test`. Under `User support email` enter your email. Under `Developer contact information -> Email addresses` enter your email. Select `SAVE AND CONTINUE`. Leave everything else blank as it's not important at this time. Complete the setup.

On the left navigation select `Credentials` and `+ CREATE CREDENTIALS`. Select `OAuth client ID`. For type select `Web application`. Give the key a name, e.g. `Advising App`. Under `Authorized redirect URIs` click `+ ADD URI` and enter your domain following the pattern `http://localhost/calendar/google/callback`. The path must match `/calendar/google/callback`. You can also add the [OAuth Playground](https://developers.google.com/oauthplayground/) if you want to test your keys `https://developers.google.com/oauthplayground`. Click `CREATE`. You will be shown a modal with the `Client ID` and `Client secret` copy these down.

## Advising App Configuration

Add the client id and secret from above to your `.env`

```
GOOGLE_CALENDAR_CLIENT_ID=
GOOGLE_CALENDAR_CLIENT_SECRET=
```

To test the integration sign into the app, click `Events` on the left navigation and then the Google icon in the provider modal. This should direct you to the `Sign in with Google` screen. Select your email and you should see the consent screen you previously configured. Select `Allow` to be sent back to the app and finally select a calendar you want to sync.
