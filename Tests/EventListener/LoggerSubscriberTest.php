<?php
/**
 * LoggerSubscriberTest.php
 * @author Andrea Giuliano <giulianoand@gmail.com>
 *         Date: 03/11/12
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
        $session = $this->getMock("Symfony\\Component\\HttpFoundation\\Session\\Session");
        $this->logPath = __DIR__."/../logs";
        $this->loggerSubscriber = new LoggerSubscriber($this->logPath, $session);

        parent::setUp();
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

        $this->assertFalse($form->isValid(), "il form non è valido");

        $this->loggerSubscriber->logForm($form);

        $file = sprintf("%s/%s", $this->logPath, "simple_form.log");

        $this->assertFileExists($file, "File log was not created");

        $log = fopen($file, 'r');

        $contents = fread($log, filesize($file));

        $this->assertRegExp("/([field])([Errors: Error!])/", $contents);

        fclose($log);

        unlink($file);

    }

    protected function createForm()
    {
        return $this->getBuilder()->getForm();
    }
}
