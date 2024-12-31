//
//Glogabl constant ends
//
//
//All the routs to send ajax request
//
const routes = {
    store: "shopping-list/store",
    updatePurchaseStatus: "shopping-list/update",
    destroyItem: "/shopping-list/destroy",
    updateBudget: "shopping-list/update-budget",
    sendShoppingListByEmail: "shopping-list/send-by-email",
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

//update purchase status
document
    .getElementById("itemList")
    .addEventListener("change", handleCheckboxEvent);
document
    .getElementById("checkedItemsList")
    .addEventListener("change", handleCheckboxEvent);

//Remove Item
function removeItemFromList(button) {
    const listItem = button.closest("li");

    const itemName = listItem
        .querySelector("div:first-child span")
        .textContent.trim();

    const itemPrice = parseFloat(
        listItem
            .querySelector(".item-price")
            .textContent.replace("£", "")
            .trim()
    );

    sendAjaxRequest(routes.destroyItem, "POST", {
        item_name: itemName,
    })
        .then((response) => {
            if (response.success) {
                const budgetLabel = document.getElementById("budget");

                //if item is checked and removed then reduce total budget and set it to the UI
                budgetLabel.textContent = response.new_budget;

                decreaseTotalAmount(itemPrice);
                listItem.remove();
                togglePurchasedItemsHeader();
            } else {
                console.error("Error deleting item:", response.message);
            }
        })
        .catch((error) => {
            console.error("AJAX request failed:", error);
        });
}

// if user want to reset limit then show update UI element
function toggleBudgetUpdateContainer() {
    const currentBudget = document.getElementById("budget").innerText;

    document.getElementById("budgetUpdateAmount").value = currentBudget
        .replace("£", "")
        .trim();

    const updateBudgetContainer = document.getElementById(
        "updateBudgetContainer"
    );

    if (
        updateBudgetContainer.style.display === "none" ||
        updateBudgetContainer.style.display === ""
    ) {
        updateBudgetContainer.style.display = "block";
    } else {
        updateBudgetContainer.style.display = "none";
    }
}

//Update limit as a budget in DB and UI
function updateShoppingListBudget() {
    const budgetAmount = document
        .getElementById("budgetUpdateAmount")
        .value.trim();

    if (
        budgetAmount === "" ||
        isNaN(budgetAmount) ||
        parseFloat(budgetAmount) <= 0
    ) {
        alert("Please enter a valid budget amount.");
        return;
    }

    sendAjaxRequest(routes.updateBudget, "POST", {
        budget: budgetAmount,
    })
        .then((response) => {
            if (response.success) {
                const budgetLabel = document.getElementById("budget");
                budgetLabel.textContent = parseFloat(budgetAmount).toFixed(2);
                document.getElementById("updateBudgetContainer").style.display =
                    "none";
            } else {
                console.error("Error updating budget:", response.message);
            }
        })
        .catch((error) => {
            console.error("AJAX request failed:", error);
        });
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

//show email UI element if wanted to send email
function toggleEmailSendContainer() {
    const sendEmailContainer = document.getElementById("sendEmailContainer");

    if (
        sendEmailContainer.style.display === "none" ||
        sendEmailContainer.style.display === ""
    ) {
        sendEmailContainer.style.display = "block";
    } else {
        sendEmailContainer.style.display = "none";
    }
}

//Send Email to given email
function sendShoppingListEmail() {
    const emailAddress = getFieldValueById("emailAddress");

    sendAjaxRequest(routes.sendShoppingListByEmail, "POST", {
        emailAddress: emailAddress,
    })
        .then((response) => {
            if (response.success) {
                document.getElementById("updateBudgetContainer").style.display =
                    "none";
                showToast(response.message, "success");
            } else {
                console.error("Error sending shopping list:", response.message);
                showToast(response.message, "danger"); // Provide error feedback
            }
        })
        .catch((error) => {
            console.error("AJAX request failed:", error);
            showToast(
                "An error occurred while sending the shopping list.",
                "danger"
            ); // Provide error feedback
        });
}

//
//addItem support function ends
//
//
//
//update support function starts
//

// update purchase status in the database
function handleCheckboxEvent(event) {
    if (event.target.classList.contains("form-check-input")) {
        const checkbox = event.target;
        const listItem = checkbox.closest("li");
        const itemName = listItem.querySelector("span").textContent.trim();
        const isPurchased = checkbox.checked ? 1 : 0;

        sendAjaxRequest(routes.updatePurchaseStatus, "POST", {
            item_name: itemName,
            is_purchased: isPurchased,
        })
            .then((response) => {
                if (response.success) {
                    moveItemInBetweenPurchasedAndUnpurchasedList(
                        checkbox,
                        listItem
                    );

                    //check if any purchased item exist or not add set purchased item header according to that
                    togglePurchasedItemsHeader();
                } else {
                    console.error(
                        "Error updating purchase status:",
                        response.message
                    );
                }
            })
            .catch((error) => {
                console.error("AJAX request failed:", error);
            });
    }
}

//move item between purchaed and unpachaed list
function moveItemInBetweenPurchasedAndUnpurchasedList(checkbox, listItem) {
    const span = listItem.querySelector("span");

    const checkedItemsList = document.getElementById("checkedItemsList");
    const itemList = document.getElementById("itemList");

    if (checkbox.checked) {
        span.style.textDecoration = "line-through";
        checkedItemsList.appendChild(listItem);
    } else {
        span.style.textDecoration = "none";
        itemList.appendChild(listItem);
    }
}

//
//update support function ends
//
//
//
//remove support function starts
//

//Deduct amonunt when an item will be deleted
function decreaseTotalAmount(amount) {
    const totalElement = document.getElementById("total");
    let total = parseFloat(totalElement.textContent.replace("£", ""));
    total -= amount;
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

//check if purchased item exist and set level according to that
function togglePurchasedItemsHeader() {
    const checkedItemsList = document.getElementById("checkedItemsList");
    const checkedItemLabel = document.getElementById("checkedItemLabel");

    // Check if any items are in the checked items list
    if (checkedItemsList.children.length > 0) {
        if (!checkedItemLabel) {
            // Add header dynamically if it doesn't exist
            const header = document.createElement("h5");
            header.id = "checkedItemLabel";
            header.textContent = "Purchased Grocery";
            const container = document.getElementById("checkedItemsContainer");
            container.insertBefore(header, checkedItemsList);
        }
    } else {
        // Remove header if no items are in the list
        if (checkedItemLabel) {
            checkedItemLabel.remove();
        }
    }
}
