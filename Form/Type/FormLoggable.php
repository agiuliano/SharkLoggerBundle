<?php
/**
 * FormLoggable.php
 * @author Andrea Giuliano <giulianoand@gmail.com>
 *         Date: 17/10/12
 */
namespace Shark\FormLoggerBundle\Form\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Shark\FormLoggerBundle\Form\EventListener\LoggerSubscriber;

class FormLoggable extends AbstractTypeExtension
{
    /**
     * @var BindRequestListener
     */
    private $listener;

    public function __construct(LoggerSubscriber $logger)
    {
        $this->listener = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(array_key_exists('loggable', $options) && $options['loggable'] === true)
        {
            $builder->addEventSubscriber($this->listener);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
