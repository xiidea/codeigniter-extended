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

5. CIX Twig Global Variables:
-------------------------
There are some global variables available to use in the twig templates:

* APPPATH - The path to the Application directory
* DIRECTORY_SEPARATOR - The OS depended directory separator (\ or /)
* \_\_FILE\__ - Current twig template path
* controller - The current controller path
* app.user - Current logged in user
* app.session - Current user session

6. CIX Twig functions:
----------------------
You can call any global function from twig template just by call as member function of **fn** object. For an example, if you like to use **site_url()** function you can call it as <code>fn.site_url()</code>
Besides that here is the available list of CIX twig functions you can use. More function will be added soon....

* _t - localization translating function (will only work if your application is **localize-ready**)
* nonce - Create a nonce variable
* valid_nonce - validate nonce value
* anchor - alias for CI anchor function
* logout_url - return logout url

*For full Twig reference, visit [Twig Site](http://twig.sensiolabs.org/)
