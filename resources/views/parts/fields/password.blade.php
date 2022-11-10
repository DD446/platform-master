@if(isset($hidden) && $hidden)
<input type="hidden" name="{{$name ?? 'password'}}" value="{{ old('password') ?? $user->password ?? null }}" />
@else
<div class="form-group {{ $errors->has($name ?? 'password') ? ' alert alert-danger' : '' }}">
        <label for="password" class="{{ $labelClass ?? '' }}">{{$label ?? trans('fields.password')}}@if(isset($required) && $required == true)*@endif</label>

        <input id="password" type="password" class="form-control @if(isset($large) && $large === true) form-control-lg @endif" name="{{$name ?? 'password'}}"
            value="{{ old($name ?? 'password') ?? $user->password ?? null }}"
            placeholder="{{$placeholder ?? trans('fields.placeholder_password')}}"
            @if(isset($required) && $required == true) required @endif
            @if(isset($autofocus) && $autofocus == true) autofocus @endif
            @if(isset($tabindex)) tabindex="{{$tabindex}}" @endif
            />

        @if ($errors->has($name ?? 'password'))
        <div class="mt-2">
            <span class="help-block">
                <strong>{{ $errors->first($name ?? 'password') }}</strong>
            </span>
        </div>
        @endif
</div>
@endif