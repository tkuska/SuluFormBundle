<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\FormBundle\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Navigation\Navigation;
use Sulu\Bundle\AdminBundle\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Routing\RouteBuilderFactoryInterface;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

/**
 * Generated by https://github.com/alexander-schranz/sulu-backend-bundle.
 */
class FormAdmin extends Admin
{
    const LIST_ROUTE = 'sulu_form.list';
    const LIST_ROUTE_DATA = 'sulu_form.list.data';
    const FORM_ROUTE = 'sulu_form.forms';
    const ADD_FORM_ROUTE = 'sulu_form.add_form';
    const ADD_FORM_DETAILS_ROUTE = 'sulu_form.add_form.details';
    const EDIT_FORM_ROUTE = 'sulu_form.edit_form';
    const EDIT_FORM_DETAILS_ROUTE = 'sulu_form.edit_form.details';

    private $securityChecker;
    private $routeBuilderFactory;
    private $webspaceManager;

    /**
     * FormAdmin constructor.
     * @param SecurityCheckerInterface $securityChecker
     * @param RouteBuilderFactoryInterface $routeBuilderFactory
     * @param WebspaceManagerInterface $webspaceManager
     */
    public function __construct(
        SecurityCheckerInterface $securityChecker,
        RouteBuilderFactoryInterface $routeBuilderFactory,
        WebspaceManagerInterface $webspaceManager
    ) {
        $this->securityChecker = $securityChecker;
        $this->routeBuilderFactory = $routeBuilderFactory;
        $this->webspaceManager = $webspaceManager;
    }

    public function getNavigation(): Navigation
    {
        $rootNavigationItem = $this->getNavigationItemRoot();

        if ($this->securityChecker->hasPermission('sulu.form.forms', PermissionTypes::VIEW)) {
            $navigationItem = new NavigationItem('sulu_form.forms');
            $navigationItem->setIcon('su-magic');
            $navigationItem->setPosition(10);
            $navigationItem->setMainRoute(static::LIST_ROUTE);
            $rootNavigationItem->addChild($navigationItem);
        }

        return new Navigation($rootNavigationItem);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getJsBundleName()
    {
        return 'suluform';
    }


    public function getRoutes(): array
    {
        $formLocales = array_values(
            array_map(
                function (Localization $localization) {
                    return $localization->getLocale();
                },
                $this->webspaceManager->getAllLocalizations()
            )
        );
        $formToolbarActions = [
            'sulu_admin.save',
            'sulu_admin.delete',
        ];
        $listToolbarActions = [
            'sulu_admin.add',
            'sulu_admin.delete'
        ];
        $dataListToolbarActions = [
            'sulu_admin.delete',
            'sulu_admin.export'
        ];

        return [
            $this->routeBuilderFactory->createListRouteBuilder(static::LIST_ROUTE, '/forms/:locale')
                ->setResourceKey('forms')
                ->setListKey('forms')
                ->setTitle('sulu_form.forms')
                ->addListAdapters(['table'])
                ->addLocales($formLocales)
                ->setDefaultLocale($formLocales[0])
                ->setAddRoute(static::ADD_FORM_ROUTE)
                ->setEditRoute(static::EDIT_FORM_ROUTE)
                ->enableSearching()
                ->addToolbarActions($listToolbarActions)
                ->getRoute(),
            $this->routeBuilderFactory->createResourceTabRouteBuilder(static::ADD_FORM_ROUTE, '/forms/:locale/add')
                ->setResourceKey('forms')
                ->addLocales($formLocales)
                ->setBackRoute(static::LIST_ROUTE)
                ->getRoute(),
            $this->routeBuilderFactory->createFormRouteBuilder(static::ADD_FORM_DETAILS_ROUTE, '/details')
                ->setResourceKey('forms')
                ->setFormKey('form_details')
                ->setTabTitle('sulu_form.general')
                ->setEditRoute(static::EDIT_FORM_ROUTE)
                ->addToolbarActions($formToolbarActions)
                ->setParent(static::ADD_FORM_ROUTE)
                ->getRoute(),
            $this->routeBuilderFactory->createResourceTabRouteBuilder(static::EDIT_FORM_ROUTE, '/forms/:locale/:id')
                ->setResourceKey('forms')
                ->addLocales($formLocales)
                ->setBackRoute(static::LIST_ROUTE)
                ->getRoute(),
            $this->routeBuilderFactory->createFormRouteBuilder(static::EDIT_FORM_DETAILS_ROUTE, '/details')
                ->setResourceKey('forms')
                ->setFormKey('form_details')
                ->setTabTitle('sulu_form.general')
                ->addToolbarActions($formToolbarActions)
                ->setParent(static::EDIT_FORM_ROUTE)
                ->getRoute(),
            $this->routeBuilderFactory->createListRouteBuilder(static::LIST_ROUTE_DATA, '/form_data')
                ->setResourceKey('dynamics')
                ->setListKey('form_data')
                ->setTabTitle('sulu_form.data')
                ->addListAdapters(['table'])
                ->addRouterAttributesToListStore(['id' => 'form'])
                ->addRouterAttributesToListMetadata(['id' => 'id'])
                ->addToolbarActions($dataListToolbarActions)
                ->setParent(static::EDIT_FORM_ROUTE)
                ->getRoute(),
            ];
    }


    /**
     * {@inheritdoc}
     */
    public function getSecurityContexts()
    {
        return [
            'Sulu' => [
                'Form' => [
                    'sulu.form.forms' => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
