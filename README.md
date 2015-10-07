# phpbb-3.0.X-evesso


### Summary
- Prelude
- How to ?
- Integrate it
- Who i'm ?
- Know Issues

## Prelude

To introduce, all files i'll talk about are available in the ``phpbb-files`` directory. The files are following phpbb structure.
For example, ``session.php`` is in ``phpbb-files/includes/session.php`` as it does in phpbb 3.0.X

And now, we'll see ... How to be registered on EvE-Developers website :)
To start, go on [https://developers.eveonline.com](https://developers.eveonline.com). Log in with you eve account (at the top right corner) then go in "Manage Applications".
Click on "Create New Application". Then you'll have to complete the form. About "Connection Type", that'll be "Authentication Only". And for the callback URL, you have to put your forum's URL with a special script.
Eg. my forum URL is ``http://frugu.net/`` so i need to put ``http://frugu.net/evesso.php`` ;) (You just have to add ``/evesso.php`` at the end !)
Then create the Application. You'll now get Client ID and Secret Key ! Keep them somewhere, you'll need them later !

## How to ?

First of all, create a ``evesso.php`` file on the phpbb root and copy all the content of ``phpbb-files/evesso.php`` in it.
You'll need to replace ``[[YOUR_DOMAIN]]`` tag by your domain, eg. i'll replace it by "frugu.net" is i want to use it for the frugu's forums.

After that, let's modify phpbb core !
Go in ``includes`` directory ! Open ``functions.php``. That'll be the most tricky file to edit cause it's separated in two parts.
Search ``$header_avatar = get_user_avatar($user->data['user_avatar'], $user->data['user_avatar_type'], 24, 24);`` and put the "First part" after that.
Don't forget to replace ``[[YOUR_FORUM_URL]]`` by your forum's url (eg. ``http://frugu.net/``) and ``[[YOUR_EVE_SSO_CLIENT_ID]]`` by the EvE-SSO Client ID you got on [https://developers.eveonline.com](https://developers.eveonline.com) .
After that, search ``'U_LOGIN_LOGOUT'		=> $u_login_logout,`` and put "Second part" just after it.

The hardest part is now done ! Let's save this file and open ``functions_user.php``. Go to the end of the file and copy the code there. That's all.

Save the last file and go to ``session.php``, still in that ``includes`` directory.
At the end of the file, you'll see ``return $forum_ids;`` and a ``}``. After that ``}``, copy the file i gave you !
Be carefull ! You need to copy this code between two ``}`` !
After that you've some replacement to do !
``[[YOUR_EVE_SSO_CLIENT_ID]]`` and ``[[YOUR_EVE_SSO_SECRET_KEY]]`` with both Client ID and Secret Key you got on developers website and finally ``[[YOUR_FORUM_URL]]` with your forum URL.

## Integrate it

There we go ! You've now all the php code to run that, let's see about integrate it into your phpbb style !

First of all, go into your used style directory ``styles/YOUR_STYLE_NAME``.
After that, we'll start with an easy step ! Go in ``imageset`` directory and copy the ``EVESSO.png`` file in it. Fast & great. You can change this image later to have another, you can saw alternative on [THIS](https://developers.eveonline.com/resource/single-sign-on) page.

Go back in your style directory and then in ``templates``. Open the ``overall_header.html`` file.
Search for ``<!-- IF not S_USER_LOGGED_IN and not S_IS_BOT -->``. You'll replace the whole code between that last code i put and ``<!-- ENDIF -->`` by what i gave to you.
That'll replace the login stuff by an EvE-SSO image with the good redirection to login :)

After that, you just have to try it, and that'll work ;)

## Who i'm ?

I'm [Mealtime](https://zkillboard.com/character/91901482/) from the corporation "SnaiLs aNd FroGs" (which is member of french alliance "Drama Sutra").
And also co-creator of [Frugu](http://frugu.net/), a french FHC, cause we all love drama !

I give that plugin free to use, but if you like my job you can send a donation to the In-Game character "Mealtime" :)
Thanks !

## Know Issues

- User registered throught EvE-SSO way can't use ACP. You need to give them a "real" password by modifying it in the ACP. After that, they'll not be able to use the EvE-SSO connection again !
