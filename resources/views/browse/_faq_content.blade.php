@if (count($faqs))
    @foreach ($faqs as $faq)
        @include('browse._faq_question', ['faq' => $faq])
    @endforeach
@else
    <p>No questions / answers found.</p>
@endif

<div class="text-center mt-4 small text-muted">{{ $faqs->count() }} result{{ $faqs->count() == 1 ? '' : 's' }} found.</div>
