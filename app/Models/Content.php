<?php

namespace App\Models;

use App\Traits\Methods\BuildJson;
use App\Traits\Methods\LanguagesFilter;
use App\Traits\Methods\ToggleActive;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Content
 *
 * @property int $id
 * @property int $is_active
 * @property string $title
 * @property mixed|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Content newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Content newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Content query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Content whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Content whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Content whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Content whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Content whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Content whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Content extends Model
{
    use BuildJson, ToggleActive, LanguagesFilter;

    protected $fillable = [
        'id',
        'title',
        'is_active',
        'title',
        'content'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $text_blocks = [
        'headline',
        'sub_headline',
        'text'
    ];

    public function getTextColumnsForTranslate(): array
    {
        return (new static)->text_blocks;
    }

    public function getContentByLang($cLang)
    {
        $active_lang = $this->getFullActiveLanguages();
        if (!$cLang || !$active_lang->contains('code', $cLang)){
            $cLang = 'en';
        }
        $contents = $this->buildOneLangAdmin(self::getModel() ,'content', $cLang);
        return collect($contents);
    }

    public function getContent($content)
    {
        $contents = $this->buildOneShowAdmin(self::getModel() ,'content', 'id');
        return collect($contents[$content->id]);
    }

    public function addNewContent($request)
    {
        $content = new static;
        $content->fill($request->all());
        $content->content = $content
            ->setJson($request, $content->text_blocks);
        $content->save();
        return $content;
    }

    public function editContent($request, $content)
    {
        if (!$request->title){
            $content->is_active = $request->is_active;
            $content->update();
        } else {
            $content->fill($request->all());
            $content->setContent($request, $content->id);
            $content->update();
        }
        return $content;
    }

    public function setContent($request, $id): void
    {
        if ($id === null) {
            return;
        }
        $this->content = $this->setJson($request, $this->text_blocks);
        $this->save();
    }
}
