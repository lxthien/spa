<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        // Homepage
        $menu->addChild('Home', ['route' => 'homepage']);

        // Homepage
        $menu->addChild('AboutUs', [
            'route' => 'news_show',
            'routeParameters' => ['slug' => 'gioi-thieu']
        ]);

        // News
        $menu->addChild('News', [
            'route' => 'news_category',
            'routeParameters' => ['level1' => 'tin-tuc']
        ]);

        $menu['News']->addChild('Business', [
            'route' => 'list_category',
            'routeParameters' => ['level1' => 'tin-tuc', 'level2' => 'tin-thi-truong']
        ]);

        // Contact us
        $menu->addChild('ContactUs', ['route' => 'contact']);

        return $menu;
    }

    public function footerMenu(FactoryInterface $factory, array $options)
    {
        $footerMenu = $factory->createItem('root');

        return $footerMenu;
    }
}