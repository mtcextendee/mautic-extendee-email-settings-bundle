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
use MauticPlugin\MauticExtendeeEmailSettingBundle\Entity\EmailSettingExtend;
use MauticPlugin\MauticRecommenderBundle\Entity\EventRepository;
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplateRepository;

class EmailSettingExtendModel extends AbstractCommonModel
{

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

}
