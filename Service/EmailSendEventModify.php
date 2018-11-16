<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendeeEmailSettingBundle\Service;

use Mautic\EmailBundle\Entity\Email;
use Mautic\EmailBundle\Event as Events;
use Mautic\EmailBundle\Helper\MailHelper;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticExtendeeEmailSettingBundle\Entity\EmailSettingExtend;
use MauticPlugin\MauticExtendeeEmailSettingBundle\Model\EmailSettingExtendModel;

class EmailSendEventModify
{
    /** @var EmailSettingExtendModel  */
    protected $emailSettingExtendModel;

    /** @var LeadModel  */
    protected $leadModel;

    /** @var  Events\EmailSendEvent */
    protected $event;

    /** @var  MailHelper */
    protected $mailHelper;

    /** @var  Lead */
    protected $contact;

    /** @var  Email */
    protected $email;

    /**
     * EmailSubscriber constructor.
     *
     * @param EmailSettingExtendModel $emailSettingExtendModel
     * @param LeadModel               $leadModel
     */
    public function __construct(EmailSettingExtendModel $emailSettingExtendModel, LeadModel $leadModel)
    {

        $this->emailSettingExtendModel = $emailSettingExtendModel;
        $this->leadModel = $leadModel;
    }

    public function setCustomAddresses(Events\EmailSendEvent $event)
    {
        $this->setEmailSendEvent($event);
        if($event->getEmail() && $event->getLead()) {
            $this->setCustomToAddress();
            $this->setCustomBccAddress();
            $this->setCustomCcAddress();
            $this->setCustomReplyTo();
            $this->setCustomFrom();
        }
    }

    private function setEmailSendEvent(Events\EmailSendEvent $event)
    {
        $this->event = $event;
        /** @var MailHelper $mailHelper */
        $this->mailHelper = $event->getHelper();
        $this->contact = $this->leadModel->getLead($event->getLead()['id']);
        $this->email = $this->mailHelper->getEmail();
    }


    /**
     * @param        $source
     * @param string $fieldType
     *
     * @return null $validLeadField
     */
    private function getValidLeadField($source, $fieldType = null)
    {
        $tokenRegex = '/({|%7B)contactfield=(.*?)(}|%7D)/';
        $validLeadField = null;
        $match          = null;
        $alias          = null;
        $contactFields =  $this->mailHelper->getLead();
        if (preg_match($tokenRegex, $source, $match)) {
            $alias = $match[2];
            //check if source alias is owneremail
            if ($alias == 'owneremail') {
                if (!empty($contactFields['owner_id'])) {
                    $owner          = $this->leadModel->getRepository()->getLeadOwner($contactFields['owner_id']);
                    $validLeadField = $owner['email'];
                }
            } else {
                if ($fieldType == 'email') {
                    if (strpos($alias, 'email') !== false) {
                        $validLeadField = (!empty($contactFields[$alias]) ? $contactFields[$alias] : null);
                    }
                } else {
                    $validLeadField = (!empty($contactFields[$alias]) ? $contactFields[$alias] : null);
                }
            }
        } else {
            $validLeadField = $source;
        }
        // Validate if fieldType is 'email'
        if ($fieldType == 'email') {
            if (filter_var($validLeadField, FILTER_VALIDATE_EMAIL) === false) {
                $validLeadField = null;
            }
        }
        return $validLeadField;
    }

    public function setCustomToAddress()
    {
        $to = [];
        foreach ($this->getAddresses($this->getToAddress()) as $address => $name) {
            $email = $this->getValidLeadField($address, 'email');
            if (!empty($email)) {
                $to[] = $email;
            }
        }
        if (!empty($to)) {
            $this->mailHelper->setTo($to);
        }
    }

    public function setCustomCcAddress()
    {
            foreach ($this->getAddresses($this->getCcAddress()) as $address => $name) {
                $ccAddress = $this->getValidLeadField($address, 'email');
                if (!empty($ccAddress)) {
                    $this->mailHelper->addCc($ccAddress);
                }
            }
    }

    private function getToAddress()
    {
        /** @var EmailSettingExtend $emailSettings */
        $emailSettings = $this->emailSettingExtendModel->getRepository()->findOneBy(['email'=>$this->email]);
        if ($emailSettings) {
            return $emailSettings->getToAddress();
        }
    }

    private function getCcAddress()
    {
        /** @var EmailSettingExtend $emailSettings */
        $emailSettings = $this->emailSettingExtendModel->getRepository()->findOneBy(['email'=>$this->email]);
        if ($emailSettings) {
            return $emailSettings->getCcAddress();
        }
    }

    private function getEmailCustomSettings()
    {


    }


    /**
     * @param string $addresses
     *
     * @return array
     */
    private function getAddresses($addresses)
    {
        $addressesArray = [];
        if (!empty($addresses)) {
            $addressesArray = array_fill_keys(array_map('trim', preg_split('/[;,]/', $addresses)), null);
        }
        return $addressesArray;
    }



    public function setCustomFrom()
    {
        $fromName  = $this->getValidLeadField($this->email->getFromName());
        $fromEmail = $this->getValidLeadField($this->email->getFromAddress(), 'email');
        $from = $this->mailHelper->getMessageInstance()->getFrom();
        if (!empty($fromEmail) || !empty($fromName)) {
            if (empty($fromName)) {
                $fromName                = array_values($from)[0];
            } elseif (empty($fromEmail)) {
                $fromEmail            = key($from);
            }
            $this->mailHelper->setFrom($fromEmail, $fromName);
        }
    }

    /**
     * Set custom reply address
     */
    public function setCustomReplyTo()
    {
        $replyTo = $this->getValidLeadField($this->email->getReplyToAddress(), 'email');
        if (!empty($replyTo)) {
            $this->mailHelper->setReplyTo($replyTo);
        }
    }

    /**
     * Set Bcc addresses
     */
    public function setCustomBccAddress()
    {
        foreach ($this->getAddresses($this->email->getBccAddress()) as $address => $name) {
            $bccAddress = $this->getValidLeadField($address, 'email');
            if (!empty($bccAddress)) {
                $this->mailHelper->addBcc($bccAddress);
            }
        }
    }
}
