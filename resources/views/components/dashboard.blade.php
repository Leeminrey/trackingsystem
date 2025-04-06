@extends('layouts.app')

@section('content')

@php
    $allowedStatuses = ['approved', 'rejected', 'pending', 'pending in CL'];
@endphp

<div id="document-table-container">
    @include('components.breadcrumb')

            <ul class="box-info">
            <li onclick="location.href='{{ route('documents.completed') }}'" style="cursor: pointer;">
                    <i class='bx bxs-calendar-check'></i>
                    <span class="text">
                        <h3>{{ $completed }}</h3>
                        <p>Completed</p>
                    </span>
                </li>
           
      

            @if(auth()->user()->role === 'receiving')
         

                <li onclick="location.href='{{ route('documents.approved') }}'" style="cursor: pointer;">
                    <i class='bx bxs-calendar-check'></i>
                    <span class="text">
                        <h3>{{ $approve }}</h3>
                        <p>Approved</p>
                    </span>
                </li>
           
            <li>
                <i class='bx bxs-time-five'></i>
                <span class="text">
                    <h3>{{ $pending }}</h3>
                    <p>Pending</p>
                </span>
            </li>
            <!-- <li onclick="location.href='{{ route('documents.reject') }}'" style="cursor: pointer;">
                <i class='bx bxs-x-circle'></i>
                <span class="text">
                    <h3>{{ $rejected }}</h3>
                    <p>Revise</p>
                </span>
            </li> -->
            @endif
        </ul>


    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Uploaded Documents</h3>
                <div class="search-container">
                    @include('components.datepicker')
                    <input type="text" id="search-input" placeholder="Search by Locator No. or." />
                    <button id="clearButton" style="display: none;">X</button>
                    <i class='bx bx-search' id="search-icon"></i>
                </div>
                <i class='bx bx-filter'></i>
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
                    @if(auth()->user()->role === 'receiving')
                        {{-- Receiving can see all documents from "incoming" --}}
                        @if($document->uploaded_from === 'incoming' &&  in_array($document->status, $allowedStatuses))
                            <tr class="document-summary 
                                @if($document->status === 'approved') approved-row 
                                @elseif($document->status === 'rejected') rejected-row 
                                @elseif($document->status === 'pending') pending-row 
                                @elseif($document->status === 'pending in CL') pending-CL-row 
                                @endif
                                " 
                                data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                <td>{{ $document->uploaded_from }}</td>
                            </tr>

                            
                        @elseif($document->uploaded_from === 'outgoing' && $document->status === 'approved')
                            {{-- Receiving can see only "approved" documents from "outgoing" --}}
                            <tr class="document-summary approved-row"
                                data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                <td>{{ $document->uploaded_from }}</td>
                            </tr>
                        @endif

                    @elseif(auth()->user()->role === 'verifier')
                        @if($document->uploaded_from === 'outgoing' && $document->status === 'checking')
                            <tr class="document-summary pending-row"
                                data-id="{{ $document->id }}" style="cursor: pointer;">
                                <td>{{ $document->locator_no }}</td>
                                <td>{{ $document->user->name }}</td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ \Carbon\Carbon::parse($document->date_received)->format('M. d, Y') }}</td>
                                <td>{{ $document->accomplishStatus() }}</td>
                                <td>{{ $document->uploaded_from }}</td>
                            </tr>
                        @endif
                    @endif
                @endforeach
           
                </tbody>
            </table>

            <!-- Pagination Controls -->
            @include('components.pagination')

            @include('components.chat-widget')
        </div>
    </div>
</div>

<div id="document-details-container" style="display: none;">
    <!-- Document details will be injected here -->
</div>


@endsection
