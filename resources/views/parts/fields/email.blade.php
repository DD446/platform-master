<?php
    $name = $name ?? 'email';
    $value = old($name ?? 'email') ?? $user->{$name} ?? $user->email ?? $email ?? null;
?>
@if(isset($hidden) && $hidden)
<input type="hidden" name="{{$name ?? 'email'}}" value="{{ old('email') ?? $user->email ?? null }}" id="{{$id ?? $name ?? 'email'}}" />
@else
<div class="form-group {{ $errors->has('email') ? ' alert alert-danger' : '' }}">
        <label for="{{$id ?? $name ?? 'f_email'}}" class="">{{$label ?? trans('fields.email')}}@if(isset($required) && $required == true)*@endif</label>
        <input id="{{$id ?? $name ?? 'f_email'}}"
               type="email"
               class="form-control @if(isset($large) && $large === true) form-control-lg @endif"
               name="{{$name ?? 'email'}}"
               value="{{$value}}"
               placeholder="{{$placeholder ?? trans('fields.placeholder_email')}}"
                @if(isset($required) && $required == true) required @endif
                @if(isset($autofocus) && $autofocus == true) autofocus @endif
                @if(isset($disabled) && $disabled === true) disabled @endif
            />

        @if ($errors->has($name ?? 'email'))
        <span class="help-block">
            <strong>{{ $errors->first($name ?? 'email') }}</strong>
        </span>
        @endif

        @isset($help)
            <div class="clearfix"></div>
            <p class="help-block">{{$help ?? null}}</p>
        @endisset
</div>
@endif
