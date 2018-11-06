<?php

namespace EventBundle\Twig;


use Yoda\EventBundle\Util\DateUtil;

class EventExtension extends \Twig_Extension
{
    public function getName(){
        return 'event';
    }

    /**
     * Filtrul pentru pipe-ul ago din twig, pe care il creez aici
     * @return array|\Twig_SimpleFilter[]
     */
    public function getFilters(){
        return [
            new \Twig_SimpleFilter('ago', [
                $this, 'calculateAgo'
            ])
        ];
    }

    public function calculateAgo(\DateTime $dt){
        return DateUtil::ago($dt);
    }
}