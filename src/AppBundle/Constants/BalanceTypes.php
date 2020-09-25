<?php
namespace AppBundle\Constants;

/**
 * Class BalanceTypes
 * @package AppBundle\Constants
 */
class BalanceTypes
{
    public const CURRENT = 'current';
    public const AWAITING = 'awaiting';
    public const SUSPENDED = 'suspended';
    public const WRITE_OFF = 'write_off';

    public const ALL_TYPES = [
        self::CURRENT,
        self::AWAITING,
        self::SUSPENDED,
        self::WRITE_OFF,
    ];
}
