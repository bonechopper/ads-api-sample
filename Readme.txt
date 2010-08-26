Readme File for Facebook Ads API Sample Code
-------------------------------------------------------------
The Facebook Ads API is currently in a closed beta. If you would 
like to have an application enabled for the Facebook Ads API;
please go to: http://developers.facebook.com/docs/adsapi
and follow the instructions.

This repository contains the open source Ads API Sample code that 
allows you to utilize the above on your website. Except as 
otherwise noted, the Facebook Ads API Sample code is licensed 
under the Apache Licence, Version 2.0
(http://www.apache.org/licenses/LICENSE-2.0.html)

This sample code is built using the Facebook PHP-SDK available
at http://github.com/facebook/php-sdk. Please checkout the 
php-sdk first and copy the src/facebook.php file in the ./inc
directory.
 
The source code is provided as a reference.

Directories and Files

images/
login.gif - login image for facebook connect

logout.gif - logout image for facebook connect

inc/
Prevent direct access to inc/ through web server config.

adlib.php - implementation for the advertising API

common.php - Initialization functions for the library

facebook.php - this is the facebook php-sdk file.
Please see http://github.com/facebook/php-sdk

header.php - common header file to print the html 
header, essential java script for facebook connect

includes.php - include all relevant files

tmp/
Web Server should be able to write to tmp/
Empty directory for temporary image uploads

./
create_campaign.php - create a campaign example

index.php - ads sample app home page

create_adgroup.php - create an ad example

multi_create_nobatch.php - Example for creating
multiple adgroups with a single call

Getting Started

Please refer to: http://developers.facebook.com/docs/adsapi

To get started with the ads api, modify the common.php file 
to enter your application ID and secret. Make sure that the 
base domain is set for the Facebook app to base domain where 
the code is hosted. Please follow instructions listed here 
to setup your application - 

http://wiki.developers.facebook.com/index.php/Connect/Setting_Up_Your_Site#Setting_Up_Your_Application_and_Getting_an_API_Key 

Point your browser to the index.php file and that's it. 

Any questions - please email ads-api-bugs@publists.facebook.com 

FAQ:

Q) I am getting an error even after granting permissions to the 
   application "Fatal error:  Uncaught Exception: 294:
   Managing advertisements requires the extended permission
   ads_management, and a participating API key"

A) Make sure you've accepted the terms; please refer to
"Agreeing to the Ads API Terms" on 
http://developers.facebook.com/docs/adsapi


Notes:

The current release of Ads Example code doesn't have batch mode 
calls. The batch mode examples are planned for future release. To
implement batch mode support - please follow the instructions on
http://developers.facebook.com/docs/reference/rest/batch.run
