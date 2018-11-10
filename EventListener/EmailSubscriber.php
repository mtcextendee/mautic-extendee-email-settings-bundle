<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendeeEmailSettingBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event as Events;
use MauticPlugin\MauticExtendeeEmailSettingBundle\Entity\EmailSettingExtend;
use MauticPlugin\MauticExtendeeEmailSettingBundle\Model\EmailSettingExtendModel;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class EmailSubscriber.
 */
class EmailSubscriber extends CommonSubscriber
{
    /**
     * @var EmailSettingExtendModel
     */
    private $emailSettingExtendModel;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * EmailSubscriber constructor.
     *
     * @param EmailSettingExtendModel $emailSettingExtendModel
     * @param RequestStack            $requestStack
     */
    public function __construct(EmailSettingExtendModel $emailSettingExtendModel, RequestStack $requestStack)
    {

        $this->emailSettingExtendModel = $emailSettingExtendModel;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_POST_SAVE   => ['onEmailPostSave', 0],
            EmailEvents::EMAIL_POST_DELETE => ['onEmailDelete', 0],
        ];
    }

    /**
     * Add an entry to the audit log.
     *
     * @param Events\EmailEvent $event
     */
    public function onEmailPostSave(Events\EmailEvent $event)
    {
        $this->addOrEdit($event->getEmail());
    }
    /**
     * Add a delete entry to the audit log.
     *
     * @param Events\EmailEvent $event
     */
    public function onEmailDelete(Events\EmailEvent $event)
    {
        $email = $event->getEmail();
        $settingsExtend = $this->emailSettingExtendModel->getRepository()->findOneBy(['email'=>$email]);
        if ($settingsExtend) {
            $this->emailSettingExtendModel->getRepository()->deleteEntity($settingsExtend);
        }
    }

    /**
     * Add or edit extended settings entity
     *
     * @param $email
     */
    private function addOrEdit($email)
    {
        $toAddress = $this->requestStack->getCurrentRequest()->get('toAddress');
        $ccAddress = $this->requestStack->getCurrentRequest()->get('ccAddress');

        $settingsExtend = $this->emailSettingExtendModel->getRepository()->findOneBy(['email'=>$email]);
        if (!$settingsExtend) {
            $settingsExtend = new EmailSettingExtend();
            $settingsExtend->setEmail($email);
        }

        $settingsExtend->setToAddress($toAddress);
        $settingsExtend->setCcAddress($ccAddress);
        $this->emailSettingExtendModel->getRepository()->saveEntity($settingsExtend);
    }
}
