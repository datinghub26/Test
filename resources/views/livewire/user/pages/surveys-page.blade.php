<div>
    <div class="card">
        <div class="card-header">
            <div class="h5 card-title fw-bold text-white d-flex align-items-center gap-1">
                <x-heroicon-s-pencil-square class="text-primary" width="25px"/>
                <span>Surveys</span>
                <x-heroicon-s-question-mark-circle class="text-secondary" width="25px" data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Complete surveys to earn rewards"/>
            </div>
        </div>

        <div class="card-body">
            <div class="py-2 card-container">
                @php
                    $userLevel = auth()->check() ? auth()->user()->level : 0;
                @endphp
                @foreach($surveys as $survey)
                    <x-cards.partner :offer="$survey" :is-locked="$survey->unlock_level > $userLevel"/>
                @endforeach
            </div>

            @if(count($surveys) == 0)
                <div class="row text-center text-secondary">
                    <x-heroicon-s-archive-box-x-mark style="width: 75px; height: 75px; margin: auto"/>
                    <span class="text-center">No surveys available at the moment</span>
                </div>
            @endif
        </div>
    </div>
</div>
