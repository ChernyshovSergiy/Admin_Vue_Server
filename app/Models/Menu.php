<?php

namespace App\Models;

use App\Traits\Methods\BuildJson;
use App\Traits\Methods\LanguagesFilter;
use App\Traits\Methods\PrepareLangStrForJsonMethods;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/**
 * App\Models\Menu
 *
 * @property int $id
 * @property int $is_active
 * @property string $section
 * @property mixed $title
 * @property string|null $url
 * @property int $parent
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu whereSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Menu whereUrl($value)
 * @mixin \Eloquent
 */
class Menu extends Model
{
    use BuildJson, LanguagesFilter, PrepareLangStrForJsonMethods;

    protected $fillable = [
        'id',
        'is_active',
        'section',
        'title',
        'url',
        'parent',
        'sort'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $menu_blocks = [
        'our_company',
        'help',
        'partner',
        '3dmriya'
    ];

    public function getTextColumnsForTranslate(): array
    {
        return (new static)->menu_blocks;
    }

    public function getMenuByLang($cLang)
    {
        $active_lang = $this->getFullActiveLanguages();
        if (!$cLang || !$active_lang->contains('code', $cLang)){
            $cLang = 'en';
        }
        $menus = $this->build(self::getModel(), 'title');
        $menu_points = [];
        LaravelLocalization::setLocale($cLang);
        foreach($menus as $key => $menu){
            $menu_points[$key] = [
                'id' => $menu->id ,
                'is_active' => $menu->is_active,
                'section' => Lang::get('blocks.'.$this->menu_blocks[$menu->section]),
                'title'=>$menu->title->$cLang,
                'url'=>$menu->url,
                'parent'=>$this->getParent($menu->parent, $cLang),
                'sort'=>$menu->sort
            ];
        }
//        $menu_points= Arr::prepend($menu_points,['max' => self::max('sort') + 1]);
        return collect($menu_points);
    }

    public function getParent($parent, $locale) :string
    {
        if ($parent !== 0){
        $title = self::where('id', $parent)->first();
            if (!empty($title)) {
                return json_decode($title->title, false)->$locale;
            }
            return '';
        }
        return Lang::get('nav.root');
    }


    public function editMenu($request, $menu)
    {
        if (!$request->url){
            $menu->is_active = $request->is_active;
            $menu->update();
        } else {
            $menu->fill($request->all());
            $menu->title = json_encode($menu->createLangString($request, 'title'));
            $menu->update();
        }
        return $menu;
    }

    public function addMenuPoint($request)
    {
        $menu = new static;
        $menu->fill($request->all());
        $menu->title = json_encode($menu->createLangString($request, 'title'));
        $menu->save();
        return $menu;
    }

    public function getMenu($menu)
    {
        $menu_point = $this->build(self::getModel() ,'title');
        return collect($menu_point[$menu->id - 1]);
    }
}
