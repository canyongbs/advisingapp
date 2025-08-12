@use(Illuminate\Support\Facades\URL)

<qna-advisor-embed url="{{ URL::to(
    URL::signedRoute(
        name: 'ai.qna-advisors.show',
        parameters: ['advisor' => $advisor],
        absolute: false,
    )
) }}"></qna-advisor-embed>
<script src="{{ url('js/widgets/qna-advisor/advising-app-qna-advisor-widget.js') }}"></script>
