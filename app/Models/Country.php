<?php

namespace App\Models;

use App\Traits\Methods\LanguagesFilter;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Country
 *
 * @property int $id
 * @property string $lang
 * @property string $lang_name
 * @property string $country_alpha2_code
 * @property string $country_alpha3_code
 * @property string $country_numeric_code
 * @property string $country_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCountryAlpha2Code($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCountryAlpha3Code($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCountryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCountryNumericCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereLangName($value)
 * @mixin \Eloquent
 */
class Country extends Model
{
    use LanguagesFilter;
    protected $hidden = [
        'id',
        'lang',
        'lang_name',
        'country_alpha3_code',
        'country_numeric_code'
    ];

    public function getCountryListByLanguages($cLang)
    {
        $active_lang = $this->getFullActiveLanguages();
        if (!$cLang || !$active_lang->contains('code', $cLang)){
            $cLang = 'en';
        }
        if ($cLang === 'ua'){
            $lang = 'uk';
        } else{
            $lang = $cLang;
        }
        $countries = self::where(strtolower('lang'), '=', $lang)
            ->orderBy('country_name')
            ->get()
            ->flatten()
            ->all();
        return collect($countries);
    }
}
