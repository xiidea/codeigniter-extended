Customize your installations
============================

Before run the composer install command you may like to customize some value as per your needs. Here are the things you can customize:

1) Enable/Disable Localization
-------------------------------
Set the value of "localize-ready": as per your need. Default is true. That means your application is ready to serve localized contents. Set it to "false" if you wish to develop single language application.


2) Change the application directory
-----------------------------------
You can configure you application directory to anything you like. The default application directory is "src/application". You can controle it by changing the value of "ci-app-dir"

3) Change the Document Root
-----------------------------------
By configuring the value of "ci-web-dir", you can name your web directory to anything you like(public/html/public_html/www).

4) Enable/Disable Twig
-------------------------------
Set the value of "use-twig": as per your need. Default is true. That means your application is ready to render twig template.

5) Enable/Disable Swift Mailer
-------------------------------
Set the value of "use-swift-mailer": as per your need. Default is true. That means your application is ready to use swit mailer library. There is a wrapper class bundled with CIS. You can use the wrapper or swift mailer directly in your code

```php
$mailer = Xiidea\Helper\SwiftMailer('mail');
$mailer->mail($to, $subject, $body, $from, $attachments = array(), $contentType = 'text/html', $charset = 'UTF-8');
```
