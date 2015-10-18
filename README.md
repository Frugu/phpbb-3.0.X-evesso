# phpbb-3.0.X-evesso


### Summary
- [Prelude](https://github.com/Frugu/phpbb-3.0.X-evesso#prelude)
- [How to ?](https://github.com/Frugu/phpbb-3.0.X-evesso#how-to-)
- [Integrate it](https://github.com/Frugu/phpbb-3.0.X-evesso#integrate-it)
- [How it works !](https://github.com/Frugu/phpbb-3.0.X-evesso#how-it-works-)
- [Who i'm ?](https://github.com/Frugu/phpbb-3.0.X-evesso#who-im-)
- [Know Issues](https://github.com/Frugu/phpbb-3.0.X-evesso#know-issues)

## Prelude

Hi,

My name is Mealtime and I am the developer of this plugin.
As you may know, there is a large demand to securely verify membership of a person on PHPBB forums. In the past there was a module by Cyerus that worked based on the XML API, but this is slow and very fragile for spying. So I decided to work around this problem and design a new login system using the EVE Single Sign On. This way members can link their accounts to their EVE login, and only login via an eve account. This will allow for safer and less privacy sensitive authentication.

#### So what do I need?

- PHPBB 3.0.X
- Download the source files in this package.
- An active account on EVE Online.

#### Initial Steps:

In the first set of steps before applying this mod, we will create a developer key for the EVE SSO. In order to do this, please go to https://developers.eveonline.com. Here we will create an application for your webpage by logging into your EVE account and press "Create New Application".

Fill out the form, and set the connection type to "Authentication Only". You'll be asked for a callback URL, which is the URL that refers to the modified script for PHPBB. Normally this script is located in the root of your forum, meaning that your callback URL would be "www.example.com/evesso.php".

When you've done this, you'll see a Client ID and Secret Key. You NEED to save this in order to connect to the application at a later stage.

## How to ?

First of all, create a ``evesso.php`` file on the phpbb root and copy all the content of ``phpbb-files/evesso.php`` in it.
You'll need to replace ``[[YOUR_DOMAIN]]`` tag by your domain, eg. i'll replace it by "frugu.net" is i want to use it for the frugu's forums.

After that, let's modify phpbb core !
Go in ``includes`` directory ! Open ``functions.php``. That'll be the most tricky file to edit cause it's separated in two parts.
Search 
```php
// The following assigns all _common_ variables that may be used at any point in a template.
```
and put the "First part" before that.
Don't forget to replace ``[[YOUR_FORUM_URL]]`` by your forum's url (eg. ``http://frugu.net/``) and ``[[YOUR_EVE_SSO_CLIENT_ID]]`` by the EvE-SSO Client ID you got on [https://developers.eveonline.com](https://developers.eveonline.com) .
After that, search 
```php
'U_LOGIN_LOGOUT'		=> $u_login_logout,
``` 
and put "Second part" just after it.

The hardest part is now done ! Let's save this file and open ``functions_user.php``. Go to the end of the file and copy the code there. That's all.

Save the last file and go to ``session.php``, still in that ``includes`` directory.
At the end of the file, you'll see
```php
return $forum_ids;
```
and a `` } ``. After that
`` } `` , copy the file i gave you !
Be carefull ! You need to copy this code between two `` } `` !
After that you've some replacement to do !
``[[YOUR_EVE_SSO_CLIENT_ID]]`` and ``[[YOUR_EVE_SSO_SECRET_KEY]]`` with both Client ID and Secret Key you got on developers website and finally ``[[YOUR_FORUM_URL]]` with your forum URL.

## Integrate it

There we go ! You've now all the php code to run that, let's see about integrate it into your phpbb style !

First of all, go into your used style directory ``styles/YOUR_STYLE_NAME``.
After that, we'll start with an easy step ! Go in ``imageset`` directory and copy the ``EVESSO.png`` file in it. Fast & great. You can change this image later to have another, you can saw alternative on [THIS](https://developers.eveonline.com/resource/single-sign-on) page.

Go back in your style directory and then in ``templates``. Open the ``overall_header.html`` file.
Search for
```html
<!-- IF not S_USER_LOGGED_IN and not S_IS_BOT -->
```
You'll replace the whole code between that last code i put and
```html
<!-- ENDIF -->
```
by what i gave to you.
That'll replace the login stuff by an EvE-SSO image with the good redirection to login :)

After that, you just have to try it, and that'll work ;)

## How it works !

Oh god, the best part ! (Yeah yeah yeah)
To start this, i'll just link a song, cause it's always better to work with music: https://www.youtube.com/watch?v=ec0XKhAHR5I

So this is quite simple in real.

The user connect throught the EvE-SSO then redirect on evesso.php. In this script, we match a corp & alliance for the character just logged.
With that data we check if they're on whitelist and to which group they can access.
If they don't have a created account, we create one and we affect groups to it.
If they already have an account, we delete all groups that are affectable (eg. directors groups are often not in this whitelist) and we affect missing groups.

#### What the fuck is that whitelist ?

The whitelist is a PHP Array that control the whole access of the forum. It's the main configuration of that plugin.
He's in ``evesso.php`` file and divided in 4 parts:

- ``groups``, this part is here to manage which groups are supposed to be monitored by this plugin. You need to put ALL GROUPS that you want to be deleted if someone leave the corp in this. Directors groups included ! (Even if you don't put directors groups in other parts)
- ``character``, this part is to link character with groups
- ``corporation``, and same as characters but for corporations
- ``alliance``, and same as corporations but for alliances

Link something (character/corporation/alliance) to a group, is quite easy.
You just have to put the id of the thing at the left, then the id to link at the right !

Let's do an use case:
We've a forum with some groups:

- Alliance Member (group: 12)
- Executive Corp Member (group: 13)
- Alliance Director (group: 14)

```php
$whitelist = Array(
    'groups'        => Array(
        12,
        13,
        14
    ),
    'character'     => Array(
    ),
    'corporation'   => Array(
        [EXECUTIVE_CORP_ID] => 13
    ),
    'alliance'      => Array(
        [ALLIANCE_ID]       => 12
    )
);
```

IDs can be found easely, go on zkillboard, check your corporation killboard then look in the link, there is a big number, it's your ID ;)
Eg. the corp "Black Doom Brotherhood of the Lightning Shadow" (i took the most long corp name i know :D) have the URL: https://zkillboard.com/corporation/98221583/, so his ID is 98221583.
It's same for characters & alliances.

## Who i'm ?

I'm [Mealtime](https://zkillboard.com/character/91901482/) from the corporation "SnaiLs aNd FroGs" (which is member of french alliance "Drama Sutra").
And also co-creator of [Frugu](http://frugu.net/), a french FHC, cause we all love drama !

I give that plugin free to use, but if you like my job you can send a donation to the In-Game character "Mealtime" :)
Thanks !

## Know Issues

- User registered throught EvE-SSO way can't use ACP. You need to give them a "real" password by modifying it in the ACP. After that, they'll not be able to use the EvE-SSO connection again !
