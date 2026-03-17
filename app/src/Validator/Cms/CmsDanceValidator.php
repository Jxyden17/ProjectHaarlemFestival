<?php

namespace App\Validator\Cms;

use App\Models\Dance\DanceDetailEditInput;
use App\Models\Dance\DanceHomeEditInput;

class CmsDanceValidator
{
    public function validateHomePageInput(DanceHomeEditInput $input): void
    {
        if ($input->pageTitle === '') {
            throw new \InvalidArgumentException('Browser tab title is required.');
        }

        if ($input->scheduleTitle === '') {
            throw new \InvalidArgumentException('Schedule title is required.');
        }

        if ($input->featuredArtistsTitle === '') {
            throw new \InvalidArgumentException('Featured artists title is required.');
        }

        if ($input->bannerTitle === '') {
            throw new \InvalidArgumentException('Banner title is required.');
        }

        if ($input->bannerDescription === '') {
            throw new \InvalidArgumentException('Banner description is required.');
        }

        if ($input->importantInformationTitle === '' || $input->importantInformationHtml === '') {
            throw new \InvalidArgumentException('Important information is required.');
        }

        if ($input->passesTitle === '' || count($input->passItems) === 0) {
            throw new \InvalidArgumentException('At least one pass row is required.');
        }

        if ($input->capacityTitle === '' || $input->capacityHtml === '') {
            throw new \InvalidArgumentException('Capacity content is required.');
        }

        if ($input->specialTitle === '' || $input->specialHtml === '') {
            throw new \InvalidArgumentException('Special session content is required.');
        }
    }

    public function validateDetailPageInput(DanceDetailEditInput $input): void
    {
        if ($input->pageTitle === '') {
            throw new \InvalidArgumentException('Browser tab title is required.');
        }

        if ($input->heroTitle === '') {
            throw new \InvalidArgumentException('Hero title is required.');
        }

        if ($input->heroSubtitle === '') {
            throw new \InvalidArgumentException('Hero subtitle is required.');
        }

        if (count($input->heroImages) === 0) {
            throw new \InvalidArgumentException('At least one hero image row is required.');
        }

        if ($input->highlightsTitle === '' || count($input->highlights) === 0) {
            throw new \InvalidArgumentException('At least one highlight is required.');
        }

        if ($input->tracksTitle === '' || count($input->tracks) === 0) {
            throw new \InvalidArgumentException('At least one track is required.');
        }

        if ($input->importantInformationTitle === '' || $input->importantInformationHtml === '') {
            throw new \InvalidArgumentException('Important information is required.');
        }
    }
}
