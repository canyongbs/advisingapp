# Creating Custom Campaign blocks

To start add a new enum case to the `CampaignActionType` file. This is the key to the functionality of the blocks. You should fill out each section of the enum to successfully integrate the block.

### Optional
If the display label for the block is more than one word add the case to the `getLabel` function to configure the correct label.

### Required
1. Add the case to the `getModel` function to associate the case to the corresponding Eloquent model.

2. Create a block file in `app-modules/campaign/src/Filament/Blocks` following the existing files as a guide. Name this file something like `{Action}Block`. Then associate this file's `editFields` function to the new case in `getEditFields`.

3. Create a new step summary blade file in `resources/views/filament/forms/components/campaigns/actions` called `{slug}.blade.php`. This file will display the summary of the block at the end of creating an action. Follow the existing files for examples on styling.

4. Create a function in the model file referenced in step 1 called `executeFromCampaignAction` it should accept `CampaignAction` as a parameter: `CampaignAction $action`: 
    ```php
   public static function executeFromCampaignAction(CampaignAction $action): bool|string
   ```
   This function is the main working area of the block. Refer to the existing blocks for suggestions on how to structure the code. Add this new function to the enum's `executeAction`.

5. Add the block to the `blocks` function in `app-modules/campaign/src/Filament/Resources/CampaignResource/Pages/CreateCampaign`. This function controls the ordering and display of the blocks.

By filling in the enum's functions you should successfully integrate a new block.

As a last step create a test in `app-modules/campaign/tests/Actions` following the existing tests as a guide.
