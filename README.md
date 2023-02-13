# Wordpress Plugin
This is a simple wordpress plugin to receive text and snippets from SnipAId.

## How to install the plugin

1. Download the snipaid.zip file from this repository.

    To download from github click [here](https://github.com/snipaid-nlg/wp-plugin/raw/main/snipaid.zip).
    
    [<img src="https://user-images.githubusercontent.com/36483428/218520593-9787ba5b-00b7-4504-8695-d14516e96be5.png" width="129" height="20">](https://github.com/snipaid-nlg/wp-plugin/raw/main/snipaid.zip)

2. From your WordPress dashboard, choose ***Plugins > Add New***.
      
    <img width="152" alt="Wordpress side menu. Plugin - Add new." src="https://user-images.githubusercontent.com/36483428/218521906-ef0dcec4-e9c2-4c0c-94bb-7a54b47018e4.png">

3. Click ***Upload Plugin*** at the top of the page.

    <img width="700" alt="Button at the top of the page with lable 'Upload Plugin'" src="https://user-images.githubusercontent.com/36483428/218522702-2bd43f8c-fb72-469d-a066-260caa510c53.png">
    
4. Click ***Choose File***, locate the plugin .zip file, then click ***Install Now***.

    <img width="410" alt="File selection field and button 'Install Now'" src="https://user-images.githubusercontent.com/36483428/218524128-5a5194a7-b7ed-409d-af1c-f32094da991d.png">

5. After the installation is complete, click ***Activate Plugin***.

6. From the Settings page, click ***Generate API Key*** to generate a new API Key.

    <img width="560" alt="Settings page" src="https://user-images.githubusercontent.com/36483428/218527149-ef33b985-95d1-480d-b08d-7bd96025c712.png">

7. Save changes and copy the webhook URL.

8. Go to [snipaid.tech](https://www.snipaid.tech). Generate some snippets and send it to the webhook URL.

9. To see if setup was successful, go to your Wordpress dashboard and check ***Posts***. You should see a new post draft with the text and snippets you just generated.

You're good to go!
    
## Settings

By default the plugin creates a new Wordpress post with the data it receives and saves it as ***Draft***.
    
If you'd like the Plugin to publish Posts directly, go to the plugin's settings page. Change Post Settings to ***Publish*** and ***Save Changes***.  
When the plugin receives data now, it will create a post and publish it for you.
    
    
