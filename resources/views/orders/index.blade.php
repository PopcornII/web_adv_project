@extends('layouts.index')

@section('content')
<div class="container">
    <h1 class="mb-4">Add Order</h1>

    <!-- Success and Error Messages -->
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <!-- Left Side: Menu List -->
        <div class="col-md-6">
            <h3>Menu List</h3>

            <!-- Search & Filter Form -->
            <form action="{{ route('orders.index') }}" method="GET" class="mb-3">
                <div class="input-group mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Search by name" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                </div>

                <!-- Category Filter dropdown-->
                {{-- <select name="category" class="form-control" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select> --}}

                <div class="mb-3">
                    <a href="{{ route('orders.index') }}" class="btn {{ request('category') ? 'btn-outline-primary' : 'btn-primary' }}">
                        All
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('orders.index', ['category' => $category]) }}" 
                        class="btn {{ request('category') == $category ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ ucfirst($category) }}
                        </a>
                    @endforeach
                </div>
            </form>

            <!-- Menu Grid -->
            <div class="row">
                @if($menuItems->isEmpty())
                    <div class="alert alert-info">No menu items found based on your filter.</div>
                @else
                    @foreach($menuItems as $menuItem)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <img src="{{ asset('storage/' . $menuItem->image) }}" alt="{{ $menuItem->name }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                <div class="card-body text-center">
                                    <h6 class="card-title">{{ $menuItem->name }}</h6>
                                    <p class="card-text">${{ number_format($menuItem->price, 2) }}</p>
                                    <button class="btn btn-primary btn-sm add-to-invoice" 
                                        data-id="{{ $menuItem->id }}" 
                                        data-name="{{ $menuItem->name }}" 
                                        data-price="{{ $menuItem->price }}" 
                                        data-description="{{ $menuItem->description }}" 
                                        data-category="{{ $menuItem->category }}">
                                        Add to Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
         <!-- Right Side: Invoice Layout -->
         <div class="col-md-6">
            <h3>Invoice</h3>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Details</h5>

                    <!-- Invoice Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-items">
                                <tr>
                                    <td colspan="4" class="text-center alert alert-info">No items added to invoice.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Price -->
                    <h6 class="mt-3"><strong>Total Price:</strong> <span id="total-price">$0.00</span></h6>

                    <!-- Invoice Form (Hidden) -->
                    <form id="invoice-form" action="{{ route('orders.store') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="order_id" id="form-order-id">
                        <input type="hidden" name="total_price" id="form-total-price">
                        <input type="hidden" name="items" id="form-items">
                    </form>

                    <!-- Print Invoice Button -->
                    <div class="col-md-12">
                        <button class="btn btn-success mt-3" id="print-invoice" disabled>Print Invoice</button>
                        <button class="btn btn-danger mt-3" id="cancel-invoice">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Initialize invoice items from localStorage or an empty array if not available
    let invoiceItems = JSON.parse(localStorage.getItem('invoiceItems')) || [];
    const invoiceContainer = document.getElementById("invoice-items");
    const totalPriceElem = document.getElementById("total-price");
    const printInvoiceBtn = document.getElementById("print-invoice");
    const cancelInvoiceBtn = document.getElementById("cancel-invoice");
    const invoiceForm = document.getElementById("invoice-form");
    const formOrderId = document.getElementById("form-order-id");
    const formTotalPrice = document.getElementById("form-total-price");
    const formItems = document.getElementById("form-items");


    // Update invoice on page load with the items in localStorage
    updateInvoice();

    // Add item to invoice
    document.querySelectorAll(".add-to-invoice").forEach(button => {
        button.addEventListener("click", function () {
            const itemId = this.dataset.id;
            const itemName = this.dataset.name;
            const itemPrice = parseFloat(this.dataset.price);
            const itemDescription = this.dataset.description;
            const itemCategory = this.dataset.category;

            let existingItem = invoiceItems.find(item => item.id === itemId);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                invoiceItems.push({ id: itemId, name: itemName, price: itemPrice, description: itemDescription, category: itemCategory, quantity: 1 });
            }

            // Save the updated invoice items to localStorage
            localStorage.setItem('invoiceItems', JSON.stringify(invoiceItems));

            updateInvoice();
        });
    });

    // Update the invoice UI
    function updateInvoice() {
        invoiceContainer.innerHTML = "";
        let totalPrice = 0;

        if (invoiceItems.length === 0) {
            invoiceContainer.innerHTML = `<tr><td colspan="5" class="text-center alert alert-info">No items added to invoice.</td></tr>`;
            totalPriceElem.innerText = "$0.00";
            printInvoiceBtn.disabled = true;
            return;
        }

        invoiceItems.forEach(item => {
            let itemTotal = item.price * item.quantity;
            totalPrice += itemTotal;

            let invoiceItemRow = document.createElement("tr");
            invoiceItemRow.innerHTML = `
                <td class="align-middle">${item.name}</td>
                <td class="align-middle text-center">
                    <button class="btn btn-sm btn-outline-danger decrease-item" data-id="${item.id}">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" class="form-control item-qty text-center d-inline-block" 
                        data-id="${item.id}" value="${item.quantity}" min="1" 
                        style="width: 64px; display: inline-block;">
                    <button class="btn btn-sm btn-outline-success increase-item" data-id="${item.id}">
                        <i class="fas fa-plus"></i>
                    </button>
                </td>
                <td class="align-middle text-end">$${itemTotal.toFixed(2)}</td>
                <td class="align-middle text-center">
                    <button class="btn btn-sm btn-danger delete-item" data-id="${item.id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            invoiceContainer.appendChild(invoiceItemRow);
        });

        totalPriceElem.innerText = `$${totalPrice.toFixed(2)}`;
        printInvoiceBtn.disabled = false;
        formTotalPrice.value = totalPrice;
        formItems.value = JSON.stringify(invoiceItems);
    }

    // Handle Quantity Change (Input & Buttons)
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('item-qty')) {
            const itemId = e.target.dataset.id;
            let existingItem = invoiceItems.find(item => item.id === itemId);
            let newQty = parseInt(e.target.value);

            if (existingItem && newQty >= 1) {
                existingItem.quantity = newQty;
                localStorage.setItem('invoiceItems', JSON.stringify(invoiceItems));
                updateInvoice();
            } else {
                e.target.value = existingItem.quantity;  // Prevent invalid input
            }
        }
    });

    // Handle Quantity Change (Input & Buttons)
    document.addEventListener('click', function (e) {
        const itemId = e.target.dataset.id;
        let existingItem = invoiceItems.find(item => item.id === itemId);

        // Decrease Quantity
        if (e.target.classList.contains('decrease-item')) {
            if (existingItem && existingItem.quantity > 1) {
                existingItem.quantity--;
                // Save the updated invoice items to localStorage
                localStorage.setItem('invoiceItems', JSON.stringify(invoiceItems));
                updateInvoice();
            }
        }

        // Increase Quantity
        if (e.target.classList.contains('increase-item')) {
            if (existingItem) {
                existingItem.quantity++;
                // Save the updated invoice items to localStorage
                localStorage.setItem('invoiceItems', JSON.stringify(invoiceItems));
                updateInvoice();
            }
        }

        // Delete Item
        if (e.target.classList.contains('delete-item')) {
            invoiceItems = invoiceItems.filter(item => item.id !== itemId);
            // Save the updated invoice items to localStorage
            localStorage.setItem('invoiceItems', JSON.stringify(invoiceItems));
            updateInvoice();
        }
    });

     // Cancel Invoice Button Event Listener
     cancelInvoiceBtn.addEventListener("click", function () {
        // Clear invoice items and update localStorage
        invoiceItems = [];
        localStorage.removeItem('invoiceItems');
        sessionStorage.removeItem('search');
        sessionStorage.removeItem('category');
        // Update UI to show empty invoice
        updateInvoice();
    });

    // Handle print invoice request
    printInvoiceBtn.addEventListener("click", function () {
        fetch("{{ route('orders.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                user_id: {{ auth()->id() }},
                items: invoiceItems
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.order_id) {
                    formOrderId.value = data.order_id;
                    invoiceForm.submit();

                    localStorage.removeItem('invoiceItems');
                    sessionStorage.removeItem('search');  
                    sessionStorage.removeItem('category'); 
                } else {
                    alert("Error creating order.");
                }
            })
            .catch(error => console.error("Error:", error));
    });


});
    // let invoiceItems = [];
    // const invoiceContainer = document.getElementById("invoice-items");
    // const totalPriceElem = document.getElementById("total-price");
    // const printInvoiceBtn = document.getElementById("print-invoice");
    // const invoiceForm = document.getElementById("invoice-form");
    // const formOrderId = document.getElementById("form-order-id");
    // const formTotalPrice = document.getElementById("form-total-price");
    // const formItems = document.getElementById("form-items");

    // document.querySelectorAll(".add-to-invoice").forEach(button => {
    //     button.addEventListener("click", function() {
    //         const itemId = this.dataset.id;
    //         const itemName = this.dataset.name;
    //         const itemPrice = parseFloat(this.dataset.price);
    //         const itemDescription = this.dataset.description;
    //         const itemCategory = this.dataset.category;

    //         let existingItem = invoiceItems.find(item => item.id === itemId);
    //         if (existingItem) {
    //             existingItem.quantity++;
    //         } else {
    //             invoiceItems.push({ id: itemId, name: itemName, price: itemPrice, description: itemDescription, category: itemCategory, quantity: 1 });
    //         }

    //         updateInvoice();
    //     });
    // });

    // function updateInvoice() {
    //     invoiceContainer.innerHTML = "";
    //     let totalPrice = 0;

    //     if (invoiceItems.length === 0) {
    //         invoiceContainer.innerHTML = `
    //             <tr>
    //                 <td colspan="4" class="text-center alert alert-info">No items added to invoice.</td>
    //             </tr>
    //         `;
    //         totalPriceElem.innerText = "$0.00";
    //         printInvoiceBtn.disabled = true;
    //         return;
    //     }

    //     invoiceItems.forEach(item => {
    //         let itemTotal = item.price * item.quantity;
    //         totalPrice += itemTotal;

    //         let invoiceItemRow = document.createElement("tr");
    //         invoiceItemRow.innerHTML = `
    //             <td>${item.name}</td>
    //             <td>${item.quantity}</td>
    //             <td>$${itemTotal.toFixed(2)}</td>
    //             <td>
    //                 <button class="btn btn-sm btn-danger decrease-item" data-id="${item.id}">-</button>
    //                 <button class="btn btn-sm btn-success increase-item" data-id="${item.id}">+</button>
    //                 <button class="btn btn-sm btn-danger delete-item" data-id="${item.id}">Delete</i></button>
    //             </td>
    //         `;
    //         invoiceContainer.appendChild(invoiceItemRow);
    //     });

    //     totalPriceElem.innerText = `$${totalPrice.toFixed(2)}`;
    //     printInvoiceBtn.disabled = false;
    //     formTotalPrice.value = totalPrice;
    //     formItems.value = JSON.stringify(invoiceItems);
    // }

    
    // // Handle Quantity Change (Input & Buttons)
    // document.addEventListener('input', function (e) {
    //     if (e.target.classList.contains('item-qty')) {
    //         const itemId = e.target.dataset.id;
    //         let existingItem = invoiceItems.find(item => item.id === itemId);
    //         let newQty = parseInt(e.target.value);

    //         if (existingItem && newQty >= 1) {
    //             existingItem.quantity = newQty;
    //             updateInvoice();
    //         } else {
    //             e.target.value = existingItem.quantity;  // Prevent invalid input
    //         }
    //     }
    // });

    // document.addEventListener('click', function (e) {
    //     const itemId = e.target.dataset.id;
    //     let existingItem = invoiceItems.find(item => item.id === itemId);

    //     // Decrease Quantity
    //     if (e.target.classList.contains('decrease-item')) {
    //         if (existingItem && existingItem.quantity > 1) {
    //             existingItem.quantity--;
    //             updateInvoice();
    //         }
    //     }

    //     // Increase Quantity
    //     if (e.target.classList.contains('increase-item')) {
    //         if (existingItem) {
    //             existingItem.quantity++;
    //             updateInvoice();
    //         }
    //     }

    //     // Delete Item
    //     if (e.target.classList.contains('delete-item')) {
    //         invoiceItems = invoiceItems.filter(item => item.id !== itemId);
    //         updateInvoice();
    //     }
    // });

    // document.addEventListener('click', function (e) {
    //     if (e.target.classList.contains('delete-item')) {
    //         const itemId = e.target.dataset.id;
    //         invoiceItems = invoiceItems.filter(item => item.id !== itemId);
    //         updateInvoice();
    //     }
    // });

    // printInvoiceBtn.addEventListener("click", function() {
    //     fetch("{{ route('orders.store') }}", {
    //         method: "POST",
    //         headers: {
    //             "Content-Type": "application/json",
    //             "X-CSRF-TOKEN": "{{ csrf_token() }}"
    //         },
    //         body: JSON.stringify({
    //             user_id: {{ auth()->id() }},
    //             items: invoiceItems
    //         })
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.order_id) {
    //             formOrderId.value = data.order_id;
    //             invoiceForm.submit();
    //         } else {
    //             alert("Error creating order.");
    //         }
    //     })
    //     .catch(error => console.error("Error:", error));
    // });
</script>
@endsection

