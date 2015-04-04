<?php

namespace LosUser\Form;

use Zend\Form\Element;
use LosBase\Form\AbstractForm;
use LosUser\Options\IdentityOptionsInterface;

class Login extends AbstractForm
{
    protected $authOptions;

    public function __construct($name, IdentityOptionsInterface $options)
    {
        $this->setIdentityOptions($options);
        parent::__construct($name);

        $this->add(array(
            'name' => 'identity',
            'options' => array(
                'label' => '',
            ),
            'attributes' => array(
                'type' => 'text',
            ),
        ));

        $emailElement = $this->get('identity');
        $label = $emailElement->getLabel('label');
        foreach ($this->getIdentityOptions()->getIdentityFields() as $mode) {
            $label = (! empty($label) ? $label.' or ' : '').ucfirst($mode);
        }
        $emailElement->setLabel($label);
        //
        $this->add(array(
            'name' => 'credential',
            'options' => array(
                'label' => 'Password',
            ),
            'attributes' => array(
                'type' => 'password',
            ),
        ));

        $submitElement = new Element\Button('submit');
        $submitElement->setLabel('Sign In')->setAttributes(array(
            'type' => 'submit',
        ));

        $this->add($submitElement, array(
            'priority' => - 100,
        ));

        $this->getEventManager()->trigger('init', $this);
    }

    public function setIdentityOptions(IdentityOptionsInterface $identityOptions)
    {
        $this->identityOptions = $identityOptions;

        return $this;
    }

    public function getIdentityOptions()
    {
        return $this->identityOptions;
    }
}
