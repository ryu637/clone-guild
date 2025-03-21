@extends('layouts.company')

@section('title', '')

@section('content')
<style>
    body {
        background-color: #F4EEE0;
    }
    h1 {
        color: rgba(66, 66, 66, 0.9);
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        font-weight: bold;
    }
    .card {
        background-color: rgba(66, 66, 66, 0.8);
        color: #F4EEE0;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 10px 10px 20px rgba(13, 2, 27, 0.8);
    }

    .card-header, .card-body {
        color: #F4EEE0;
        border: none;
    }
    .deadline-box {
        width: 50px;
        height: 50px;
        font-size: 0.9rem;
        font-weight: bold;
        color: #333;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .star {
        font-size: 1rem;
    }
    .text-truncate {
    white-space: nowrap;       /* 改行を防ぐ */
    overflow: hidden;          /* はみ出た部分を隠す */
    text-overflow: ellipsis;   /* 省略記号（…）を表示 */
    width: 400px;              /* 最大の幅を指定（任意のサイズに変更） */
    display: block; 
    color: #F4EEE0;
    text-decoration: none;

    }
    .text-truncate:hover{
        text-decoration: underline;
    }
    .carousel-control-prev{
        margin-left: -70px;
    }
    .carousel-control-next{
        margin-right: -70px;
    }
    .recommend-circle {
        border-radius: 50%;
        background-color: gray;
        color: white;
        font-weight: bold;
        font-size: 1rem;
    }
</style>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Simplify Your Hiring Process!</h1>
            <p>Stay organized and attract the right candidates. Streamline your hiring process to connect with top talent and build a reliable team.</p>
        </div>
        <div class="col-md-6">
            <h1>Keep track of your job posts and attract the best talent.</h1>
        </div>
    </div>

    @php
        $projects = array_pad($projects, 8, (object)[
            'deadline' => null,
            'title' => 'No Project Available',
            'reward_amount' => '-',
            'required_rank' => 0,
            'id' => null,
            'recommended_freelancers_count' => 0
        ]);

        $chunks = array_chunk($projects, 8); // 8個ごとに分割
    @endphp

    <div class="mb-3">
        <a href="{{ route('company.project.for_create') }}" class="btn btn-outline-secondary me-2">Create New Job</a>
        <a href="{{ route('company.freelancer.favorite.list') }}" class="btn btn-outline-secondary">Favorite Freelancer</a>
    </div>

    <!-- Bootstrap Carousel for Pagination -->
    <div id="projectCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach ($chunks as $index => $chunk)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        @foreach ($chunk as $project)
                        <div class="col">
                            <div class="card border mt-1  p-3  h-100">
                                <div class="row align-items-center">
                                    <div class="col-2 d-flex justify-content-center">
                                        <div class="deadline-box">
                                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('m/d') : '--/--' }}
                                        </div>
                                    </div>
                                    <div class="col-5">
                                      @if ($project->id)
    <a href="{{route('company.project.detail', (int)$project->id)}}" class="fs-5 fw-bold text-truncate">{{ $project->title }}</a>
@else
    <span class="fs-5 fw-bold text-muted">{{ $project->title }}</span>
@endif

                                        <div class="d-flex align-items-center mt-2">
                                            <p class="mb-0 me-2 text-muted fw-bold">Price: {{ $project->reward_amount }}</p>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="star {{ $i <= $project->required_rank ? 'text-warning' : 'text-muted' }}">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        @if ($project->id)
                                            <a href="{{ route('company.project.recommended_freelancers', $project->id) }}" class="recommend-circle  ms-5 ">
                                                {{ $project->recommended_freelancers_count }}
                                            </a>
                                            <a href="{{ route('company.project.for_update', $project->id) }}" class="btn btn-sm btn-outline-secondary mt-4 ms-3">Edit</a>
                                            <button class="btn btn-sm btn-outline-secondary mt-4" data-bs-toggle="modal" data-bs-target="#delete-project-{{ $project->id }}">
                                                Delete
                                            </button>
                                            @include('companies.projects.modal.delete')
                                        @else
                                            <p class="text-muted">No Actions Available</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<button class="carousel-control-prev" type="button" data-bs-target="#projectCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
</button>
<button class="carousel-control-next" type="button" data-bs-target="#projectCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
</button>

@endsection
