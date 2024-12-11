@use('App\Enums\OcrDocumentStatus')

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">{{ __('Pobrane dokumenty') }}</h2>
        <div class="border shadow p-3">
            <form class="mb-4 pb-3 border-bottom">
                <div class="row align-items-end">
                    <div class="col">
                        <label class="form-label mb-0 text-muted">{{ __('Tytuł maila') }}</label>
                        <input type="text" name="mail_subject" value="{{ request()->input('mail_subject') }}" class="form-control">
                    </div>
                    <div class="col">
                        <label class="form-label mb-0 text-muted">{{ __('Data dodania (YYYY-MM-DD)') }}</label>
                        <div class="d-flex">
                            <input type="text" name="create_date_from" value="{{ request()->input('create_date_from') }}" placeholder="{{ __('od') }}" class="form-control">
                            &nbsp;
                            <input type="text" name="create_date_to" value="{{ request()->input('create_date_to') }}" placeholder="{{ __('do') }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label mb-0 text-muted">{{ __('Status') }}</label>
                        <select class="form-control" name="status">
                            <option></option>
                            @foreach (OcrDocumentStatus::cases() as $status)
                                <option value="{{ $status->value }}" @if(request()->input('status') == $status->value){{ "selected" }}@endif>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-secondary">{{ __('Szukaj') }}</button>
                    </div>
                </div>
            </form>


            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <td style="width: 160px">{{ __('Data dodania') }}</td>
                        <td>{{ __('Tutuł maila') }}</td>
                        <td style="width: 120px">{{ __('Status') }}</td>
                        <td style="width: 350px">{{ __('Plik') }}</td>
                        <td style="width: 60px"></td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($documents as $document)
                        <tr>
                            <td class="align-middle">{{ $document->created_at  }}</td>
                            <td class="align-middle">{{ $document->mail_subject  }}</td>
                            <td class="align-middle">{{ $document->status->label()  }}</td>
                            <td class="align-middle">{{ $document->file }}</td>
                            <td class="align-middle text-end">
                                <a href="{{ route('document.show', $document) }}" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {!! $documents->links() !!}
            </div>
        </div>
    </div>
@endsection
