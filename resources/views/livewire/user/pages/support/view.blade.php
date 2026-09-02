<div>
    <div class="card">
        <div class="card-header">
            <div class="">
                <a href="{{ route('support') }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back
                </a>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">

                        <h2 class="mb-0 fs-4">{{ $ticket->title }}</h2>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($ticket->status == 'open')
                            <a onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                               wire:click="closeTicket"
                               class="btn btn-sm btn-danger">
                                Close Ticket
                            </a>
                        @endif

                        <span class="badge bg-label-{{ $ticket->status == 'open' ? 'success' : 'danger' }}">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                </div>
                <small class="text-secondary small">Created on {{ $ticket->created_at->diffForHumans() }}</small>
            </div>
        </div>

        <div class="card-body">
            @if($ticket->isClosed())
                <div class="alert alert-warning small">
                    This ticket is closed. You may reply to this ticket to reopen it.
                </div>
            @endif

            <div class="mb-3">
                <p class="mb-1 small fw-medium">Attachments:</p>
                <div class="d-flex gap-2">
                    @forelse($ticket->media as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank"
                           class="btn btn-sm btn-label-secondary">
                            <i class="fa-solid fa-paperclip me-2"></i>{{ $media->file_name  }}
                        </a>
                    @empty
                        <span class="text-secondary small">No attachments</span>
                    @endforelse
                </div>
            </div>

            @foreach($ticket->messages as $reply)
                <div class="card mb-3">
                    <div class="card-body bg-body" style="border-radius: 20px">
                        <div class="d-flex align-items-start">
                            <img
                                src="{{ $reply->user->avatar() }}"
                                alt="{{ $reply->user->username }}"
                                class="rounded-circle me-3"
                                style="width: 42px; height: 42px"/>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="d-flex align-items-center justify-items-start gap-2">
                                            <h6 class="mb-0 f-md">{{ $reply->user->username }}</h6>
                                            @if($reply->user->id == $ticket->user_id)
                                                <span class="badge bg-label-success"
                                                      style="font-size: 11px; padding: 4px 4px">Owner</span>
                                            @else
                                                <span class="badge bg-label-primary"
                                                      style="font-size: 11px; padding: 4px 4px">Support</span>
                                            @endif
                                        </div>
                                        <small class="text-secondary d-block">{{ $reply->user->email }}</small>
                                    </div>

                                    <small class="text-secondary">{{ $reply->created_at->format('d M Y H:i') }}</small>
                                </div>
                                <p class="mt-2 mb-0">{!!  nl2br($reply->message) !!}</p>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach


            <div class="mt-3">
                <textarea wire:model="message" class="form-control @error('message') is-invalid @enderror"
                          placeholder="Type your message here" rows="3"></textarea>

                @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="d-flex justify-content-end mt-3">
                    <button wire:click="addReply" class="btn btn-primary  mt-2">Submit Reply</button>
                </div>
            </div>
        </div>
    </div>
</div>
