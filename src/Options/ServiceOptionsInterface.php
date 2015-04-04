<?php

namespace LosUser\Options;

interface ServiceOptionsInterface
{
    public function setUserEntityClass($userEntityClass);

    public function getUserEntityClass();
}
