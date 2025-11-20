# Azure OpenAI

The Advising App project provides a simple way to connect with and use Azure OpenAI in order to connect to OpenAI's powerful language models, summoning the power of AI inside of the application in a secure fashion.

## Before You Begin

In order to get started, please ensure you have met all of the prerequisites for this integration. As of writing, documentation for this can be found at the link below, but is subject to change:

https://learn.microsoft.com/en-us/azure/ai-services/openai/chatgpt-quickstart?tabs=command-line&pivots=rest-api#prerequisites

## Getting Started

Once you have completed all of the prerequisites and have access to an Azure Open AI Service resource, you can begin working on the integration.

In the `.env.example` file, you will find the 4 variables you need in order to set up the connection with your instance. Please copy the following over to your `.env`

```
AZURE_OPEN_AI_BASE_ENDPOINT=
AZURE_OPEN_AI_API_KEY=
AZURE_OPEN_AI_API_VERSION=
AZURE_OPEN_AI_DEPLOYMENT_NAME=
```

The values for the variables should be accessible from your Azure OpenAI dashboard, and they should be mapped as shown:

```
AZURE_OPEN_AI_BASE_ENDPOINT="https://yourendpoint.openai.azure.com"
AZURE_OPEN_AI_API_KEY="Your-Open-AI-API-Key"
AZURE_OPEN_AI_API_VERSION="2023-05-15"
AZURE_OPEN_AI_DEPLOYMENT_NAME="your-model-deployment-name"
```

You will notice a 5th variable related to Azure OpenAI: `AZURE_OPEN_AI_ENABLE_TEST_MODE`. This variable is used to control test mode, discussed next.

## Testing

In order to preserve tokens and keep costs low in local environments, it is advised that you keep the `AZURE_OPEN_AI_ENABLE_TEST_MODE` variable set to `true`. In doing so, the application will always use a "playground" instance that doesn't actually communicate with the Azure Open AI service. This is helpful if you want to test functionality or adjust the UI/UX experience, but don't want to rack up a large bill.
