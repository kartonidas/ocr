@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">{{ __('Pobrane dokumenty') }}</h2>
        <div class="border shadow p-3">
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

            <div class="mt-4">
                {!! $documents->links() !!}
            </div>
        </div>
    </div>
@endsection
