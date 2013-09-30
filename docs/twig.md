Using Twig Template Engine
===========================
CIX Bundled with twig template engine. And configured with CIX_twig extension to allow some Codeigniter function in your twig template
Here some instruction to get best out of your twig template.

1. Enable twig feature:
-----------------------
If initially you chose not to use twig, don't worry, you can install it again just by running <code>composer update twig/twig</code> after enabling the use-twig value in your composer file

2. Disable Twig Temporarily:
---------------------------
If you have installed but do not want to use the twig for some reason, you can temporarily disable the twig. To do so just set <code>$config['enable_twig'] = FALSE;</code> at **{APPDIR}/config/config.php**.

3. Twig template location:
--------------------------
The Twig template is configurable throw a config variable. Set <code>$config['twig_dir'] = 'your_chosen_dir';</code> at **{APPDIR}/config/config.php**. By default it is "twig".

4. Twig Instance:
-----------------
You can access the Twig instance from your controller by $this->twig();

5. Create Twig Extension
-------------------------
You can create you own extension following the twig [guideline](http://twig.sensiolabs.org/doc/advanced.html#creating-an-extension). You can put the extension in the application/library directory or in your own library bundle created in the src/libs directory using Name space.
Then you can register the extension in two way. You can register as with global instance or you can register on demand.
To register with global twig instance you need to do following things:
* Add it to the configuration file. <code>$config['twig_extensions'] = array('your_extension_with_full_name_space');</code> at **{APPDIR}/config/config.php**
* define constructor in your extension with a parameter that expect CI_Controller Object (optional, but this way you will get access to the controller object from your extension)
```php
    public function __construct(\CI_Controller $ci)
    {

    }
```

You can also register twig extension whenever you need by using the Twig instance.

```php

$this->twig()->addExtension(new Your_Twig_Extension());

```

6. CIX Twig Global Variables:
-------------------------
There are some global variables available to use in the twig templates:

* APPPATH - The path to the Application directory
* DIRECTORY_SEPARATOR - The OS depended directory separator (\ or /)
* \_\_FILE\__ - Current twig template path
* controller - The current controller path
* app.user - Current logged in user
* app.session - Current user session

7. CIX Twig functions:
----------------------
You can call any global function from twig template just by call the function prefixed with php_. The implementation is inspired from [LidaaTwigBundle](https://github.com/lidaa/LidaaTwigBundle/blob/master/Resources/doc/php.rst). For an example, if you like to use **site_url()** function you can call it as <code>php_site_url()</code>
Besides that here is the available list of CIX twig functions you can use. More function will be added soon....

* _t - localization translating function (will only work if your application is **localize-ready**)
* nonce - Create a nonce variable
* valid_nonce - validate nonce value
* logout_url - return logout url

*For full Twig reference, visit [Twig Site](http://twig.sensiolabs.org/)
