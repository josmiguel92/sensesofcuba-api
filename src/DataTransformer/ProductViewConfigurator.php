<?php

namespace App\DataTransformer;

use App\Controller\Api\ApiController;
use App\Entity\TranslatedDocument;
use App\Entity\User;
use App\Entity\SocProduct;

class ProductViewConfigurator
{
    private SocProduct $product;
    private User $user;
    private string $lang;
    private \Vich\UploaderBundle\Templating\Helper\UploaderHelper $uploaderHelper;
    private ?array $filesArray = null;

    /**
     * ProductConfigurator constructor.
     */
    public function __construct(
        SocProduct $product,
        User $user,
        $lang,
        \Vich\UploaderBundle\Templating\Helper\UploaderHelper $uploaderHelper
    ) {
        $this->product = $product;
        $this->user = $user;
        $this->lang = $lang;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function isAllowedToShow(): bool
    {
        if ($this->product->isEnabled() === false) {
            return false;
        }

        if ($this->user->getHiddenProducts() && $this->user->getHiddenProducts()->contains($this->product)) {
            return false;
        }
        return true;
    }
    public function formatAsArray()
    {
        return [
            'id' => $this->product->getId(),
            'title' => $this->product->getTranslatedNameOrReference($this->lang),
            'description' =>  $this->product->getTranslatedDescOrNull($this->lang),
            'file' => $this->filesArray && isset($this->filesArray[0]) ? $this->filesArray[0] : null,
            'modified_on' =>  $this->product->getUpdatedAt()->format(ApiController::DATE_FORMAT),
            'image' => $this->getImage(),
            'child_of' => $this->product->getParent() ? $this->product->getParent()->getId() : null,
            'subscribed' => $this->user->getSubscribedProducts()->contains($this->product),
            'files' => $this->getFilesArray()
        ];
    }

    private function getImage()
    {
        return $this->product->hasImage() ? 'uploads/images/' . $this->product->getImage()->getThumbnailPath() : null;
    }

    private function getFilesArray(): ?array
    {
        if ($this->filesArray != null) {
            return $this->filesArray;
        }

        //TODO: debug
        $files = [];
        foreach ($this->product->getDocumentCollectionInclusive() as $doc) {
            /**
             * @var TranslatedDocument $doc
             */
            $shouldHideThisDoc = false;
            foreach ($this->user->getUserGroup() as $userGroup) {
                //if the user is in one hidden group, or is not in the doc->showOnlyForGroup
                if (in_array($userGroup, $doc->getHideForGroups())) {
                    $shouldHideThisDoc = true;
                    break;
                }
            }


            $shouldShowThisDoc = true;
            if ($doc->getShowForGroups()) {
                $shouldShowThisDoc = false;
                foreach ($doc->getShowForGroups() as $showGroup) {
                    if ($this->user->getUserGroup()->contains($showGroup)) {
                        $shouldShowThisDoc = true;
                        break;
                    }
                }
            }


            if (!$shouldHideThisDoc && $shouldShowThisDoc) {
                $title = $doc->translate($this->lang) ? $doc->translate($this->lang)->getTitle() : null;
                $file = $doc->translate($this->lang) ? $this->uploaderHelper->asset($doc->translate($this->lang), 'file') : null;

                if ($file) {
                    $files[] = [
                        'title' => $title,
                        'file' => $file,
                    ];
                }
            }
        }

        $this->filesArray = $files;
        return $this->filesArray;
    }
}
