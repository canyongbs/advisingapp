# Outlook Calendar

<!-- TODO: Update this documentation for how to properly setup local Outlook Calendar connection -->

> [!WARNING]
> This article is out of date and does not properly reflect how to setup Outlook calendar integrations locally. It will be updated soon.

The Advising App project provides a simple way to connect with and use Azure OpenAI in order to connect to OpenAI's powerful language models, summoning the power of AI inside of the application in a secure fashion.

## Outlook Setup

An application must be registered with Microsoft to use the Outlook Calendar API. This is a one time setup and can be done at https://apps.dev.microsoft.com/.

Once it is setup up the correct credentials must be added to your `.env` file.

```
AZURE_TENANT_ID=
AZURE_CLIENT_ID=
AZURE_CLIENT_SECRET=
```

A redirect must be set to `[YOUR_DOMAIN]/calendar/outlook/callback` within the Azure application.
