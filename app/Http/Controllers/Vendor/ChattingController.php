<?php

namespace App\Http\Controllers\Vendor;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Events\ChattingEvent;
use App\Services\ChattingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Traits\PushNotificationTrait;
use Illuminate\Http\RedirectResponse;
use App\Enums\ViewPaths\Vendor\Chatting;
use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests\Vendor\ChattingRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\ShopRepositoryInterface;
use App\Contracts\Repositories\VendorRepositoryInterface;
use App\Contracts\Repositories\ChattingRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\DeliveryManRepositoryInterface;

class ChattingController extends BaseController
{
    use PushNotificationTrait;

    /**
     * @param ChattingRepositoryInterface $chattingRepo
     * @param ShopRepositoryInterface $shopRepo
     * @param ChattingService $chattingService
     * @param VendorRepositoryInterface $vendorRepo
     * @param DeliveryManRepositoryInterface $deliveryManRepo
     * @param CustomerRepositoryInterface $customerRepo
     */
    public function __construct(
        private readonly ChattingRepositoryInterface $chattingRepo,
        private readonly ShopRepositoryInterface $shopRepo,
        private readonly ChattingService $chattingService,
        private readonly VendorRepositoryInterface $vendorRepo,
        private readonly DeliveryManRepositoryInterface $deliveryManRepo,
        private readonly CustomerRepositoryInterface $customerRepo,
    )
    {
    }


    /**
     * @param Request|null $request
     * @param string|array|null $type
     * @return View|Collection|LengthAwarePaginator|callable|RedirectResponse|null
     */
    public function index(?Request $request, string|array $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        return $this->getListView(type:$type);
    }

    /**
     * @param string|array $type
     * @return View
     */
    public function getListView(string|array $type): View
    {
        $shop = $this->shopRepo->getFirstWhere(params: ['seller_id' => auth('seller')->id()]);
        $vendorId = auth('seller')->id();
        if ($type == 'delivery-man') {
            $allChattingUsers = $this->chattingRepo->getListWhereNotNull(
                orderBy: ['created_at' => 'DESC'],
                filters: ['seller_id' => $vendorId],
                whereNotNull: ['delivery_man_id', 'seller_id'],
                relations: ['deliveryMan'],
                dataLimit: 'all'
            )->unique('delivery_man_id');

            if (count($allChattingUsers) > 0) {
                $lastChatUser = $allChattingUsers[0]->deliveryMan;
                $this->chattingRepo->updateAllWhere(
                    params: ['seller_id' => $vendorId, 'delivery_man_id' => $lastChatUser['id']],
                    data: ['seen_by_seller' => 1]
                );

                $chattingMessages = $this->chattingRepo->getListWhereNotNull(
                    orderBy: ['created_at' => 'DESC'],
                    filters: ['seller_id' => $vendorId, 'delivery_man_id' => $lastChatUser->id],
                    whereNotNull: ['delivery_man_id', 'seller_id'],
                    relations: ['deliveryMan'],
                    dataLimit: 'all'
                );
                $categories = Category::all();

                return view(Chatting::INDEX[VIEW], [
                    'userType' => $type,
                    'allChattingUsers' => $allChattingUsers,
                    'lastChatUser' => $lastChatUser,
                    'chattingMessages' => $chattingMessages,
                    'categories' => $categories
                ]);
            }
        } elseif ($type == 'customer') {
            $allChattingUsers = $this->chattingRepo->getListWhereNotNull(
                orderBy: ['created_at' => 'DESC'],
                filters: ['seller_id' => $vendorId],
                whereNotNull: ['user_id', 'seller_id'],
                relations: ['customer'],
                dataLimit: 'all'
            )->unique('user_id');

            if (count($allChattingUsers) > 0) {
                $lastChatUser = $allChattingUsers[0]->customer;
                $this->chattingRepo->updateAllWhere(
                    params: ['seller_id' => $vendorId, 'user_id' => $lastChatUser['id']],
                    data: ['seen_by_seller' => 1]
                );

                $chattingMessages = $this->chattingRepo->getListWhereNotNull(
                    orderBy: ['created_at' => 'DESC'],
                    filters: ['seller_id' => $vendorId, 'user_id' => $lastChatUser->id],
                    whereNotNull: ['user_id', 'seller_id'],
                    relations: ['customer'],
                    dataLimit: 'all'
                );
                $categories = Category::all();
                return view(Chatting::INDEX[VIEW], [
                    'userType' => $type,
                    'allChattingUsers' => $allChattingUsers,
                    'lastChatUser' => $lastChatUser,
                    'chattingMessages' => $chattingMessages,
                    'categories' => $categories
                ]);
            }
        }
        return view(Chatting::INDEX[VIEW], compact('shop'));
    }

    public function fetchChatUpdates(Request $request): JsonResponse
    {
        $type = $request->input('type', 'customer');
        $vendorId = auth('seller')->id();

        // Determine chat configuration
        $config = match ($type) {
            'delivery-man' => [
                'filters' => ['seller_id' => $vendorId],
                'whereNotNull' => ['delivery_man_id', 'seller_id'],
                'relations' => ['deliveryMan'],
                'idKey' => 'delivery_man_id',
                'relationKey' => 'deliveryMan',
            ],
            default => [
                'filters' => ['seller_id' => $vendorId],
                'whereNotNull' => ['user_id', 'seller_id'],
                'relations' => ['customer'],
                'idKey' => 'user_id',
                'relationKey' => 'customer',
            ]
        };

        // Fetch all users chatting with this seller
        $allChattingUsers = $this->chattingRepo->getListWhereNotNull(
            orderBy: ['created_at' => 'DESC'],
            filters: $config['filters'],
            whereNotNull: $config['whereNotNull'],
            relations: $config['relations'],
            dataLimit: 'all'
        )->unique($config['idKey']);

        if ($allChattingUsers->isEmpty()) {
            return response()->json([
                'chatUsersView' => '',
                'chatMessagesView' => ''
            ]);
        }

        // Get the last user chatted and update seen status
        $lastChatUser = $allChattingUsers[0]->{$config['relationKey']};
        $this->chattingRepo->updateAllWhere(
            params: array_merge(['seller_id' => $vendorId], [$config['idKey'] => $lastChatUser->id]),
            data: ['seen_by_seller' => 1]
        );

        // Get chat messages with this user
        $chattingMessages = $this->chattingRepo->getListWhereNotNull(
            orderBy: ['created_at' => 'DESC'],
            filters: array_merge(['seller_id' => $vendorId], [$config['idKey'] => $lastChatUser->id]),
            whereNotNull: $config['whereNotNull'],
            relations: $config['relations'],
            dataLimit: 'all'
        );

        // Return rendered partial views
        return response()->json([
            'chatUsersView' => view('vendor-views.chatting.chat_users', [
                'allChattingUsers' => $allChattingUsers,
                'userType' => $type,
            ])->render(),
            'chatMessagesView' => view('vendor-views.chatting.list_user_message', [
                'lastChatUser' => $lastChatUser,
                'chattingMessages' => $chattingMessages,
                'userType' => $type,
            ])->render(),
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMessageByUser(Request $request):JsonResponse
    {
        $vendorId = auth('seller')->id();
        $data = [];
        if ($request->has(key: 'delivery_man_id')) {
            $getUser = $this->deliveryManRepo->getFirstWhere(params: ['id' => $request['delivery_man_id']]);
            $this->chattingRepo->updateAllWhere(
                params: ['seller_id' => $vendorId, 'delivery_man_id' => $request['delivery_man_id']],
                data: ['seen_by_seller' => 1]);

            $chattingMessages = $this->chattingRepo->getListWhereNotNull(
                orderBy: ['created_at' => 'DESC'],
                filters: ['seller_id' => $vendorId, 'delivery_man_id' => $request['delivery_man_id']],
                whereNotNull: ['delivery_man_id', 'seller_id'],
                dataLimit: 'all'
            );
            $data = self::getRenderMessagesView(user: $getUser, message: $chattingMessages, type: 'delivery_man');
        } elseif ($request->has(key: 'user_id')) {
            $getUser = $this->customerRepo->getFirstWhere(params: ['id' => $request['user_id']]);
            $this->chattingRepo->updateAllWhere(
                params: ['seller_id' => $vendorId, 'user_id' => $request['user_id']],
                data: ['seen_by_seller' => 1]
            );
            $chattingMessages = $this->chattingRepo->getListWhereNotNull(
                orderBy: ['created_at' => 'DESC'],
                filters: ['seller_id' => $vendorId, 'user_id' => $request['user_id']],
                whereNotNull: ['user_id', 'seller_id'],
                dataLimit: 'all'
            );
            $data = self::getRenderMessagesView(user: $getUser, message: $chattingMessages, type: 'customer');
        }
        return response()->json($data);
    }

    /**
     * @param ChattingRequest $request
     * @return JsonResponse
     */
    public function addVendorMessage(ChattingRequest $request):JsonResponse
    {
        $data = [];
        $vendor = $this->vendorRepo->getFirstWhere(params: ['id' => auth('seller')->id()]);
        $shop = $this->shopRepo->getFirstWhere(params: ['seller_id' => auth('seller')->id()]);
        $attachment = $this->chattingService->getAttachment($request);
        $firebaseService = app(\App\Services\FirebaseService::class);
        if ($request->has(key: 'delivery_man_id')) {
            $chatting = $this->chattingRepo->add(
                data: $this->chattingService->getDeliveryManChattingData(
                    request: $request,
                    shopId: $shop['id'],
                    vendorId: $vendor['id']
                )
            );
            $deliveryMan = $this->deliveryManRepo->getFirstWhere(params: ['id' => $request['delivery_man_id']]);
            ChattingEvent::dispatch('message_from_seller', 'delivery_man', $deliveryMan, $vendor);

            $chattingMessages = $this->chattingRepo->getListWhereNotNull(
                orderBy: ['created_at' => 'DESC'],
                filters: ['seller_id' => $vendor['id'], 'delivery_man_id' => $request['delivery_man_id']],
                whereNotNull: ['delivery_man_id', 'seller_id'],
                dataLimit: 'all'
            );
            $data = self::getRenderMessagesView(user: $deliveryMan, message: $chattingMessages, type: 'delivery_man');

        } elseif ($request->has(key: 'user_id')) {
            $chatting = $this->chattingRepo->add(
                data: $this->chattingService->getCustomerChattingData(
                    request: $request,
                    shopId: $shop['id'],
                    vendorId: $vendor['id'])
            );
            $customer = $this->customerRepo->getFirstWhere(params: ['id' => $request['user_id']]);
            ChattingEvent::dispatch('message_from_seller', 'customer', $customer, $vendor);

            $chattingMessages = $this->chattingRepo->getListWhereNotNull(
                orderBy: ['created_at' => 'DESC'],
                filters: ['seller_id' => $vendor['id'], 'user_id' => $request['user_id']],
                whereNotNull: ['user_id', 'seller_id'],
                dataLimit: 'all'
            );

            \Log::info('chatting response :' . json_encode($chatting) );

            $customer = User::find($request->user_id);

            $userFcmToken = $customer?->cm_firebase_token;
            if($userFcmToken){
                $data = [
                    'title' => 'New Message from ' . $vendor->f_name . ' '. $vendor->l_name,
                    'description' => $chatting->message,
                    'data' => [
                        'id' => $chatting->id,
                        'type' => 'customer',
                        'shop_id' => $chatting->shop_id,
                        'admin_id' => $chatting->admin_id,
                        'seller_id' => $chatting->seller_id,
                        'seen_by_seller' => $chatting->seen_by_seller
                    ],
                ];

                try{

                    $firebaseService->sendToDevice($userFcmToken, $data['title'], $data['description'], $data['data']);

                }catch(\Exception $e){
                    \Log::info('Chat to customer message push error: ' . $e->getMessage());
                }
            }

            $data = self::getRenderMessagesView(user: $customer, message: $chattingMessages, type: 'customer');
        }
        return response()->json($data);
    }

    /**
     * @param string $tableName
     * @param string $orderBy
     * @param string|int|null $id
     * @return Collection
     */
    protected function getChatList(string $tableName, string $orderBy, string|int $id = null) :Collection
    {
        $vendorId = auth('seller')->id();
        $columnName = $tableName == 'users' ? 'user_id' : 'delivery_man_id';
        $filters = isset($id) ? ['chattings.seller_id' => $vendorId, $columnName => $id] : ['chattings.seller_id' => $vendorId];
        return $this->chattingRepo->getListBySelectWhere(
            joinColumn: [$tableName, $tableName . '.id', '=', 'chattings.' . $columnName],
            select: ['chattings.*', $tableName . '.f_name', $tableName . '.l_name', $tableName . '.image'],
            filters: $filters,
            orderBy: ['chattings.created_at' => $orderBy],
        );
    }

    /**
     * @param object $user
     * @param object $message
     * @param string $type
     * @return array
     */
    protected function getRenderMessagesView(object $user, object $message, string $type): array
    {
        $userData = ['name' => $user['f_name'].' '.$user['l_name'],'phone' => $user['country_code'].$user['phone']];

        if ($type == 'customer') {
            $userData['image'] = getValidImage(path: 'storage/app/public/profile/' . ($user['image']), type: 'backend-profile');
        }else {
            $userData['image'] = getValidImage(path: 'storage/app/public/delivery-man/' . ($user['image']), type: 'backend-profile');
        }

        return [
            'userData' => $userData,
            'chattingMessages' => view('vendor-views.chatting.messages', [
                'lastChatUser' => $user,
                'userType' => $type,
                'chattingMessages' => $message
            ])->render(),
        ];
    }
}
