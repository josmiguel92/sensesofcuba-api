<?php


namespace App\ORM\Interfaces;

/**
 * MachineName Interface
 */
interface MachineNameInterface
{
    public function setMachineName($machineName);

    public function getMachineName(): string;

    public function generateMachineName();

    public function getNameFieldValue():? string;
}
