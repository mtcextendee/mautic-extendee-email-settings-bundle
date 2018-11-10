<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendeeEmailSettingBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomContentEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\TemplatingHelper;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\EmailBundle\Entity\Email;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticExtendeeAnalyticsBundle\Helper\GoogleAnalyticsHelper;
use MauticPlugin\MauticExtendeeAnalyticsBundle\Integration\EAnalyticsIntegration;
use MauticPlugin\MauticExtendeeEmailSettingBundle\Entity\EmailSettingExtend;
use MauticPlugin\MauticExtendeeEmailSettingBundle\Model\EmailSettingExtendModel;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class InjectCustomContentSubscriber extends CommonSubscriber
{

    /**
     * @var TemplatingHelper
     */
    private $templatingHelper;

    /**
     * @var EmailSettingExtendModel
     */
    private $emailSettingExtendModel;

    /**
     * InjectCustomContentSubscriber constructor.
     *
     * @param TemplatingHelper        $templatingHelper
     * @param EmailSettingExtendModel $emailSettingExtendModel
     */
    public function __construct(TemplatingHelper $templatingHelper, EmailSettingExtendModel $emailSettingExtendModel)
    {

        $this->templatingHelper = $templatingHelper;
        $this->emailSettingExtendModel = $emailSettingExtendModel;
    }

    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_CONTENT => ['injectViewCustomContent', 0],
        ];
    }

    /**
     * @param CustomContentEvent $customContentEvent
     */
    public function injectViewCustomContent(CustomContentEvent $customContentEvent)
    {
        //enabled intergration

        $parameters = $customContentEvent->getVars();
        if ($customContentEvent->getContext() != 'email.settings.advanced') {
            return;
        }else if (empty($parameters['email']) || !$parameters['email'] instanceof Email) {
            return;
        }

        $passParams = ['form'=>$parameters['form']];
        $passParams['toAddress'] = '';
        $passParams['ccAddress'] = '';

        /** @var EmailSettingExtend $emailSettings */
        $emailSettings = $this->emailSettingExtendModel->getRepository()->findOneBy(['email'=> $parameters['email']]);
        if ($emailSettings instanceof EmailSettingExtend) {
            $passParams['toAddress'] = $emailSettings->getToAddress();
            $passParams['ccAddress'] = $emailSettings->getCcAddress();
        }

        $content = $this->templatingHelper->getTemplating()->render(
            'MauticExtendeeEmailSettingBundle:Email:settings.html.php',
            $passParams
        );
        $customContentEvent->addContent($content);

    }


}
