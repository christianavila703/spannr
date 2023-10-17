@extends('layouts.app')

@section('content')
    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$organization->generateBreadcrumbs()"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
        <x-slot:buttons></x-slot:buttons>
        <x-slot:title>Financial Statements of {{ $organization->name }}</x-slot:title>

        <table class="table table-responsive organization-financial-statements">
            <thead>
                <tr>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Fiscal Period</th>
                    <th>Fiscal Year</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($organization->financialStatements as $financialStatement)
                <tr>
                    <td>{{ $financialStatement->start_date->format('M d, Y') }}</td>
                    <td>{{ $financialStatement->end_date->format('M d, Y') }}</td>
                    <td>{{ $financialStatement->fiscal_period }}</td>
                    <td>{{ $financialStatement->fiscal_year }}</td>
                    <td>
                        <button class="btn btn-info btn-xs btn-show-financials collapsed" data-bs-toggle="collapse"
                                data-bs-target="#row{{ $loop->index }}" aria-expanded="false"
                                aria-controls="collapse">
                            <i class="fa fa-arrow-down icon-open"></i>
                            <i class="fa fa-arrow-up icon-close"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="padding: 0;">
                        <div class="collapse" id="row{{ $loop->index }}">
                            <table class="table table-striped">
                                <tbody>
                                @foreach($financialStatement->financials as $label => $data)
                                    <tr>
                                        <th>{{ Str::title(str_replace('_', ' ', $label)) }}</th>
                                        <th></th>
                                    </tr>
                                    @foreach($data as $subLabel => $subData)
                                        <tr>
                                            <td>{{ Str::title(str_replace('_', ' ', $subLabel)) }}:</td>
                                            <td>
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ $subData['unit'] != 'USD' ? $subData['unit'] : '$' }}</span>
                                                    <span>{{ number_format($subData['value'])}}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </x-entities.show>

@endsection
