@extends('layouts.app')

@section('content')


    <div class="shopping-list">
        <div class="card">
            <h5 class="card-title">Shopping Lists <i class="fa-solid fa-rectangle-list"></i></h5>

            {{-- Budget container --}}
            <div class="row mb-3 mt-2">
                <div class="col">
                    <label for="budget" class="form-label budget">
                        Budget: £<span id="budget">{{ Auth::user()->total_budget }}</span>
                        <a href="javascript:void(0);" onclick=""
                            class="d-inline-flex align-items-center text-decoration-none cursor-pointer ms-2">
                            <i class="fa-solid fa-money-bill-trend-up"></i>
                            <span class="ms-2">Set Limit</span>
                        </a>
                    </label>
                </div>
                <div class="col" id="updateBudgetContainer" style="display: none">
                    <div class="input-group">
                        <input type="number" id="budgetUpdateAmount" class="form-control" placeholder="£ Price">
                        <button class="btn btn-add" onclick="">Update</button>
                    </div>
                </div>
            </div>

            {{-- Shopping list container --}}
            <div class="input-group mb-3">
                <input type="text" id="itemName" class="form-control" placeholder="Enter item name">
                <input type="number" id="itemPrice" class="form-control" placeholder="£ Price">
                <button class="btn btn-add" onclick="addNewItemToList()">Add</button>
            </div>

            {{-- Unpurchased item container --}}
            <div class="uncheckedItemContainer">
                <ul class="list-group mb-3" id="itemList">
                    <!-- Existing item (example) -->
                    @if ($shoppingList)
                        @foreach ($shoppingList as $shoppingListItem)
                            @if (!$shoppingListItem->is_purchased)
                                <li class="list-group-item d-flex justify-content-between align-items-center"
                                    draggable="true">
                                    <div>
                                        <input type="checkbox" class="form-check-input me-2">
                                        <span>{{ $shoppingListItem->item_name }}</span>
                                    </div>
                                    <div>
                                        £<span class="item-price">{{ $shoppingListItem->item_price }}</span>
                                        <button class="btn btn-sm btn-danger ms-2"
                                            onclick="removeItemFromList(this)">×</button>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>

            {{-- Purchased item container --}}
            <div id="checkedItemsContainer">
                @if ($shoppingList && $shoppingList->where('is_purchased', true)->isNotEmpty())
                    <h5 id="checkedItemLabel">Purchased Grocery</h5>
                @endif
                <ul class="list-group mb-3" id="checkedItemsList">
                    @if ($shoppingList)
                        @foreach ($shoppingList as $shoppingListItem)
                            @if ($shoppingListItem->is_purchased)
                                <li class="list-group-item d-flex justify-content-between align-items-center"
                                    draggable="true">
                                    <div>
                                        <input type="checkbox" class="form-check-input me-2" checked>
                                        <span
                                            style="text-decoration: line-through;">{{ $shoppingListItem->item_name }}</span>
                                    </div>
                                    <div>
                                        £<span class="item-price">{{ $shoppingListItem->item_price }}</span>
                                        <button class="btn btn-sm btn-danger ms-2"
                                            onclick="removeItemFromList(this)">×</button>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>

            {{-- Total amount container --}}
            <div class="d-flex justify-content-between total">
                <span>Total:</span>
                <span>£<span id="total">{{ number_format($shoppingList->sum('item_price'), 2) }}</span></span>
            </div>

            {{-- Send email container --}}
            <div class="row mb-2 mt-3">
                <div class="col">
                    <label class="form-label budget">
                        Share:
                        <i class="fa-solid fa-envelope" style="cursor: pointer;" onclick=""></i>
                    </label>
                </div>
                <div class="col-lg-8" id="sendEmailContainer" style="display: none">
                    <div class="input-group">
                        <input type="email" id="emailAddress" class="form-control" placeholder="example@gmail.com">
                        <button class="btn btn-add" onclick="">Send</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Toast Container (it will hold the toasts) -->
    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <!-- Toast will be dynamically added here -->
    </div>

@endsection
