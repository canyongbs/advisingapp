@props([
    'count',
    'max',
])

<?php
    $colorClass = 'text-green-500';

    if ($count > $max) {
        $colorClass = 'text-red-500';
    } elseif ($count === $max) {
        $colorClass = 'text-yellow-500';
    }
?>


<span class='{{ $colorClass }}'>{{ $count }}/{{ $max }}</span> CRM Seats Used