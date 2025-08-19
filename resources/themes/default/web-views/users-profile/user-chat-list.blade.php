@if (isset($inhouseShop))
    <div class="chat_list {{ request()->has('id') && request('id') == 0 ? 'active' : '' }} get-view-by-onclick"
        data-link="{{ route('chat', ['type' => 'seller']) }}/?id={{ '0' }}" id="user_{{ '0' }}">
        <div class="chat_people">
            <div class="chat_img">
                <img alt="" class="__inline-14 __rounded-10 img-profile"
                    src="{{ getValidImage(path: 'storage/app/public/company/' . $web_config['fav_icon']->value, type: 'shop') }}">
            </div>
            <div class="chat_ib">
                <div>
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-1">
                        <h5 class="{{ $inhouseShopUnseenMessage == 0 ? 'active-text' : '' }}">
                            {{ $web_config['name']->value }}</h5>
                        <span class="date">
                            {{ $inhouseShop->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        @if ($inhouseShop->message)
                            <span class="last-msg">{{ $inhouseShop->message }}</span>
                        @elseif(json_decode($inhouseShop['attachment'], true) != null)
                            <span class="last-msg">
                                <i class="fa fa-paperclip pe-1"></i>
                                {{ translate('sent_attachments') }}
                            </span>
                        @endif
                        @if ($inhouseShopUnseenMessage > 0)
                            <span class="new-msg badge btn--primary rounded-full">
                                {{ $inhouseShopUnseenMessage }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if (isset($unique_shops))
    @foreach ($unique_shops as $key => $shop)
        @php($type = $shop->delivery_man_id ? 'delivery-man' : 'seller')
        @php($unique_id = $shop->delivery_man_id ?? $shop->shop_id)
        <div class="chat_list {{ $last_chat->delivery_man_id == $unique_id || $last_chat->shop_id == $unique_id ? 'active' : '' }} get-view-by-onclick"
            data-link="{{ route('chat', ['type' => $type]) }}/?id={{ $unique_id }}" id="user_{{ $unique_id }}">
            <div class="chat_people">
                <div class="chat_img">
                    @if ($shop->delivery_man_id)
                        <img alt="" class="__inline-14 __rounded-10 img-profile"
                            src="{{ getValidImage(path: 'storage/app/public/delivery-man/' . $shop->deliveryMan->image, type: 'avatar') }}">
                    @else
                        <img alt="" class="__inline-14 __rounded-10 img-profile"
                            src="{{ getValidImage(path: 'storage/app/public/shop/' . $shop->image, type: 'shop') }}">
                    @endif
                </div>
                <div class="chat_ib">
                    <div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-1">
                            <h5 class="{{ $shop->seen_by_customer == 0 ? 'active-text' : '' }}">
                                {{ $shop->f_name ? $shop->f_name . ' ' . $shop->l_name : $shop->name }}</h5>
                            <span class="date">
                                {{ $shop->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            @if ($shop->message)
                                <span class="last-msg">{{ $shop->message }}</span>
                            @elseif(json_decode($shop['attachment'], true) != null)
                                <span class="last-msg">
                                    <i class="fa fa-paperclip pe-1"></i>
                                    {{ translate('sent_attachments') }}
                                </span>
                            @endif

                            @if ($shop->unseen_message_count > 0)
                                <span class="new-msg badge btn--primary rounded-full">
                                    {{ $shop->unseen_message_count }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endForeach
@endif
