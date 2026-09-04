<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Livewire\WithPagination;

new
#[Layout('layouts.app')]
class extends Component {

    use WithPagination;

    protected $listeners = ['echo:leads,LeadsUpdated' => '$refresh'];

    public function with(): array
    {
        return [
            'leads' => \App\Models\Lead::with('user')->latest()->paginate(20),
        ];
    }
};

//

?>

<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title fw-bold">
                        <i class="fas fa-clock"></i>
                        Live Leads
                    </h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 small " style="font-weight: 600">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Name</th>
                                <th>Time</th>
                                <th>ERC</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($leads as $lead)
                                <tr>
                                    <td class="">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($lead->user->avatar_url)
                                                <img src="{{ $lead->user->avatar_url }}"
                                                     alt="{{ $lead->user->username }}"
                                                     onerror="this.onerror=null; this.src='{{ asset('assets/avatars/memoji_1.png') }}';"
                                                     class="rounded-3" style="width: 30px;">
                                            @else
                                                <div
                                                    class="bg-label-primary bg-opacity-50 rounded-3  d-flex align-items-center"
                                                    style="width: 30px; height: 30px;">
                                                    <i class="fa-solid fa-rocket text-primary m-auto mt-2"
                                                       style="font-size: 18px;"></i>
                                                </div>
                                            @endif

                                            <div class="d-flex flex-column justify-content-center align-items-start">
                                                <span class="text-truncate">{{ $lead->user->username }}</span>
                                                <span class="text-truncate text-secondary">{{ $lead->user->email }}</span>
                                            </div>

{{--                                            <span class="text-truncate">{{ $lead->user->username }}</span>--}}
                                        </div>
                                    </td>
                                    <td class="d-flex align-items-center gap-2">
                                        @if($lead->image)
                                            <img src="{{ $lead->image }}" alt="{{ $lead->name }}"
                                                 onerror="this.onerror=null; this.src='{{ asset('assets/img/icon-light.png') }}';"
                                                 class="rounded-3" style="width: 30px;">
                                        @else
                                            <div
                                                class="bg-label-primary bg-opacity-50 rounded-3  d-flex align-items-center"
                                                style="width: 30px; height: 30px;">
                                                <i class="fa-solid fa-rocket text-primary m-auto mt-2"
                                                   style="font-size: 18px;"></i>
                                            </div>
                                        @endif
                                        <span class="text-truncate">{{ $lead->name }}</span>
                                    </td>
                                    <td>{{ $lead->created_at->diffForHumans() }}</td>
                                    <td class="text-truncate">
                                        <x-coins :coins="$lead->points"/>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No activity found</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $leads->links(data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
