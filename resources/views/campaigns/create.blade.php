@extends('main')

@section('content')
    <div class="container">

        <section class="flush-with-above">
            <h1>Kampagne anlegen</h1>
            <div class="container">
                <form action="{{ route('campaigns.store') }}" method="post">
                    @csrf

                    @include('parts.fields.title', ['autofocus' => true, 'required' => true, 'value' => old('title', $campaign->title), 'label' => 'Titel der Kampagne/Kampagnen-Name', 'placeholder' => 'Der Titel ist frei wählbar, sollte aber die Kampagne klar erkennbar machen.'])

                    @include('parts.fields.textarea', ['required' => true, 'name' => 'description', 'value' => old('description', $campaign->description), 'label' => 'Beschreibung der Kampagne/Einladungstext der an Podcaster verschickt wird', 'placeholder' => 'Die Beschreibung sollte Lust darauf machen, an der Werbekampagne teilzunehmen. Du brauchst keine Ansprache, wie "Hallo", oder Verabschiedung, wie "Viele Grüße" ergänzen. Das wird automatisch vom System ergänzt.'])

                    @include('parts.fields.name', ['required' => true, 'value' => old('name', $campaign->name), 'label' => 'Name des Kampagnen-Veranstalters', 'placeholder' => 'Schreibe hier den Namen des Ansprechpartners für die Kampagne rein.'])

                    @include('parts.fields.email', ['required' => true, 'name' => 'reply_to', 'value' => old('reply_to', $campaign->reply_to), 'label' => 'Antwortadresse für E-Mails', 'placeholder' => 'E-Mail-Adresse an die Antworten geschickt werden.'])


                    <div class="form-group{{ $errors->has('itunes_category') ? ' alert alert-danger' : '' }} clearfix">
                        <label for="icategory" class="">Kategorien</label>
                        <select name="itunes_category[]" required id="icategory" class="form-control form-control-lg" multiple size="10">
                            @foreach($itunesCategories as $key => $category)
                                <option value="{{ $key }}" @if(in_array($key, old("itunes_category"))) selected @endif>{{ $category }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('itunes_category'))
                            <span class="help-block">
                                <strong>{{ $errors->first('itunes_category') }}</strong>
                            </span>
                        @endif

                        <small class="help-text">Wähle bis zu 5 Kategorien aus, die für deine Kampagne passend sind.</small>
                    </div>

                    <div class="mt-0">
                        <button class="btn btn-lg btn-success">Kampagne anlegen</button>
                    </div>
                </form>
            </div>
        </section>
        <!--end of container-->
    </div>
@endsection