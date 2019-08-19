<?php

namespace App\Traits\Methods;

use App\Models\Language;

trait LanguagesFilter
{
    public function getActiveLanguages(): array
    {
        return Language::where('is_active', '=','1')
            ->pluck( 'code', 'id')->all();
    }

    public function getLanguages(): array
    {
        return Language::pluck('name', 'id')->all();
    }

    public function getFullActiveLanguages()
    {
        return Language::all()->where('is_active', '=','1');
    }
}
