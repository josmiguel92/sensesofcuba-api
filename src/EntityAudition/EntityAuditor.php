<?php


namespace App\EntityAudition;


use App\Entity\SocFile;
use App\Entity\SocImage;
use App\Entity\SocProduct;
use App\Entity\SocProductTranslation;
use App\Entity\TranslatedDocument;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;

class EntityAuditor
{


    private $em;

    private $uow;

    private $updatesEntities;
    /**
     * @var array
     */
    private $changes;

    /**
     * AbstractAuditor constructor.
     * @param EntityManager $em
     * @param UnitOfWork $uow
     */
    public function __construct(EntityManager $em, UnitOfWork $uow)
    {
        $this->em = $em;
        $this->uow = $uow;

        $this->uow->computeChangeSets();
        $this->updatesEntities = array_values($uow->getScheduledEntityUpdates());
    }


    /**
     * @return bool
     */
    public function areUpdates(): bool
    {
        return count($this->updatesEntities) > 0;
    }


    public function getChanges()
    {
        $this->changes = [];
        foreach ($this->updatesEntities as $entity)
        {
            $_changes = $this->uow->getEntityChangeSet($entity);
            if(is_array($_changes))
                $this->changes[$this->getClassDescription($entity)] = $_changes;
        }
        return $this->changes;
    }

    private function getClassDescription($entity)
    {
        $className = str_replace("Proxies\\__CG__\\",null,get_class($entity));


        switch ($className){
            case 'App\\Entity\\SocProduct':
                /**
                 * @var SocProduct $entity
                 */
                return 'Product: '.$entity->getReferenceName();

            case 'App\\Entity\\SocProductTranslation':
                /**
                 * @var SocProductTranslation $entity
                 */
                return 'Product content translation: ('.$entity->getLocale().') ['.$entity->getTranslatable()->getReferenceName().']';

            case 'App\\Entity\\TranslatedDocument':
                /**
                 * @var TranslatedDocument $entity
                 */
                return 'Product file translation';

            case 'App\\Entity\\SocImage':
                /**
                 * @var SocImage $entity
                 */
                return 'Product image';

            case 'App\\Entity\\SocFile':
                /**
                 * @var SocFile $entity
                 */
                return 'Product image';
            default:
                return str_replace("App\\Entity\\",null,$className);
        }

    }

    public function getFormattedDiffStr()
    {
        $changes = $this->getChanges();
        $str = '';
        $allowedTypes = ["boolean","integer",'string', 'NULL', 'double', 'object'];
        $allowedClasses = [SocProduct::class, SocFile::class, SocImage::class];
        if(is_array($changes))
        {
            $index = 1;
            foreach ($changes as $key => $change)
            {
                $str .= " ■ ($index) $key \n";
                $index++;
                foreach ($change as $field => $values)
                {
                    if(
                        in_array(gettype($values[0]), $allowedTypes) || in_array(gettype($values[0]), $allowedClasses)
                        ||
                        in_array(gettype($values[1]), $allowedTypes) || in_array(gettype($values[1]), $allowedClasses)
                      )
                    {
                        $str .= "  ● $field\n";
                        $str .= '    Before: '.$values[0]."\n";
                        $str .= '    After: '.$values[1]."\n";
                        $str .= "\n\n\n";
                    }
                }

            }
            return $str;
        }

    }
}
