<?php

return [
    'name'        => 'MauticExtendeeEmailSettingBundle',
    'description' => '',
    'author'      => 'kuzmany.biz',
    'version'     => '1.0.0',
    'services' => [
        'events' => [
            'mautic.extendee.email.settings.inject.custom.content.subscriber' => [
                'class'     => 'MauticPlugin\MauticExtendeeEmailSettingBundle\EventListener\InjectCustomContentSubscriber',
                'arguments' => [
                    'mautic.helper.templating',
                    'mautic.extendee.email.settings.model'
                ],
            ],
            'mautic.extendee.email.settings.email.subscriber' => [
                'class'     => 'MauticPlugin\MauticExtendeeEmailSettingBundle\EventListener\EmailSubscriber',
                'arguments' => [
                    'mautic.extendee.email.settings.model',
                    'request_stack',
                ],
            ],
        ],
        'models' => [
            'mautic.extendee.email.settings.model' => [
                'class'     => 'MauticPlugin\MauticExtendeeEmailSettingBundle\Model\EmailSettingExtendModel',
            ],
        ],
    ],
];
