@use('App\Enums\MatchingRuleType')

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">
            {{ $action == 'create' ? __('Nowa reguła') : __('Aktualizacja reguły') }}
        </h2>

        <div class="border p-3">
            @if ($errors->any())
                <div class="alert alert-danger pb-0">
                    <strong>{{ __('Formularz zawiera następujące błędy') }}</strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="post">
                @csrf

                <div class="row">
                    <div class="col-12 col-sm-6 mb-3">
                        <label for="formRuleType" class="form-label">{{ __('Rodzaj dopasowania') }}*</label>
                        <select name="type" class="form-control" id="formRuleType">
                            @foreach (MatchingRuleType::cases() as $rule)
                                <option value="{{ $rule->value }}" @if(($matchingRule['type'] ?? '') == $rule->value){{ "selected" }}@endif>{{ $rule->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-sm-3 mb-3">
                        <label for="formProductId" class="form-label">{{ __('Identyfikator produktu') }}*</label>
                        <input type="number" name="product_id" class="form-control" id="formProductId" value="{{ $matchingRule['product_id'] ?? '' }}">
                    </div>

                    <div class="col-12 col-sm-3 mb-3">
                        <label for="formPriority" class="form-label">{{ __('Priorytet') }}*</label>
                        <input type="number" name="priority" class="form-control" id="formPriority" value="{{ $matchingRule['priority'] ?? '' }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label for="formRule" class="form-label">{{ __('Tekst dopasowania') }}*</label>
                        <input type="text" name="rule" class="form-control" id="formRule" value="{{ $matchingRule['rule'] ?? '' }}">
                    </div>

                    <div class="col-12 mb-3 d-flex justify-content-between">
                        <a href="{{ route('matching-rules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-chevron-left me-1"></i>
                            {{ __('Anuluj') }}
                        </a>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Zapisz') }}
                            <i class="bi bi-floppy ms-1"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
