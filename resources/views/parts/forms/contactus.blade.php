<form action="{{route('contactus.store')}}" method="post" id="contactform" onsubmit="window.location.href='#nav'"
    @if(request()->ajax() || request()->wantsJson()) data-async="true" data-modal="contactModal" @endif>
    @csrf
    @method('PUT')
    @include('parts.fields.name', ['required' => true, 'label' => trans('fields.name'), 'user' => $user, 'large' => true])
    @include('parts.fields.email', ['required' => true, 'label' => trans('fields.email'), 'user' => $user, 'large' => true])

    @if($enquiry_type)
        <input type="hidden" name="enquiry_type" value="{{ $enquiry_type }}">
    @else
    <div class="form-group">
        <label for="f_enquiry_type" class="">
            {{trans('contact_us.enquiry_type')}}*
        </label>
        <select name="enquiry_type" id="f_enquiry_type" class="form-control form-control-lg" required>
            @foreach($enquiry_types as $type => $label)
            <option value="{{$type}}" @if(old('enquiry_type') == $type) selected @endif>{{$label}}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="mb-3">
        @include('parts.fields.comment', ['required' => true, 'label' => trans('contact_us.comment'), 'comment' => $comment, 'large' => true, 'autofocus' => true])
    </div>

    @auth
    @else
        <div class="form-group {{ $errors->has('mathcaptcha') ? ' alert alert-danger' : '' }} clearfix">
            <label for="mathgroup">{{ trans('contact_us.label_math_equation', ['equation' => app('mathcaptcha')->label()]) }}</label>
            {!! app('mathcaptcha')->input(['class' => 'form-control', 'id' => 'mathgroup', 'placeholder' => trans('contact_us.placeholder_mathcaptcha')]) !!}
            @if ($errors->has('mathcaptcha'))
                <span class="help-block">
                    <strong>{{ $errors->first('mathcaptcha') }}</strong>
                </span>
            @endif
        </div>
        {{--@captcha('de')--}}
        {{--<div id="contact_us_id" class="mt-4"></div>--}}
    @endauth

{{--    <div class="form-group{{ $errors->has('screenshot') ? ' alert alert-danger' : '' }} pt-3">
        <label for="screenshot">{{ trans('feedback.label_screenshot') }}</label>
        <input type="file" name="screenshot" class="form-control-file" id="screenshot" accept="image/gif, image/jpg, image/jpeg, image/png, image/bmp">

        @if ($errors->has('screenshot'))
            <p class="help-block">
                <strong>{{ $errors->first('screenshot') }}</strong>
            </p>
        @endif
    </div>--}}

    <button class="btn btn-lg btn-success">{{trans('contact_us.send')}} <i class="fa fa-paper-plane left-spacer--xs"></i></button>

    <div class="form-group mt-4">
        <small>
            {!! trans('contact_us.legal') !!}
        </small>
    </div>
</form>
<div class="clearfix"></div>

{{--
@section('footerscripts')
@auth
@else
{!!
    GoogleReCaptchaV3::render([
       'contact_us_id'=>'contact_us',  // the div id=contact_us_id maps to action name contact_us
    ])
!!}
@endauth
@endsection--}}
