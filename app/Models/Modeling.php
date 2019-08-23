<?php

namespace App\Models;

use App\Models\Language;
use App\Mail\Order\Modeling\VerifyMail;
use App\Traits\Methods\LanguagesFilter;
use App\Traits\Relations\HasOne\Executors;
use App\Traits\Relations\HasOne\Languages;
use App\Traits\Relations\HasOne\Statuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Lang;
use Mail;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


/**
 * App\Models\Modeling
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $link
 * @property int $texturing
 * @property string|null $verify_token
 * @property int $status_id
 * @property int|null $language_id
 * @property int|null $executor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $executor
 * @property-read \App\Models\Language $language
 * @property-read \App\Models\Status $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereExecutorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereTexturing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Modeling whereVerifyToken($value)
 * @mixin \Eloquent
 */
class Modeling extends Model
{
    use Languages, Statuses, Executors, LanguagesFilter;

    protected $fillable = [
        'name', 'email',
        'link', 'texturing',
        'status_id',
        'language_id', 'executor_id'
    ];
    protected $hidden = [
        'verify_token'
    ];

    public function getConfirmModelingOrdersByLang($cLang)
    {
        $active_lang = $this->getFullActiveLanguages();
        if (!$cLang || !$active_lang->contains('code', $cLang)){
            $cLang = 'en';
        }
        $active_modeling_orders = self::all()->where('status_id', '>','2');
        $order_points = [];
        LaravelLocalization::setLocale($cLang);
        foreach($active_modeling_orders as $key => $order){
            $order_points[$key] = [
                'id' => $order->id ,
                'name' => $order->name,
                'email' => $order->email,
                'link' => $order->link,
                'texturing' => $order->texturing,
                'status' => json_decode($order->status->title, false)->$cLang,
                'language' => $order->language->name,
                'executor' => $order->executor['name'] ?: Lang::get('admin.not_assigned')
            ];
        }
        return collect($order_points);
    }

    public function setUserLanguage($code): void
    {
        if ($code === null){
            return;
        }
        $this->language_id = Language::where('code', '=', $code)
            ->first()->id;
        $this->save();
    }

    public function setTexturingModel($checkbox): void
    {
        if ($checkbox === null) {
            return;
        }

        if ($checkbox === true) {
            $this->texturing = 1;
        } else {
            $this->texturing = 0;
        }
        $this->save();
    }

    public function generateToken(): void
    {
        $this->verify_token = Str::random(100);
        $this->save();
    }

    public static function addNewModelingOrder($fields) :void
    {
        $order = new static;
        $order->fill($fields->all());
        $order->setUserLanguage($fields->get('cLang'));
        $order->setTexturingModel($fields->get('checkbox'));
        $order->status_id = 2;
        $order->save();

        $order->generateToken();
        LaravelLocalization::setLocale($fields->get('cLang'));

        Mail::to($order)->send(new VerifyMail($order));

    }

    public static function adminAddNewModelingOrder($fields) :void
    {
        $order = new static;
        $order->fill($fields->all());
        $order->save();
    }

    public function editModelingOrder($request, $id): void
    {
        $order = self::find($id);
        $order->fill($request->all());
        $order->update($request->all());
    }


    public function verifyModelingOrder($token): void
    {
        $order = self::where('verify_token', $token)->firstOrFail();
        $order->verify_token = null;
        $order->status_id = 3;
        $order->save();
    }

    public static function getConfirmModelingOrders()
    {
        return self::all()->where('status_id', '>','2');
    }

    public function getStatus()
    {
        $locale = LaravelLocalization::getCurrentLocale();
        return ($this->status_id !== null)
            ? json_decode($this->status->title)->$locale
            : 'don`t have status';
    }

    public function getLangName()
    {
        return ($this->language_id !== null)
            ? $this->language->name
            : 'don`t have language';
    }

    public function getExecutor()
    {
        return ($this->executor_id !== null)
            ? $this->executor->name
            : Lang::get('admin.not_assigned');
    }


    public function removeOrder($id): void
    {
        self::find($id)->delete();
    }
}
