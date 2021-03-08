<?php

namespace Prodigious\Sonata\MenuBundle\Knp;

use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Menu\Matcher\Voter\UriVoter as BaseVoter;

/**
 * Class UriVoter
 *
 * @package Prodigious\Sonata\MenuBundle\Knp
 * @author Danylo Baran <danil.baran15@gmail.com>
 */
class UriVoter extends BaseVoter
{
    /**
     * UriVoter constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack->getCurrentRequest()->getRequestUri());
    }
}