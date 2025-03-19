<?php

namespace Grav\Plugin;

use Grav\Common\Grav;
use \Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use Grav\Common\Page\Interfaces\PageInterface;
use Grav\Plugin\Admin\Admin;

class AdminAddonTypesPlugin extends Plugin
{
    public static function getSubscribedEvents(): array
    {
        return [
            'onAdminPageTypes' => ['onAdminPageTypes', 0]
        ];
    }

    public function onAdminPageTypes(Event $event)
    {
        $types = $event['types'];

        $page = $this->getPage();
        if (!$page) {
            return;
        }

        $blueprint = $page->getBlueprint();

        $childrenTypes =  $blueprint->get('children-page-types');

        if (!$childrenTypes) {
            return;
        }

        foreach ($types as $type => $typeLabel) {
            if (!in_array($type, $childrenTypes)) {
                unset($types[$type]);
                continue;
            }
        }

        $event['types'] = $types;
    }

    public static function getPage()
    {
        $grav = Grav::instance();

        $pages = Admin::enablePages();

        $route = '/' . ltrim($grav['admin']->route, '/');

        /** @var ?PageInterface $page */
        $page = $pages->find($route);
        return $page;
    }
}
