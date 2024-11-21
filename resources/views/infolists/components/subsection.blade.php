<div
    {{ $attributes->merge(
            [
                'id' => $getId(),
            ],
            escape: false,
        )->merge($getExtraAttributes(), escape: false)->class(['p-6']) }}>
    {{ $getChildComponentContainer() }}
</div>
