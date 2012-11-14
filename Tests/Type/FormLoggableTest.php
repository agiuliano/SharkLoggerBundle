<?php
/**
 * FormLoggableTest.php
 * @author Andrea Giuliano <giulianoand@gmail.com>
 *         Date: 03/11/12
 */
namespace Shark\FormLoggerBundle\Test\EventListener;

use Shark\FormLoggerBundle\Form\Type\FormLoggable;
use Shark\FormLoggerBundle\Form\EventListener\LoggerSubscriber;

class FormLoggableTest extends \PHPUnit_Framework_TestCase
{
    protected $loggable;
    protected $builder;

    public function setUp()
    {
        $session = $this->getMock("Symfony\\Component\\HttpFoundation\\Session\\Session");
        $this->logPath = __DIR__."/../../../../../app/logs";
        $loggerSubscriber = new LoggerSubscriber($this->logPath, $session);

        $this->loggable = new FormLoggable($loggerSubscriber);
        $this->builder = $this->getMock("Symfony\\Component\\Form\\FormBuilder", array(), array(), '', false);
    }

    public function testEventSubscriberIsAddedWithLoggableOption()
    {
        $this->builder->expects($this->once())
            ->method('addEventSubscriber');

        $this->loggable->buildForm($this->builder, array('loggable' => true));
    }

    public function testEventSubscriberIsNotAddedWithoutLoggableOption()
    {
        $this->builder->expects($this->never())
            ->method('addEventSubscriber');

        $this->loggable->buildForm($this->builder, array());
    }

    /**
     * @dataProvider getWrongLoggableOption
     */
    public function testEventSubscriberIsNotAddedWithWrongLoggableOption($options)
    {
        $this->builder->expects($this->never())
            ->method('addEventSubscriber');
        $this->loggable->buildForm($this->builder, $options);
    }

    public function getWrongLoggableOption()
    {
        return array(
            array(
                array('loggable' => 'foo'),
            ),
            array(
                array('loggable' => false),
            ),
            array(
                array('foo' => true,)
            )
        );

    }
}
