@if (isset($allChattingUsers) && count($allChattingUsers) > 0)
    @foreach ($allChattingUsers as $key => $chatting)
        @if ($chatting->user_id && $chatting->customer)
            <div class="list_filter">
                <div class="chat_list p-3 d-flex gap-2 @if ($key == 0) bg-soft-secondary @endif get-ajax-message-view"
                    data-user-id="{{ $chatting->user_id }}">
                    <div class="chat_people media gap-10 w-100" id="chat_people">
                        <div class="chat_img avatar avatar-sm avatar-circle">
                            <img src="{{ getValidImage(path: 'storage/app/public/profile/' . $chatting->customer->image, type: 'backend-profile') }}"
                                id="{{ $chatting->user_id }}" class="avatar-img avatar-circle" alt="">
                            <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                        </div>
                        <div class="chat_ib media-body">
                            <h5 class="mb-2 seller {{ $chatting->seen_by_seller ? 'active-text' : '' }}"
                                id="{{ $chatting->user_id }}"
                                data-name="{{ $chatting->customer->f_name . ' ' . $chatting->customer->l_name }}"
                                data-phone="{{ $chatting->customer->phone }}">
                                {{ $chatting->customer->f_name . ' ' . $chatting->customer->l_name }}

                                <span class="lead small float-end">{{ $chatting->created_at->diffForHumans() }}</span>
                            </h5>
                            <span class="mt-2 font-weight-normal text-muted d-block" id="{{ $chatting->user_id }}"
                                data-name="{{ $chatting->customer->f_name . ' ' . $chatting->customer->l_name }}"
                                data-phone="{{ $chatting->customer->phone }}">{{ $chatting->customer->phone }}</span>
                        </div>
                    </div>
                    @if (!$chatting->seen_by_seller && !($key == 0))
                        <div class="message-status bg-danger notify-alert-{{ $chatting->user_id }}"></div>
                    @endif
                </div>
            </div>
        @elseif($chatting->delivery_man_id && $chatting->deliveryMan)
            <div class="list_filter">
                <div class="chat_list p-3 d-flex gap-2 @if ($key == 0) bg-soft-secondary @endif get-ajax-message-view"
                    data-user-id="{{ $chatting->delivery_man_id }}">
                    <div class="chat_people media gap-10 w-100" id="chat_people">
                        <div class="chat_img avatar avatar-sm avatar-circle">
                            <img src="{{ getValidImage(path: 'storage/app/public/delivery-man/' . $chatting->deliveryMan->image, type: 'backend-profile') }}"
                                id="{{ $chatting->delivery_man_id }}" class="avatar-img avatar-circle" alt="">
                            <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                        </div>
                        <div class="chat_ib media-body">
                            <h5 class="mb-2 seller {{ $chatting->seen_by_seller ? 'active-text' : '' }}"
                                id="{{ $chatting->delivery_man_id }}"
                                data-name="{{ $chatting->deliveryMan->f_name . ' ' . $chatting->deliveryMan->l_name }}"
                                data-phone="{{ $chatting->deliveryMan->phone }}">
                                {{ $chatting->deliveryMan->f_name . ' ' . $chatting->deliveryMan->l_name }}
                                <span class="lead small float-end">{{ $chatting->created_at->diffForHumans() }}</span>
                            </h5>
                            <span class="mt-2 font-weight-normal text-muted d-block" id="{{ $chatting->user_id }}"
                                data-name="{{ $chatting->deliveryMan->f_name . ' ' . $chatting->deliveryMan->l_name }}"
                                data-phone="{{ $chatting->deliveryMan->phone }}">{{ $chatting->deliveryMan->phone }}</span>
                        </div>
                    </div>
                    @if (!$chatting->seen_by_seller && !($key == 0))
                        <div class="message-status bg-danger notify-alert-{{ $chatting->delivery_man_id }}"></div>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
@endif
