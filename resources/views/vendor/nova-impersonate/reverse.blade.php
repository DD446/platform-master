<div style="
     position: fixed;
     padding: 15px 20px 15px 15px;
     min-width: 160px;
     top: 25%;
     right: -5px;
     background-color: #fff;
    -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, .05);
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, .05);
    border-radius: .5rem;
    text-align: center;
    z-index: 9999;
    "
>
    @if(session('impersonated_by') === 1)
    <p>
        User: {{ auth()->user()->username }}
    </p>

    <a href="{{route('nova.impersonate.leave')}}" style="text-decoration:underline;color: black;font-weight: bold">
        Verlassen
    </a>
    @else
        <p title="{{ auth()->user()->fullname }} &lt;{{ auth()->user()->email }}">
            <a href="{{route('nova.impersonate.leave')}}" style="text-decoration:underline;color: black;font-weight: bold">
                {{__('Logout from project')}}
            </a>
        </p>
    @endif
</div>
