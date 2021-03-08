<?php

namespace Prodigious\Sonata\MenuBundle\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Prodigious\Sonata\MenuBundle\Manager\MenuManager;

/**
 * Class KnpMenuAdapter
 *
 * Warning !
 * Using or calling this adapter require to install knplabs/knp-menu-bundle :
 * `composer require knplabs/knp-menu-bundle`
 *
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 */
class KnpMenuAdapter
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var MenuManager
     */
    protected $menuManager;

    /**
     * KnpMenuAdapter constructor.
     *
     * @param FactoryInterface $factory
     * @param MenuManager $menuManager
     */
    public function __construct(
        FactoryInterface $factory,
        MenuManager $menuManager
    ) {
        $this->factory = $factory;
        $this->menuManager = $menuManager;
    }

    /**
     * @param string $alias
     * @return ItemInterface
     */
    public function createMenu(string $alias): ItemInterface
    {
        $knp = $this->factory->createItem('root');

        $menu = $this->menuManager->loadByAlias($alias);
        // Get root list
        $items = $this->menuManager->getRootItems($menu, MenuManager::STATUS_ENABLED);

        foreach ($items as $item) {
            $this->recursiveAddItem($knp, $item);
        }

        return $knp;
    }

    /**
     * @param ItemInterface $menu
     * @param MenuItemInterface $menuItem
     * @return ItemInterface
     */
    protected function recursiveAddItem(ItemInterface $menu, MenuItemInterface $menuItem)
    {
        /** @var ArrayCollection $activeChildren */
        $activeChildren = $menuItem->getActiveChildren();
        $childMenu = $menu->addChild(sprintf('%s.%d', $menu->getName(), $menuItem->getId()), [
            'route' => $menuItem->getPage() ?? null,
            'uri' => $menuItem->getUrl(),
            'label' => $menuItem->getName(),
            'attributes' => [
                'dropdown' => !$activeChildren->isEmpty(),
            ],
            'linkAttributes' => [
                'target' => $menuItem->getTarget() ? '_blank' : null,
            ],
        ])
            ->setLinkAttribute('class', $menuItem->getClassAttribute())
        ;

        if (!$activeChildren->isEmpty()) {
            foreach ($activeChildren as $childPage) {
                $this->recursiveAddItem($childMenu, $childPage);
            }
        }

        return $menu;
    }
}