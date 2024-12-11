@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-content-center mb-4">
            <h2 class="mb-0">{{ __('Szczegóły dokumentu') }}</h2>
            <div>
                <a href="{{ route('documents.index') }}" class="btn btn-secondary h-auto">
                    <i class="bi bi-chevron-left"></i>
                    {{ __('Powrót do listy dokumentów') }}
                </a>
            </div>
        </div>

        <div class="border shadow p-3 mb-4">
            <div class="row align-items-center">
                <div class="col-2 lh-sm">
                    <div>{{ __('Status') }}:</div>
                    {{ $document->status->label() }}
                </div>
                <div class="col lh-sm">
                    <div>{{ __('Temat wiadomości') }}:</div>
                    {{ $document->mail_subject }}
                </div>
                <div class="col-2 lh-sm">
                    <div>{{ __('Data pobrania') }}:</div>
                    {{ $document->created_at }}
                </div>
                <div class="col-2 text-end">
                    <a href="{{ route('document.pdf', $document) }}" class="btn btn-primary">
                        {{ __('Pobierz plik') }}
                        <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="border shadow p-3">
            <div class="row">
                <div class="col-6">
                    <object data="{{ route('document.pdf', [$document->id, true]) }}" type="application/pdf" width="100%" height="1000px">
                        <embed src="{{ route('document.pdf', [$document->id, true]) }}" type="application/pdf">
                            <p>{{ __('Twoja przeglądarka nie obsługuje plików PDF. Możesz pobrać plik') }}: <a href="{{ route('document.pdf', $document->id) }}">{{ __('pobierz PDF') }}</a>.</p>
                        </embed>
                    </object>
                </div>

                <div class="col-6">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-tables-tab" data-bs-toggle="tab" data-bs-target="#nav-tables" type="button" role="tab" aria-controls="nav-tables" aria-selected="true">{{ __('TABLES') }}</button>
                            <button class="nav-link" id="nav-forms-tab" data-bs-toggle="tab" data-bs-target="#nav-forms" type="button" role="tab" aria-controls="nav-forms" aria-selected="false">{{ __('FORMS') }}</button>
                            <button class="nav-link" id="nav-forms-tab" data-bs-toggle="tab" data-bs-target="#nav-idosell" type="button" role="tab" aria-controls="nav-idosell" aria-selected="false">{{ __('IdoSell') }}</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-tables" role="tabpanel" aria-labelledby="nav-tables-tab">
                            @foreach ($tables as $i => $table)
                                <div class="mt-2 mb-4">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                @foreach($table['header'] as $j => $header)
                                                    <th style="min-width: {{ $table['lengths'][$j] }}px">
                                                        <small>{{ $header }}</small>
                                                    </th>
                                                @endforeach
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($table['rows'] as $row)
                                                <tr>
                                                    @foreach($row as $cell)
                                                        <td>
                                                            <small>{{ $cell }}</small>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="tab-pane fade" id="nav-forms" role="tabpanel" aria-labelledby="nav-forms-tab">
                            <table class="table table-sm">
                                @foreach ($forms as $i => $form)
                                    @foreach ($form->result as $name => $row)
                                        <tr>
                                            <td><small>{{ $name }}</small></td>
                                            <td>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($row as $r)
                                                        <li><small>{{ $r }}</small></li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </table>
                        </div>

                        <div class="tab-pane fade" id="nav-idosell" role="tabpanel" aria-labelledby="nav-idosell-tab">
                            Rozczytane parametry do IdoSell...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
