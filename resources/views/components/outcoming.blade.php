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
                        <th>Date Received</th>
                        <th>From</th>
   
                    </tr>
                </thead>
                <tbody>
                @foreach ($documents as $document)
                
                @if($document->uploaded_from === 'outgoing')  {{-- Filter documents with 'incoming' in the 'uploaded_from' column --}}
                    @if(auth()->user()->usertype === 'admin') {{-- Show both pending statuses --}}
                                <tr class="document-summary" data-id="{{ $document->id }}" style="cursor: pointer;">
                                    <td>{{ $document->locator_no }}</td>
                                    <td>{{ $document->user->name }}</td>
                                    <td>{{ $document->subject }}</td>
                                    <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                    <td>{{ $document->uploaded_from }}</td>
                                    
                                </tr>
                    @elseif(auth()->user()->usertype === 'section')
                        @if($document->status === 'pending' || $document->status === 'pending in CL' || $document->status === 'checking') {{-- Show both pending statuses --}}
                            <tr class="document-summary pending-row" data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                <td>{{ $document->uploaded_from }}</td>
                                
                            </tr>
                        @endif
                    @elseif(auth()->user()->role === 'verifier')
                        @if($document->status === 'checking') {{-- Show both pending statuses --}}
                            <tr class="document-summary pending-row" data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                <td>{{ $document->uploaded_from }}</td>
                                
                            </tr>
                        @endif
                    @elseif(auth()->user()->role === 'CL') {{-- Check if the logged-in user is Boss 1 --}}
                        @if($document->status === 'pending in CL') {{-- Show only if status is 'pending_boss1' --}}
                            <tr class="document-summary" data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                <td>{{ $document->uploaded_from }}</td>
                            </tr>
                        @endif
                    @elseif(auth()->user()->role === 'ACL') {{-- Check if the logged-in user is Boss 2 --}}
                        @if($document->status === 'pending') {{-- Show only if status is 'pending' --}}
                            <tr class="document-summary pending-row" data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                 <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                 <td>{{ $document->uploaded_from }}</td>
                            </tr>
                        @endif
                    @elseif(auth()->user()->usertype === 'section' || auth()->user()->usertype === 'user') {{-- Check if the logged-in user is a section user --}}
                        @if($document->status === 'approved') {{-- Show only if status is 'approved' --}}
                            <tr class="document-summary approved-row" data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                <td>{{ $document->uploaded_from }}</td>
                            </tr>
                        @endif
                  
                    @endif
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
