<?php

namespace App\Validator\Cms;

class CmsDanceValidator
{
    public function validateHomePageInput(array $input): void
    {
        if ($input['page_title'] === '') {
            throw new \InvalidArgumentException('Browser tab title is required.');
        }

        if ($input['schedule_title'] === '') {
            throw new \InvalidArgumentException('Schedule title is required.');
        }

        if ($input['banner_title'] === '') {
            throw new \InvalidArgumentException('Banner title is required.');
        }

        if ($input['banner_description'] === '') {
            throw new \InvalidArgumentException('Banner description is required.');
        }

        if ($input['important_information_title'] === '' || $input['important_information_html'] === '') {
            throw new \InvalidArgumentException('Important information is required.');
        }

        if ($input['passes_title'] === '' || count($input['pass_items']) === 0) {
            throw new \InvalidArgumentException('At least one pass row is required.');
        }

        if ($input['capacity_title'] === '' || $input['capacity_html'] === '') {
            throw new \InvalidArgumentException('Capacity content is required.');
        }

        if ($input['special_title'] === '' || $input['special_html'] === '') {
            throw new \InvalidArgumentException('Special session content is required.');
        }
    }

    public function validateDetailPageInput(array $input): void
    {
        if ($input['page_title'] === '') {
            throw new \InvalidArgumentException('Browser tab title is required.');
        }

        if ($input['hero_title'] === '') {
            throw new \InvalidArgumentException('Hero title is required.');
        }

        if ($input['hero_subtitle'] === '') {
            throw new \InvalidArgumentException('Hero subtitle is required.');
        }

        if (count($input['hero_images']) === 0) {
            throw new \InvalidArgumentException('At least one hero image row is required.');
        }

        if ($input['highlights_title'] === '' || count($input['highlights']) === 0) {
            throw new \InvalidArgumentException('At least one highlight is required.');
        }

        if ($input['tracks_title'] === '' || count($input['tracks']) === 0) {
            throw new \InvalidArgumentException('At least one track is required.');
        }

        if ($input['important_information_title'] === '' || $input['important_information_html'] === '') {
            throw new \InvalidArgumentException('Important information is required.');
        }
    }
}
