<div>
    <div class="card">
        <div class="card-header pb-0">
            <div class="card-title h5 fw-bold text-white d-flex align-items-center gap-2">
                <x-heroicon-s-chat-bubble-left-ellipsis class="text-primary" class="text-primary " width="25px"/>
                Support
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#ticketModal">
                    <i class="fa-solid fa-plus me-2"></i>New Ticket
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="card card bg-body p-0 mt-3">
                <div class="card-body p-0">
                    <div class="table table-responsive">
                        <table class="table table-border-bottom-0 align-middle mb-0 small " style="font-weight: 600">
                            <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->title }}</td>
                                    <td>
                                        @php($status = ucfirst($ticket->status))

                                        @if($status == 'Open')
                                            <span class="badge bg-success rounded-pill">
                                        {{ $status }}
                                    </span>
                                        @elseif($status == 'Closed')
                                            <span class="badge bg-secondary rounded-pill">
                                        {{ $status }}
                                    </span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('support.show', $ticket) }}"
                                           class="btn btn-sm btn-label-success">
                                            <i class="fa-solid fa-eye me-2"></i>View
                                        </a>

                                        @if($ticket->isOpen())
                                            <a onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                               wire:click="closeTicket({{ $ticket->id }})"
                                               class="btn btn-sm btn-label-secondary">
                                                <i class="fa-solid fa-check me-2"></i>Close
                                            </a>
                                        @else
                                            <a class="btn btn-sm btn-label-secondary disabled"> <i
                                                    class="fa-solid fa-check me-2"></i>Closed</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center m-auto">
                                        <i class="fa-solid fa-info-circle me-2" style="font-size: 15px"></i>
                                        <span class="small text body fw-normal">No tickets found</span>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($tickets->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $tickets->links(data: ['scrollTo' => false]) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>


    </div>


    <!-- Create Ticket Modal -->
    <div class="modal fade" wire:ignore.self id="ticketModal" tabindex="-1"
         x-data
         x-ref="modal"
         x-init="
             const bootstrapModal = new bootstrap.Modal($refs.modal);
             $refs.modal.addEventListener('shown.bs.modal', () => $refs.subject.focus());
             window.addEventListener('ticket-created', () => bootstrapModal.hide());
        ">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="createTicket">
                    <div class="modal-body pb-0">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Name</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->username }}" disabled
                                       style="cursor: not-allowed">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Email</label>
                                <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled
                                       style="cursor: not-allowed">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Subject</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                   x-ref="subject"
                                   autofocus
                                   tabindex="1"
                                   wire:model="subject">
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" rows="4"
                                      wire:model="description"></textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Priority</label>
                            <select class="form-select @error('priority') is-invalid @enderror" wire:model="priority">
                                <option value="">Select Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Attachments (Optional)</label>
                            <x-filepond::upload wire:model="file" max-files="2" multiple/>

                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <span class="text-secondary small d-block mt-2">allowed file types: jpg, jpeg, png, pdf and
                                max file size: 2mb</span>
                        </div>

                        <div class="d-flex gap-2 justify-content-end py-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>Create Ticket</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                    Creating...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @filepondScripts
    <style>
        .filepond--drop-label {
            color: var(--bs-secondary);
        }

        .filepond--panel-root {
            background-color: transparent;
            border: 2px solid var(--bs-dark);
        }

        .filepond--root {
            font-family: inherit;
            font-size: 0.875rem;
        }
    </style>
</div>

