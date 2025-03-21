<!-- resources/views/components/approve.blade.php -->
@extends('layouts.app')

@section('content')

<div id="document-table-container">
    @include('components.breadcrumb')

    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Approved Documents</h3>
                <div class="search-container">
                    @include('components.datepicker')
                    <input type="text" id="search-input" placeholder="Search by subject." />
                    <button id="clearButton" style="display: none;">X</button>
                    <i class='bx bx-search' id="search-icon"></i>
                </div>
            </div>

            @if($documents->isEmpty())
                <p>No approved documents at the moment.</p>
            @else
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <table id="documentsTable" class="table">
                    <thead>
                        <tr>
                            <th>Locator No.</th>
                            <th>Subject</th>
                            <th>File Name</th>
                            <th>Date Receive</th>
                            <th>Accomplish Status</th>
                        </tr>
                    </thead>
                    <tbody  id="documentsTableBody">
                    @foreach ($documents as $document)
                            <tr class="document-summary 
                                @if($document->status === 'approved') 
                                approved-row 
                            
                                @endif
                                " 
                                data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                <td>{{ $document->accomplishStatus() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

<div id="document-details-container" style="display: none;">
    <!-- Document details will be injected here -->
</div>

@endsection
