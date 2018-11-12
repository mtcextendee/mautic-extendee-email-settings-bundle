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
use Mautic\EmailBundle\Helper\MailHelper;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\MauticExtendeeEmailSettingBundle\Model\EmailSettingExtendModel;
use MauticPlugin\MauticExtendeeEmailSettingBundle\Service\EmailSendEventModify;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class EmailSubscriber.
 */
class EmailSubscriber extends CommonSubscriber
{
    protected $from;

    protected $contactFields;

    /**
     * @var EmailSettingExtendModel
     */
    private $emailSettingExtendModel;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /** @var  MailHelper */
    private $mailHelper;

    /** @var  Lead */
    private $contact;


    /**
     * @var EmailSendEventModify
     */
    private $emailSendEventModify;

    /**
     * EmailSubscriber constructor.
     *
     * @param EmailSettingExtendModel $emailSettingExtendModel
     * @param EmailSendEventModify    $emailSendEventModify
     */
    public function __construct(EmailSettingExtendModel $emailSettingExtendModel, EmailSendEventModify $emailSendEventModify)
    {

        $this->emailSettingExtendModel = $emailSettingExtendModel;
        $this->emailSendEventModify = $emailSendEventModify;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_POST_SAVE   => ['onEmailPostSave', 0],
            EmailEvents::EMAIL_POST_DELETE => ['onEmailDelete', 0],
            EmailEvents::EMAIL_ON_SEND => ['onEmailSend', 0],
        ];
    }

    /**
     * Add an entry to the audit log.
     *
     * @param Events\EmailEvent $event
     */
    public function onEmailPostSave(Events\EmailEvent $event)
    {
        $this->emailSettingExtendModel->addOrEditEntity($event->getEmail());
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
     * Add an unsubscribe email to the List-Unsubscribe header if applicable.
     *
     * @param EmailSendEvent $event
     */
    public function onEmailSend(Events\EmailSendEvent $event)
    {
        $this->emailSendEventModify->setCustomAddresses($event);
    }
}
