<?php

namespace EPHEC\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EPHECUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
