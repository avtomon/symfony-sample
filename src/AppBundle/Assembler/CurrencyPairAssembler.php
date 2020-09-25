<?php

namespace AppBundle\Assembler;

use AppBundle\Exception\StoredProcedure\CurrencyUnprocessableException;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\DTO\CurrencyRateDTO;

/**
 * Class CurrencyPairAssembler
 * @package AppBundle\Assembler
 */
class CurrencyPairAssembler
{
    /**
     * @param string $pairCode
     * @param float $rate
     *
     * @return CurrencyRateDTO
     */
    public function writeDTO(string $pairCode, float $rate) : CurrencyRateDTO
    {
        $rateDTO = new CurrencyRateDTO();
        $pairCodeArray = explode('/', $pairCode);
        if (\count($pairCodeArray) !== 2) {
            throw new CurrencyUnprocessableException('Pair code is corrupt.');
        }

        $rateDTO->setTargetCode($pairCodeArray[1]);
        $rateDTO->setSourceCode($pairCodeArray[0]);
        $rateDTO->setRate($rate);

        return $rateDTO;
    }

    /**
     * @param array $pairs
     * @return CurrencyRateDTO[]|ArrayCollection
     */
    public function writeCollectionDTO(array $pairs) : ArrayCollection
    {
        $result = new ArrayCollection();
        foreach ($pairs as $pairCode => $rate) {
            $result->add($this->writeDTO($pairCode, $rate));
        }

        return $result;
    }
}
