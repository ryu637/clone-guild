@extends('layouts.company')

@section('title', 'Dashboard')

@section('content')

<style>
body {
    background-color: #F4EEE0;
    font-family: Georgia, 'Times New Roman', Times, serif;
    width: 100%;
    height: 100%;
}

/* タイトルの強調 */
h1 {
    color: rgba(66, 66, 66, 0.9);
    text-shadow: 0.2vw 0.2vw 0.4vw rgba(0, 0, 0, 0.3);
    font-weight: bold;
    font-size: 3vw; /* 画面サイズに応じたフォント */
}

/* カードのスタイル */
.card {
    border: none;
    transition: 0.3s ease-in-out;
    border-radius: 5%;
    background-color: rgba(66, 66, 66, 0.9);
    box-shadow: 0 0.4vw 1vw rgba(0, 0, 0, 0.3);
    text-align: center;
    width:100%;
    margin: auto;
    padding: 2%;
}

.card:hover {
    transform: translateY(-1%);
    box-shadow: 1vw 1vw 2vw rgba(13, 2, 27, 0.8);
}

.card-header, .card-body {
    color: #F4EEE0;
    border: none;
    font-size: 1.5vw;
}

/* ステータスバッジの強調 */
.status-badge {
    /* min-width: 30%;
    text-align: center;
    border-radius: 1vw;
    padding: 1.5% 2%;
    background-color: #C976DE;
    color: white;
    box-shadow: 0.2vw 0.2vw 0.6vw rgba(0, 0, 0, 0.2);
    font-weight: bold;
    font-size: 1vw; */
    width: 30%; /* 幅を固定 */
    text-align: center;
    border-radius: 1vw;
    padding: 1.5% 2%;
    background-color: #C976DE;
    color: white;
    box-shadow: 0.2vw 0.2vw 0.6vw rgba(0, 0, 0, 0.2);
    font-weight: bold;
    font-size: min(1vw, 0.5em); /* 文字がはみ出そうなら小さくなる */
    white-space: wrap; /* テキストを折り返さない */
}

/* 価格の強調 */
.fw-bold {
    font-size: 2vw;
}

/* アクションボタン */
.actions a {
    text-decoration: none;
    margin-right: 2%;
}

/* 星評価のデザイン */
.star {
    font-size: 1.5vw;
    cursor: default;
}

.text-warning {
    color: #ffc107 !important;
}

.text-muted {
    color: #6c757d !important;
}
a {
    color: inherit;  /* 親要素の色を継承 */
    text-decoration: none;  /* 下線を削除 */
}

</style>

<div class="container mt-5">
    <div class="row">
        <div class="col-6">
            <h1>Stay on Top of Your Current Job Contracts</h1>
        </div>
        <div class="col-6">
            <h1>Stay on Schedule!</h1>
            <p class="mb-5">Keep track of milestones, review pending tasks, and ensure projects are delivered on time. Your success starts with great project management!</p>
        </div>
    </div>

    <div class="row mt-1">
        @php
            $projects_progress = array_pad($projects_progress, 6, null);
        @endphp

        @foreach ($projects_progress as $project_progress)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        @if($project_progress)
                        <a href="{{ route('company.project.detail', $project_progress->id)}}" class="m-0  text-uppercase">
                            {{ $project_progress ? $project_progress['title'] : 'No Project' }}
                        </a>
                        @else
                        <h5 class="m-0 text-uppercase">
                            {{ $project_progress ? $project_progress['title'] : 'No Project' }}
                        </h5>
                        @endif
                    </div>
                    <div class="card-body">
                        {{-- <p class="text-truncate">
                            {{ $project_progress ? $project_progress['description'] : 'No description available.' }}
                        </p> --}}
                        @if($project_progress)
                        <p><a href="{{ route('company.freelancer.profile.show',$project_progress['application']['freelancer']['user']['id'])}}">Freelancer: {{ $project_progress ? $project_progress['application']['freelancer']['user']['name'] : 'N/A' }}</a></p>
                        @else
                        <p>Freelancer: {{ $project_progress ? $project_progress['application']['freelancer']['user']['name'] : 'N/A' }}</p>
                        @endif
                        {{-- <p class="fw-bold">Price: {{ $project_progress ? $project_progress['reward_amount'] : '-' }}</p> --}}

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span>Deadline: {{ $project_progress ? \Carbon\Carbon::parse($project_progress['deadline'])->format('m/d') : '-' }}</span>
                            @for ($i = 1; $i <= 5; $i++)
                                <label for="rank-{{ $i }}" class="star {{ $project_progress && $i <= ($project_progress['required_rank'] ?? 0) ? 'text-warning' : 'text-muted' }}">★</label>
                            @endfor

                            @if ($project_progress)
                                <div class="status-badge">
                                    {{ $project_progress['application']['status'] == 'resulted' ? 'freelancer checking' : $project_progress['application']['status'] }}
                                </div>
                            @endif
                        </div>

                        @if ($project_progress)
                            <div class="actions mt-3 d-flex justify-content-center">
                                @if ($project_progress['application']['status'] == 'requested')
                                    <a href="{{ route('company.paypal.payment', ['price' => $project_progress['reward_amount'], 'id' => $project_progress['id']]) }}" class="btn btn-sm btn-primary">accept and next PayPal (*10%)</a>
                                    <a href="{{ route('company.project.status.decline', ['id' => $project_progress])}}" class="btn btn-sm btn-outline-danger">Decline</a>
                                    <a href="{{ route('company.contact.with_freelancer', ['id' => $project_progress['application']['freelancer']['user_id']])}}" class="btn btn-sm btn-outline-secondary">Message</a>
                                @elseif ($project_progress['application']['status'] == 'submitted')
                                    <a href="{{ route('company.evaluation.evaluation', $project_progress['id']) }}" class="btn btn-sm btn-success">Accept</a>
                                    <a href="{{ route('company.project.status.submittedDecline', ['id' => $project_progress])}}" class="btn btn-sm btn-outline-danger">Decline</a>

                                    @if ($project_progress->application->submission_path)
                                        <a href="{{ route('company.project.download.file', $project_progress->application->id) }}" class=""><i class="fa-solid fa-download fa-2x text-white"></i></a>
                                    @endif

                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID"></script>

@endsection