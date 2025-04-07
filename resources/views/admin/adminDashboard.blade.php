
@extends('layouts.app')

@section('content')

<div id="document-table-container">
@include('components.breadcrumb')


<ul class="box-info">
            <li onclick="location.href='{{ route('documents.incoming') }}'" style="cursor: pointer;">
				<i class='bx bxs-file-import'></i>
                <span class="text">
                    <h3>{{ $incomingCount }}</h3>
                    <p>Incoming</p>
                </span>
            </li>
			<li onclick="location.href='{{ route('documents.outcoming') }}'" style="cursor: pointer;">
				<i class='bx bxs-file-export'></i>
                <span class="text">
                    <h3>{{ $outgoingCount }}</h3>
                    <p>Outgoing</p>
                </span>
            </li>
            <li onclick="location.href='{{ route('admin.users') }}'" style="cursor: pointer;">
				<i class='bx bxs-user-circle'></i>
                <span class="text">
                    <h3>{{ $userCount }}</h3>
                    <p>Users</p>
                </span>
            </li>
			<li onclick="location.href='{{ route('sections.create') }}'" style="cursor: pointer;">
				<i class='bx bx-paperclip'></i>
                <span class="text">
                    <h3>{{ $sectionCount }}</h3>
                    <p>Sections</p>
                </span>
            </li>
			
        </ul>

        
        @include('components.chat-widget', ['users' => $users])


</div>

@endsection