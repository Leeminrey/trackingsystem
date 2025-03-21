@extends('layouts.app')

@section('content')



<div id="document-table-container">
    @include('components.breadcrumb')

    <ul class="box-info">

             <li onclick="location.href='{{ route('documents.completed') }}'" style="cursor: pointer;">
                <i class='bx bxs-x-circle'></i>
                <span class="text">
                    <h3>{{ $completedCount }}</h3>
                    <p>Accomplished</p>
                </span>
            </li>   
                <li onclick="location.href='{{ route('documents.approved') }}'" style="cursor: pointer;">
                    <i class='bx bxs-calendar-check'></i>
                    <span class="text">
                        <h3>{{ $approvedCount }}</h3>
                        <p>Approved</p>
                    </span>
                </li>
            <li>
                <i class='bx bxs-time-five'></i>
                <span class="text">
                    <h3>{{ $pendingCount }}</h3>
                    <p>Pending</p>
                </span>
            </li>
            <li onclick="location.href='{{ route('documents.reject') }}'" style="cursor: pointer;">
                <i class='bx bxs-x-circle'></i>
                <span class="text">
                    <h3>{{ $rejectedCount }}</h3>
                    <p>Rejected</p>
                </span>
            </li>
          
            
        </ul>

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
                        <th>Accomplish Status</th>
                        <th>From</th>
                    </tr>
                </thead>
                <tbody id="documentsTableBody">
                @foreach ($documents as $document)
                    @if($document->uploaded_from === 'outgoing' || ($document->uploaded_from === 'incoming' && $document->status === 'approved')) 
                        <tr class="document-summary 
                            @if($document->status === 'approved') 
                            approved-row 
                            @elseif($document->status === 'rejected')
                            rejected-row 
                            @elseif($document->status === 'pending')
                            pending-row 
                            @elseif($document->status === 'pending in CL')
                            pending-CL-row 
                            @elseif($document->status === 'checking')
                            pending-row
                            @endif
                            " 
                            data-id="{{ $document->id }}" style="cursor: pointer;">
                            <td>{{ $document->locator_no }}</td>
                            <td>{{ $document->user->name }}</td>
                            <td>{{ $document->subject }}</td>
                            <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                            <td>{{ $document->accomplishStatus() }}</td>
                            <td>{{ $document->uploaded_from}}</td>
                        </tr>
                    @endif
                @endforeach


                </tbody>
            </table>

            @include('components.pagination')
        </div>
    </div>
</div>

<div id="document-details-container" style="display: none;">
    <!-- Document details will be injected here -->
</div>

@endsection
