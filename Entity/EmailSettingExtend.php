<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendeeEmailSettingBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\EmailBundle\Entity\Email;

class EmailSettingExtend
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Email
     */
    protected $email;

    /**
     * @var string
     */
    private $ccAddress;
    /**
     * @var string
     */
    private $toAddress;


    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('emails_settings')
            ->setCustomRepositoryClass(EmailSettingExtendRepository::class)
            ->addNamedField('toAddress', Type::TEXT, 'to_address', false)
            ->addNamedField('ccAddress', Type::TEXT, 'cc_address', false)
            ->addId();

        $builder->createManyToOne(
            'email',
            'Mautic\EmailBundle\Entity\Email'
        )->addJoinColumn('email_id', 'id', true, false, 'CASCADE')->build();

    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('email_settings')
            ->addListProperties(
                [
                    'id',
                    'email',
                    'toAddress',
                    'ccAddress',
                ]
            )
            ->build();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToAddress()
    {
        return $this->toAddress;
    }
    /**
     * @param $toAddress
     *
     * @return Email
     */
    public function setToAddress($toAddress)
    {
        $this->toAddress = $toAddress;
        return $this;
    }
    /**
     * @return string
     */
    public function getCcAddress()
    {
        return $this->ccAddress;
    }
    /**
     * @param $ccAddress
     *
     * @return $this
     */
    public function setCcAddress($ccAddress)
    {
        $this->ccAddress = $ccAddress;
        return $this;
    }

    /**
     * @param Email $email
     *
     * @return EmailSettingExtend
     */
    public function setEmail(Email $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }
}
