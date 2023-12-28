# SES Event Tracking

This applications can make use of SES event tracking to track email all sorts of email events and process them within the application.
In order to make use of this feature, the application first needs to be using SES as the mail provider either via API or SMTP.
Next a Configuration Set will need to be created within the AWS account that is sending the emails, and configured with an SNS delivery destination.

## Configuration Set

Create a SES Configuration set following the following instructions: https://docs.aws.amazon.com/ses/latest/dg/creating-configuration-sets.html

Make note of the Configuration Set name as it will be needed later.

## SNS Topic

Once the Configuration Set is created, you can then add destinations, for our purposes we will be adding SNS destination to publish a webhook to our application.

https://docs.aws.amazon.com/ses/latest/dg/event-publishing-add-event-destination-sns.html

Though currently, the documentation above says you need to adjust the "Access policy", this currently does not seem to be the case. It should be setup automatically with the correct policy for you.

Once the SNS topic is created, you will need to subscribe to it. You can do this by going to the SNS topic and clicking "Create Subscription" and selecting "HTTPS" as the protocol. You will need to enter the URL of your application's webhook endpoint. The endpoint will be: `https://<your-domain>/inbound/webhook/awsses`

## Application Configuration

Once the above steps are completed, you will need to configure the integration under `Integrations` -> `Amazon SES Settings`.

The application should now be adding that configuration set as a header to all outgoing emails. If things are set up correctly on the AWS end once you send emails you should start seeing SNS webhooks being processed by the application, triggered by the SES events.
