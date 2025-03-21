@extends('layouts.app')

@section('content')

<div id="document-table-container">
    
    @include('components.breadcrumb')


    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Uploaded Documents</h3>
                <div class="search-container">
                    @include('components.datepicker')
                    <input type="text" id="search-input" placeholder="Search by Locator No." />
                    <button id="clearButton" style="display: none;">X</button>
                    <i class='bx bx-search' id="search-icon"></i>
                </div>
            </div>

            <table id="documentsTable">
                <thead>
                    <tr>
                        <th>Locator No.</th>
                        <th>Uploader</th>
                        <th>Subject</th>
                        <th>Date Receive</th>
                        <th>Accomplish Status</th>
                        <th>From</th>
                    </tr>
                </thead>

                <tbody  >
                        @foreach ($documents as $document)
                            <tr class="document-summary 
                                 
                                @if($document->status === 'pending')
                                pending-row 
                                @elseif($document->status === 'pending in CL')
                                pending-CL-row 
                                @endif
                                " 
                                data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                <td>{{ $document->accomplishStatus() }}</td>
                                <td>{{ $document->uploaded_from }}</td>
                            </tr>
                        @endforeach
                    
                </tbody>

            </table>

            <!-- Pagination Controls -->
            @include('components.pagination')
        </div>
    </div>
</div>

<div id="document-details-container" style="display: none;">
    <!-- Document details will be injected here -->
</div>


@endsection
