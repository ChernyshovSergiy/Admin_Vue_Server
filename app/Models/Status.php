<?php

namespace App\Models;

use App\Traits\Methods\BuildJson;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Status
 *
 * @property int $id
 * @property mixed|null $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Status whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Status whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Status extends Model
{
    use BuildJson;

    protected $fillable = [
        'title'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function getStatusNameByLang($cLang)
    {
        $active_lang = Language::all()->where('is_active', '=','1');
        if (!$cLang || !$active_lang->contains('code', $cLang)){
            $cLang = 'en';
        }
        $statuses = $this->build(self::getModel(), 'title');
        $status_titles = [];
        foreach($statuses as $key => $status){
            $status_titles[$key] = ['id' => $status->id , 'title'=>$status->title->$cLang];
        }
        return collect($status_titles);
    }

    public function addNewStatus($request)
    {
        $status = new static;
        $status->fill($request);
        $status->title = json_encode($request);
        $status->save();
        return $status;
    }

    public function getStatus($status)
    {
        $statuses = $this->build(self::getModel(), 'title');
        return $statuses->firstWhere('id', '=', $status->id);
    }

    public function editStatus($request, $status)
    {
        $status->fill($request);
        $status->title = json_encode($request);
        $status->update($request);

        return $status;
    }
}
