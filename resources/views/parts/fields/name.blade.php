@if(isset($hidden) && $hidden)
<input type="hidden" name="name" value="{{ old('name') ?? $name ?? (($user instanceof \App\Models\User) ? $user->first_name . ' ' . $user->last_name : null) }}" />
@else
<div class="form-group {{ $errors->has('name') ? ' alert alert-danger' : '' }}">
    <label for="name" class="{{ $labelClass ?? '' }}">
        {{$label ?? trans('fields.name')}}@if(isset($required) && $required == true)*@endif
    </label>
    <input id="name" type="text" class="form-control @if(isset($large) && $large === true) form-control-lg @endif" name="{{$name ?? 'name'}}"
        value="{{ old('name') ?? $name ?? (!empty($user) ? $user->first_name . ' ' . $user->last_name : null) }}"
        placeholder="{{$placeholder ?? trans('fields.placeholder_name')}}"
        @if(isset($required) && $required == true) required @endif
        @if(isset($autofocus) && $autofocus == true) autofocus @endif
        />

    @if ($errors->has('name'))
    <span class="help-block">
        <strong>{{ $errors->first('name') }}</strong>
    </span>
    @endif
</div>
@endif
