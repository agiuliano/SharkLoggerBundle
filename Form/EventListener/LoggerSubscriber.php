<?php

/*
 * This file is part of the FormLoggerBundle package.
 *
 * (c) Andrea Giuliano <giulianoand@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shark\FormLoggerBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\Form;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpFoundation\Session\Session;

class LoggerSubscriber implements EventSubscriberInterface
{
    private $logPath;
    private $session;
    private $log;

    public function __construct($path, Session $session)
    {
        $this->logPath = $path;
        $this->session = $session;
    }
    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_BIND => 'logData');
    }

    /**
     * @param DataEvent $event
     */
    public function logData(DataEvent $event)
    {
        $form = $event->getForm();


        $this->logForm($form);
    }

    public function logForm(Form $form)
    {
        $formName = $form->getName();
        $this->generateLog($formName);

        $this->logElem($form, '[Global error]');

        foreach($form->all() as $child) {
            $this->walkAndLogChild($child);
        }
    }

    protected function walkAndLogChild(Form $form, $prefix = null)
    {
        $prefix = $this->prefixify($prefix, $form->getName());
        if ($form->count()) {
            foreach ($form->getChildren() as $child) {
                $this->walkAndLogChild($child, $prefix);
            }
        } else {
            $this->logElem($form, $prefix);
        }
    }

    protected  function logElem($elem, $prefix = null)
    {
        $token = substr($this->session->getId(), 0, 8);
        $errorString = implode(', ', $this->errorMessagesToArray($elem));
        $error = sprintf("%s => '%s' [Errors: %s]", $prefix, $elem->getViewData(), $errorString);
        $this->log->addError(sprintf("[%s] : %s", $token, $error));
    }

    protected function generateLog($name)
    {
        $this->log = new Logger('shark.form');
        if (!file_exists(sprintf("%s/%s.log", $this->logPath, $name))) {
            touch(sprintf("%s/%s.log", $this->logPath, $name));
        }
        $this->log->pushHandler(new StreamHandler(sprintf("%s/%s.log", $this->logPath, $name), Logger::WARNING));
    }

    protected function errorMessagesToArray($field)
    {
        $errors = array();
        foreach ($field->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }

    protected function prefixify($prefix, $string)
    {
        return sprintf("%s[%s]", $prefix, $string);
    }
}