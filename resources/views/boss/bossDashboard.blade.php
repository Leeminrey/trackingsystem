@extends('layouts.app')

@section('content')

<div id="document-table-container">
    @include('components.breadcrumb')

    <ul class="box-info">
 

    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Uploaded Documents</h3>
                <div class="search-container">
                    @include('components.datepicker')
                    <input type="text" id="search-input" placeholder="Search by subject." />
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
                        <th>From</th>
                    </tr>
                </thead>
                <tbody  id="documentsTableBody">
                @foreach ($documents as $document)
                    @if ($document->status === 'pending')
                        
                        <tr class="document-summary" data-id="{{ $document->id }}" style="cursor: pointer;">
                            <td>{{ $document->locator_no }}</td>
                            <td>{{ $document->user->name }}</td>
                            <td>{{ $document->subject }}</td>
                            <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                            <td>{{ $document->uploaded_from }}</td>
                        </tr>
                    @elseif ($document->status === 'pending in CL')
                        <tr class="document-summary" data-id="{{ $document->id }}" style="cursor: pointer;">
                            <td>{{ $document->locator_no }}</td>
                            <td>{{ $document->user->name }}</td>
                            <td>{{ $document->subject }}</td>
                            <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                            <td>{{ $document->uploaded_from }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>

            </table>

            @include('components.pagination')

        </div>
    </div>
</div>

    @include('components.chat-widget', ['users' => $users])

    <div id="document-details-container" style="display: none;">
    <!-- Document details will be injected here -->
</div>

            
@endsection
