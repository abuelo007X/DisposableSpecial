<?php

namespace Modules\DisposableSpecial\Widgets;

use App\Contracts\Widget;
use Illuminate\Support\Facades\Auth;
use Modules\DisposableSpecial\Models\DS_Marketitem;
use Modules\DisposableSpecial\Models\DS_Marketowner;

class FeaturedItem extends Widget
{
    public function run()
    {
        $user = Auth::user();

        if ($user) {
            $useritems = DS_Marketowner::where('user_id', $user->id)->orderBy('marketitem_id')->pluck('marketitem_id')->toArray();
            $item = DS_Marketitem::where('active', 1)->whereNotIn('id', $useritems)->inRandomOrder()->first();
        }

        return view('DSpecial::widgets.featured_item', [
            'item'       => isset($item) ? $item : null,
            'units'      => DS_GetUnits(),
            'seperation' => (in_array(setting('units.currency'), ['EUR', 'TRY'])) ? false : true,
        ]);
    }
}
