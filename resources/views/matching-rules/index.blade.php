@use('App\Enums\MatchingRuleType')
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between">
            <h2 class="mb-4">{{ __('Reguły dopasowań') }}</h2>
            <div>
                <a href="{{ route('matching-rules.create') }}" class="btn btn-primary">
                    {{ __('Dodaj regułę') }}
                </a>
            </div>
        </div>
        <div class="border p-3">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 450px">{{ __('Typ reguły') }}</th>
                            <th>{{ __('Tekst dopasowania') }}</th>
                            <th style="width: 120px">{{ __('Id produktu') }}</th>
                            <th style="width: 100px">{{ __('Priorytet') }}</th>
                            <th style="width: 120px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($matchingRules->isNotEmpty())
                            @foreach ($matchingRules as $matchingRule)
                                <tr>
                                    <td class="align-middle">{{ MatchingRuleType::from($matchingRule->type)->label() }}</td>
                                    <td class="align-middle">{{ $matchingRule->rule }}</td>
                                    <td class="align-middle">{{ $matchingRule->product_id }}</td>
                                    <td class="align-middle">{{ $matchingRule->priority }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('matching-rules.update', $matchingRule) }}" class="btn btn-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <a href="#" data-bs-toggle="modal" data-bs-target="#destroyRuleModal-{{ $matchingRule->id }}" class="btn btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </a>

                                        <div class="modal fade text-start" id="destroyRuleModal-{{ $matchingRule->id }}" tabindex="-1" aria-labelledby="destroyRuleModalLabel-{{ $matchingRule->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form method="post" action="{{ route('matching-rules.destroy', $matchingRule) }}">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="destroyRuleModalLabel-{{ $matchingRule->id }}">{{ __('Usuń regułę') }}</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            {{ __('Wybrana reguła zostanie bezpowrotnie usunięta.') }}
                                                            <br/>
                                                            {{ __('Czy na pewno chcesz ją usunąć?') }}
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Anuluj') }}</button>
                                                            <button type="submit" class="btn btn-danger btn-sm">{{ __('Usuń') }}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5">{{ __('Brak reguł') }}</td>
                            </tr>
                       @endif
                    </tbody>
                </table>
            </div>

            {!! $matchingRules->links() !!}
        </div>
    </div>
@endsection
