![Logo sqStorage](https://www.picflash.org/img/2018/12/31/hwxkb96wq17sfvu.png "Logo sqStorage")

A easy to use and super quick way to organize your inventory, storage and storage areas.

## Language and Translations
At the moment sqStorage is available in German and in English.

***The language locale must be present on the webserver to be served***

### Updating the language files
The standard behavior of apache is to cache language files until the server is restarted.
To avoid the requirement to restart it every time the language file changes, the **message.po** file will be analyzed (time of last edit) and a copy will be linked automatically.

### Parsing the templates for new phrases
By running the command 
`vendor/smarty-gettext/smarty-gettext/tsmarty2c.php -o languages/sqstorage.pot templates/`
on the command line from within the sqStorage main directory, all templates will be analyzed for
`{t} {/t}` - Tags containing phrases.
Those phrases will be saved in the **languages/sqstorage.pot** file.

You can use this ***pot*** file as source to refresh the language **po** files.
Once you have refreshed a **po** file, compile it into the **message.mo**, overwriting the existing on
##### Example
Language: `de_CH`
Target-Directory for the **mo** file: `languages/locale/de_CH/LC_MESSAGES/`

### Translation
Create your own translation from the **languages/sqstorage.pot** file and save it in the corresponding language/locale/ subdirectory.

Add the new language to the existing array in
support/language_tools.php
`$langsAvailable = array('en_GB','de_DE','youLanguage');`
`$langsLabels = array(
    'en_GB' => 'English',
    'de_DE' => 'Deutsch',
    'yourLanguage' => 'SpecialNameOfYourLanguage'
);`

#### Howto: Example - new language `en_GB`
Copy the directory `languages/locale/de_DE` to `languages/locale/de_CH`
Open `languages/locale/de_CH/messages.po` with you PO-Editor
Optional: Update the file from the **languages/sqstorage.pot** file
Change the translations
Save the **po** file.
Compile in into `languages/locale/de_CH/LC_MESSAGES/messages.mo`

Add the new language to the existing array in
support/language_tools.php
`$langsAvailable = array('en_GB','de_DE','de_CH');`
`$langsLabels = array(
    'en_GB' => 'English',
    'de_DE' => 'Deutsch',
    'de_CH' => 'Schwiitzerdüütsch'
);`

Make sure you have compiled the locale on the server your webpage is running
Debian: `dpkg-reconfigure locales`





