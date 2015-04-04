<?php

namespace LosUser\Form;

use LosUser\Options\IdentityOptionsInterface;
use Zend\InputFilter\InputFilter;

class LoginFilter extends InputFilter
{
    public function __construct(IdentityOptionsInterface $options)
    {
        $identityParams = array(
            'name' => 'identity',
            'required' => true,
            'validators' => array(),
        );

        $identityFields = $options->getIdentityFields();
        if ($identityFields == array(
            'email',
        )) {
            $validators = array(
                'name' => 'EmailAddress',
            );
            array_push($identityParams['validators'], $validators);
        }

        $this->add($identityParams);

        $this->add(array(
            'name' => 'credential',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 3,
                    ),
                ),
            ),
            'filters' => array(
                array(
                    'name' => 'StringTrim',
                ),
            ),
        ));
    }
}
