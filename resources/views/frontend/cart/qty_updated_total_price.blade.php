<?php $totalPrice = 0.00; ?>

@if($controlPanelCartData->isNotEmpty())
	@foreach($controlPanelCartData as $key=> $val)
		<?php $totalPrice += round($val->price * $val->qty); ?>
	@endforeach
@endif

@if($atmosCartData->isNotEmpty())
	@foreach($atmosCartData as $key=> $val)
		@if($val['is_bareshaft_selection'] != "1")
			<?php $totalPrice += $val->price * $val->qty; ?>
		 @else
	        <?php $totalPrice += $val->bare_pump_price * $val->qty; ?>
	    @endif
	@endforeach
@endif

@if($scpCartData->isNotEmpty())
	@foreach($scpCartData as $key=> $val)
		<?php $totalPrice += $val->price * $val->qty; ?>
	@endforeach
@endif

<!-- A Code: 18-02-2026 Start -->
@if($scpvCartData->isNotEmpty())
	@foreach($scpvCartData as $key=> $val)
		<?php $totalPrice += $val->price * $val->qty; ?>
	@endforeach
@endif
<!-- A Code: 18-02-2026 End -->

@if($boosterCartData->isNotEmpty())
	@foreach($boosterCartData as $key=> $val)
		<?php $totalPrice += $val->price * $val->qty; ?>
	@endforeach
@endif

@if($fireFightingCartData->isNotEmpty())
	@foreach($fireFightingCartData as $key=> $val)
		<?php $totalPrice += $val->price * $val->qty; ?>
	@endforeach
@endif

{{App\Helpers\CurrencyHelper::withCurrency($totalPrice) }}

