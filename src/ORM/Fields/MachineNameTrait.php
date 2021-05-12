<?php


namespace App\ORM\Fields;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait MachineNameTrait
 * @ORM\HasLifecycleCallbacks()
 */
trait MachineNameTrait
{
    /**
     * @ORM\Column(type="string", length=180)
     */
    private string $machineName;


    public function setMachineName($machineName)
    {
        $this->machineName = $machineName;
        return $this;
    }

    public function getMachineName(): string
    {
        return  $this->machineName ?: $this->generateMachineName();
    }

    /**
     * @return string
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function generateMachineName(): string
    {
        $machineName = $this->machineName ??
            strtolower(
                trim(
                    preg_replace('/[^A-Za-z0-9]+/', '_', $this->getNameFieldValue()),
                    '_'
                )
            );
        $this->setMachineName($machineName);
        return $this->machineName;
    }
}
