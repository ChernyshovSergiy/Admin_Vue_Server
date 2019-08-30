<?php

namespace App\Models;

use App\Traits\Methods\LanguagesFilter;
use App\Traits\Methods\ToggleActive;
use Illuminate\Database\Eloquent\Model;
use Lang;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/**
 * App\Models\Size
 *
 * @property int $id
 * @property int $is_active
 * @property string $value
 * @property int $sort
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Size newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Size newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Size query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Size whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Size whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Size whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Size whereValue($value)
 * @mixin \Eloquent
 */
class Size extends Model
{
    use ToggleActive, LanguagesFilter;

    protected $fillable = [
        'id',
        'value'
    ];

    protected $hidden = [
        'is_active',
        'sort'
    ];

    public function getActiveSizeByLang($cLang)
    {
        $active_lang = $this->getFullActiveLanguages();
        if (!$cLang || !$active_lang->contains('code', $cLang)){
            $cLang = 'en';
        }
        $collection_size = self::all()->where('is_active', '=', 1);
        LaravelLocalization::setLocale($cLang);
        $other = new static;
        $other->id = 0;
        $other->value = Lang::get('admin.other');
        $collection_size->push($other);
        return $collection_size;
    }
}
