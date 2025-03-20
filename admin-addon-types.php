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
            'onPluginsInitialized' => [
                ['onPluginsInitialized', 0]
            ]
        ];
    }

    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->enable([
                'onAdminPageTypes' => [
                    ['onAdminPageTypes', 0]
                ]
            ]);
        }
    }

    public function onAdminPageTypes(Event $event)
    {
        $event['types'] = $this->filterAdminPageTypes($event['types']);
    }

    private function filterAdminPageTypes(array $types)
    {
        $page = $this->getCurrentAdminPage();

        if (!$page) {
            return $types;
        }

        $blueprint = $page->getBlueprint();

        $templatesSelector = $this->config->get('plugins.admin-addon-types.templates_selector');
        $childrenTypes =  $blueprint->get($templatesSelector);

        if (!$childrenTypes) {
            return $types;
        }

        foreach ($types as $type => $typeLabel) {
            if (!in_array($type, $childrenTypes)) {
                unset($types[$type]);
                continue;
            }
        }

        return $types;
    }

    public function getCurrentAdminPage()
    {
        $grav = Grav::instance();

        $pages = Admin::enablePages();

        $route = '/' . ltrim($grav['admin']->route, '/');

        /** @var ?PageInterface $page */
        $page = $pages->find($route);
        return $page;
    }
}
