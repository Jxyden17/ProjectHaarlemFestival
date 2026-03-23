<?php

namespace App\Validator;

use App\Models\Requests\UpdateDanceDetailRequest;
use App\Models\Requests\UpdateDanceHomeRequest;

class CmsDanceValidator
{
    public function validateHomePageInput(UpdateDanceHomeRequest $request, array $passItems, string $bannerDescription, string $importantInformationHtml, string $capacityHtml, string $specialHtml): void
    {
        if ($request->pageTitle() === '') {
            throw new \InvalidArgumentException('Browser tab title is required.');
        }

        if ($request->scheduleTitle() === '') {
            throw new \InvalidArgumentException('Schedule title is required.');
        }

        if ($request->featuredArtistsTitle() === '') {
            throw new \InvalidArgumentException('Featured artists title is required.');
        }

        if ($request->bannerTitle() === '') {
            throw new \InvalidArgumentException('Banner title is required.');
        }

        if ($bannerDescription === '') {
            throw new \InvalidArgumentException('Banner description is required.');
        }

        if ($request->importantInformationTitle() === '' || $importantInformationHtml === '') {
            throw new \InvalidArgumentException('Important information is required.');
        }

        if ($request->passesTitle() === '' || count($passItems) === 0) {
            throw new \InvalidArgumentException('At least one pass row is required.');
        }

        if ($request->capacityTitle() === '' || $capacityHtml === '') {
            throw new \InvalidArgumentException('Capacity content is required.');
        }

        if ($request->specialTitle() === '' || $specialHtml === '') {
            throw new \InvalidArgumentException('Special session content is required.');
        }
    }

    public function validateDetailPageInput(UpdateDanceDetailRequest $request, array $heroImages, array $highlights, array $tracks, string $importantInformationHtml): void
    {
        if ($request->pageTitle() === '') {
            throw new \InvalidArgumentException('Browser tab title is required.');
        }

        if ($request->heroTitle() === '') {
            throw new \InvalidArgumentException('Hero title is required.');
        }

        if ($request->heroSubtitle() === '') {
            throw new \InvalidArgumentException('Hero subtitle is required.');
        }

        if (count($heroImages) === 0) {
            throw new \InvalidArgumentException('At least one hero image row is required.');
        }

        if ($request->highlightsTitle() === '' || count($highlights) === 0) {
            throw new \InvalidArgumentException('At least one highlight is required.');
        }

        if ($request->tracksTitle() === '' || count($tracks) === 0) {
            throw new \InvalidArgumentException('At least one track is required.');
        }

        if ($request->importantInformationTitle() === '' || $importantInformationHtml === '') {
            throw new \InvalidArgumentException('Important information is required.');
        }
    }
}
