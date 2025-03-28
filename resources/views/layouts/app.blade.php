<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	

	<!-- Add this in your layout file (layouts/app.blade.php) -->
	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	

	<title> 
        DTS | 
            @if (auth()->check())
                    @if (auth()->user()->usertype === 'admin')
                        ADMIN
                    @elseif (auth()->user()->role === 'CL')
                        City Librarian
					@elseif (auth()->user()->role === 'ACL')
                        Assistant City Librarian
					@elseif (auth()->user()->usertype === 'section')
                        {{auth()->user()->role}} 
                    @else
                        USER
                    @endif
                @else
                    DOCUMENT TRACKING SYSTEM
                @endif
    </title>
</head>
<body>


	<!-- SIDEBAR -->
	<section id="sidebar">
		@if (auth()->user()->usertype === 'admin')
			<a href="{{ url('admin/dashboard') }}" class="brand">
				<img src="{{ asset('images/QCPL.png')}}" alt="QCPL Logo" class="logo">
			</a>
		@elseif (auth()->user()->usertype === 'boss')
			<a href="{{ url('boss/dashboard') }}" class="brand">
				<img src="{{ asset('images/QCPL.png')}}" alt="QCPL Logo" class="logo">
			</a>
		@elseif (auth()->user()->usertype === 'section')
			<a href="{{ url('section/dashboard') }}" class="brand">
				<img src="{{ asset('images/QCPL.png')}}" alt="QCPL Logo" class="logo">
			</a>
		@else
			<a href="{{ route('dashboard') }}" class="brand">
				<img src="{{ asset('images/QCPL.png')}}" alt="QCPL Logo" class="logo">
			</a>
		@endif
		<ul class="side-menu top">
			<li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" data-route="dashboard">
				@if(auth()->user()->usertype === 'boss')	
					<a href="{{ url('boss/dashboard') }}"><i class='bx bxs-dashboard' ></i>Dashboard</a>

				@elseif(auth()->user()->usertype === 'admin')
					
					<a href="{{ url('admin/adminDashboard') }}"><i class='bx bxs-dashboard' ></i>Dashboard</a>
				@elseif(auth()->user()->usertype === 'section')
					
					<a href="{{ url('section/dashboard') }}"><i class='bx bxs-dashboard' ></i>Dashboard</a>
				@else
				
					<a href="{{ url('dashboard') }}"> <i class='bx bxs-dashboard' ></i>Dashboard</a>
				@endif
			</li>
			
			@if(auth()->user()->usertype === 'section' || auth()->user()->role === 'receiving')
				<li data-route="analytics">
					<a href="{{ route('documents.upload') }}">
					<i class='bx bx-upload'></i>
						<span class="text">Upload</span>
					</a>
				</li>

	
			@endif
			
			<li>
				<a href="#" class="dropdown-toggle" data-route="documents">
					<i class='bx bxs-shopping-bag-alt'></i>
					<span class="text">Documents</span>
					<i class='bx bxs-chevron-down'></i>
				</a>
        	</li>
			@if(in_array(auth()->user()->usertype, ['admin','boss', 'section']) || auth()->user()->role === 'receiving')
			<li class="document-menu-item">
				<a href="{{ route('documents.incoming') }}"><i class='bx bxs-file-export'></i>
				<span>Incoming</span></a>
			</li>

			@endif
			@if(auth()->user()->usertype === 'section' || auth()->user()->role === 'receiving')

			<li data-route="notification" class="document-menu-item sub-menu-item">
				<a href="{{ route('documents.approved') }}">
				<i class='bx bxs-check-square'></i>
					<span class="text">Approve</span>
				</a>
			</li>

			
			<li data-route="notification" class="document-menu-item sub-menu-item">
				<a href="{{ route('documents.reject') }}">
				<i class='bx bxs-x-square'></i>
					<span class="text">Revise</span>
				</a>
			</li>

			@endif

			<li class="document-menu-item">
				<a href="{{ route('documents.outcoming') }}"><i class='bx bxs-file-import' ></i>
			    <span>Outgoing</span></a>
			</li>

			<li data-route="notification" class="document-menu-item">
				<a href="{{ route('documents.completed') }}">
				<i class='bx bxs-check-square'></i>
					<span class="text">Accomplished</span>
				</a>

			<!-- <li class="document-menu-item">
				<a href="{{ route('documents.submitted') }}"><i class='bx bxs-file-blank' ></i>
				<span>Submitted</span></a>
			</li> -->
		

			

			

			@if(auth()->user()->usertype === 'boss')
				<li data-route="history">
					<a href="#">
					<i class='bx bx-history'></i>
						<span class="text">History</span>
					</a>
				</li>

				
			</li>

			<li data-route="ongoing"  class="document-menu-item">
				<a href="{{ route('documents.ongoing') }}">
				<i class='bx bxs-time'></i>
					<span class="text">Ongoing</span>
				</a>
			</li>

			@endif

			

			

			@if(auth()->user()->usertype === 'admin')
			<li data-route="users">
				<a href="{{ route('admin.users') }}">
					<i class='bx bxs-group' ></i>
					<span class="text">Users</span>
				</a>
			</li>
			<li data-route="create-users">
				<a href="{{ route('admin.createUser') }}">
				<i class='bx bxs-user-plus'></i>
					<span class="text">Create Users</span>
				</a>
			</li>
			<li data-route="create-section">
				<a href="{{ route('sections.create') }}">
				<i class='bx bx-list-plus' ></i>
					<span class="text">Create Section</span>
				</a>
			</li>
			@endif
		</ul>
		<ul class="side-menu">
		@if(in_array(auth()->user()->usertype, ['user', 'boss', 'section']))
			<li class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}" data-route="Profile Edit">
				<a href="{{ route('profile.edit') }}">
					<i class='bx bxs-cog'></i>
					<span class="text">Profile</span>
				</a>
			</li>
		@endif

<!-- 
			<li >
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <a href="#" class="logout" onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class='bx bxs-log-out-circle'></i>
                        <span class="text">Logout</span>
                    </a>
                </form>
			</li> -->
		</ul>
	</section>

	
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
    
		<!-- NAVBAR -->
		<x-dashboard-navbar />
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
    
        @yield('content')
		
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->

	<!-- Modal Structure -->
	<div id="fileModal" class="modal">
		<div class="modal-content">
			<span class="close" onclick="closeFileModal()">&times;</span>
			<iframe id="fileViewer" style="width: 100%; height: 600px;" frameborder="0"></iframe>
		</div>
	</div>
	

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	
	<script src="{{ asset('js/scrip.js') }}"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>



</body>
</html>