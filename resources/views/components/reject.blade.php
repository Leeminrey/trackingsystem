<!-- resources/views/components/reject.blade.php -->
@extends('layouts.app')

@section('content')

<div id="document-table-container">
@include('components.breadcrumb')

    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Reject</h3>
                <div class="search-container">
                    @include ('components.datepicker')
                    <input type="text" id="search-input" placeholder="Search by subject." />
                    <button id="clearButton" style="display: none;">X</button>
                    <i class='bx bx-search' id="search-icon"></i>
                </div>
            </div>

            @if($documents->isEmpty())
                <p>No reject at the moment.</p>
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
                            <th>Comment</th>
                            <th>Status</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            <tr class="document-summary" data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ $document->original_file_name }}</td>
                                <td>{{ $document->librarianComments->last()->comment ?? 'N/A' }}</td> 
                                <td> 
                                    <span class="{{ $document->status === 'approved' ? 'status-approved' : ($document->status === 'rejected' ? 'status-rejected' : 'status-pending') }}">
                                        {{ $document->status }}
                                    </span>
                                </td>
                               
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
