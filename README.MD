# GitHub Language

![php](https://img.shields.io/badge/php-8.1-informational)
![api](https://img.shields.io/badge/pocketmine-5.0-informational)

GitHub-Language is a very advanced github based language library.
You can use it, if you have an international server and want to translate messages, popups, uis etc. to the player's personal language.
All the language files can be stored on a GitHub repository, which makes it a lot easier to edit and contribute languages.
It also includes auto translations for ui's and command descriptions.

## Categories
+ [Registration](#registration)
    + [Explanation](#explanation)
    + [Example](#example)
+ [Language](#language)
    + [Language Manager](#language-manager)
      + [Reload Languages](#if-you-want-to-reload-the-languages-for-example-using-a-command-you-can-just-do)
        + [LanguageReloadEvent](#itll-automatically-call-a-languagereloadevent-once-its-done-if-you-cancel-that-event-it-wont-apply-the-reloaded-changes)
      + [Add a Language](#if-you-want-to-add-an-own-language-per-code-just-do)
      + [Search a Language](#you-can-also-search-for-languages-if-you-want-to-get-a-language-per-its-name-or-shortcut-this-can-be-really-useful-for-commands-fe-a-translate-from-to-text-command-by-doing)
    + [Translatable Trait](#translatable-trait)
    + [Translating](#translating)
    + [Get an array value](#)
+ [Language Repository](#language-repository)
  + [Required file (locales.txt)](#required-file)
  + [Language file example](#this-is-how-your-file-should-look-like-this-example-is-a-file-of-the-language-english)

## Registration
### Explanation
- `$this` is the instance of your plugin base
- `$repositoryType` is basically the type of your language repository, which contains the url and an access token if the repository is private
- `$getPlayerLanguage` is a closure function that returns the player's language. This is used to automatically translate command descriptions and ui contents to the player's language. You can use null if you don't want to use this feature
### Example 
```php

protected function onEnable(): void{
    new LanguageManager(
        $this,
        RepositoryType::PUBLIC("YourOrganization/language/main/") //RepositoryType::PUBLIC("YourOrganization/language/main/", "ACCESS_TOKEN)
        fn (Player $player): Language => Session::get($player)->getLanguage()
    );
}
```

## Language
### Language Manager
#### If you want to reload the languages (for example using a command), you can just do
```php
LanguageManager::getInstance()->reloadLanguages();
```
#### It'll automatically call a LanguageReloadEvent once it's done. If you cancel that event, it won't apply the reloaded changes
```php
public function onLanguageReload(LanguageReloadEvent $event): void{
    $event->cancel(); //To cancel the event
    $event->setLanguages($customLanguages); //To manually change the reloaded languages 
}
```
#### If you want to add an own language per code, just do
```php
LanguageManager::getInstance()->addLanguage($firstLanguage, $secondLanguage, $thirdLanguage, $etc);
```
#### You can also search for languages if you want to get a language per it's name or shortcut (this can be really useful for commands, f.e. a "/translate <from> <to> <text>" command) by doing
```php
$language = LanguageManager::getInstance()->searchLanguage($string); //Returns Language or null if the language couldn't be found
```

### Translatable Trait
#### The TranslatableTrait can be really useful if you want to store a language in a class and use it to translate something. Basicall do
```php
class MyClass{
    use TranslatableTrait;
    
    public function __construct(){
        $this->setLanguage(LanguageManager::getInstance()->getLanguage("my_language")); //You can make this dynamic if you want
    }
}
```
#### to initialize the trait and set a default language. Now you can easily get the language or translate something by doing
```php
$language = $myClass->getLanguage(); //Returns the initialized language object
$translated = $myClass->translate("message.helloWorld", ["Example"]); //Output: "Example: Hello world"
```

### Translating
#### There are multiple ways to translate a key to the player's language. The first one would be this
```php
$playerLanguage->translate($text, $params);
```
#### that way you always have to get the player's language first, which might get a little bit annoying, so here's an easier way
```php
Language::translateStatic($player, $text, $params);
```
#### this will automatically get the player's language, using the `getPlayerLanguage` closure you defined in the LanguageManager and translate the given text into the player's language.
#### **IMPORTANT NOTE**: This will only work, if you have defined a get `getPlayerLanguage` closure in the LanguageManager, otherwise it'll just return your `$text` so it won't get translated.
#### You might wonder what `$params` is. You can basically pass custom parameters into the text, by using this variable.
#### For example, if you have a "/fly" command, and you want to pass the targets name to the success message, just do
```php
Language::translateStatic($sender, "message.fly.success.other", [$target->getName()]);
```
#### In order to actually display the target's name, you need to use the parameter in the language key as well, by doing
```
"message.fly.success.other": "§aYou have successfully toggled the flight of§b {0}§a!",
```
#### as you can see, i used {0} here, which is gonna get replaced with the actual target name.
#### If you have multiple parameters, just count up the number between the {}, for example {0}, {1}, {2}
#### Here's another example
```php
//"message.counting": "This message will count {0} numbers. {1}, {2}, {3}. {4}"
Language::translateStatic($sender, "message.counting", ["up", 1, 2, 3, "Bin Chillin"]); //Output: "This message will count up numbers. 1, 2, 3. Bin Chillin"
```

### Translate/Get an array value
#### As you can see in the example below, it's also possible to add array values to your language file.
#### Since `Language::translate` only supports strings, you might wonder how you're supposed to get the translated array value then.
#### Well, it's actually really simple, if you want to get the raw value (like without any parameters or replacements) or an array value. Just do
```php
$language->getValue($key, $default);
//Example: $language->getValue("broadcasts", ["This is an example broadcast"]);
```

## Language Repository
### Required File
#### You will need the following file in order to make the plugin able to actually get the languages from your repo.
#### The file name is `locales.txt`, it'll contain the locales of all your languages. Here's a little example
```
en_US
de_DE
gr_GR
tr_TR
```

### Language file
#### Now where you've put the locale of your language in the `locales.txt` file, you can create your actual language file.
#### You basically only need to define a few default values, everything else is optional.
#### This is how your file should look like (this example is a file of the language "English")
```json
{
  "name": "English", //the name of the language
  "prefix": "§bMyServer §8>§7 ", //#optional | the prefix of the language (recommend to use the server prefix)
  "locale": "en_US", //the locale of the language (should be the same as the file name)
  "shortcut": "en", //the language's shortcut to make it easier for commands and other things to get the language by a string
  "contributors": ["Jibix"], //#optional | the language's contributors (in case you want to display the contributors in some ui or something)
  
  "values": { //the language's values that eventually get translated to the player's language 
    "raw.none": "§cNone",

    "time.day": "Day",
    "time.night": "Night",

    "message.fly.success": "{PREFIX}§aYou have successfully toggled your flight!", //{PREFIX} will basically get replaced with the language's selected prefix
    "message.fly.success.other": "{PREFIX}§aYou have successfully toggled the flight of§b {0}§a!",
    
    "command.description.fl": "Toggle your flight!",
    
    "ui.title.fly": "§bThis is a fly UI",
    "ui.description.fly": "Click a button to toggle your flight!",
    "ui.button.confirm": "§aEnable",
    "ui.button.cancel": "§cDisable",

    "popup.welcome": "§aWelcome to the server§b {0}§a, you got§6 {1}§a start coins!",

    "broadcasts": [
      "§aStar this repository!",
      "§bFollow J1b1x on github!",
      "§5You like men!"
    ]
  }
}
```