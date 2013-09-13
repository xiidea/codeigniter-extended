<?php
/**
 * @Author: Roni Kumar Saha
 * Date: 12/31/12
 * Time: 1:01 PM
 */

namespace Xiidea\Helper;

class SwiftMailer
{
    private $_mailer = NULL;
    private $_message = NULL;
    private $_transport = 'mail';
    private $_config = NULL;
    private $_logger = NULL;
    private $_debug;

    function __construct($transport = 'mail', $config = NULL)
    {
        $this->_debug = ENVIRONMENT != 'production';

        $this->_transport = $transport;
        if($config != NULL){
            $this->_config    = $config;
        }

        $this->_mailer    = $this->_mailer_init();
    }

    public function getMailer()
    {
        return $this->_mailer;
    }

    public function Message($subject = "")
    {
        if ($this->_message === NULL) {
            $this->_message = $this->newInstance($subject);
        }
        return $this->_message;
    }

    private function newInstance($subject = "")
    {
        return \Swift_Message::newInstance($subject);
    }

    public function mail($to, $subject, $body, $from, $attachments = array(), $contentType = 'text/html', $charset = 'UTF-8')
    {
        if ($this->_message === NULL) {
            $this->_message = $this->newInstance();
        }

        $this->_message->setFrom($from)
            ->setBody($body, $contentType, $charset)
            ->setTo($to)
            ->setSubject($subject);

        foreach($attachments as $attachment){
            $this->attach($attachment['path'], $attachment['name']);
        }

        return $this->send();
    }

    public function setBody($body, $contentType = 'text/html', $charset = 'UTF-8')
    {
        $this->_message->setBody($body, $contentType, $charset);
        return $this->_message;
    }

    public function setFrom($from)
    {
        $this->_message->setFrom($from);
        return $this->_message;
    }

    public function setTo($to)
    {
        $this->_message->setTo($to);
        return $this->_message;
    }

    public function setSubject($subject)
    {
        $this->_message->setSubject($subject);
        return $this->_message;
    }

    public function send()
    {
        return $this->_mailer->send($this->_message);
    }

    public function attach($path, $name = NULL)
    {
        $attachment = \Swift_Attachment::fromPath($path);
        if(!empty($name)){
          $attachment->setFilename($name);
        }

        $this->_message->attach($attachment);

    }

    private function getTransport()
    {
        switch (strtolower($this->_transport)){
            case 'sendmail' :
                $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
                break;
            case 'smtp' :
                $transport = \Swift_SmtpTransport::newInstance($this->_config['host'],$this->_config['port'],$this->_config['security'])
                    ->setUsername($this->_config['username'])
                    ->setPassword($this->_config['password']);
                break;
            case 'mail' :
            default:
                $transport = \Swift_MailTransport::newInstance();
        }
        return $transport;
    }

    private function _mailer_init()
    {
        try{
            $transport = $this->getTransport();
            // Create the Mailer using your created Transport
            $mailer = \Mailer::newInstance($transport);

            if($this->_debug){
                $this->_logger = new \Swift_Plugins_Loggers_EchoLogger();
                $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($this->_logger));
            }
        }catch (\Exception $e){
            error_log('Swift Mailer is not enabled. Install swift mailer first by "composer update swiftmailer/swiftmailer"');
            show_error('Swift Mailer is not enabled. Install swift mailer first by "composer update swiftmailer/swiftmailer"');
        }

        return $mailer;
    }

}
