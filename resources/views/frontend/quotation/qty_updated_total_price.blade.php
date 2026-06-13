
<?php $totalPrice = 0.00; ?>

@if($controlPanelCartData->isNotEmpty())
@foreach($controlPanelCartData as $key=> $val)
<?php $totalPrice += round($val->price * $val->qty); ?>
@endforeach
@endif

@if($atmosCartData->isNotEmpty())
@foreach($atmosCartData as $key=> $val)
<?php $totalPrice += round($val->price * $val->qty); ?>
@endforeach
@endif

@if($scpCartData->isNotEmpty())
@foreach($scpCartData as $key=> $val)
<?php $totalPrice += round($val->price * $val->qty); ?>
@endforeach
@endif

<!-- A Code: 25-02-2026 Start -->

@if($scpvCartData->isNotEmpty())
@foreach($scpvCartData as $key=> $val)
<?php $totalPrice += round($val->price * $val->qty); ?>
@endforeach
@endif

<!-- A Code: 25-02-2026 End -->

@if($boosterCartData->isNotEmpty())
@foreach($boosterCartData as $key=> $val)
<?php $totalPrice += round($val->price * $val->qty); ?>
@endforeach
@endif

{{App\Helpers\CurrencyHelper::withCurrency($totalPrice) }}

