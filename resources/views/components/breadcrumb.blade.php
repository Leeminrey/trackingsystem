@php
$sectionName = DB::table('sections')->where('id', auth()->user()->section_id)->value('name');
@endphp

<div class="head-title">
				<div class="left">
					<h1>
					@if(auth()->user()->role === 'CL')
						City Librarian 
					@elseif(auth()->user()->role === 'ACL')
						Assistant City Librarian
					@elseif(auth()->user()->usertype === 'admin')
						Admin Dashboard
					@elseif(auth()->user()->usertype === 'section')
						{{ auth()->user()->role }}
					@elseif(auth()->user()->role === 'verifier')
						Verifier	
					@else
						Receiving
					@endif
					</h1>
					<ul class="breadcrumb">
						<li>
							<a href="#">{{$sidebarActive ?? 'Dashboard'}}</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
                            <a class="active" href="#">{{ $activeMenu ?? 'Home' }}</a>
						</li>
					</ul>
				</div>
				
</div>