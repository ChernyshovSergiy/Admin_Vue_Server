<?php

namespace App\Models;

use App\Traits\Methods\ToggleActive;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Language
 *
 * @property int $id
 * @property int $is_active
 * @property string $flag_country
 * @property string $code
 * @property string $iso
 * @property string $file
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereFlagCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereIso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Language whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Language extends Model
{
    use ToggleActive;
    protected $fillable = [
        'flag_country',
        'code',
        'iso',
        'file',
        'name'
    ];

    protected $hidden = [
        'id',
        'is_active',
        'created_at',
        'updated_at'
    ];

    public function addNewLanguage($request): void
    {
        $language = new static;
        $language->fill($request->all());
        $language->save();
    }

    public function editLanguage($request, $language)
    {
        $id = $language->id;
        $language = self::find($id);
        $language->fill($request->all());
        $language->is_active = $request->get('is_active');
        $language->update($request->all());

        return self::find($id);
    }

    public function removeLanguage($id): void
    {
        self::find($id)->delete();
    }

    public function getActiveLanguages()
    {
        $activeLang = self::all()->where('is_active', '=', 1)
            ->pluck('code')
            ->all();

        return $activeLang;
    }

}
