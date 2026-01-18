@extends('layouts.app')

@section('title', 'Meetups')

@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-body">
                <h1>Michigan Meetups!</h1>

                @forelse($meetupGroups as $group)
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="h3">{{ $group['name'] }}</div>
                        </div>
                        <div class="card-body">
                            @foreach($group['description'] as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                            <p>
                                <a href="{{ $group['url'] }}" target="_new">More Information</a>
                            </p>
                        </div>
                    </div>
                @empty
                    <p>No meetup groups available at this time.</p>
                @endforelse

            </div>
        </div>
    </div>
@endsection
