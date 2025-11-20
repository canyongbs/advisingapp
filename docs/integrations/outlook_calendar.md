# Outlook Calendar

## Outlook Setup

An application must be registered with Microsoft to use the Outlook Calendar API. This is a one time setup and can be done at https://apps.dev.microsoft.com/.

Once it is setup up the correct credentials must be added to your `.env` file.

```
AZURE_TENANT_ID=
AZURE_CLIENT_ID=
AZURE_CLIENT_SECRET=
```

A redirect must be set to `[YOUR_DOMAIN]/calendar/outlook/callback` within the Azure application.
