@extends('layouts.app')

@section('content')
    <div class="shopping-list">
        <div class="card">
            <h5 class="card-title">Shopping Lists <i class="fa-solid fa-rectangle-list"></i></h5>

            {{-- Budget container starts --}}
            <div class="row mb-3 mt-2">
                <div class="col">
                    <label for="budget" class="form-label budget">
                        Budget: £<span id="budget">{{ Auth::user()->total_budget }}</span>
                        <a href="javascript:void(0);"
                            class="d-inline-flex align-items-center text-decoration-none cursor-pointer ms-2">
                            <i class="fa-solid fa-money-bill-trend-up"></i>
                            <span class="ms-2">Set Limit</span>
                        </a>
                    </label>
                </div>

                <div class="col" id="updateBudgetContainer" style="display: none">
                    <div class="input-group">
                        <input type="number" id="budgetUpdateAmount" class="form-control" placeholder="£ Price">
                        <button class="btn btn-add">Update</button>
                    </div>
                </div>

            </div>

            {{-- Shopping list adder --}}
            <div class="input-group mb-3">
                <input type="text" id="itemName" class="form-control" placeholder="Enter item name">
                <input type="number" id="itemPrice" class="form-control" placeholder="£ Price">
                <button class="btn btn-add">Add</button>
            </div>

            <div class="uncheckedItemContainer">
                <ul class="list-group mb-3" id="itemList">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <input type="checkbox" class="form-check-input me-2">
                            <span>Example Item 1</span>
                        </div>
                        <div>
                            £<span class="item-price">10.00</span>
                            <button class="btn btn-sm btn-danger ms-2">×</button>
                        </div>
                    </li>
                </ul>
            </div>

            <div id="checkedItemsContainer">
                <h5 id="checkedItemLabel">Purchased Grocery</h5>
                <ul class="list-group mb-3" id="checkedItemsList">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <input type="checkbox" class="form-check-input me-2" checked>
                            <span style="text-decoration: line-through;">Example Purchased Item</span>
                        </div>
                        <div>
                            £<span class="item-price">5.00</span>
                            <button class="btn btn-sm btn-danger ms-2">×</button>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="d-flex justify-content-between total">
                <span>Total:</span>
                <span>£<span id="total">15.00</span></span>
            </div>

            <div class="row mb-2 mt-3">
                <div class="col">
                    <label class="form-label budget">
                        Share:
                        <i class="fa-solid fa-envelope"></i>
                    </label>
                </div>
                <div class="col-lg-8" id="sendEmailContainer" style="display: none">
                    <div class="input-group">
                        <input type="email" id="emailAddress" class="form-control" placeholder="example@gmail.com">
                        <button class="btn btn-add">Send</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
