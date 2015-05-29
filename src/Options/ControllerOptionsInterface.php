<?php

namespace LosUser\Options;

interface ControllerOptionsInterface
{
    public function setUseRedirect($useRedirect);

    public function getUseRedirect();

    public function getLoginRoute();

    public function setLoginRoute($loginRoute);
}
