<style>
/* Coupon Modal Styling */
.coupon__details {
    display: flex;
    justify-content: space-between;
    align-items: stretch;
    padding: 20px;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    gap: 20px;
}

/* Left side */
.coupon__details-left {
    flex: 1;
}

.coupon__details-left .title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 5px;
}

.coupon__details-left .subtitle {
    font-size: 14px;
    margin-bottom: 8px;
    color: #666;
}

.coupon-info {
    margin-top: 15px;
}

.coupon-info-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 14px;
}

.coupon-info-item span {
    color: #555;
}

.coupon-info-item strong {
    font-weight: 600;
    color: #000;
}

/* Right side (circle discount badge) */
.coupon__details-right {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.coupon {
    background: #6f42c1; /* Purple like Admin */
    border-radius: 50%;
    width: 140px;
    height: 140px;
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    text-align: center;
}

.coupon h4 {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
}

.coupon span {
    font-size: 14px;
    text-transform: uppercase;
    margin-top: 5px;
}
</style>


<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <i class="tio-clear"></i>
</button>
<div class="coupon__details">
    <div class="coupon__details-left">
        <div class="text-center">
            <h6 class="title" id="title">{{ $coupon->title }}</h6>
            <h6 class="subtitle">{{translate('code')}} : <span id="coupon_code">{{ $coupon->code }}</span></h6>
            <div class="text-capitalize">
                <span>{{translate(str_replace('_',' ',$coupon->coupon_type))}}</span>
            </div>
        </div>
        <div class="coupon-info">
            <div class="coupon-info-item">
                <span>{{translate('minimum_purchase')}} :</span>
                <strong id="min_purchase">{{Helpers::set_symbol($coupon->min_purchase)}}</strong>
            </div>
            @if($coupon->coupon_type != 'free_delivery' && $coupon->discount_type == 'percent')
            <div class="coupon-info-item" id="">
                <span>{{translate('maximum_discount')}} : </span>
                <strong id="max_discount">{{Helpers::set_symbol($coupon->max_discount)}}</strong>
            </div>
            @endif
            <div class="coupon-info-item">
                <span>{{translate('start_date')}} : </span>
                <span id="start_date">{{ \Carbon\Carbon::parse($coupon->start_date)->format('dS M Y') }}</span>
            </div>
            <div class="coupon-info-item">
                <span>{{translate('expire_date')}} : </span>
                <span id="expire_date">{{ \Carbon\Carbon::parse($coupon->expire_date)->format('dS M Y') }}</span>
            </div>
        </div>
    </div>
    <div class="coupon__details-right">
        <div class="coupon">
            <div class="d-flex">
                <h4 id="discount">
                    {{$coupon->discount_type=='amount'?(Helpers::set_symbol($coupon->discount)):$coupon->discount.'%'}}
                </h4>
            </div>
            <span>{{translate('off')}}</span>
        </div>
    </div>
</div>
