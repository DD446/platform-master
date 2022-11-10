@extends('main')

@section('content')
<section class="bg-info text-light">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1>Auftragsverarbeitungs (AV)-Vertrag</h1>
                <span>Gemäss Art. 28 der Datenschutzgrundverordnung (DS-GVO)</span>
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
    </div>
    <!--end of container-->
</section>
<!--end of section-->
<section>
    <div class="container p-4 card">
        @if (count($dpas) > 0)
        <div class="row mb-4">
            <div class="col-12 col-md-8 col-lg-7">
                <h4>Erstellte Verträge</h4>

                <ul class="list-unstyled">
                    @foreach($dpas as $dpa)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-lg-9 col-sm-8 col-xs-6">
                                <strong>AV-Vertrag vom {{ $dpa->created_at->format('d.m.Y') }}</strong>
                            </div>
                            <div class="col-lg-3 col-sm-4 col-xs-6">
                                <a href="{{ route('dpa.download', ['id' => $dpa->id]) }}" class="btn btn-primary btn-sm" title="AV-Vertrag herunterladen">
                                    <i class="icon-download"></i> Download
                                </a>
                                <a href="{{ route('dpa.delete', ['id' => $dpa->id]) }}" class="btn btn-secondary btn-sm" title="AV-Vertrag löschen">
                                    <i class="icon-trash"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-12">
                <h2>Neuen AV-Vertrag erstellen</h2>

                <form name="{{ route('dpa.store') }}" method="post" id="dpa">
                    @csrf
                    <input type="hidden" name="av_id" value="{{ $av->id }}">

                    <div class="row pt-3">
                        <div class="col-12 col-md-8 col-lg-7">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <table class="table bg-white">
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>Name:</strong>
                                        </td>
                                        <td>
                                            <div class="form-row form-group">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" name="first_name" value="{{ auth()->user()->first_name }}" placeholder="Vorname" required class="form-control">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" name="last_name" value="{{ auth()->user()->last_name }}" placeholder="Nachname" required class="form-control">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Organisation:</strong></td>
                                        <td>
                                            <div class="form-row form-group">
                                                <div class="col">
                                                    <input type="text" name="organisation" value="{{ auth()->user()->organisation }}" placeholder="Organisation" class="form-control">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Handelsregister/Nr:</strong>
                                        </td>
                                        <td>
                                            <div class="form-row form-group">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" name="register_court" value="{{ auth()->user()->register_court }}" placeholder="Handelsregister" class="form-control">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" name="register_number" value="{{ auth()->user()->register_number }}" placeholder="Handelsregisternummer" class="form-control">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Geschäftsführer:</strong></td>
                                        <td>
                                            <div class="form-row">
                                                <div class="col">
                                                    <input type="text" name="representative" value="{{ auth()->user()->representative }}" placeholder="Geschäftsführer" class="form-control">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Straße/Hausnummer:</strong></td>
                                        <td>
                                            <div class="form-row form-group">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" name="street" value="{{ auth()->user()->street }}" placeholder="Straße" class="form-control">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" name="housenumber" value="{{ auth()->user()->housenumber }}" placeholder="Hausnummer" class="form-control">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>PLZ/Ort:</strong></td>
                                        <td>
                                            <div class="form-row form-group">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" name="post_code" value="{{ auth()->user()->post_code }}" placeholder="PLZ" class="form-control">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <input type="text" name="city" value="{{ auth()->user()->city }}" placeholder="Ort" class="form-control">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Land:</strong></td>
                                        <td>
                                            <div class="form-row">
                                                <div class="col">
                                                    <select class="form-control" name="country">
                                                        @foreach($countries as $country)
                                                            <option @if(auth()->user()->country == $country->iso_3166_2) selected @endif value="{{ $country->iso_3166_2 }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
            {{--                        <tr>
                                        <td><strong>E-Mail-Adresse</strong></td>
                                        <td></td>
                                    </tr>--}}
                                    <tr>
                                        <td><strong>Kundennummer:</strong></td>
                                        <td>{{ auth()->id() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row pt-3">
                        <div class="col-lg-7 col-md-9 col-sm-12 col-xs-12">
                            <h4>Art der Daten</h4>

                            <div>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" value="meta" id="data1" checked readonly required name="dpa_data[]" >
                                    <label class="custom-control-label" for="data1">
                                        {{trans('privacy.user_dpa_attribute_meta')}}
                                    </label>
                                    <small class="form-text text-muted">Betrifft Podcast-Feeds, Mediendateien, Blogs</small>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" value="contact" id="data2" name="dpa_data[]">
                                    <label class="custom-control-label" for="data2">
                                        {{trans('privacy.user_dpa_attribute_contact')}}
                                    </label>
                                    <small class="form-text text-muted">Betrifft nur das Blog</small>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" value="content" id="data3" name="dpa_data[]">
                                    <label class="custom-control-label" for="data3">
                                        {{trans('privacy.user_dpa_attribute_content')}}
                                    </label>
                                    <small class="form-text text-muted">Betrifft nur das Blog</small>
                                </div>
                            </div>

            {{--                <a class="btn btn-xs btn-success add-input" data-target="#input-type">
                                <i class="icon-plus"></i> Datenart hinzufügen
                            </a>--}}
                        </div>
                    </div>

                    <div class="row pt-3">
                        <div class="col-lg-7 col-md-9 col-sm-12 col-xs-12">
                            <h4>Kreis der Betroffenen</h4>

                            <div>
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" value="users" id="concerned1" checked readonly required name="dpa_concerned[]">
                                    <label class="custom-control-label" for="concerned1">
                                        Nutzer des Auftraggebers
                                    </label>
                                </div>
                            </div>
            {{--                <a class="btn btn-xs btn-success add-input" data-target="#input-affected">
                                <i class="icon-plus"></i> Betroffene hinzufügen
                            </a>--}}
                        </div>
                    </div>

                    <div class="row pt-3">
                        <div class="col-lg-7 col-md-9 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-lg-9">
                                    <h4>Auftrag gemäss Art. 28 DS-GVO</h4>
                                </div>
                                <div class="col-lg-3">
                                    <a href="{{ route('dpa.av') }}" class="btn btn-black btn-xs" title="PDF herunterladen">
                                        <i class="icon-download"></i>
                                        Vorlage
                                    </a>
                                </div>
                            </div>
                            <div class="av-document">
                                @include('pdf.av' . $av->id)
                            </div>
        {{--                    <div class="pt-3">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <h4 class="flex-ie10">Technische und organisatorische Massnahmen nach Art. 32 DS-GVO und Anlage</h4>
                                    </div>
                                    <div class="col-lg-3">
                                        <a href="{{ route('dpa.tom') }}" class="btn btn-black btn-xs" title="PDF herunterladen">
                                            <i class="icon-download"></i>
                                            Vorlage
                                        </a>
                                    </div>
                                </div>
                                <div class="av-document">
                                    {!! $tom->tom !!}
                                </div>
                            </div>--}}
                        </div>
                    </div>

                    <div class="row pt-4">
                        <div class="col-lg-7 col-md-9 col-sm-12 col-xs-12">
                            <div class="{{ $errors->has('dpa_confirmation') ? ' alert alert-danger' : '' }} input-group">
                                <div class="custom-control custom-checkbox">
                                    <input name="dpa_confirmation" value="on" required
                                           type="checkbox" class="custom-control-input{{ $errors->has('dpa_confirmation') ? ' is-invalid' : '' }}" id="dpa_confirmation">
                                    <label class="custom-control-label" for="dpa_confirmation">Ich stimme der Vereinbarung zu.</label>
                                    @if ($errors->has('dpa_confirmation'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('dpa_confirmation') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="pt-3">
                                <button class="btn btn-primary btn-lg btn-block" type="submit"><i class="fa fa-check-square-o"></i>
                                    AV-Vertrag erstellen
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>
@endsection('content')
