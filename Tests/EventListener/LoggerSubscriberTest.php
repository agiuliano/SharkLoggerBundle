<?php

/*
 * This file is part of the FormLoggerBundle package.
 *
 * (c) Andrea Giuliano <giulianoand@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shark\FormLoggerBundle\Test\EventListener;

use Shark\FormLoggerBundle\Form\EventListener\LoggerSubscriber;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Form\Tests\AbstractFormTest;
use Symfony\Component\Form\FormConfigBuilder;


class LoggerSubscriberTest extends AbstractFormTest
{
    protected $loggerSubscriber;
    protected $event;
    protected $logPath;

    public function setUp()
    {
        parent::setUp();

        $session = $this->getMock("Symfony\\Component\\HttpFoundation\\Session\\Session");
        $this->logPath = __DIR__."/../logs";
        $this->loggerSubscriber = new LoggerSubscriber($this->logPath, $session);

    }

    public function testLogFormSimple()
    {
        $form = $this->getBuilder('simple_form')
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->getForm();

        $child = $this->getBuilder('field')->getForm();

        $form->add($child);

        $form->bind(array('foo'));

        $child->addError(new FormError('Error!'));

        $this->assertFalse($form->isValid(), "simple_form is not valid");

        $this->loggerSubscriber->logForm($form);

        $file = sprintf("%s/%s", $this->logPath, "simple_form.log");

        $this->assertFileExists($file, "File log was not created");

        $log = fopen($file, 'r');

        $contents = fread($log, filesize($file));

        $this->assertRegExp("/([field])([Errors: Error!])/", $contents);

        fclose($log);

        unlink($file);
    }

    public function testLogGlobalError()
    {
        $form = $this->getBuilder('simple_form')
            ->setCompound(true)
            ->setDataMapper($this->getDataMapper())
            ->getForm();

        $form->bind(array('foo'));

        $form->addError(new FormError('global_error'));
        $form->addError(new FormError('global_error_2'));

        $this->assertFalse($form->isValid());

        $this->loggerSubscriber->logForm($form);

        $file = sprintf("%s/%s", $this->logPath, "simple_form.log");

        $this->assertFileExists($file, "File log was not created");

        $log = fopen($file, 'r');

        $contents = fread($log, filesize($file));

        $this->assertRegExp("/(global_error)/", $contents);

        fclose($log);

        unlink($file);
    }


    protected function createForm()
    {
        return $this->getBuilder()->getForm();
    }
}
