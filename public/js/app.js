//
//Glogabl constant ends
//
//
//All the routs to send ajax request
//
const routes = {
    store: "shopping-list/store",
};

let csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

//
//Glogabl constant ends
//
//
//
// main functionality related to UI starts
//

//add item
function addNewItemToList() {
    const itemName = getFieldValueById("itemName");
    const itemPrice = parseFloat(getFieldValueById("itemPrice"));

    if (itemName && itemPrice && !isNaN(itemPrice)) {
        //sending Ajax Request
        sendAjaxRequest(routes.store, "POST", {
            item_name: itemName,
            item_price: itemPrice,
        })
            .then((response) => {
                if (response.success) {
                    const itemList = document.getElementById("itemList");
                    const newItem = createShoppingItemElement(
                        itemName,
                        itemPrice
                    );

                    //show Alert is total Exceed the limit
                    checkIfBudgetExceeded(itemPrice);

                    itemList.appendChild(newItem);
                    clearInputFields();
                    initializeDragAndDropForItems();

                    //update total shoping amount in the From
                    updateTotalShoppingAmount(itemPrice);

                    document.getElementById("itemName").style.borderColor = "";
                    document.getElementById("itemPrice").style.borderColor = "";
                } else {
                    showToast(response.message, "danger");
                }
            })
            .catch((error) => {
                showToast("An error occurred while adding the item.", "danger");
            });
    } else if (!itemName || !itemPrice) {
        document.getElementById("itemName").style.borderColor = "";
        document.getElementById("itemPrice").style.borderColor = "";
        if (!itemName) {
            document.getElementById("itemName").style.borderColor = "red";
        }
        if (!itemPrice) {
            document.getElementById("itemPrice").style.borderColor = "red";
        }
    }
}

//
//main functionality related to UI ends
//
//
//
//addItem support function starts
//

// Create a dynamic new item element to add to the list
function createShoppingItemElement(name, price) {
    const newItem = document.createElement("li");
    newItem.classList.add(
        "list-group-item",
        "d-flex",
        "justify-content-between",
        "align-items-center"
    );
    newItem.setAttribute("draggable", "true");

    newItem.innerHTML = `
        <div>
            <input type="checkbox" class="form-check-input me-2">
            <span>${name}</span>
        </div>
        <div>
            £<span class="item-price">${price.toFixed(2)}</span>
            <button class="btn btn-sm btn-danger ms-2" onclick="removeItemFromList(this)">×</button>
        </div>
    `;
    return newItem;
}

// compare total and limit if exceed show alert
function checkIfBudgetExceeded(itemPrice) {
    const totalElement = document.getElementById("total");
    let currentTotal =
        parseFloat(totalElement.textContent.replace("£", "").trim()) || 0;

    const budgetElement = document.getElementById("budget");
    let currentBudget =
        parseFloat(budgetElement.innerText.replace("£", "").trim()) || 0;

    if (currentTotal + itemPrice > currentBudget) {
        alert(
            "Info: Your shopping list total exceeds the available budget. Please review your items and adjust accordingly.!"
        );
    }
}

// Reset input fields
function clearInputFields() {
    document.getElementById("itemName").value = "";
    document.getElementById("itemPrice").value = "";
}

// Initialize drag-and-drop events
function initializeDragAndDropForItems() {
    let items = document.querySelectorAll("#itemList .list-group-item");

    items.forEach((item) => {
        item.setAttribute("draggable", "true");
    });
}

//calculate total amount and set it in UI
function updateTotalShoppingAmount(itemPrice) {
    const totalElement = document.getElementById("total");
    let total = parseFloat(totalElement.textContent.replace("£", "")) || 0;
    total += itemPrice;
    totalElement.textContent = total.toFixed(2);
}

//
//
//
//
//
//Common functions
//

// Get input field values
function getFieldValueById(elementId) {
    return document.getElementById(elementId).value.trim();
}

//send ajaxRequest
function sendAjaxRequest(url, method, data = {}) {
    const formData = new FormData();

    // Append data to FormData
    Object.entries(data).forEach(([key, value]) => {
        formData.append(key, value);
    });

    // Add CSRF token automatically
    formData.append("_token", csrfToken);

    return fetch(url, {
        method: method.toUpperCase(),
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json(); // Parse and return the JSON response
        })
        .catch((error) => {
            console.error("AJAX request failed:", error);
            throw error; // Re-throw the error for further handling
        });
}

//Toast Message
function showToast(message, type = "success", delay = 3000) {
    // Create a new toast element
    const toastElement = document.createElement("div");

    toastElement.classList.add("toast", "fade", "show", `bg-${type}`);
    toastElement.setAttribute("role", "alert");
    toastElement.setAttribute("aria-live", "assertive");
    toastElement.setAttribute("aria-atomic", "true");

    toastElement.innerHTML = `
        <div class="toast-body">
            ${message}
        </div>
    `;

    // Append the toast to the toast container
    const toastContainer = document.getElementById("toastContainer");
    toastContainer.appendChild(toastElement);

    // Remove the toast after the specified delay
    setTimeout(() => {
        toastElement.classList.remove("show");
        setTimeout(() => toastElement.remove(), 150); // Wait for fade-out animation before removing it
    }, delay);
}
