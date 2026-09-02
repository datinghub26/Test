<div>
    <h3 class="mb-4 text-white text-uppercase fw-bold">Frequently Asked Questions</h3>
    <p class="f-md"><strong>Last updated: Oct 10, 2024</strong></p>

    <!-- Nav tabs -->
    <ul class="nav nav-pills" id="faqTab" role="tablist">
        @foreach($faqsInfo as $category => $faqs)
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center {{ $loop->first ? 'active' : '' }}"
                        id="{{ $category }}-tab"
                        data-bs-toggle="tab" href="#{{ $category }}" role="tab" aria-controls="{{ $category }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ ucfirst($category) }}
                </button>
            </li>
        @endforeach
    </ul>

    <!-- Tab content -->
    <div class="tab-content p-0  mt-3" id="faqTabContent">
        @foreach($faqsInfo as $category => $faqs)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $category }}"
                 role="tabpanel" aria-labelledby="{{ $category }}-tab">
                <div class="accordion">
                    @foreach($faqs as $index => $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button f-md text-white" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#as-{{ $index }}">
                                    {{ $faq['question'] }}
                                </button>
                            </h2>
                            <div id="as-{{ $index }}"
                                 class="accordion-collapse collapse {{ $loop->iteration == 1 ? 'show' : '' }}">
                                <div class="accordion-body small">
                                    {{ $faq['answer'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</div>
