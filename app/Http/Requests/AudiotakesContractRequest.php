<?php

namespace App\Http\Requests;

use App\Models\Feed;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AudiotakesContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!auth()->check()) {
            return false;
        }

        $feedId = $this->get('feed_id');

        if (!$feedId) {
            return false;
        }

        $feed = Feed::owner()->where('feed_id', '=', $feedId)->first();

        if (!$feed) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $countries = \Countrylist::getList('de');

        return [
            'feed_id' => ['required', 'string', 'max:100', 'exists:App\Models\Feed,feed_id,username,' . auth()->user()->username],
/*            'user.first_name' => ['required', 'string', 'max:50'],
            'user.last_name' => ['required', 'string', 'max:100'],
            'user.email' => ['required', 'email', 'max:150'],
            'user.street' => ['required', 'string', 'max:50'],
            'user.housenumber' => ['required', 'string', 'max:10'],
            'user.city' => ['required', 'string', 'max:50'],
            'user.post_code' => ['required', 'max:10'],
            'user.country' =>  [
                'required',
                Rule::in(array_keys($countries)),
            ],*/
            'toc' => ['required', 'in:yes'],
            //'contract_partner' => ['required', 'in:private,corporate'],
            //'user.organisation' => ['nullable', 'required_if:contract_partner,corporate', 'string', 'max:255'],
            'audiotakes_contract_partner_id' => ['required', 'exists:audiotakes_contract_partners,id,user_id,' . auth()->id()],
        ];
    }

    public function attributes()
    {
        return [
            'user.first_name' => trans('audiotakes.first_name'),
            'user.last_name' => trans('audiotakes.last_name'),
            'user.email' => trans('audiotakes.email'),
            'user.telephone' => trans('audiotakes.telephone'),
            'user.street' => trans('audiotakes.street'),
            'user.housenumber' => trans('audiotakes.housenumber'),
            'user.city' => trans('audiotakes.city'),
            'user.post_code' => trans('audiotakes.post_code'),
            'user.organisation' => trans('audiotakes.organisation_name'),
            'audiotakes_contract_partner_id' => trans('audiotakes.contract_partner'),
            'corporate' => trans('audiotakes.contract_partner_corporate'),
        ];
    }


}
