<?php

namespace App\Http\Controllers;


use App\Http\Requests\MatchingRuleRequest;
use App\Models\MatchingRule;

class MatchingRuleController extends Controller
{
    public function index()
    {
        $matchingRules = MatchingRule::orderBy('priority', 'asc')->paginate(50);

        return view('matching-rules.index', compact('matchingRules'));
    }

    public function create()
    {
        $data = [
            'action' => 'create',
            'matchingRule' => request()->old(),
        ];

        return view('matching-rules.form', $data);
    }

    public function store(MatchingRuleRequest $request)
    {
        $matchingRule = MatchingRule::create($request->validated());

        return redirect()->route('matching-rules.update', $matchingRule)->with('status', __('Reguła została dodana.'));
    }

    public function edit(MatchingRule $matchingRule)
    {
        $data = [
            'action' => 'update',
            'matchingRule' => request()->old() ? request()->old() : $matchingRule
        ];

        return view('matching-rules.form', $data);
    }

    public function update(MatchingRuleRequest $request, MatchingRule $matchingRule)
    {
        $matchingRule->update($request->validated());

        return redirect()->route('matching-rules.update', $matchingRule)->with('status', __('Reguła została zaktualizowana.'));
    }

    public function destroy(MatchingRule $matchingRule)
    {
        $matchingRule->delete();

        return redirect()->route('matching-rules.index')->with('status', __('Reguła została usunięta.'));
    }
}
