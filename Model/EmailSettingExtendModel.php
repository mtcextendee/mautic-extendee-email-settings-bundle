<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendeeEmailSettingBundle\Model;

use Mautic\CoreBundle\Model\AbstractCommonModel;
use Mautic\EmailBundle\Entity\Email;
use MauticPlugin\MauticExtendeeEmailSettingBundle\Entity\EmailSettingExtend;
use MauticPlugin\MauticRecommenderBundle\Entity\EventRepository;
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplateRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class EmailSettingExtendModel extends AbstractCommonModel
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * EmailSettingExtendModel constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {

        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     *
     * @return EventRepository
     */
    public function getRepository()
    {
        /** @var RecommenderTemplateRepository $repo */
        $repo = $this->em->getRepository('MauticExtendeeEmailSettingBundle:EmailSettingExtend');

        $repo->setTranslator($this->translator);

        return $repo;
    }

    /**
     * Add or edit email settings entity based on request
     *
     * @param Email $email
     */
    public function addOrEditEntity(Email $email)
    {
        $toAddress = $this->requestStack->getCurrentRequest()->get('toAddress');
        $ccAddress = $this->requestStack->getCurrentRequest()->get('ccAddress');

        $settingsExtend = $this->getRepository()->findOneBy(['email'=>$email]);

        if (!$settingsExtend) {
            $settingsExtend = new EmailSettingExtend();
            $settingsExtend->setEmail($email);
        }

        $settingsExtend->setToAddress($toAddress);
        $settingsExtend->setCcAddress($ccAddress);
        $this->getRepository()->saveEntity($settingsExtend);
    }

}
